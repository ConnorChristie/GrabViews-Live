<?php
	if (!defined('INDEX'))
		exit('No direct script access allowed');
	
    if (isset($_POST['submit']) && !isset($_POST['ticket_id']))	{
        if ($_POST['submit'] == "Mark as Resolved")		{
            $mainSystem->loginManager->markBadTransactionAsResolved($_POST['id']);

            $success = "Bad transaction '" . $_POST['id'] . "' has been marked as resolved";
        } else if ($_POST['submit'] == "Suspend")		{
			if (isset($_POST['id']))
			{
				$username = $mainSystem->loginManager->suspendUser($_POST['user_id'], isset($_POST['message']) ? $_POST['message'] : "Your account has been permanently banned because of a suspicious paypal transaction");
			} else
			{
				$username = $mainSystem->loginManager->suspendUser($_POST['user_id'], isset($_POST['message']) ? $_POST['message'] : "");
			}
			
            $success = "User '" . $username . "' has been suspended";
        } else if ($_POST['submit'] == "Ban")		{
			if (isset($_POST['id']))
			{
				$username = $mainSystem->loginManager->banUser($_POST['id'], $_POST['user_id'], isset($_POST['message']) ? $_POST['message'] : "Your account has been permanently banned because of a suspicious paypal transaction");
			} else
			{
				$username = $mainSystem->loginManager->banUser(-1, $_POST['user_id'], isset($_POST['message']) ? $_POST['message'] : "");
			}
						$success = "User '" . $username . "' has been permanently banned";
        } else if ($_POST['submit'] == "Unsuspend")		{
            $username = $mainSystem->loginManager->unSuspendUser($_POST['user_id']);
            			$success = "User '" . $username . "' has been unsuspended";
        } else if ($_POST['submit'] == "Unban")		{
            $username = $mainSystem->loginManager->unBanUser($_POST['user_id']);
           		   $success = "User '" . $username . "' has been unbanned";
        } else if ($_POST['submit'] == "Delete Account")		{
            $username = $mainSystem->loginManager->deleteUser($_POST['user_id']);
            			$success = $username . "'s account has been deleted";
        } else if ($_POST['submit'] == "Back")		{
            header("Location /?page=Admin+Panel#Users");
        }
    } else if (isset($_POST['submit']) && isset($_POST['ticket_id']))
    {
        if ($_POST['submit'] == "Send Message")
        {
            if ($_POST['message'] != "")
            {
                $mainSystem->loginManager->sendMessage($_POST['ticket_id'], $_POST['uid_id'], $_POST['message'], 1, true);

                $success = "Successfully sent message for Ticket ID " . $_POST['ticket_id'];
            } else
            {
                $error = "Please enter in a message";
            }
        } else if ($_POST['submit'] == "Mark as Resolved")
        {
            $mainSystem->loginManager->markTicket($_POST['ticket_id'], "Resolved");

            $success = "Ticket ID '" . $_POST['ticket_id'] . "' has been resolved";
        } else if ($_POST['submit'] == "Mark as Pending")
        {
            $mainSystem->loginManager->markTicket($_POST['ticket_id'], "Pending");

            $success = "Ticket ID '" . $_POST['ticket_id'] . "' has been pending";
        } else if ($_POST['submit'] == "Mark as Closed")
        {
            $mainSystem->loginManager->markTicket($_POST['ticket_id'], "Closed");

            $success = "Ticket ID '" . $_POST['ticket_id'] . "' has been closed";
        } else if ($_POST['submit'] == "Delete")
		{
			$mainSystem->loginManager->deleteTicket($_POST['ticket_id']);

            $_SESSION['session'] = "Ticket ID '" . $_POST['ticket_id'] . "' has been deleted";
			header("Location: /?page=Admin+Panel");
		}
    } else if (isset($_POST['action']) && $_POST['action'] == "changeValue")
    {
		if (isset($_POST['user_id']))
		{
			$mainSystem->loginManager->updateUserData($_POST['user_id'], $_POST['name'], $_POST['value']);
		} else if (isset($_POST['video_id']))
		{
			$mainSystem->videoAction->updateUserVideo($_POST['video_id'], isset($_POST['name']) ? $_POST['name'] : $_POST['value'], $_POST['value']);
			
			if ($_POST['value'] == "Delete")
			{
				header("Location: /?page=Admin+Panel");
			}
		}
    } else if (isset($_GET['ticket_id']))
    {
        $stmt = $mainSystem->getMysqli()->prepare("UPDATE ticket_messages SET is_read = 1 WHERE ticket_id = ? AND is_read = 0 AND from_admin = 0");
        $stmt->bind_param("i", $_GET['ticket_id']);
        $stmt->execute();

        if ($stmt->affected_rows > 0)
        {
            header("Location: /?page=Admin+Panel&ticket_id=" . $_GET['ticket_id']);
        }

        $stmt->close();
    }
	
	if (isset($_SESSION['success']))	{
		$success = $_SESSION['success'];	
        unset($_SESSION['success']);
    }
?>

<?php if (isset($success)) { ?><div class="g_12"><div class="success iDialog"><?php echo $success; ?></div></div><?php } ?>
<?php if (isset($error)) { ?><div class="g_12"><div class="error iDialog"><?php echo $error; ?></div></div><?php } ?>

<?php if ((isset($_GET['t_id']) && isset($_GET['type'])) || isset($_GET['user_id']) || isset($_GET['ticket_id']) || isset($_GET['video_id'])) { ?>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title"><?php echo (isset($_GET['user_id']) ? "User Account" : (isset($_GET['ticket_id']) ? "Support Ticket" : (isset($_GET['video_id']) ? "Video" : ($_GET['type'] == "buy" ? "Buy Paypal Transaction" : "Sub Paypal Transaction")))); ?></h4>
        </div>
        <div class="widget_contents noPadding">
            <?php if (!isset($_GET['user_id']) && !isset($_GET['ticket_id']) && !isset($_GET['video_id'])) {
					$transaction = $mainSystem->loginManager->getTransaction($_GET['t_id']);
					
					if ($transaction == null) header("Location: /?page=Admin+Panel#Transactions");
				?>
                <?php if ($transaction['type'] == "buy") { ?>
                    <table class="tables">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Resolved / Status</th>
                            <th>Txn ID</th>
                            <th>Payment Status</th>
                            <th>Payer Status</th>
                            <th>Item / Credits</th>
                            <th>Payed / Total</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b><?php echo $transaction['id']; ?></b></td>
                                <td><b><?php echo $transaction['username']; ?></b></td>
                                <td><b><?php echo ($transaction['resolved'] == 1 ? "True" : "False") . "<br />" . $transaction['status']; ?></b></td>
                                <td><b><?php echo $transaction['txn_id']; ?></b></td>
                                <td><b><?php echo $transaction['payment_status']; ?></b></td>
                                <td><b><?php echo $transaction['payer_status']; ?></b></td>
                                <td><b><?php echo $transaction['item_number'] . "<br />" . number_format($transaction['credits']); ?></b></td>
                                <td><b><?php echo "$" . $transaction['mc_gross'] . "<br />$" . $transaction['total']; ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } else if ($transaction['type'] == "sub") { ?>
                    <table class="tables">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Resolved / Status</th>
                                <th>Sub ID / Txn ID</th>
                                <th>Payment Status</th>
                                <th>Payer Status</th>
                                <th>Group</th>
                                <th>Payed / Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b><?php echo $transaction['id']; ?></b></td>
                                <td><b><?php echo $transaction['username']; ?></b></td>
                                <td><b><?php echo ($transaction['resolved'] == 1 ? "True" : "False") . "<br />" . $transaction['status']; ?></b></td>
                                <td><b><?php echo $transaction['subscr_id']; ?><br /><?php echo $transaction['txn_id']; ?></b></td>
                                <td><b><?php echo $transaction['payment_status']; ?></b></td>
                                <td><b><?php echo $transaction['payer_status']; ?></b></td>
                                <td><b><?php $group = $mainSystem->loginManager->getGroupInfo($transaction['group']); echo str_replace(" P", "<br />P", $group['title']); ?></b></td>
                                <td><b><?php echo "$" . $transaction['mc_gross'] . "<br />$" . $transaction['total']; ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                <?php } ?>
            <?php } else if (isset($_GET['user_id'])) { ?>
                <table class="tables">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>IP</th>
                        <th>Group</th>
                        <th>Credits</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
						$person = $mainSystem->loginManager->getUserData($_GET['user_id']);
						
						if ($person == null) header("Location: /?page=Admin+Panel");
						
						$group = $mainSystem->loginManager->getGroupInfo($person['group']);
						$color = "style='color: " . $group['color'] . ";'";
					?>
                        <tr>
                            <td <?php echo $color; ?> id="id" class="jEditable"><b><?php echo $person['id']; ?></b></td>
                            <td <?php echo $color; ?> id="username" class="jEditable"><b><?php echo $person['username']; ?></b></td>
                            <td <?php echo $color; ?> id="email" class="jEditable"><b><?php echo $person['email']; ?></b></td>
                            <td <?php echo $color; ?> id="ip" class="jEditable"><b><?php echo $person['ip']; ?></b></td>
                            <td <?php echo $color; ?> id="group" class="jEditable"><b><?php $group = $mainSystem->loginManager->getGroupInfo($person['group']); echo $group['title']; ?></b></td>
                            <td <?php echo $color; ?> id="credits" class="jEditable"><b><?php echo number_format($person['credits']); ?></b></td>
                        </tr>
                    </tbody>
                </table>

                <script type="text/javascript">
                    $(function() {
                        $(".jEditable").each(function() {
							<?php if (($mainSystem->loginManager->isUserMod() && $person['group'] != 6 && $person['group'] != 8) || (!$mainSystem->loginManager->isUserMod())) { ?>
								$(this).children("").first().one("click", clickFunction);
							<?php } ?>
                        });

                        function clickFunction() {
                            var name = $(this).parent().attr("id");
                            var value = $(this).text();
                            var html = $(this).html();

                            if (name == "group")
                            {
                                $(this).html(
                                    "<select name='" + name + "' style='text-align: center; font-weight: bold; outline: none; color: #696969; font-family: Droid Sans; border: #FDFDFD; background-color: #FDFDFD; background: transparent; width: 141px;'>" +
                                        "<option " + (value == "Banned" ? "selected " : "") + "value='Banned'>Banned</option>" +
                                        "<option " + (value == "Suspended" ? "selected " : "") + "value='Suspended'>Suspended</option>" +
                                        "<option " + (value == "Validating" ? "selected " : "") + "value='Validating'>Validating</option>" +
                                        "<option " + (value == "Basic" ? "selected " : "") + "value='Basic'>Basic</option>" +
                                        "<option " + (value == "Level 1 Premium" ? "selected " : "") + "value='Level 1 Premium'>Level 1 Premium</option>" +
                                        "<option " + (value == "Level 2 Premium" ? "selected " : "") + "value='Level 2 Premium'>Level 2 Premium</option>" +
                                        "<option " + (value == "Level 3 Premium" ? "selected " : "") + "value='Level 3 Premium'>Level 3 Premium</option>" +
										<?php if (!$mainSystem->loginManager->isUserMod()) { ?>
											"<option " + (value == "Moderator" ? "selected " : "") + "value='Moderator'>Moderator</option>" +
											"<option " + (value == "Admin" ? "selected " : "") + "value='Admin'>Admin</option>" +
										<?php } ?>
                                    "</select>"
                                );
                            } else if (name == "credits")
                            {
                                value = value.split(",").join("");

                                $(this).html("<input type='number' name='" + name + "' value='" + value + "' style='outline: none; text-align: center; font-weight: bold; color: #696969; font-family: Droid Sans; border: #FDFDFD; background-color: #FDFDFD; background: transparent; width: " + ($(this).width() + 30) + "px;' />");
                            } else
                            {
                                $(this).html("<input type='text' name='" + name + "' value='" + value + "' style='outline: none; text-align: center; font-weight: bold; color: #696969; font-family: Droid Sans; border: #FDFDFD; background-color: #FDFDFD; background: transparent; width: " + $(this).width() + "px;' />");
                            }

                            var title = (name == "group" ? "select" : name == "credits" ? "input[type='number']" : "input[type='text']");

                            $(this).children(title).focus().first().blur(function() {
                                $(this).parent().one("click", clickFunction);

                                if ($(this).val() == value)
                                {
                                    $(this).parent().html(html);
                                } else
                                {
                                    $.ajax("/?page=Admin+Panel", {
                                        type: "POST",
                                        data: { "action": "changeValue", "name": name, "value": $(this).val(), "user_id": "<?php echo $person['id']; ?>" },
                                        success: function(data)
                                        {
                                            location.reload();
                                        }
                                    });

                                    $(this).parent().html(html.replace(value, $(this).val()));
                                }
                            });
                        }
                    });
                </script>
            <?php } else if (isset($_GET['ticket_id'])) { ?>
                <table class="tables">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>Subject</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
							$ticket = $mainSystem->loginManager->getTicket($_GET['ticket_id']);
							
							if ($ticket == null) header("Location: /?page=Admin+Panel#Tickets");
							
							$color = $ticket['status'] == "Closed" || $ticket['status'] == "Resolved" ? "style='color: green;'" : ($ticket['status'] == "Question" ? "style='color: #FF8200;'" : ($ticket['status'] == "Pending" ? "style='color: #FF8200;'" : "style='color: #25C4E8;'"));
							
							$ticket['subject'] = str_replace("\\", "", $ticket['subject']);
						?>
                        <tr>
                            <td <?php echo $color; ?>><b><?php echo $ticket['id']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php $person = $mainSystem->loginManager->getUserData($ticket['user_id']); echo $person['username']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo date("m-d-Y", $ticket['date_time']) . "<br />" . date("g:i:s a", $ticket['date_time']); ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $ticket['status']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $ticket['category']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $ticket['subject']; ?></b></td>
                        </tr>
                    </tbody>
                </table>
            <?php } else if (isset($_GET['video_id'])) { ?>
				<table class="tables">
                    <thead>
                        <tr>
                            <th>ID</th>
							<th>Username</th>
							<th>Video ID</th>
							<th>Views</th>
							<th>View Length</th>
							<th>Likes / Limit</th>
							<th>Subs / Limit</th>
							<th>Credits</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
							$video = $mainSystem->videoAction->getUserVideo($_GET['video_id']);
							
							if ($video == null) header("Location: /?page=Admin+Panel#Videos");
							
							$color = $video['type'] == "active" ? "style='color: green;'" : "style='color: #FF8200;'";
						?>
                        <tr>
                            <td <?php echo $color; ?>><b><?php echo $video['id']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php $user_d = $mainSystem->loginManager->getUserData($video['user_id']); echo $user_d['username']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $video['vid_id']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo number_format($video['views']); ?></b></td>
							<td <?php echo $color; ?>><b><?php echo $video['length']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo number_format($video['likes']) . " / " . number_format($video['like_limit']); ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo number_format($video['subscribes']) . " / " . number_format($video['sub_limit']); ?></b></td>
                            <td <?php echo $color; ?> id="credits" class="jEditable"><b><?php echo number_format($video['credits']); ?></b></td>
                        </tr>
                    </tbody>
                </table>
				<script type="text/javascript">
                    $(function() {
                        $(".jEditable").each(function() {
                            $(this).children("").first().one("click", clickFunction);
                        });

                        function clickFunction() {
                            var name = $(this).parent().attr("id");
                            var value = $(this).text().split(",").join("");
                            var html = $(this).html();

                            $(this).html("<input type='number' name='" + name + "' value='" + value + "' style='outline: none; text-align: center; font-weight: bold; color: #696969; font-family: Droid Sans; border: #FDFDFD; background-color: #FDFDFD; background: transparent; width: " + ($(this).width() + 30) + "px;' />");
							
                            $(this).children("input[type='number']").focus().first().blur(function() {
                                $(this).parent().one("click", clickFunction);

                                if ($(this).val() == value)
                                {
                                    $(this).parent().html(html);
                                } else
                                {
                                    $.ajax("/?page=Admin+Panel", {
                                        type: "POST",
                                        data: { "action": "changeValue", "name": name, "value": $(this).val(), "video_id": "<?php echo $video['id']; ?>" },
                                        success: function(data)
                                        {
											location.reload();
                                        }
                                    });

                                    $(this).parent().html(html.replace(value, $(this).val()));
                                }
                            });
                        }
                    });
                </script>
			<?php } ?>
        </div>
    </div>

    <?php if (isset($_GET['user_id']) && !isset($_GET['ticket_id'])) { ?>
    <div class="g_12" id="table">
        <div class="widget_header">
            <h4 class="widget_header_title">Videos</h4>
        </div>
        <div class="widget_contents noPadding">
            <div id="Active">
                <table class="userTable tables">
                    <thead>
                        <tr>
                            <th>Video ID</th>
                            <th>Views</th>
                            <th>View Length</th>
                            <th>Likes / Limit</th>
                            <th>Subs / Limit</th>
                            <th>Credits</th>
							<th style="display: none;"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($mainSystem->videoAction->getUserVideos($_GET['user_id']) as $video) {
                            $like = $video['like'] == 1 ? ($video['likes'] < $video['like_limit'] || $video['like_limit'] == 0 ? "style='color: green;'" : "style='color: red;'") : "style='color: red;'";
                            $subscribe = $video['subscribe'] == 1 ? ($video['subscribes'] < $video['sub_limit'] || $video['sub_limit'] == 0 ? "style='color: green;'" : "style='color: red;'") : "style='color: red;'";
							$color = $video['type'] == "active" ? "style='color: green;'" : "style='color: #FF8200;'";
                            ?>
                            <tr onclick="$(this).children('td:last').children('form').submit();">
                                <td <?php echo $color; ?>><b><?php echo $video['vid_id']; ?></b></td>
                                <td <?php echo $color; ?>><b><?php echo number_format($video['views']); ?></b></td>
                                <td <?php echo $color; ?>><b><?php echo $video['length']; ?></b></td>
                                <td <?php echo $like; ?>><b><?php echo number_format($video['likes']) . " / " . number_format($video['like_limit']); ?></b></td>
                                <td <?php echo $subscribe; ?>><b><?php echo number_format($video['subscribes']) . " / " . number_format($video['sub_limit']); ?></b></td>
                                <td <?php echo str_replace("#FF8200", "red", $color); ?>><b><?php echo number_format($video['credits']); ?></b></td>
								<td style="display: none"><form method="GET" action="" class="form_class"><input type="hidden" name="page" value="Admin Panel" /><input type="hidden" name="video_id" value="<?php echo $video['id']; ?>" /></form></td>
                            </tr>
                        <?php
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if (!isset($_GET['user_id']) && isset($_GET['ticket_id'])) { ?>
        <div class="g_12">
            <div class="widget_header">
                <h4 class="widget_header_title">Messages</h4>
            </div>
            <div class="widget_contents noPadding">
                <table class="tables">
                    <thead>
                        <tr>
                            <th style="width: 90px;">Type</th>
                            <th style="width: 90px;">Date</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mainSystem->loginManager->getMessages($_GET['ticket_id']) as $ticket) {
                            $ticket['message'] = str_replace("\\", "", $ticket['message']);
                            ?>
                            <tr>
                                <td><?php echo $ticket['from_admin'] == 1 ? "Answer" : "Question"; ?></td>
                                <td><?php echo date("m-d-Y", $ticket['date_time']) . "<br />" . date("g:i:s a", $ticket['date_time']); ?></td>
                                <td><span style="float: left; margin-left: 20px;"><?php echo $ticket['message']; ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>
	<?php if ((isset($_GET['user_id']) && (($mainSystem->loginManager->isUserMod() && $person['group'] != 6 && $person['group'] != 8) || !$mainSystem->loginManager->isUserMod())) || !isset($_GET['user_id'])) { ?>
		<div class="g_12">
			<div class="widget_header">
				<h4 class="widget_header_title">Actions</h4>
			</div>
			<div class="widget_contents noPadding">
				<?php if (!isset($_GET['user_id']) && !isset($_GET['ticket_id']) && !isset($_GET['video_id'])) {
					$group = $mainSystem->loginManager->getUserData($transaction['user_id']);
					?>
					<form action="" method="POST">
						<div class="g_3"><span class="label">Message</span></div>
						<div class="g_9">
							<input class="simple_field" type="text" name="message" value="" maxlength="128" />
						</div>
						
						<input type="hidden" name="id" value="<?php echo $transaction['id']; ?>" />
						<input type="hidden" name="user_id" value="<?php echo $transaction['user_id']; ?>" />

						<div class="g_3"></div>
						<div class="g_9">
							<?php if ($transaction['resolved'] == "0") { ?>
								<input style="float: right;" type="submit" name="submit" value="Mark as Resolved" class="submitIt simple_buttons" />
							<?php } ?>
							<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="<?php echo ($group['group'] == 0 ? "Unsuspend" : "Suspend"); ?>" class="submitIt simple_buttons" />
							<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="<?php echo ($group['group'] == 1 ? "Unban" : "Ban"); ?>" class="submitIt simple_buttons" />
							<input style="float: right; margin-right: 10px;" type="button" name="submit" value="Back" onclick="document.location='/?page=Admin+Panel'" class="submitIt simple_buttons" />
						</div>
					</form>
				<?php } else if (isset($_GET['user_id'])) {
					$group = $mainSystem->loginManager->getUserData($person['id']);
					?>
					<form action="" method="POST">
						<div class="g_3"><span class="label">Message</span></div>
						<div class="g_9">
							<input class="simple_field" type="text" name="message" value="" maxlength="128" />
						</div>

						<input type="hidden" name="user_id" value="<?php echo $person['id']; ?>" />
						
						<div class="g_3"></div>
						<div class="g_9">
							<input style="float: right;" type="button" name="submit" value="Back" onclick="document.location='/?page=Admin+Panel#Users'" class="submitIt simple_buttons" />
							<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="Delete Account" class="submitIt simple_buttons" />
							<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="<?php echo ($group['group'] == 1 ? "Unban" : "Ban"); ?>" class="submitIt simple_buttons" />
							<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="<?php echo ($group['group'] == 0 ? "Unsuspend" : "Suspend"); ?>" class="submitIt simple_buttons" />
						</div>
					</form>
				<?php } else if (isset($_GET['ticket_id'])) {
					$ticket = $mainSystem->loginManager->getTicket($_GET['ticket_id']);
					?>
					<form action="" method="POST">
						<div class="g_3"><span class="label">Message</span></div>
						<div class="g_9">
							<textarea id="message" class="simple_field" name="message"></textarea>

							<script type="text/javascript">
								$(function() {
									$("#message").blur();
								});
							</script>
						</div>

						<input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>" />
						<input type="hidden" name="uid_id" value="<?php echo $ticket['user_id']; ?>" />

						<div class="g_3"></div>
						<div class="g_9">
							<input style="float: right;" type="button" name="submit" value="Back" onclick="document.location='/?page=Admin+Panel#Tickets'" class="submitIt simple_buttons" />
							<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="Delete" class="submitIt simple_buttons" />
							<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="Mark as Closed" class="submitIt simple_buttons" />
							<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="Mark as <?php echo $ticket['status'] == "Resolved" ? "Pending" : "Resolved"; ?>" class="submitIt simple_buttons" />
							<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="Send Message" class="submitIt simple_buttons" />
						</div>
					</form>
				<?php } else if (isset($_GET['video_id'])) { ?>
					<form action="" method="POST">
						<input type="hidden" name="action" value="changeValue" />
						<input type="hidden" name="video_id" value="<?php echo $video['id']; ?>" />

						<div class="g_3"></div>
						<div class="g_9">
							<input style="float: right;" type="button" name="submit" value="Back" onclick="document.location='/?page=Admin+Panel#Videos'" class="submitIt simple_buttons" />
							<input style="float: right; margin-right: 10px;" type="submit" name="value" value="Delete" class="submitIt simple_buttons" />
							<input style="float: right; margin-right: 10px;" type="submit" name="value" value="<?php echo $video['type'] == "active" ? "Disable" : "Enable"; ?>" class="submitIt simple_buttons" />
						</div>
						
						<script type="text/javascript">
							$(function() {
								$("input").blur();
							});
						</script>
					</form>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
<?php } else { ?>
    <div class="g_12" id="table_wTabs">
        <div class="widget_header wwOptions">
            <h4 class="widget_header_title">Administration</h4>
            <ul class="w_Tabs">
                <li><a id="Users" href="#Users" title="Users">Users</a></li>
				<li><a id="Videos" href="#Videos">Videos</a></li>
                <li><a id="Tickets" href="#Tickets">Tickets</a></li>
				<li><a id="Transactions" href="#Transactions">Transactions</a></li>
            </ul>
        </div>
        <div class="widget_contents noPadding">
            <div id="Users">
                <table class="userTable tables">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>IP</th>
                            <th>Group</th>
                            <th>Credits</th>
                            <th style="display: none"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mainSystem->loginManager->getAllUsers() as $person)
					{
                        $group = $mainSystem->loginManager->getGroupInfo($person['group']);
						$color = "style='color: " . $group['color'] . ";'";
                        ?>
                        <tr onclick="$(this).children('td:last').children('form').submit();">
                            <td <?php echo $color; ?>><b><?php echo $person['id']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $person['username']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $person['email']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $person['ip']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $group['title']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo number_format($person['credits']); ?></b></td>
                            <td style="display: none"><form method="GET" action="" class="form_class"><input type="hidden" name="page" value="Admin Panel" /><input type="hidden" name="user_id" value="<?php echo $person['id']; ?>" /></form></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
			<div id="Videos" class="ui-tabs-hide">
                <table class="subTable tables">
                    <thead>
						<tr>
							<th>ID</th>
							<th>Username</th>
							<th>Video ID</th>
							<th>Views</th>
							<th>View Length</th>
							<th>Likes / Limit</th>
							<th>Subs / Limit</th>
							<th>Credits</th>
							<th style="display: none"></th>
						</tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mainSystem->videoAction->getAllVideos() as $video) {
                        $color = $video['type'] == "active" ? "style='color: green;'" : "style='color: #FF8200;'";
                        ?>
                        <tr onclick="$(this).children('td:last').children('form').submit();">
							<td <?php echo $color; ?>><b><?php echo $video['id']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php $user_d = $mainSystem->loginManager->getUserData($video['user_id']); echo $user_d['username']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $video['vid_id']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo number_format($video['views']); ?></b></td>
							<td <?php echo $color; ?>><b><?php echo $video['length']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo number_format($video['likes']) . " / " . number_format($video['like_limit']); ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo number_format($video['subscribes']) . " / " . number_format($video['sub_limit']); ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo number_format($video['credits']); ?></b></td>
							<td style="display: none"><form method="GET" action="" class="form_class"><input type="hidden" name="page" value="Admin Panel" /><input type="hidden" name="video_id" value="<?php echo $video['id']; ?>" /></form></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div id="Tickets" class="ui-tabs-hide">
                <table class="subTable tables">
                    <thead>
						<tr>
							<th>ID</th>
							<th>Username</th>
							<th>Date</th>
							<th>Status</th>
							<th>Category</th>
							<th>Subject</th>
							<th style="display: none"></th>
						</tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mainSystem->loginManager->getAllTickets() as $ticket) {
                        $color = $ticket['status'] == "Closed" || $ticket['status'] == "Resolved" ? "style='color: green;'" : ($ticket['status'] == "Question" ? "style='color: #FF8200;'" : ($ticket['status'] == "Pending" ? "style='color: #FF8200;'" : "style='color: #25C4E8;'"));
                        
						$ticket['subject'] = str_replace("\\", "", $ticket['subject']);
						?>
                        <tr onclick="$(this).children('td:last').children('form').submit();">
                            <td <?php echo $color; ?>><b><?php echo $ticket['id']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php $person = $mainSystem->loginManager->getUserData($ticket['user_id']); echo $person['username']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo date("m-d-Y", $ticket['date_time']) . "<br />" . date("g:i:s a", $ticket['date_time']); ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $ticket['status']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $ticket['category']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $ticket['subject']; ?></b></td>
                            <td style="display: none"><form method="GET" action="" class="form_class"><input type="hidden" name="page" value="Admin Panel" /><input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>" /></form></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
			<div id="Transactions" class="ui-tabs-hide">
                <table class="subTable tables">
                    <thead>
						<tr>
							<th>ID</th>
							<th>Username</th>
							<th>Date</th>
							<th>Sub ID / Txn ID</th>
							<th>Payment Status</th>
							<th>Payer Status</th>
							<th>Group / Credits</th>
							<th>Payed / Total</th>
							<th style="display: none"></th>
						</tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mainSystem->loginManager->getAllTransactions() as $transaction) {
						switch ($transaction['priority']) {
                            case 1:
                                $color = "style='color: red;'";
                                break;
                            case 2:
                                $color = "style='color: #FF8200;'";
                                break;
                            case 3:
                                $color = "style='color: #FFC000;'";
                                break;
                            case 4:
                                $color = "style='color: lightgreen;'";
                                break;
                            default:
                                $color = "style='color: #25C4E8;'";
                                break;
                        }
						
						if ($transaction['type'] == "buy")
						{
							?>
							<tr onclick="$(this).children('td:last').children('form').submit();">
								<td <?php echo $color; ?>><b><?php echo $transaction['id']; ?></b></td>
								<td <?php echo $color; ?>><b><?php $person = $mainSystem->loginManager->getUserData($transaction['user_id']); echo $person['username']; ?></b></td>
								<td <?php echo $color; ?>><b><?php echo date("m-d-Y", $transaction['date']); ?></b></td>
								<td <?php echo $color; ?>><b><?php echo $transaction['status']; ?></b></td>
								<td <?php echo $color; ?>><b><?php echo $transaction['txn_id']; ?></b></td>
								<td <?php echo $color; ?>><b><?php echo $transaction['payment_status']; ?></b></td>
								<td <?php echo $color; ?>><b><?php echo $transaction['payer_status']; ?></b></td>
								<td <?php echo $color; ?>><b><?php echo $transaction['item_number'] . "<br />" . number_format($transaction['credits']); ?></b></td>
								<td <?php echo $color; ?>><b><?php echo "$" . $transaction['mc_gross'] . "<br />$" . $transaction['total']; ?></b></td>
								<td style="display: none"><form method="GET" action="" class="form_class"><input type="hidden" name="page" value="Admin Panel" /><input type="hidden" name="t_id" value="<?php echo $transaction['id']; ?>" /></form></td>
							</tr>
							<?php
						} else if ($transaction['type'] == "sub")
						{
							?>
							<tr onclick="$(this).children('td:last').children('form').submit();">
								<td <?php echo $color; ?>><b><?php echo $transaction['id']; ?></b></td>
								<td <?php echo $color; ?>><b><?php $person = $mainSystem->loginManager->getUserData($transaction['user_id']); echo $person['username']; ?></b></td>
								<td <?php echo $color; ?>><b><?php echo date("m-d-Y", $transaction['date']); ?></b></td>
								<td <?php echo $color; ?>><b><?php echo $transaction['status']; ?></b></td>
								<td <?php echo $color; ?>><b><?php echo $transaction['subscr_id']; ?><br /><?php echo $transaction['txn_id']; ?></b></td>
								<td <?php echo $color; ?>><b><?php echo $transaction['payment_status']; ?></b></td>
								<td <?php echo $color; ?>><b><?php echo $transaction['payer_status']; ?></b></td>
								<td <?php echo $color; ?>><b><?php $group = $mainSystem->loginManager->getGroupInfo($transaction['group']); echo str_replace(" P", "<br />P", $group['title']); ?></b></td>
								<td <?php echo $color; ?>><b><?php echo "$" . $transaction['mc_gross'] . "<br />$" . $transaction['total']; ?></b></td>
								<td style="display: none"><form method="GET" action="" class="form_class"><input type="hidden" name="page" value="Admin Panel" /><input type="hidden" name="t_id" value="<?php echo $transaction['id']; ?>" /></form></td>
							</tr>
							<?php
						}
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php } ?>