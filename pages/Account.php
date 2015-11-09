<?php
    if (!defined('INDEX'))
		exit('No direct script access allowed');

    if (isset($_SESSION['success']))
	{
        $success = $_SESSION['success'];
		
        unset($_SESSION['success']);
    }

    $username = "";
    $email = "";
    $cemail = "";

    if (isset($_GET['action']))
	{
        if ($_GET['action'] == "cu" && isset($_POST['curr_p'], $_POST['username']))
		{
            if ($mainSystem->loginManager->checkUserPassword($_POST['curr_p']))
			{
				$_POST['username'] = $mainSystem->loginManager->cleanInput($_POST['username']);
				
                if ($mainSystem->loginManager->changeUserName($_POST['username']))
				{
                    $_SESSION['success'] = "Your username has successfully been changed to '" . $_POST['username'] . "'";
					
                    header("Location: /?page=Account");
                } else
				{
                    $error = "Somebody else is already using that username";
                }
            } else
			{
                $error = "The password you entered was incorrect";
            }

            $username = $_POST['username'];
        } else if ($_GET['action'] == "cp" && isset($_POST['curr_p'], $_POST['pass'], $_POST['cpass'], $_POST['pass_length']))
		{
            if ($mainSystem->loginManager->checkUserPassword($_POST['curr_p']))
			{
                if ($_POST['pass'] == $_POST['cpass'])
				{
                    $mainSystem->loginManager->changeUserPassword($_POST['pass_length'], $_POST['pass']);

                    $fake_pass = "";
                    for ($i = 0; $i < $_POST['pass_length']; $i++) { $fake_pass .= "*"; }

                    $_SESSION['success'] = "Your password has successfully been changed to '" . $fake_pass . "'";
                    header("Location: /?page=Account");
                } else
				{
                    $error = "The passwords you entered do not match";
                }
            } else
			{
                $error = "The password you entered was incorrect";
            }
        }
    }
?>

<?php if (isset($success)) { ?><div class="g_12"><div class="success iDialog"><?php echo $success; ?></div></div><?php } ?>
<?php if (isset($error)) { ?><div class="g_12"><div class="error iDialog"><?php echo $error; ?></div></div><?php } ?>

<?php if (isset($_GET['action']) && $_GET['action'] == "cu") { ?>
    <div class="g_9">
        <div class="widget_header">
            <h4 class="widget_header_title">Change Username</h4>
        </div>
        <div class="widget_contents">
            <form action="" method="POST"onsubmit="this.curr_p.value = hex_md5(this.curr_pass.value); this.curr_pass.value = '';">
                <div class="g_3"><span class="label">Current Password <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="password" required name="curr_pass" value="" maxlength="20" />
                </div>

                <div class="g_3"><span class="label">New Username <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="text" required name="username" value="<?php echo $username; ?>" maxlength="20" />
                </div>

                <input type="hidden" name="curr_p" value="" />

                <div class="g_3"></div>
                <div class="g_9">
                    <input style="float: right;" type="submit" name="cu" value="Change" class="submitIt simple_buttons" />
                    <input style="float: right; margin-right: 10px;" type="button" onclick="window.location='/?page=Account';" value="Cancel" class="submitIt simple_buttons" />
                </div>
            </form>

            <br style="clear: both;"/>
        </div>
    </div>
<?php } else if (isset($_GET['action']) && $_GET['action'] == "cp") { ?>
    <div class="g_9">
        <div class="widget_header">
            <h4 class="widget_header_title">Change Password</h4>
        </div>
        <div class="widget_contents">
            <form action="" method="POST" onsubmit="this.pass_length.value = this.password.value.length; this.curr_p.value = hex_md5(this.curr_pass.value); this.curr_pass.value = ''; this.pass.value = hex_md5(this.password.value); this.password.value = ''; this.cpass.value = hex_md5(this.cpassword.value); this.cpassword.value = '';">
                <div class="g_3"><span class="label">Current Password <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="password" required name="curr_pass" value="" maxlength="20" />
                </div>

                <div class="g_3"><span class="label">New Password <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="password" required name="password" value="" maxlength="20" />
                </div>

                <div class="g_3"><span class="label">Confirm Password <span class="must">*</span></span></div>
                <div class="g_9">
                    <input class="simple_field" type="password" required name="cpassword" value="" maxlength="20" />
                </div>

                <input type="hidden" name="curr_p" value="" />
                <input type="hidden" name="pass" value="" />
                <input type="hidden" name="cpass" value="" />
                <input type="hidden" name="pass_length" />

                <div class="g_3"></div>
                <div class="g_9">
                    <input style="float: right;" type="submit" name="cu" value="Change" class="submitIt simple_buttons" />
                    <input style="float: right; margin-right: 10px;" type="button" onclick="window.location='/?page=Account';" value="Cancel" class="submitIt simple_buttons" />
                </div>
            </form>

            <br style="clear: both;"/>
        </div>
    </div>
<?php } else if (isset($_GET['action']) && $_GET['action'] == "ua") { ?>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">Change Account Type</h4>
        </div>
        <div class="widget_contents noPadding">
            <table class="tables">
                <thead>
                <tr>
                    <th style="width: 25%; color: <?php $group = $mainSystem->loginManager->getGroupInfo(2); echo $group['color']; ?>;"><?php echo $group['title']; ?></th>
                    <th style="width: 25%; color: <?php $group = $mainSystem->loginManager->getGroupInfo(3); echo $group['color']; ?>;"><?php echo $group['title']; ?></th>
                    <th style="width: 25%; color: <?php $group = $mainSystem->loginManager->getGroupInfo(4); echo $group['color']; ?>;"><?php echo $group['title']; ?></th>
                    <th style="width: 25%; color: <?php $group = $mainSystem->loginManager->getGroupInfo(5); echo $group['color']; ?>;"><?php echo $group['title']; ?></th>
                </tr>
                </thead>
                <tbody id="videos">
                    <tr>
                        <td style="text-align: left; padding-left: 30px;">&bull; 50 Credit Signup Bonus</td>
                        <td style="text-align: left; padding-left: 30px;">&bull; 10,000 Credits per month</td>
                        <td style="text-align: left; padding-left: 30px;">&bull; 50,000 Credits per month</td>
                        <td style="text-align: left; padding-left: 30px;">&bull; 100,000 Credits per month</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; padding-left: 30px;">&bull; Max of 5 videos</td>
                        <td style="text-align: left; padding-left: 30px;">&bull; Add unlimited amount of videos</td>
                        <td style="text-align: left; padding-left: 30px;">&bull; Add unlimited amount of videos</td>
                        <td style="text-align: left; padding-left: 30px;">&bull; Add unlimited amount of videos</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; padding-left: 30px;">&bull; Earn 100 credits per referral</td>
                        <td style="text-align: left; padding-left: 30px;">&bull; Earn 200 credits per referral</td>
                        <td style="text-align: left; padding-left: 30px;">&bull; Earn 200 credits per referral</td>
                        <td style="text-align: left; padding-left: 30px;">&bull; Earn 200 credits per referral</td>
                    </tr>
                    <tr>
                        <td style="text-align: right; padding-right: 20px;"><b>$0.00</b> USD per Month</td>
                        <td style="text-align: right; padding-right: 20px;"><b>$5.00</b> USD per Month</td>
                        <td style="text-align: right; padding-right: 20px;"><b>$15.00</b> USD per Month</td>
                        <td style="text-align: right; padding-right: 20px;"><b>$30.00</b> USD per Month</td>
                    </tr>
                    <tr>
                        <td style="padding-top: 10px; padding-bottom: 10px; padding-right: 20px;">
                            <form action="https://<?php echo PAYPAL_URL; ?>/cgi-bin/webscr" method="get">
                                <input type="hidden" name="cmd" value="_subscr-find">
                                <input type="hidden" name="alias" value="<?php echo PAYPAL_EMAIL; ?>">
                                <input <?php if ($user['group'] == 2) { ?>disabled="disabled"<?php } ?> style="float: right;" id="downgrade" type="submit" value="Downgrade" class="submitIt simple_buttons" />
                            </form>
                            <script type="text/javascript">
                                $(function() {
                                    $("#downgrade").blur();
                                });
                            </script>
                        </td>
                        <td style="padding-top: 10px; padding-bottom: 10px; padding-right: 20px;">
                            <form action="https://<?php echo PAYPAL_URL; ?>/cgi-bin/webscr" method="post">
                                <input type="hidden" name="cmd" value="_xclick-subscriptions">
                                <input type="hidden" name="business" value="<?php echo PAYPAL_EMAIL; ?>">
                                <input type="hidden" name="item_name" value="Level 1 Premium, User ID: <?php echo $user['id']; ?>">
                                <input type="hidden" name="item_number" value="3">
                                <input type="hidden" name="no_shipping" value="1">
                                <input type="hidden" name="no_note" value="1">
                                <input type="hidden" name="currency_code" value="USD">
                                <input type="hidden" name="lc" value="US">
                                <input type="hidden" name="bn" value="PP-SubscriptionsBF">
                                <input type="hidden" name="notify_url" value="http://<?php echo WEBSITE_URL; ?>/?paypal=paypal_ipn_sub">
                                <input type="hidden" name="a3" value="5">
                                <input type="hidden" name="p3" value="1">
                                <input type="hidden" name="t3" value="M">
                                <input type="hidden" name="src" value="1">
                                <input type="hidden" name="sra" value="1">
                                <input type="hidden" name="cancel_return" value="http://<?php echo WEBSITE_URL; ?>/?page=Account&action=ua">
                                <input type="hidden" name="return" value="http://<?php echo WEBSITE_URL; ?>/?page=Account&action=ua&paypal=success">

                                <input <?php if ($user['group'] != 2) { ?>disabled="disabled"<?php } ?> style="float: right;" id="upgrade" type="submit" value="Upgrade" class="submitIt simple_buttons" />
                            </form>
                            <script type="text/javascript">
                                $(function() {
                                    $("#upgrade").blur();
                                });
                            </script>
                        </td>
                        <td style="padding-top: 10px; padding-bottom: 10px; padding-right: 20px;">
                            <form action="https://<?php echo PAYPAL_URL; ?>/cgi-bin/webscr" method="post">
                                <input type="hidden" name="cmd" value="_xclick-subscriptions">
                                <input type="hidden" name="business" value="<?php echo PAYPAL_EMAIL; ?>">
                                <input type="hidden" name="item_name" value="Level 2 Premium, User ID: <?php echo $user['id']; ?>">
                                <input type="hidden" name="item_number" value="4">
                                <input type="hidden" name="no_shipping" value="1">
                                <input type="hidden" name="no_note" value="1">
                                <input type="hidden" name="currency_code" value="USD">
                                <input type="hidden" name="lc" value="US">
                                <input type="hidden" name="bn" value="PP-SubscriptionsBF">
                                <input type="hidden" name="notify_url" value="http://<?php echo WEBSITE_URL; ?>/?paypal=paypal_ipn_sub">
                                <input type="hidden" name="a3" value="15">
                                <input type="hidden" name="p3" value="1">
                                <input type="hidden" name="t3" value="M">
                                <input type="hidden" name="src" value="1">
                                <input type="hidden" name="sra" value="1">
                                <input type="hidden" name="cancel_return" value="http://<?php echo WEBSITE_URL; ?>/?page=Account&action=ua">
                                <input type="hidden" name="return" value="http://<?php echo WEBSITE_URL; ?>/?page=Account&action=ua&paypal=success">

                                <input <?php if ($user['group'] != 2) { ?>disabled="disabled"<?php } ?> style="float: right;" id="upgrade" type="submit" value="Upgrade" class="submitIt simple_buttons" />
                            </form>
                            <script type="text/javascript">
                                $(function() {
                                    $("#upgrade").blur();
                                });
                            </script>
                        </td>
                        <td style="padding-top: 10px; padding-bottom: 10px; padding-right: 20px;">
                            <form action="https://<?php echo PAYPAL_URL; ?>/cgi-bin/webscr" method="post">
                                <input type="hidden" name="cmd" value="_xclick-subscriptions">
                                <input type="hidden" name="business" value="<?php echo PAYPAL_EMAIL; ?>">
                                <input type="hidden" name="item_name" value="Level 3 Premium, User ID: <?php echo $user['id']; ?>">
                                <input type="hidden" name="item_number" value="5">
                                <input type="hidden" name="no_shipping" value="1">
                                <input type="hidden" name="no_note" value="1">
                                <input type="hidden" name="currency_code" value="USD">
                                <input type="hidden" name="lc" value="US">
                                <input type="hidden" name="bn" value="PP-SubscriptionsBF">
                                <input type="hidden" name="notify_url" value="http://<?php echo WEBSITE_URL; ?>/?paypal=paypal_ipn_sub">
                                <input type="hidden" name="a3" value="30">
                                <input type="hidden" name="p3" value="1">
                                <input type="hidden" name="t3" value="M">
                                <input type="hidden" name="src" value="1">
                                <input type="hidden" name="sra" value="1">
                                <input type="hidden" name="cancel_return" value="http://<?php echo WEBSITE_URL; ?>/?page=Account&action=ua">
                                <input type="hidden" name="return" value="http://<?php echo WEBSITE_URL; ?>/?page=Account&action=ua&paypal=success">

                                <input <?php if ($user['group'] != 2) { ?>disabled="disabled"<?php } ?> style="float: right;" id="upgrade" type="submit" value="Upgrade" class="submitIt simple_buttons" />
                            </form>
                            <script type="text/javascript">
                                $(function() {
                                    $("#upgrade").blur();
                                });
                            </script>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php } else if (!isset($_GET['action'])) { ?>
    <div class="g_9">
        <div class="widget_header">
            <h4 class="widget_header_title">My Account</h4>
        </div>
        <div class="widget_contents">
            <div style="float: left; background-color: white; border: 1px solid #DBDBDB; width: 36px;">
                <img style="margin: 0 0 0 0; width: 36px; height: 36px;" src="img/user_avatar.png" alt="user_avatar" class="user_avatar" />
                <br style="clear: both;"/>
            </div>
            <div style="float: left; margin-left: 10px; margin-top: -2px;">
                <span style="font-size: 14pt;" class="label"><?php echo $user['username']; ?></span>
                <br />
                <span class="label"><b>Member since:</b> <?php echo date("M j, Y", $user['join_date']); ?></span>
            </div>

            <br style="clear: both;"/>
        </div>
    </div>

    <div class="g_3">
        <div class="widget_header">
            <h4 class="widget_header_title">Account Settings</h4>
        </div>
        <div class="widget_contents">
            <a class="acc_settings" href="/?page=Get+Credits"><span class="label acc_settings">Get more credits &raquo;</span></a>
            <br /><br />
            <a class="acc_settings" href="/?page=Account&action=cu"><span class="label acc_settings">Change username &raquo;</span></a>
            <br /><br />
            <a class="acc_settings" href="/?page=Account&action=cp"><span class="label acc_settings">Change password &raquo;</span></a>
            <br /><br />
            <a class="acc_settings" href="/?page=Account&action=ua"><span class="label acc_settings">Change account type &raquo;</span></a>
        </div>
    </div>

    <div class="g_6">
        <div class="widget_header">
            <h4 class="widget_header_title">View Statistics from last 7 days</h4>
        </div>
        <div class="widget_contents">
            <?php
			$mainSystem->databaseSystem->where("user_id", $user['id'])->order("`date`", "DESC");
			$stats = $mainSystem->databaseSystem->get("statistics_views", 7);
			
			$max = 0;
			
			foreach ($stats as $stat)
			{
				if ($stat['views'] > $max) $max = $stat['views'];
			}
			
            foreach ($stats as $id => $stat) {
                ?>
                    <span class="label acc_settings"><?php echo date("M j, Y", strtotime($stat['date'])); ?><b style="float: right;"><?php echo $stat['views']; ?> Views</b></span>
                    <div class="progress scBlue" id="pbv_<?php echo $id; ?>"></div>

                    <script type="text/javascript">
                        $(function() {
                            $("#pbv_<?php echo $id; ?>").progressbar({
                                value: <?php echo $stat['views'] * 100 / $max; ?>
                            });
                        });
                    </script>
                <?php

                echo $id != count($stats) - 1 ? "<br />" : "";
            }

            if (count($stats) == 0) {
                ?>
                <span style="font-size: 14pt;" class="label">You have not received any views</span>
            <?php
            }
            ?>
        </div>
    </div>

    <div class="g_6">
        <div class="widget_header">
            <h4 class="widget_header_title">Credit Statistics from last 7 days</h4>
        </div>
        <div class="widget_contents">
            <?php
			$mainSystem->databaseSystem->where("user_id", $user['id'])->order("`date`", "DESC");
			$stats = $mainSystem->databaseSystem->get("statistics_credits", 7);
			
            $max = 0;
			
			foreach ($stats as $stat)
			{
				if ($stat['credits'] > $max) $max = $stat['credits'];
			}
			
            foreach ($stats as $id => $stat)
			{
                ?>
                <span class="label acc_settings"><?php echo date("M j, Y", strtotime($stat['date'])); ?><b style="float: right;"><?php echo $stat['credits']; ?> Credits</b></span>
                <div class="progress scBlue" id="pbc_<?php echo $id; ?>"></div>

                <script type="text/javascript">
                    $(function() {
                        $("#pbc_<?php echo $id; ?>").progressbar({
                            value: <?php echo $stat['credits'] * 100 / $max; ?>
                        });
                    });
                </script>
                <?php

                echo $id != count($stats) - 1 ? "<br />" : "";
            }

            if (count($stats) == 0) {
                ?>
                <span style="font-size: 14pt;" class="label">You have not earned any credits</span>
                <?php
            }
            ?>
        </div>
    </div>
<?php } ?>
<?php if (isset($_GET['action']) && $_GET['action'] != "ua") { ?>
    <div class="g_3">
        <div class="widget_header">
            <h4 class="widget_header_title">Account Settings</h4>
        </div>
        <div class="widget_contents">
            <a class="acc_settings" href="/?page=Buy+Credits"><span class="label acc_settings">Buy more credits &raquo;</span></a>
            <br /><br />
            <a class="acc_settings" href="/?page=Account&action=cu"><span class="label acc_settings">Change username &raquo;</span></a>
            <br /><br />
            <a class="acc_settings" href="/?page=Account&action=cp"><span class="label acc_settings">Change password &raquo;</span></a>
            <br /><br />
            <a class="acc_settings" href="/?page=Account&action=ua"><span class="label acc_settings">Change account type &raquo;</span></a>
        </div>
    </div>
<?php } ?>