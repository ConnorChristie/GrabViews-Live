<?php

class Users
{
	private $messageSystem;
	private $databaseSystem;
	
	private $allUsers;
	private $loggedUser;
	
	public function __construct($messageSystem, $databaseSystem)
	{
		$this->messageSystem = Messages::getInstance();
		$this->databaseSystem = Database::getInstance();
		
		
	}
	
	public function checkLoginStatus()
	{
		if (isset($_SESSION['login_string'], $_SESSION['user_id']))
		{
			$login_string = $_SESSION['login_string'];
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
		}
	}
	
	public function getLoggedData($key)
	{
		return $this->loggedUserData[$key];
	}
	
	public function getUserData($id, $key)
	{
		return $this->userData[$id][$key];
	}
}