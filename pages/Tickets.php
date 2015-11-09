<?php
	if (!defined('INDEX'))
		exit('No direct script access allowed');

	if (isset($_SESSION['success'])) {
		$success = $_SESSION['success'];
		unset($_SESSION['success']);
	}

	$subject = "";
	$message = "";

	if (isset($_POST['submit']))	{
		if ($_POST['submit'] == "Submit Ticket")		{
			$subject = $mainSystem->loginManager->cleanInput($_POST['subject']);
			$message = $mainSystem->loginManager->cleanInput($_POST['message']);

			if (isset($_POST['category']) && $_POST['category'] != "")			{
				if (isset($_POST['subject']) && $_POST['subject'] != "")				{
					if (isset($_POST['message']) && $_POST['message'] != "")					{
						$date_time = time();						$category = $mainSystem->loginManager->cleanInput($_POST['category']);						
						$stmt = $mainSystem->getMysqli()->prepare("INSERT INTO tickets (user_id, date_time, category, subject) VALUES (?, ?, ?, ?)");
						$stmt->bind_param("iiss", $user['id'], $date_time, $category, $subject);
						$stmt->execute();
						$stmt->close();						
						$stmt = $mainSystem->getMysqli()->prepare("INSERT INTO ticket_messages (ticket_id, user_id, date_time, message) VALUES (LAST_INSERT_ID(), ?, ?, ?)");
						$stmt->bind_param("iis", $user['id'], $date_time, $message);
						$stmt->execute();
						$stmt->close();						
						$success = "You have successfully submitted a ticket, we will try and review it within the next 72 hours";

						$subject = "";
						$message = "";
					} else					{
						$error = "You need to enter a message";
					}
				} else				{
					$error = "You need to enter a subject";
				}
			} else			{
				$error = "You need to select a category";
			}
		} else if ($_POST['submit'] == "Send Message")
		{
			$message = $mainSystem->loginManager->cleanInput($_POST['message']);		
			if ($_POST['message'] != "")
			{
				$mainSystem->loginManager->sendMessage($_GET['ticket_id'], $user['id'], $message, 0, false);
				$success = "Successfully sent message";
			} else
			{
				$error = "You need to enter a message";
			}
		}
	} else if (isset($_GET['ticket_id']))
	{
		$stmt = $mainSystem->getMysqli()->prepare("SELECT id FROM tickets WHERE id = ? AND user_id = ?");
		$stmt->bind_param("ii", $_GET['ticket_id'], $user['id']);
		$stmt->execute();
		$stmt->store_result();
		
		if ($stmt->num_rows == 0)
		{
			$stmt->close();
			
			header("Location: /?page=Tickets");
		}
		
		$stmt->close();
		
		$stmt = $mainSystem->getMysqli()->prepare("UPDATE ticket_messages SET is_read = 1 WHERE ticket_id = ? AND user_id = ? AND is_read = 0 AND from_admin = 1");
		$stmt->bind_param("ii", $_GET['ticket_id'], $user['id']);
		$stmt->execute();

		if ($stmt->affected_rows > 0)
		{
			$stmt->close();
			
			header("Location: /?page=Tickets&ticket_id=" . $_GET['ticket_id']);
		}

		$stmt->close();
	}
?>

<?php if (isset($success)) { ?><div class="g_12"><div class="success iDialog"><?php echo $success; ?></div></div><?php } ?>
<?php if (isset($error)) { ?><div class="g_12"><div class="error iDialog"><?php echo $error; ?></div></div><?php } ?>

<?php if (!isset($_GET['ticket_id'])) { ?>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">Support Tickets</h4>
        </div>
        <div class="widget_contents noPadding">
            <table class="ticketTable tables">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th style="display: none;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($mainSystem->loginManager->getTickets() as $ticket) {
                        $color = $ticket['status'] == "Closed" || $ticket['status'] == "Resolved" ? "style='color: green;'" : ($ticket['status'] == "Answered" ? "style='color: #25C4E8;'" : "style='color: #FF8200;'");
						
						$ticket['subject'] = str_replace("\\", "", $ticket['subject']);
						?>
                        <tr onclick="$(this).children('td:last').children('form').submit();">
                            <td <?php echo $color; ?>><b><?php echo date("m-d-Y", $ticket['date_time']) . "<br />" . date("g:i:s a", $ticket['date_time']); ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $ticket['status']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $ticket['category']; ?></b></td>
                            <td <?php echo $color; ?>><b><?php echo $ticket['subject']; ?></b></td>
                            <td style="display: none;">
                                <form action="" method="GET">
                                    <input type="hidden" name="page" value="Tickets" />
                                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>" />
                                </form>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>

                <script type="text/javascript">
                    $(function() {
                        $("input[type='submit']").blur();

                        $(".ticketTable").dataTable({
                            "bSort": false,
                            "sDom": "<'dtTop'<'dtShowPer'l><'dtFilter'f>><'dtTables't><'dtBottom'<'dtInfo'i><'dtPagination'p>>",
                            "oLanguage": {
                                "sLengthMenu": "Show entries _MENU_"
                            },
                            "sPaginationType": "full_numbers",
                            "fnInitComplete": function(){
                                $(".dtShowPer select").uniform();
                                $(".dtTables").css({
                                    "overflow-x": "hidden"
                                });
                                $(".dtFilter input").addClass("simple_field").css({
                                    "width": "auto",
                                    "margin-left": "15px"
                                });
                            }
                        });
                    });
                </script>
            </table>
        </div>
    </div>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">New Ticket</h4>
        </div>
        <div class="widget_contents noPadding">
            <form action="" method="POST">
                <div class="g_3"><span class="label">Category <span class="must">*</span></span></div>
                <div class="g_9">
                    <select name="category" class="simple_form">
                        <option value="Account">Account Issue (Ex. Credits, Views)</option>
                        <option value="Payment">Payment Issue (Include Transaction ID / Subscription ID)</option>
                        <option value="Viewer">GrabViews Viewer Bug / Issue</option>
                        <option value="Website">Website Bug / Issue</option>
                        <option value="Forum">Forum Bug / Issue</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="g_3"><span class="label">Subject <span class="must">*</span></span></div>
                <div class="g_9">
                    <input type="text" class="simple_field" name="subject" value="<?php echo $subject; ?>" maxlength="30" />
                </div>

                <div class="g_3"><span class="label">Message <span class="must">*</span></span></div>
                <div class="g_9">
                    <textarea class="simple_field" name="message"><?php echo $message; ?></textarea>
                </div>

                <div class="g_3"></div>
                <div class="g_9">
                    <input style="float: right;" type="submit" name="submit" value="Submit Ticket" class="submitIt simple_buttons" />
                </div>
            </form>
        </div>
    </div>
<?php } else if (isset($_GET['ticket_id'])) { ?>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">Support Ticket</h4>
        </div>
        <div class="widget_contents noPadding">
            <table class="tables">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Subject</th>
                </tr>
                </thead>
                <tbody>
                    <?php
						$ticket = $mainSystem->loginManager->getUserTicket($_GET['ticket_id']);
						
						if ($ticket == null) header("Location: /?page=Tickets");
						
						$color = $ticket['status'] == "Closed" || $ticket['status'] == "Resolved" ? "style='color: green;'" : ($ticket['status'] == "Answered" ? "style='color: #25C4E8;'" : "style='color: #FF8200;'");
						
						$ticket['subject'] = str_replace("\\", "", $ticket['subject']);
					?>
                    <tr>
                        <td <?php echo $color; ?>><b><?php echo date("m-d-Y", $ticket['date_time']) . "<br />" . date("g:i:s a", $ticket['date_time']); ?></b></td>
                        <td <?php echo $color; ?>><b><?php echo $ticket['status']; ?></b></td>
                        <td <?php echo $color; ?>><b><?php echo $ticket['category']; ?></b></td>
                        <td <?php echo $color; ?>><b><?php echo $ticket['subject']; ?></b></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">Messages</h4>
        </div>
        <div class="widget_contents noPadding">
            <table class="tables">
                <thead>
                <tr>
                    <th style="width: 110px;">Type</th>
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
    <div class="g_12">
        <div class="widget_header">
            <h4 class="widget_header_title">New Message</h4>
        </div>
        <div class="widget_contents noPadding">
            <?php $ticket = $mainSystem->loginManager->getUserTicket($_GET['ticket_id']); ?>
            <form action="/?page=Tickets&action=Details&ticket_id=<?php echo $ticket['id']; ?>" method="POST">
                <div class="g_3"><span class="label">Message</span></div>
                <div class="g_9">
                    <textarea id="message" class="simple_field" name="message"></textarea>

                    <script type="text/javascript">
                        $(function() {
                            $("#message").blur();
                        });
                    </script>
                </div>

                <div class="g_3"></div>
                <div class="g_9">
                    <input style="float: right;" type="button" name="submit" value="Back" onclick="document.location='/?page=Tickets'" class="submitIt simple_buttons" />
					<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="Send Message" class="submitIt simple_buttons" />				</div>
            </form>
        </div>
    </div>
<?php } ?>