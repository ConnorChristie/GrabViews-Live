<?php
    if (!defined('INDEX'))
		exit('No direct script access allowed');

	$username = "";
	$email = "";
	$cemail = "";
	$tac = "";

    $registerSuccess = false;
	
	if (isset($_POST['register']))
	{
        $ip_address = $_SERVER["REMOTE_ADDR"];
		$resp = recaptcha_check_answer($privatekey, $ip_address, $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
		
		$username = $_POST['username'];
		$email = $_POST['email'];
		$cemail = $_POST['cemail'];
        $pass_length = $_POST['pass_length'];
		$password = $_POST['password'];
		$cpassword = $_POST['cpassword'];
		$tac = isset($_POST['tac']) ? "checked" : "";
		
		if ($resp->is_valid)
		{
			if ($tac == "checked")
			{
				$referral = 0;				
                if (isset($_SESSION['ref']))				{
                    $referral = preg_replace("/[^0-9]+/", "", $_SESSION['ref']);
                }

				$return = $mainSystem->loginManager->doRegister($username, $email, $cemail, $pass_length, $password, $cpassword, $referral);
				
				if ($return == "Success")
				{
                    unset($_SESSION['ref']);

                    $registerSuccess = true;
				} else
					$error = $return;
			} else
				$error = "You have to agree to the Terms and Conditions";
		} else
			$error = "The captcha you entered was incorrect";
	}
?>

<?php if (isset($error)) { ?><div class="g_12"><div class="error iDialog"><?php echo $error; ?></div></div><?php } ?>

<?php if (!$registerSuccess) { ?>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">Register</h4>
        </div>
        <div class="widget_contents noPadding">
            <form action="" method="POST" onsubmit="this.pass_length.value = this.pass.value.length;this.password.value = hex_md5(this.pass.value); this.pass.value = '';this.cpassword.value = hex_md5(this.cpass.value); this.cpass.value = '';">
                <div class="g_3"><span class="label">Username <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="text" required name="username" value="<?php echo $username; ?>" maxlength="20" />
                </div>

                <div class="g_3"><span class="label">Email <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="email" required name="email" value="<?php echo $email; ?>" />
                </div>

                <div class="g_3"><span class="label">Confirm Email <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="email" required name="cemail" value="<?php echo $cemail; ?>" />
                </div>

                <div class="g_3"><span class="label">Password <span class="must">*</span></span></div>
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
                    <label class="label ilC" for="rem_me" style="cursor: pointer;"><input type="checkbox" <?php echo $tac; ?> name="tac" id="rem_me" class="simple_form" /><label class="label" style="position: absolute; margin-top: 2px;">I agree with the <a style="color: #545454;" href="/?page=Terms" target="_blank"><b style="vertical-align: top;">Terms and Conditions</b></a></label></label>
                    <input style="float: right;" type="submit" name="register" value="Register" class="submitIt simple_buttons" />
                </div>
            </form>
        </div>
    </div>
<?php } else { ?>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">Register</h4>
        </div>
        <div class="widget_contents">
            <span class="label">
                Thank you <?php echo $username; ?>. Your registration has been submitted.
                <br /><br />
                The community administrator has chosen to require validation for all email addresses. Within the next 10 minutes (usually instantly) you'll receive an email with instructions on the next step. Don't worry, it won't take long before you can start grabbing views!
                <br /><br />
                The email has been sent to <?php echo $email; ?>.
            </span>
        </div>
    </div>
<?php } ?>