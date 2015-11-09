<?php
    if (!defined('INDEX'))
		exit('No direct script access allowed');
	$username = "";
    $email = "";
    if (isset($_GET['action'], $_GET['vk']) && $_GET['action'] == "Forgot" && $_GET['vk'] != "")
    {
        if ($stmt = $mainSystem->getMysqli()->prepare("SELECT user_id FROM verifies WHERE hash = ? AND is_verify = 0"))		{
            $stmt->bind_param('s', $_GET['vk']);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows == 0)
            {
                $stmt->close();
                header("Location: /?page=Login&action=Forgot");
            }
        } else
        {
            header("Location: /?page=Login&action=Forgot");
        }
    }
	if (isset($_POST['login']))
	{
		$username = $_POST['username'];
		$password = $_POST['password'];
		$remember = isset($_POST['remember']);		
		$return = $mainSystem->loginManager->doLogin($username, $password, $remember);		
		if ($return == "Success" || (isset($_SESSION['is_banned']) && $_SESSION['is_banned']))
			if (isset($_GET['refer']))
				header("Location: " . $_GET['refer']);
			else
				header("Location: /");
		else
            $error = $return;
			
		if ($remember)
			$remember = "checked";
	} else if (isset($_GET['action'], $_POST['forgot']) && $_GET['action'] == "Forgot" && !isset($_GET['vk']) && !isset($_POST['change']))
    {
        $username = $_POST['username'];

        $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

        if ($resp->is_valid)
        {
            $back = $mainSystem->loginManager->sendPasswordReset($username);

            if (is_array($back))
            {
				$username = $back['username'];
                $email = $back['email'];
            } else
            {
                $error = $back;
            }
        } else
        {
            $error = "The captcha you entered was incorrect";
        }
    } else if (isset($_GET['action'], $_GET['vk'], $_POST['change']) && $_GET['action'] == "Forgot")
    {
        $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

        if ($_GET['vk'] != "")
        {
            if ($resp->is_valid)
            {
                if ($_GET['password'] == $_GET['cpassword'])
                {
                    $back = $mainSystem->loginManager->changeUserPasswordFromEmail($_GET['vk'], $_POST['pass_length'], $_POST['password']);

                    if ($back !== true)
                    {
                        $error = $back;
                    } else
                    {
                        header("Location: /?page=Login&from=Reset");
                    }
                } else
                {
                    $error = "The passwords you entered do not match";
                }
            } else
            {
                $error = "The captcha you entered was incorrect";
            }
        } else
        {
            header("Location: /?page=Login");
        }
    }

    if (isset($_GET['from']) && $_GET['from'] == "Verify")
    {
        $success = "Your account has successfully been verified, you may now login";
    } else if (isset($_GET['from']) && $_GET['from'] == "Reset")
    {
        $success = "Your password has successfully been reset, you may now login";
    }
	
	if (isset($_SESSION['success']))
	{
		$success = $_SESSION['success'];	
		unset($_SESSION['success']);
	}
?>

<?php if (isset($success)) { ?><div class="g_12"><div class="success iDialog"><?php echo $success; ?></div></div><?php } ?>
<?php if (isset($error)) { ?><div class="g_12"><div class="error iDialog"><?php echo $error; ?></div></div><?php } ?>

<?php if (!isset($_GET['action'])) { ?>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">Login</h4>
        </div>
        <div class="widget_contents noPadding">
            <form action="/?page=Login" method="POST" onsubmit="this.password.value = hex_md5(this.pass.value); this.pass.value = '';">
                <div class="g_3"><span class="label">Username or Email <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="text" required name="username" value="<?php echo $username; ?>" />
                </div>

                <div class="g_3"><span class="label">Password <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="password" required name="pass" />
                </div>

                <input type="hidden" name="password" />

                <div class="g_3"></div>
                <div class="g_9">
					<label class="label ilC" for="rem_me" style="cursor: pointer;"><input type="checkbox" <?php echo isset($remember) ? $remember : ""; ?> name="remember" id="rem_me" class="simple_form" /><label class="label" style="position: absolute; margin-top: 2px; cursor: pointer;">Remember Me?</label></label>
					<input style="float: right;" type="submit" name="login" value="Login" class="submitIt simple_buttons" />
                    <a href="/?page=Login&action=Forgot" style="float: right; margin-right: 10px; margin-top: 3px;"><span class="label">Forgot Password?</span></a>
                </div>
            </form>
        </div>
    </div>
<?php } else if (isset($_GET['action']) && $_GET['action'] == "Forgot" && !isset($_GET['vk']) && (!isset($_POST['forgot']) || isset($error))) { ?>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">Forgot Password</h4>
        </div>
        <div class="widget_contents noPadding">
            <form action="/?page=Login&action=Forgot" method="POST">
                <div class="g_3"><span class="label">Username or Email <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="text" required name="username" value="<?php echo $username; ?>" />
                </div>

                <div class="g_3"><span class="label">Captcha <span class="must">*</span></span></div>
                <div class="g_9">
                    <?php echo recaptcha_get_html($publickey); ?>
                </div>

                <div class="g_3"></div>
                <div class="g_9">
                    <input style="float: right;" type="submit" name="forgot" value="Submit" class="submitIt simple_buttons" />
                </div>
            </form>
        </div>
    </div>
<?php } else if (isset($_GET['action'], $_POST['forgot']) && $_GET['action'] == "Forgot") { ?>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">Forgot Password</h4>
        </div>
        <div class="widget_contents">
            <span class="label">
                Thank you <?php echo $username; ?>. You have been sent an email telling you how to reset your password.
                <br /><br />
                The email has been sent to <?php echo $email; ?>.
            </span>
        </div>
    </div>
<?php } else if (isset($_GET['action'], $_GET['vk']) && $_GET['action'] == "Forgot") { ?>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">Forgot Password Reset</h4>
        </div>
        <div class="widget_contents noPadding">
            <form action="/?page=Login&action=Forgot&vk=<?php echo $_GET['vk']; ?>" method="POST" onsubmit="this.pass_length.value = this.pass.value.length;this.password.value = hex_md5(this.pass.value); this.pass.value = '';this.cpassword.value = hex_md5(this.cpass.value); this.cpass.value = '';">
                <div class="g_3"><span class="label">New Password <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="password" required name="pass" maxlength="20" />
                </div>

                <div class="g_3"><span class="label">Confirm Password <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="password" required name="cpass" maxlength="20" />
                </div>

                <input type="hidden" name="pass_length" />
                <input type="hidden" name="password" />
                <input type="hidden" name="cpassword" />

                <div class="g_3"><span class="label">Captcha <span class="must">*</span></span></div>
                <div class="g_9">
                    <?php echo recaptcha_get_html($publickey); ?>
                </div>

                <div class="g_3"></div>
                <div class="g_9">
                    <input style="float: right;" type="submit" name="change" value="Change Password" class="submitIt simple_buttons" />
                </div>
            </form>
        </div>
    </div>
<?php } ?>