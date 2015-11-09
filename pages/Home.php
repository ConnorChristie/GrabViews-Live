<?php
	if (!defined('INDEX'))
		exit('No direct script access allowed');

	if (!$isLoggedIn) { ?>
		<div class="g_12">
			<div class="widget_header">
				<h4 class="widget_header_title">Information</h4>
			</div>
			<div class="widget_contents">
				<div id="Information">
					<div class="line_grid">
						<span class="label lwParagraph" style="font-size: 20pt; font-weight: bold;">Start grabbing those views you've always wanted!</span>
						<br /><br />
					</div>
					
					<div class="line_grid">
						<br />
						<span class="label lwParagraph">
							<span style="font-size: 14pt;"><b>Step 1:</b></span><br /><br />
							&nbsp;&nbsp;&nbsp;&nbsp;<a href="/?page=Register"><b><span class="label">Sign up</span></b></a> to the site to be able to start grabbing views.
							
							<br /><br />
							
							<span style="font-size: 14pt;"><b>Step 2:</b></span><br /><br />
							&nbsp;&nbsp;&nbsp;&nbsp;Download our youtube video viewing program to earn credits or just simply buy credits.
							
							<br /><br />
							
							<span style="font-size: 14pt;"><b>Step 3:</b></span><br /><br />
							&nbsp;&nbsp;&nbsp;&nbsp;Add your youtube video and start grabbing those views!
						</span>
						<br /><br />
					</div>
					
					<div class="line_grid">
						<br />
						<span class="label lwParagraph">
							This is a youtube video exchange site. We let the other members view your video legitimately through our youtube video viewing program.
						</span>
						<br /><br />
					</div>
				</div>
			</div>
		</div>
	<?php } else {
		$referrals = $mainSystem->videoAction->fetch_all_assoc("SELECT username FROM users WHERE referral='" . $user['id'] . "'");
    ?>
	<div class="g_12">
		<div class="widget_header wwOptions">
			<h4 class="widget_header_title">Account Details</h4>
		</div>
		<div class="widget_contents noPadding">
			<table class="tables">
				<tbody>
                    <tr>
                        <td class="TextRight" style="width: 35%;"><b>Account ID:</b></td>
                        <td class="TextLeft"><?php echo $user['id']; ?></td>
                        <td style="width: 15%;"><a href="/?page=Account"><b><span class="label">My Account</span></b></a></td>
                    </tr>
					<tr>
						<td class="TextRight"><b>Username:</b></td>
						<td class="TextLeft"><?php echo $user['username']; ?></td>
						<td style="width: 15%;"><a href="/?page=Account&action=cu"><b><span class="label">Change</span></b></a></td>
					</tr>
					<tr>
						<td class="TextRight"><b>Email:</b></td>
						<td class="TextLeft"><?php echo $user['email']; ?></td>
						<td style="width: 15%;"></td>
					</tr>
					<tr>
						<td class="TextRight"><b>Password:</b></td>
						<td class="TextLeft"><?php for ($i = 0; $i < $user['pass_length']; $i++) { echo "*"; } ?></td>
						<td style="width: 15%;"><a href="/?page=Account&action=cp"><b><span class="label">Change</span></b></a></td>
					</tr>
					<tr>
						<td class="TextRight"><b>Account Type:</b></td>
                        <?php $group = $mainSystem->loginManager->getGroupInfo($user['group']); ?>
						<td class="TextLeft" <?php echo "style='color: " . $group['color'] . ";'"; ?>><?php echo $group['title']; ?></td>
						<td style="width: 15%;"><a href="/?page=Account&action=ua"><b><span class="label">Change</span></b></a></td>
					</tr>
					<tr>
						<td class="TextRight"><b>Credits:</b></td>
						<td class="TextLeft"><?php echo number_format($user['credits']); ?></td>
						<td style="width: 15%;"><a href="/?page=Get+Credits"><b><span class="label">Get Credits</span></b></a></td>
					</tr>
                    <tr>
                        <td class="TextRight"><b>Number of Videos:</b></td>
                        <td class="TextLeft">
                            <?php echo number_format($mainSystem->videoAction->getActiveVideosCount()); ?> active |
                            <?php echo number_format($mainSystem->videoAction->getDisabledVideosCount()); ?> disabled |
                            <?php echo number_format($mainSystem->videoAction->getActiveVideosCount() + $mainSystem->videoAction->getDisabledVideosCount()); ?> total
                        </td>
                        <td style="width: 15%;"><a href="/?page=Videos"><b><span class="label">My Videos</span></b></a></td>
                    </tr>
                    <tr>
                        <td class="TextRight"><b>Referral Link:</b></td>
                        <td class="TextLeft">http://<?php echo WEBSITE_URL; ?>/?ref=<?php echo $user['id']; ?></td>
                        <td style="width: 15%;"><a href="/?page=Referrals"><b><span class="label">My Referrals</span></b></a></td>
                    </tr>
                    <tr>
                        <td class="TextRight"><b>Referrals:</b></td>
                        <td class="TextLeft"><?php echo number_format(count($referrals)); ?></td>
                        <td style="width: 15%;"><a href="/?page=Referrals"><b><span class="label">My Referrals</span></b></a></td>
                    </tr>
                    <?php if ($user['group'] > 2 && $user['group'] < 6) { ?>
                        <tr>
                            <td class="TextRight"><b>Paypal Subscription ID:</b></td>
                            <td class="TextLeft"><?php echo $user['subscr_id']; ?></td>
                            <td style="width: 15%;"><a href="https://<?php echo PAYPAL_URL; ?>/cgi-bin/webscr?cmd=_subscr-find&alias=<?php echo PAYPAL_EMAIL; ?>"><b><span class="label">Cancel</span></b></a></td>
                        </tr>
                    <?php } ?>
				</tbody>
			</table>
		</div>
	</div>
<?php } ?>