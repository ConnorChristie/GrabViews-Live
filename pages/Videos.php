<?php
    if (!defined('INDEX'))
		exit('No direct script access allowed');

	$stayOnAdd = false;
	$stayOnEdit = false;
	
	$vid_id = "";
    $vid_title = "";
	$view_for = 30;
	$like_check = 0;
	$like_limit = 0;
	$comment_check = 0;
	$comm_limit = 0;
	$subscribe_check = 0;
	$sub_limit = 0;
	$credits = 0;

    if (isset($_SESSION['success']))
	{
        $success = $_SESSION['success'];
		
        unset($_SESSION['success']);
    }
	
	if (isset($_GET['video_id']) && isset($_POST['submit']))
	{
		if ($_POST['submit'] == "Save")
		{
			$view_for = $_POST['view_for'];
			
			echo "Length: " . $view_for;
			
			$like_check = isset($_POST['like']) ? 1 : 0;
			$subscribe_check = isset($_POST['subscribe']) ? 1 : 0;
			
			$like_limit = $_POST['like_limit'];
			$sub_limit = $_POST['sub_limit'];
			
			$credits = $_POST['credits'];

			$stmt = $mainSystem->getMysqli()->prepare("SELECT id FROM videos WHERE id=? AND user_id=?");
			$stmt->bind_param('ii', $_GET['video_id'], $user['id']);
			$stmt->execute();
			$stmt->store_result();
			
			if ($stmt->num_rows == 1)
			{
				$stmt->close();

				if ($_POST['credits'] >= 0 && $_POST['credits'] <= $user['credits'])
				{
					$video = $mainSystem->videoAction->getActiveVideo($_GET['video_id']);

					$creds = $video['credits'] + $credits;
					$user_creds = $user['credits'] - $credits;

					$stmt = $mainSystem->getMysqli()->prepare("UPDATE videos SET credits=?,`length`=?,`like`=?,`like_limit`=?,subscribe=?,sub_limit=? WHERE id=? AND user_id=?");
					$stmt->bind_param('iiiiiiii', $creds, $view_for, $like_check, $like_limit, $subscribe_check, $sub_limit, $video['id'], $user['id']);
					$stmt->execute();
					$stmt->close();

					$stmt = $mainSystem->getMysqli()->prepare("UPDATE users SET credits=? WHERE id=?");
					$stmt->bind_param('ii', $user_creds, $user['id']);
					$stmt->execute();
					$stmt->close();

					$_SESSION['success'] = "Your video '" . $video['vid_id'] . "' has been saved";
					header("Location: /?page=Videos");
				} else
				{
					$error = "Credits have to be more than 0 and less than " . $user['credits'];
					$stayOnEdit = true;
				}
			} else
			{
				$stmt->close();
				
				header("Location: /?page=Videos");
			}
		} else if ($_POST['submit'] == "Disable")
		{
			$stmt = $mainSystem->getMysqli()->prepare("SELECT id FROM videos WHERE id=? AND user_id=?");
			$stmt->bind_param('ii', $_GET['video_id'], $user['id']);
			$stmt->execute();
			$stmt->store_result();
			
			if ($stmt->num_rows == 1)
			{
				$stmt->close();
				
				$video = $mainSystem->videoAction->getActiveVideo($_GET['video_id']);
				
				$stmt = $mainSystem->getMysqli()->prepare("INSERT INTO inactive SELECT * FROM videos WHERE id=? AND user_id=?");
				$stmt->bind_param('ii', $_GET['video_id'], $user['id']);
				$stmt->execute();
				$stmt->close();

				$stmt = $mainSystem->getMysqli()->prepare("DELETE FROM videos WHERE id=? AND user_id=?");
				$stmt->bind_param('ii', $_GET['video_id'], $user['id']);
				$stmt->execute();
				$stmt->close();

				$_SESSION['success'] = "Your video '" . $video['vid_id'] . "' has been disabled";
				header("Location: /?page=Videos");
			} else
			{
				$stmt->close();
				
				header("Location: /?page=Videos");
			}
		} else if ($_POST['submit'] == "Delete")
		{
			$stmt = $mainSystem->getMysqli()->prepare("SELECT id FROM videos WHERE id=? AND user_id=?");
			$stmt->bind_param('ii', $_GET['video_id'], $user['id']);
			$stmt->execute();
			$stmt->store_result();
			
			if ($stmt->num_rows == 1)
			{
				$stmt->close();
				
				$video = $mainSystem->videoAction->getActiveVideo($_GET['video_id']);
				
				$stmt = $mainSystem->getMysqli()->prepare("UPDATE users SET credits=credits+? WHERE id=?");
				$stmt->bind_param('ii', $video['credits'], $user['id']);
				$stmt->execute();
				$stmt->close();
				
				$stmt = $mainSystem->getMysqli()->prepare("DELETE FROM videos WHERE id=? AND user_id=?");
				$stmt->bind_param('ii', $_GET['video_id'], $user['id']);
				$stmt->execute();
				$stmt->close();

				$_SESSION['success'] = "Your video '" . $video['vid_id'] . "' has been deleted, your credits have been refunded";
				header("Location: /?page=Videos");
			} else
			{
				$stmt->close();
				
				header("Location: /?page=Videos");
			}
		}
	} else if (isset($_POST['submit']) && $_POST['submit'] == "Add Video")
	{
        if ($mainSystem->videoAction->getActiveVideosCount() + $mainSystem->videoAction->getDisabledVideosCount() >= 5 && $user['group'] == 2) {
            $error = "You have reached your max video limit, either delete a video or upgrade your account";
        } else {
            $vid_id = $_POST['vid_id'];
            $view_for = $_POST['view_for'];
			
            $like_check = isset($_POST['like']) ? 1 : 0;
			$subscribe_check = isset($_POST['subscribe']) ? 1 : 0;
			
            $like_limit = $_POST['like_limit'];
            $sub_limit = $_POST['sub_limit'];
			
            $credits = $_POST['credits'];

            $resp = recaptcha_check_answer($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

            if ($resp->is_valid)
            {
                if ($credits >= 0 && $credits <= $user['credits'])
                {
					$stmt = $mainSystem->getMysqli()->prepare("UPDATE users SET credits=credits-? WHERE id=?");
					$stmt->bind_param('ii', $credits, $user['id']);
					$stmt->execute();
					$stmt->close();

					$stmt = $mainSystem->getMysqli()->prepare("INSERT INTO videos (vid_id, user_id, credits, `length`, `like`, `like_limit`, subscribe, sub_limit) VALUES (?,?,?,?,?,?,?,?)");
					$stmt->bind_param('siiiiiii', $vid_id, $user['id'], $credits, $view_for, $like_check, $like_limit, $subscribe_check, $sub_limit);
					$stmt->execute();
					
					$video = $mainSystem->videoAction->getActiveVideo($stmt->insert_id);
					
					$stmt->close();

					$_SESSION['success'] = "Your video '" . $video['vid_id'] . "' has been successfully added to your account";
					header("Location: /?page=Videos");
                } else
                {
                    $error = "Credits have to be more than 0 and less than " . $user['credits'];
                    $stayOnAdd = true;
                }
            } else
            {
                $error = "The captcha you entered was incorrect";
                $stayOnAdd = true;
            }
        }
	}
?>

<?php if (isset($success)) { ?><div class="g_12"><div class="success iDialog"><?php echo $success; ?></div></div><?php } ?>
<?php if (isset($error)) { ?><div class="g_12"><div class="error iDialog"><?php echo $error; ?></div></div><?php } ?>

<div class="g_12" id="table_wTabs">
	<div class="widget_header wwOptions">
		<h4 class="widget_header_title">Videos</h4>
		<ul class="w_Tabs">
			<li><a id="VideosList" href="#VideosList">Videos List</a></li>
			<li <?php echo $stayOnAdd ? 'class="ui-tabs-selected"' : ''; ?>><a id="AddVideoA" href="#AddVideo">Add Video</a></li>
		</ul>
	</div>
	<div class="widget_contents noPadding">
		<div id="VideosList">
			<?php if (!isset($_GET['video_id'])) { ?>
				<table class="videoTable tables">
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
					<tbody id="videos">
						<?php
							foreach ($mainSystem->videoAction->getActiveVideos() as $video) {
								$like = $video['like'] == 1 ? ($video['likes'] < $video['like_limit'] || $video['like_limit'] == 0 ? "style='color: green;'" : "style='color: red;'") : "style='color: red;'";
								$subscribe = $video['subscribe'] == 1 ? ($video['subscribes'] < $video['sub_limit'] || $video['sub_limit'] == 0 ? "style='color: green;'" : "style='color: red;'") : "style='color: red;'";
								?>
								<tr onclick="$(this).children('td:last').children('form').submit();">
									<td><b><?php echo $video['vid_id']; ?></b></td>
									<td><b><?php echo number_format($video['views']); ?></b></td>
									<td><b><?php echo $video['length'] . " seconds"; ?></b></td>
									<td <?php echo $like; ?>><b><?php echo number_format($video['likes']) . " / " . number_format($video['like_limit']); ?></b></td>
									<td <?php echo $subscribe; ?>><b><?php echo number_format($video['subscribes']) . " / " . number_format($video['sub_limit']); ?></b></td>
									<td style="color: green;"><b><?php echo number_format($video['credits']); ?></b></td>
									<td style="display: none;">
										<form method="GET" action="">
											<input type="hidden" name="page" value="Videos" />
											<input type="hidden" name="video_id" value="<?php echo $video['id']; ?>" />
										</form>
									</td>
								</tr>
								<?php
							}
						?>
					</tbody>
				</table>
			<?php } else if (isset($_GET['video_id'])) {
					$video = $mainSystem->videoAction->getActiveVideo($_GET['video_id']);
					
					if ($video == null) header("Location: /?page=Videos");
				?>
				<form action="" method="POST">
					<div class="g_3"><span class="label">Video ID <span class="must">*</span></span></div>
					<div class="g_9">
						<input class="simple_field" type="text" required placeholder="<?php echo $video['vid_id']; ?>" disabled />
					</div>
					
					<div class="g_3"><span class="label">View For <span class="must">*</span></span></div>
					<div class="g_9" id="addVideo">
						<select class="simple_form" name="view_for" required>
							<option class="edit-option" value="30" <?php echo $video['length'] == 30 ? "selected" : ""; ?> >30 Seconds (1 Credit)</option>
							<option class="edit-option" value="60" <?php echo $video['length'] == 60 ? "selected" : ""; ?> >1 Minute (2 Credits)</option>
							<option class="edit-option" value="90" <?php echo $video['length'] == 90 ? "selected" : ""; ?> >1 Minute, 30 Seconds (3 Credits)</option>
                            <option class="edit-option" value="120" <?php echo $video['length'] == 120 ? "selected" : ""; ?> >2 Minutes (4 Credits)</option>
						</select>
						<div class="field_notice">Length you want your video to be watched for</div>
					</div>
					
					<div class="g_3"><span class="label">Like </span></div>
					<div class="g_3">
						<input type="checkbox" name="like" class="simple_form" <?php echo $video['like'] == 1 ? "checked" : ""; ?> /><span class="label">(1 Credit)</span><span class="label" style="font-size: 15px; margin-top: 4px; float: right;">Limit</span>
					</div>
					<div class="g_6">
						<input class="simple_field" type="number" name="like_limit" min="0" value="<?php echo $video['like_limit']; ?>" />
						<div class="field_notice">0 = Unlimited</div>
					</div>
					
					<div class="g_3"><span class="label">Subscribe </span></div>
					<div class="g_3">
						<input type="checkbox" name="subscribe" class="simple_form" <?php echo $video['subscribe'] == 1 ? "checked" : ""; ?> /><span class="label">(1 Credit)</span><span class="label" style="font-size: 15px; margin-top: 4px; float: right;">Limit</span>
					</div>
					<div class="g_6">
						<input class="simple_field" type="number" name="sub_limit" min="0" value="<?php echo $video['sub_limit']; ?>" />
						<div class="field_notice">0 = Unlimited</div>
					</div>
					
					<div class="g_3"><span class="label">Credits to Add </span></div>
					<div class="g_9">
						<input class="simple_field" type="number" min="0" max="<?php echo $user['credits']; ?>" name="credits" value="0" />
						<div class="field_notice">Amount of credits to add to this video<br />Currently allocated: <?php echo number_format($video['credits']); ?><br />Max: <?php echo number_format($user['credits']); ?></div>
					</div>

					<div class="g_3"></div>
					<div class="g_9">
						<input style="float: right;" type="button" onclick="window.location='/?page=Videos';" value="Cancel" class="submitIt simple_buttons" />
						<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="Delete" title="Don't worry you get your credits back!" class="buttonTooltip submitIt simple_buttons" />
						<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="Disable" class="submitIt simple_buttons" />
						<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="Save" class="submitIt simple_buttons" />
					</div>
				</form>
				<br style="clear: both;" />
			<?php } ?>
		</div>
		
		<div id="AddVideo" class="ui-tabs-hide">
			<form action="" method="POST">
				<div class="g_3"><span class="label">Video ID <span class="must">*</span></span></div>
				<div class="g_9">
					<input class="simple_field" type="text" name="vid_id" value="<?php echo $vid_id; ?>" required placeholder="Ex: VTgNJoNT8So" maxlength="11" />
					<div class="field_notice">http://www.youtube.com/watch?v=<u>VTgNJoNT8So</u></div>
				</div>
				
				<div class="g_3"><span class="label">View For <span class="must">*</span></span></div>
				<div class="g_9" id="addVideo">
					<select class="simple_form" name="view_for" required>
						<option value="30" <?php echo $view_for == 30 ? "selected" : ""; ?> >30 Seconds (1 Credit)</option>
						<option value="60" <?php echo $view_for == 60 ? "selected" : ""; ?> >1 Minute (2 Credits)</option>
						<option value="90" <?php echo $view_for == 90 ? "selected" : ""; ?> >1 Minute, 30 Seconds (3 Credits)</option>
                        <option value="120" <?php echo $view_for == 120 ? "selected" : ""; ?> >2 Minutes (4 Credits)</option>
					</select>
					<div class="field_notice">Length you want your video to be watched for</div>
				</div>
				
				<div class="g_3"><span class="label">Like </span></div>
				<div class="g_3">
					<input type="checkbox" name="like" class="simple_form" <?php echo $like_check == 1 ? "checked" : ""; ?> /><span class="label">(1 Credit)</span><span class="label" style="font-size: 15px; margin-top: 4px; float: right;">Limit</span>
				</div>
				<div class="g_6">
					<input class="simple_field" type="number" name="like_limit" min="0" value="<?php echo $like_limit; ?>" />
					<div class="field_notice">0 = Unlimited</div>
				</div>
				
				<div class="g_3"><span class="label">Subscribe </span></div>
				<div class="g_3">
					<input type="checkbox" name="subscribe" class="simple_form" <?php echo $subscribe_check == 1 ? "checked" : ""; ?> /><span class="label">(1 Credit)</span><span class="label" style="font-size: 15px; margin-top: 4px; float: right;">Limit</span>
				</div>
				<div class="g_6">
					<input class="simple_field" type="number" name="sub_limit" min="0" value="<?php echo $sub_limit; ?>" />
					<div class="field_notice">0 = Unlimited</div>
				</div>
				
				<div class="g_3"><span class="label">Credits <span class="must">*</span></span></div>
				<div class="g_9">
					<input class="simple_field" type="number" min="0" max="<?php echo $user['credits']; ?>" name="credits" value="<?php echo $credits; ?>" />
					<div class="field_notice">Amount of credits to allocate to this video<br />Max: <?php echo number_format($user['credits']); ?></div>
				</div>
				
				<div class="g_3"><span class="label">Captcha <span class="must">*</span></span></div>
				<div class="g_9">
					<?php echo recaptcha_get_html($publickey); ?>
				</div>

				<div class="g_3"></div>
				<div class="g_9">
					<input style="float: right;" type="button" onclick="window.location='/?page=Videos';" value="Cancel" class="submitIt simple_buttons" />
					<input style="float: right; margin-right: 10px;" type="submit" name="submit" value="Add Video" class="submitIt simple_buttons" />
				</div>
			</form>
		</div>
	</div>
</div>