<?php
require "phpmailer.class.php";

class LoginManager
{
    private $mysqli;
	
	private $currentUser;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
		
		$this->startSession();
    }

    public function startSession()
    {
        ini_set('session.use_only_cookies', 1);

        $session_name = 'gv_session';
        $secure = false;
        $httponly = true;
        $cookieParams = session_get_cookie_params();
		
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
        session_name($session_name);
        session_start();
        session_regenerate_id(true);
    }
	
	public function cleanInput($input)
	{
		return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
	}

    public function doLogout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['login_string']);
		
		setcookie("gv_remember", "", time() - 3600);
        session_destroy();

        header("Location: /");
    }
	
	private function generateCredentialsCookie()
	{
		$token = hash('md5', uniqid(mt_rand(), true) . uniqid(mt_rand(), true));
		$cookieToken = $token . ':' . hash_hmac('md5', $token, "amazing-Viewer**Grab&Views_");
		
		setcookie("gv_remember", $cookieToken, time() + 3600 * 24 * 30);
		
		return $token;
	}

    public function doLogin($username, $password, $remember, $is_db = false)
    {
        if ($stmt = $this->mysqli->prepare("SELECT id, `group`, password, salt, suspend_msg FROM users WHERE username = ? OR email = ?"))
		{
            $stmt->bind_param('ss', $username, $username);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($user_id, $group, $db_password, $salt, $suspend_msg);
            $stmt->fetch();

            if ($stmt->num_rows > 0)
			{
                $stmt->close();

                if ($group != 7)
				{
                    $time = $this->checkBrute($user_id);

                    if ($time !== true)					{
                        return "Too many failed login attempts. You are not able to login for " . (intval(date("i", $time)) != 0 ? intval(date("i", $time)) . " minute, " : " ") . intval(date("s", $time)) . " seconds";
                    } else					{
                        $password = $is_db ? $password : hash('md5', $password . hash('md5', $salt));
                        $ip_address = $_SERVER['REMOTE_ADDR'];

                        if ($db_password == $password)
						{
                            $_SESSION['is_banned'] = $group == 0 || $group == 1;
                            $_SESSION['ban_msg'] = $_SESSION['is_banned'] ? $suspend_msg : "";
                            $_SESSION['user_id'] = $user_id = preg_replace("/[^0-9]+/", "", $user_id);
							
                            if ($_SESSION['is_banned'])
							{
                                $return = $_SESSION['ban_msg'];
                            } else
							{
                                $user_browser = $_SERVER['HTTP_USER_AGENT'];

                                $_SESSION['login_string'] = hash('md5', $user_id . $password . $ip_address . $user_browser);
								
                                $stmt = $this->mysqli->prepare("DELETE FROM login_attempts WHERE user_id = ? AND ip = ?");
                                $stmt->bind_param("is", $user_id, $ip_address);
                                $stmt->execute();
                                $stmt->close();
								
								if ($remember)
								{
									$token = $this->generateCredentialsCookie($ip_address, $user_browser, $salt);
									
									$stmt = $this->mysqli->prepare("UPDATE users SET token = ?,ip = ? WHERE id = ?");
									$stmt->bind_param("ssi", $token, $ip_address, $user_id);
								} else
								{
									$stmt = $this->mysqli->prepare("UPDATE users SET ip = ? WHERE id = ?");
									$stmt->bind_param("si", $ip_address, $user_id);
								}
								
								$stmt->execute();
								$stmt->close();
                            }

                            $stmt = $this->mysqli->prepare("DELETE FROM login_attempts WHERE user_id = ? AND ip = ?");
                            $stmt->bind_param("is", $user_id, $ip_address);
                            $stmt->execute();
                            $stmt->close();
							
                            return isset($return) ? $return : "Success";
                        } else
						{
                            $now = time();

                            $stmt = $this->mysqli->prepare("INSERT INTO login_attempts (user_id, `time`, ip) VALUES (?,?,?)");
                            $stmt->bind_param("iis", $user_id, $now, $ip_address);
                            $stmt->execute();
                            $stmt->close();

                            return "The username/email or password you entered was incorrect";
                        }
                    }
                } else
				{
                    return "You have to verify your account through your email before logging in";
                }
            } else
			{
                return "The username/email or password you entered was incorrect";
            }
        } else
		{
            return "There was an issue with the database please try again later";
        }
    }

    public function isBanned()
    {
        return isset($_SESSION['is_banned']) ? $_SESSION['is_banned'] : false;
    }

    public function getBanMessage()
    {
        return isset($_SESSION['ban_msg']) ? ($_SESSION['ban_msg'] != "" ? $_SESSION['ban_msg'] : "Your account has been banned") : "Your account has been banned";
    }

    public function doRegister($username, $email, $cemail, $pass_length, $password, $cpassword, $referral)
    {
        if ($email == $cemail)
		{
            if ($password == $cpassword)
			{
                $ip = $_SERVER['REMOTE_ADDR'];
                $time = time();

                $salt = $this->getRandomSalt();
                $password = hash('md5', $password . hash('md5', $salt));

                $stmt = $this->mysqli->prepare("SELECT id FROM users WHERE register_ip = ?");
                $stmt->bind_param('s', $ip);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows == 0)
				{
                    $stmt->close();

                    $stmt = $this->mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                    $stmt->bind_param('ss', $username, $email);
                    $stmt->execute();
                    $stmt->store_result();

                    if ($stmt->num_rows == 0)
					{
                        $stmt->close();

                        $join_credits = 50;

                        $stmt = $this->mysqli->prepare("SELECT id FROM users WHERE id = ?");
                        $stmt->bind_param('i', $referral);
                        $stmt->execute();
                        $stmt->store_result();

                        if ($stmt->num_rows != 0)						{
                            $stmt = $this->mysqli->prepare("UPDATE users SET credits=credits+CASE `group` WHEN 2 THEN 100 ELSE 200 END WHERE id = ?");
                            $stmt->bind_param('i', $referral);
                            $stmt->execute();
                            $stmt->close();

                            $join_credits = 100;
                        } else						{
                            $referral = 0;
						}
						
						$join_credits *= 100;						
						if ($stmt = $this->mysqli->prepare("INSERT INTO users (username, email, password, pass_length, salt, ip, register_ip, join_date, referral, credits) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"))						{
							$stmt->bind_param('sssisssiii', $username, $email, $password, $pass_length, $salt, $ip, $ip, $time, $referral, $join_credits);
							$stmt->execute();
							$stmt->close();														if ($this->sendUserEmail($this->mysqli->insert_id, $username, $email))							{
								return "Success";							} else								return "Error sending validation email, please try again later";
						} else
							return $this->mysqli->error;
                    } else
                        return "Username or Email is already in use";
                } else
                    return "You have already made an account with this IP address";
            } else
                return "The passwords you entered do not match";
        } else
            return "The emails you entered do not match";
    }

    public function sendPasswordReset($username)
    {
        if ($stmt = $this->mysqli->prepare("SELECT id, `group`, username, email FROM users WHERE username = ? OR email = ? LIMIT 1"))
		{
            $stmt->bind_param('ss', $username, $username);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($id, $group, $username, $email);
            $stmt->fetch();

            if ($stmt->num_rows == 1)
			{
                $stmt->close();

                if ($group != 7 && $group != 0 && $group != 1)
                {
                    if ($this->sendUserEmail($id, $username, $email, false))
                    {
                        return array("username" => $username, "email" => $email);
                    } else
                    {
                        return "Error sending password reset email, please try again later";
                    }
                } else
                {
                    return "You are not allowed to reset your password";
                }
            } else
            {
                return "The username or email you entered does not appear to belong to anyone in our system";
            }
        }

        return "Error sending password reset email, please try again later";
    }

    private function sendUserEmail($id, $username, $email, $isVerify = true)
    {
		$hash = hash('md5', $id . $username . $email . time());				$headers  = "MIME-Version: 1.0" . "\r\n";		$headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";		$headers .= "From: GrabViews <noreply@grabviews.com>" . "\r\n";		
        if ($isVerify)
        {
            $subject = "New Registration at GrabViews";
            $body = "
            $username,<br />
            You have received this email because this email address<br />
            was used during registration for our site.			<br /><br />
            If you did not register at our site, please disregard this<br />
            email. You do not need to unsubscribe or take any further action.			<br /><br />
            ------------------------------------------------<br />
            Activation Instructions<br />
            ------------------------------------------------			<br /><br />
            Thank you for registering!			<br /><br />
            We require that you \"validate\" your registration to ensure that<br />
            the email address you entered was correct. This protects against<br />
            unwanted spam and malicious abuse.			<br /><br />
            To activate your account, simply click on the following link:<br />
            <a href='http://viewgrab.com/?action=Verify&vk=$hash' target='_blank'>http://viewgrab.com/?action=Verify&vk=$hash</a>			<br /><br />
            Thank you for registering and enjoy your views!
			<br /><br />
			This email has been sent from http://viewgrab.com/
            ";
        } else
        {
            $subject = "GrabViews Password Reset";
            $body = "
            ------------------------------------------------<br />
            Password Reset Instructions<br />
            ------------------------------------------------			<br /><br />
            To reset your password, simply click on the following link:<br />
            <a href='http://viewgrab.com/?page=Login&action=Forgot&vk=$hash' target='_blank'>http://viewgrab.com/?page=Login&action=Forgot&vk=$hash</a>
			<br /><br />
			This email has been sent from http://viewgrab.com/
            ";
		}				mail($email, $subject, $body, $headers);		
		if ($stmt = $this->mysqli->prepare("INSERT INTO verifies (user_id, is_verify, hash) VALUES (?, ?, ?)"))		{
			$stmt->bind_param('iis', $id, $isVerify, $hash);
			$stmt->execute();
			$stmt->close();
			
			return true;
		}

        return false;
    }
	
    private function getRandomSalt()
    {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ~!@#$%^&*()_+=-?"), 0, 8);
    }

    private function checkBrute($user_id)
    {
        $now = time();
        $valid_attempts = $now - (2 * 60);
        $ip_address = $_SERVER['REMOTE_ADDR'];

        if ($stmt = $this->mysqli->prepare("SELECT `time` FROM login_attempts WHERE user_id = ? AND `time` > ? AND ip = ? ORDER BY `time` DESC"))
		{
            $stmt->bind_param('iis', $user_id, $valid_attempts, $ip_address);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($time);
            $stmt->fetch();

            if ($stmt->num_rows > 3)
			{
                return (2 * 60) - ($now - $time);
            } else
			{
                return true;
            }
        }
    }

    public function checkUserLogin()
    {
        if (isset($_SESSION['login_string'], $_SESSION['user_id']))
		{
            $user_id = $_SESSION['user_id'];

            if ($stmt = $this->mysqli->prepare("SELECT password, salt FROM users WHERE id = ?")) {
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($password, $salt);
                    $stmt->fetch();
                    $stmt->close();

                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];

                    $login_string = hash('md5', $user_id . $password . $ip_address . $user_browser);

                    if ($login_string == $_SESSION['login_string']) {
                        return $user_id;
                    }
                }
            }
			
			unset($_SESSION['login_string']);
			
			return false;
        } else if (isset($_COOKIE['gv_remember']))
		{
			list($token, $hmac) = explode(':', $_COOKIE['gv_remember'], 2);
			
			if ($hmac == hash_hmac('md5', $token, "amazing-Viewer**Grab&Views_"))
			{
				if ($stmt = $this->mysqli->prepare("SELECT username, password FROM users WHERE token = ?"))
				{
					$stmt->bind_param('s', $token);
					$stmt->execute();
					$stmt->store_result();
					
					if ($stmt->num_rows == 1)
					{
						$stmt->bind_result($username, $password);
						$stmt->fetch();
						$stmt->close();
						
						$return = $this->doLogin($username, $password, true, true);
						
						if ($return == "Success")
							return true;
					}
				}
			}
			
			return false;
		}
    }

    public function getUserData($user_id)
    {
        if ($query = $this->mysqli->query("SELECT * FROM users WHERE id = " . intval($this->mysqli->real_escape_string($user_id))))
		{
            $result = $query->fetch_assoc();

            return $result;
        }

        return array();
    }

    public function checkBanState($user_id)
    {
        if ($stmt = $this->mysqli->prepare("SELECT `group`, suspend_msg FROM users WHERE id = ? LIMIT 1"))
		{
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1)
			{
                $stmt->bind_result($group, $suspend_msg);
                $stmt->fetch();
                $stmt->close();

                $_SESSION['is_banned'] = $group == 0 || $group == 1;
                $_SESSION['ban_msg'] = $_SESSION['is_banned'] ? empty($suspend_msg) ? ($group == 0 ? "Your account has been suspended" : "") : $suspend_msg : "";
            }
        }
    }

    public function isLoggedIn(&$user)
    {
        isset($_SESSION['user_id']) ? $this->checkBanState($_SESSION['user_id']) : null;
		
        if ($user_id = $this->checkUserLogin())
		{
            $this->currentUser = $user = $this->getUserData($user_id);
			
            return true;
        }

        return false;
    }

    public function updateUserData($user_id, $name, $value)
    {
        if ($name != "group" && $name != "credits")
		{
            $stmt = $this->mysqli->prepare("SELECT id FROM users WHERE " . $name . " = ? AND id != ?");
            $stmt->bind_param('si', $value, $user_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 0)
			{
                $stmt->close();

                $stmt = $this->mysqli->prepare("UPDATE users SET " . $name . " = ? WHERE id = ?");
                $stmt->bind_param('si', $value, $user_id);
                $stmt->execute();
                $stmt->close();
            }
        } else if ($name == "group")
		{
            $stmt = $this->mysqli->prepare("SELECT id FROM groups WHERE title = ?");
            $stmt->bind_param('s', $value);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($group_id);
                $stmt->fetch();
                $stmt->close();

                $stmt = $this->mysqli->prepare("UPDATE users SET `group` = ? WHERE id = ?");
                $stmt->bind_param('ii', $group_id, $user_id);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $stmt = $this->mysqli->prepare("UPDATE users SET " . $name . " = ? WHERE id = ?");
            $stmt->bind_param('si', $value, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function checkUserPassword($curr_password)
    {
        return hash('md5', $curr_password . hash('md5', $this->currentUser['salt'])) == $this->currentUser['password'];
    }

    public function changeUserName($username)
    {
        if ($stmt = $this->mysqli->prepare("SELECT id FROM users WHERE username=?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows == 0) {
                $stmt->close();

                $stmt = $this->mysqli->prepare("UPDATE users SET username=? WHERE id=?");
                $stmt->bind_param('si', $username, $this->currentUser['id']);
                $stmt->execute();
                $stmt->close();

                return true;
            }
        }

        return false;
    }

    public function changeUserPasswordFromEmail($vk, $pass_length, $password)
    {
        if ($stmt = $this->mysqli->prepare("SELECT user_id FROM verifies WHERE hash = ? AND is_verify = 0")) {
            $stmt->bind_param('s', $vk);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows != 0)
            {
                $stmt->bind_result($user_id);
                $stmt->fetch();
                $stmt->close();

                $salt = $this->getRandomSalt();
                $password = hash('md5', $password . hash('md5', $salt));

                $stmt = $this->mysqli->prepare("UPDATE users SET password = ?, pass_length = ?, salt = ? WHERE id = ?");
                $stmt->bind_param('sisi', $password, $pass_length, $salt, $user_id);
                $stmt->execute();
                $stmt->close();

                $stmt = $this->mysqli->prepare("DELETE FROM verifies WHERE user_id = ? AND is_verify = 0");
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $stmt->close();

                return true;
            } else
            {
                return "Could not verify the Validation Key";
            }
        }

        return "Could not verify the Validation Key";
    }

    public function changeUserPassword($pas_length, $password)
    {
        $salt = $this->getRandomSalt();
        $password = hash('md5', $password . hash('md5', $salt));

        $stmt = $this->mysqli->prepare("UPDATE users SET password = ?, pass_length = ?, salt = ? WHERE id = ?");
        $stmt->bind_param('sisi', $password, $pas_length, $salt, $this->currentUser['id']);
        $stmt->execute();
        $stmt->close();

        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        $_SESSION['login_string'] = hash('md5', $this->currentUser['id'] . $password . $ip_address . $user_browser);
    }

    public function getAllUsers()
    {
        if ($this->isUserAdmin()) {
            return $this->fetch_all_assoc("SELECT * FROM users  ORDER BY join_date DESC, id DESC");
        }
    }

    public function getTickets()
    {
        return $this->fetch_all_assoc(
            "SELECT * FROM tickets WHERE user_id = " . $this->currentUser['id'] . " ORDER BY date_time DESC"
        );
    }

    public function getTicket($id)
    {
        if ($this->isUserAdmin()) {
            $result = $this->mysqli->query(
                "SELECT * FROM tickets WHERE id = " . $id
            );

            return $result->fetch_assoc();
        }
    }

    public function getUserTicket($id)
    {
        $result = $this->mysqli->query(
            "SELECT * FROM tickets WHERE id = " . $id . " AND user_id = " . $this->currentUser['id']
        );

        return $result->fetch_assoc();
    }

    public function getAllTickets()
    {
        if ($this->isUserAdmin()) {
            return $this->fetch_all_assoc(
                "SELECT * FROM tickets ORDER BY date_time DESC"
            );
        }
    }
	
	public function deleteTicket($id)
	{
		if ($this->isUserAdmin())
		{
			$stmt = $this->mysqli->prepare("DELETE FROM tickets WHERE id = ?");
			$stmt->bind_param("i", $_GET['ticket_id']);
			$stmt->execute();
			$stmt->close();
			
			$stmt = $this->mysqli->prepare("DELETE FROM ticket_messages WHERE ticket_id = ?");
			$stmt->bind_param("i", $_GET['ticket_id']);
			$stmt->execute();
			$stmt->close();
		}
	}

    public function markTicket($id, $status)
    {
        if ($this->isUserAdmin())
		{
            $stmt = $this->mysqli->prepare("UPDATE tickets SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function sendMessage($id, $user_id, $message, $fromAdmin, $isAnswer)
    {
        $status = ($isAnswer ? "Answered" : "Question");

        $stmt = $this->mysqli->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();

        $date_time = time();

        $stmt = $this->mysqli->prepare("INSERT INTO ticket_messages (ticket_id, user_id, date_time, from_admin, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiis", $id, $user_id, $date_time, $fromAdmin, $message);
        $stmt->execute();
        $stmt->close();
    }

    public function getMessages($id)
    {
        return $this->fetch_all_assoc(
            "SELECT * FROM ticket_messages WHERE ticket_id = " . $id . " ORDER BY date_time ASC"
        );
    }
	
	public function getAllTransactions()
    {
        if ($this->isUserAdmin()) {
			return $this->fetch_all_assoc(
				"SELECT * FROM paypal_transactions"
			);
        }
    }
	
	public function getTransaction($id)
	{
		if ($this->isUserAdmin()) {
			$result = $this->mysqli->query(
                "SELECT * FROM paypal_transactions WHERE id='" . $id . "'"
            );

            return $result->fetch_assoc();
		}
	}
	
    public function markBadTransactionAsResolved($id)
    {
        if ($this->isUserAdmin()) {
            $stmt = $this->mysqli->prepare("UPDATE paypal_transactions SET resolved=1 WHERE id=?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function suspendUser($user_id, $message)
    {
        if ($this->isUserAdmin()) {
            $stmt = $this->mysqli->prepare("UPDATE users SET `group`=0,suspend_msg=? WHERE id=?");
            $stmt->bind_param('si', $message, $user_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->mysqli->prepare("SELECT username FROM users WHERE id=?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($username);
            $stmt->fetch();
            $stmt->close();

            return $username;
        }
    }

    public function banUser($t_id, $user_id, $message)
    {
        if ($this->isUserAdmin())
		{
            $stmt = $this->mysqli->prepare("UPDATE users SET `group`=1,suspend_msg=? WHERE id=?");
            $stmt->bind_param('si', $message, $user_id);
            $stmt->execute();
            $stmt->close();

            if ($t_id != -1) $this->markBadTransactionAsResolved($t_id);

            $stmt = $this->mysqli->prepare("SELECT username FROM users WHERE id=?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($username);
            $stmt->fetch();
            $stmt->close();

            return $username;
        }
    }

    public function unSuspendUser($user_id)
    {
        if ($this->isUserAdmin()) {
            $stmt = $this->mysqli->prepare("UPDATE users SET `group`=2,suspend_msg='' WHERE id=?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->mysqli->prepare("SELECT username FROM users WHERE id=?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($username);
            $stmt->fetch();
            $stmt->close();

            return $username;
        }
    }

    public function unBanUser($user_id)
    {
        if ($this->isUserAdmin()) {
            $stmt = $this->mysqli->prepare("UPDATE users SET `group`=2,suspend_msg='' WHERE id=?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->mysqli->prepare("SELECT username FROM users WHERE id=?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($username);
            $stmt->fetch();
            $stmt->close();

            return $username;
        }
    }

    public function deleteUser($user_id)
    {
        if ($this->isUserAdmin()) {
            $stmt = $this->mysqli->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->mysqli->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->bind_result($username);
            $stmt->fetch();
            $stmt->close();

            return $username;
        }
    }

    public function getGroupInfo($group_id)
    {
        $result = $this->mysqli->query(
            "SELECT * FROM groups WHERE id='" . $group_id . "'"
        );

        return $result->fetch_assoc();
    }
	
	public function isUserMod()
	{
        return $this->currentUser['group'] == 8;
	}

    public function isUserAdmin()
    {
        return $this->currentUser['group'] == 6 || $this->currentUser['group'] == 8;
    }

    public function fetch_all_assoc($query)
    {
        $result = $this->mysqli->query($query);

        $assoc = array();

        $init = 0;

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $assoc[$init] = $row;

            $init = $init + 1;
        }

        $result->close();

        return $assoc;
    }
}
?>