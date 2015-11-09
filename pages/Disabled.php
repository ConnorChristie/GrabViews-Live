<?php
    if (!defined('INDEX'))
		exit('No direct script access allowed');

	if (isset($_SESSION['success']))
	{
		$success = $_SESSION['success'];
		unset($_SESSION['success']);
	}
	
	if (isset($_POST['submit']) && $_POST['submit'] == "Enable")
	{
		$stmt = $mainSystem->getMysqli()->prepare("SELECT id FROM inactive WHERE id=? AND user_id=?");
		$stmt->bind_param('ii', $_POST['id'], $user['id']);
		$stmt->execute();
		$stmt->store_result();
		
		if ($stmt->num_rows == 1)
		{
			$stmt = $mainSystem->getMysqli()->prepare("INSERT INTO videos SELECT * FROM inactive WHERE id=? AND user_id=?");
			$stmt->bind_param('ii', $_POST['id'], $user['id']);
			$stmt->execute();
			$stmt->close();

			$stmt = $mainSystem->getMysqli()->prepare("DELETE FROM inactive WHERE id=? AND user_id=?");
			$stmt->bind_param('ii', $_POST['id'], $user['id']);
			$stmt->execute();
			$stmt->close();
			
			$video = $mainSystem->videoAction->getActiveVideo($_POST['id']);
			
			$_SESSION['success'] = "Your video '" . $video['vid_id'] . "' has been enabled.";
			header("Location: /?page=Disabled");
		} else
		{
			header("Location: /?page=Disabled");
		}
	}
?>

<?php if (isset($success)) { ?><div class="g_12"><div class="success iDialog"><?php echo $success; ?></div></div><?php } ?>
<?php if (isset($error)) { ?><div class="g_12"><div class="error iDialog"><?php echo $error; ?></div></div><?php } ?>

<div class="g_12">
	<div class="widget_header wwOptions">
		<h4 class="widget_header_title">Disabled Videos</h4>
	</div>
	<div class="widget_contents noPadding">
		<table class="datatable tables">
			<thead>
				<tr>
					<th>Video ID</th>
					<th>Views</th>
					<th>View Length</th>
					<th>Likes / Limit</th>
					<th>Subs / Limit</th>
					<th>Credits</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($mainSystem->videoAction->getDisabledVideos() as $video)				{
					$like = $video['like'] == 1 ? ($video['likes'] < $video['like_limit'] || $video['like_limit'] == 0 ? "green" : "red") : "red";
					$subscribe = $video['subscribe'] == 1 ? ($video['subscribes'] < $video['sub_limit'] || $video['sub_limit'] == 0 ? "green" : "red") : "red";
					?>
					<tr>
						<td><b><?php echo $video['vid_id']; ?></b></td>
						<td><b><?php echo number_format($video['views']); ?></b></td>
						<td><b><?php echo $video['length']; ?></b></td>
						<td><b><font color="<?php echo $like; ?>"><?php echo number_format($video['likes']) . " / " . number_format($video['like_limit']); ?></font></b></td>
						<td><b><font color="<?php echo $subscribe; ?>"><?php echo number_format($video['subscribes']) . " / " . number_format($video['sub_limit']); ?></font></b></td>
						<td><b><font color="red"><?php echo number_format($video['credits']); ?></font></b></td>
						<td><b><form method="POST" action="/?page=Disabled"><input type="hidden" name="id" value="<?php echo $video['id']; ?>" /><input type="submit" name="submit" value="Enable" style="outline: none;" class="submitIt simple_buttons" /></form></b></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>