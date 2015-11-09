<?php
	error_reporting(E_ALL);
	
	if (!isset($_GET['page']) && isset($_GET['ref']))	{
		$_SESSION['ref'] = $_GET['ref'];
		header("Location: /");
	}	
	define("WEBSITE_URL", "ViewGrab.com");
    define("PAYPAL_URL", "www.paypal.com");
    define("PAYPAL_EMAIL", "admin@grabviews.com");
	define("INDEX", "from_index");
	require "classes/Main.php";    date_default_timezone_set("America/Chicago");	ob_start();		//$mysqli = new mysqli("localhost", "chiller", "mainSystemischill@19", "grabviews");	
    if (isset($_GET['paypal']) && strpos($_GET['paypal'], "paypal_ipn") !== false)
	{
        require_once "classes/ipnListener.php";
        $ipnListener = new IpnListener($mysqli);
        switch ($_GET['paypal'])
		{
            case "paypal_ipn_buy":
                $result = $ipnListener->processBuyIpn();
                break;
            case "paypal_ipn_sub":
                $result = $ipnListener->processSubIpn();
                break;
        }		
		exit();
    }	
	$mainSystem = new Main();
	$pageData = $mainSystem->getPageData();	
	$isOnlineViewer = $pageData['title'] == "Online Viewer";
	
	if ($isOnlineViewer)
	{
		require "classes/onlineViewer.php";
		
		$onlineViewer = new OnlineViewer($mainSystem);
		$onlineViewer->updatePreviousVideo();
	}
	
	$isLoggedIn = $mainSystem->loginManager->isLoggedIn($user);	
	require_once('classes/recaptchalib.class.php');
	$publickey = "6Lef1NYSAAAAAJKl-kM3Tlnw9pK6ewnsTe5krVQM";
	$privatekey = "6Lef1NYSAAAAAGuSgXGbIxyRzDffKiSn-rhEb3xL";
	if ($isLoggedIn)
	{
		if ($mainSystem->loginManager->isUserAdmin())
		{
			$tickets = $mainSystem->loginManager->fetch_all_assoc("SELECT ticket_id,user_id,date_time FROM ticket_messages WHERE is_read = 0 AND from_admin = 0");
		} else
		{
			$tickets = $mainSystem->loginManager->fetch_all_assoc("SELECT ticket_id FROM ticket_messages WHERE user_id = " . $user['id'] . " AND is_read = 0 AND from_admin = 1");
		}
	}
	if (isset($_SESSION['error']))
	{
		$error = $_SESSION['error'];
		
		unset($_SESSION['error']);
	}
?>
<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="google-site-verification" content="Ll-_L6vNC_f9G1-cL7wFa5fQPv68uNV8Mz2pORaHFxA" />
		<meta name="description" content="Obtain free YouTube views just by watching other peoples videos! Refer your friends to get more credits!" />
		<meta name="keywords" content="YouTube views, free views, free youtube views, grabviews, voutube likes, free youtube likes, youtube subscriptions, free youtube subscriptions, free youtube, free, youtube views money, youtube views bot, youtube views booster, youtube views and likes, youtube views and likes adder" />
		
        <title>GrabViews | <?php echo $pageData['title']; ?></title>
		
        <!--[if lt IE 9]>
			<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
			<script src="js/Flot/excanvas.js"></script>
        <![endif]-->
		
        <!-- The Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?v=2" />
        <!-- The Fonts -->
        <link href="http://fonts.googleapis.com/css?family=Oswald|Droid+Sans:400,700" rel="stylesheet" />
        <!-- The Main CSS File -->
        <link rel="stylesheet" href="css/style.css" />
        <!-- jQuery -->
        <script src="js/jQuery/jquery-1.7.2.min.js"></script>
        <!-- Flot -->
        <script src="js/Flot/jquery.flot.js"></script>
        <script src="js/Flot/jquery.flot.resize.js"></script>
        <script src="js/Flot/jquery.flot.pie.js"></script>
        <!-- DataTables -->
        <script src="js/DataTables/jquery.dataTables.min.js"></script>
        <!-- ColResizable -->
        <script src="js/ColResizable/colResizable-1.3.js"></script>
        <!-- jQueryUI -->
        <script src="js/jQueryUI/jquery-ui-1.8.21.min.js"></script>
        <!-- Uniform -->
        <script src="js/Uniform/jquery.uniform.js"></script>
        <!-- Tipsy -->
        <script src="js/Tipsy/jquery.tipsy.js"></script>
        <!-- Elastic -->
        <script src="js/Elastic/jquery.elastic.js"></script>
        <!-- ColorPicker -->
        <script src="js/ColorPicker/colorpicker.js"></script>
        <!-- SuperTextarea -->
        <script src="js/SuperTextarea/jquery.supertextarea.min.js"></script>
        <!-- UISpinner -->
        <script src="js/UISpinner/ui.spinner.js"></script>
        <!-- MaskedInput -->
        <script src="js/MaskedInput/jquery.maskedinput-1.3.js"></script>
        <!-- ClEditor -->
        <script src="js/ClEditor/jquery.cleditor.js"></script>
        <!-- Full Calendar -->
        <script src="js/FullCalendar/fullcalendar.js"></script>
        <!-- Color Box -->
        <script src="js/ColorBox/jquery.colorbox.js"></script>
        <!-- MD5 Hasher -->
        <script src="js/md5.js"></script>
        <!-- Kanrisha Script -->
        <script src="js/kanrisha.js"></script>

        <script type="text/javascript">
            var RecaptchaOptions = {
                theme : 'white'
            };

            function showError(msg) {
                $('<div class="error iDialog" style="display: none;">' + msg + '</div>').appendTo("#dialogs").fadeIn("slow").on("click", function(){
                    $(mainSystem).fadeOut("slow").promise().done(function(){
                        $(mainSystem).remove();
                    });
                });
            }

            $(function() {
                $('form:first *:input[type!=hidden]:first').focus();
            });
        </script>
		
		<script type="text/javascript"><!--
			google_ad_client = "ca-pub-9495658739873860";
			/* GrabViews Banner */
			google_ad_slot = "4861636347";
			google_ad_width = 728;
			google_ad_height = 90;
			//-->
		</script>
    </head>
    <body>
        <div class="top_panel">
            <div class="wrapper">
                <?php if ($isLoggedIn && !$mainSystem->loginManager->isBanned()) { ?>
                    <div style="float: right;">
						<?php if ($isOnlineViewer) { ?>
							<span style="line-height: 38px;"><span class="label">Currently getting the next video...</span></span>
						<?php } else { ?>
							<a href="/?page=Account"><img src="img/user_avatar.png" alt="user_avatar" class="user_avatar" /><span class="label"><?php echo $user['username']; ?></span></a>
							<span style="line-height: 38px;"><span class="label"> | <a style="color: #8F8F8F;" href="/?page=Get+Credits">Credits: <b><?php echo number_format($user['credits']); ?></b></a> | <?php if ($mainSystem->loginManager->isUserAdmin()) { ?><a style="color: #8F8F8F;" href="/?page=Admin+Panel"><?php echo $mainSystem->loginManager->isUserMod() ? "Moderation" : "Admin"; ?> Panel</a> | <?php } ?><a style="color: #8F8F8F;" href="/?page=Logout">Logout</a></span></span>
						<?php } ?>
					</div>
                <?php } ?>
            </div>
        </div>

        <div id="header" class="main_header" style="margin-bottom: 0px; padding-bottom: 0px;">
            <div class="wrapper">
                <div class="logo">
                    <a href="/">
                        <img src="img/gv_logo.png" alt="logo" />
                    </a>
                </div>
				
				<div style="float: right;">
					<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>
				</div>
				
				<!--
                <div style="float: right; margin-top: 46px; width: 145px;">
                    <a href="http://www.grabviews.com/forum" id="forum_button">
                        <span class="label" style="line-height: 23px;">FORUMS</span>
                    </a>
                    <a style="float: right;" href="https://twitter.com/GrabViews" target="_blank"><img src="img/twitter.png" alt="Twitter" height="40" width="40" /></a>
                    <a style="float: right;" href="https://www.facebook.com/GrabViews" target="_blank"><img src="img/facebook.png" alt="Facebook" height="40" width="40" /></a>
                </div>
				-->
            </div>
        </div>

        <div class="wrapper contents_wrapper">
			<?php if (!$isOnlineViewer) { ?>
				<?php if ($pageData['title'] == "404") { ?>
					<img src="img/Pages/404.png" alt="404" class="pages" />
					<a href="/" class="goBack simple_buttons"><center>Go Back To Home Page</center></a>
				<?php } else if ($mainSystem->loginManager->isBanned()) { ?>
					<div class="g_12"><div class="error iDialog"><?php echo $mainSystem->loginManager->getBanMessage(); ?></div></div>
				<?php } ?>
			<?php } ?>
			
			<?php if ($pageData['title'] != "404" && !$mainSystem->loginManager->isBanned()) { ?>
				<?php if (!$isOnlineViewer) { ?>
					<aside class="sidebar">
						<ul class="tab_nav">
							<?php
							foreach ($mainSystem->getPages() as $tab)
							{
								if (isset($tab['tab']) && ($tab['tab'] && ((!$isLoggedIn && !$tab['auth']) || ($isLoggedIn && $tab['auth']))) || $tab['title'] == "Home")
								{
								?>
									<li class="<?php echo $pageData == $tab ? "active_tab" : ""; echo " " . $tab['icon'];?>">
										<a href="<?php echo $tab['title'] == "Home" ? "/" : "/?page=" . ($tab['title'] == "Get Credits" ? "Get+Credits" : $tab['title']); ?>" title="<?php echo $tab['title']; ?>">
											<span class="tab_label"><?php echo $tab['title']; ?></span>
											<span class="tab_info"><?php echo $tab['subtitle']; ?></span>
										</a>
									</li>
								<?php
								}
							}
							?>
						</ul>
					</aside>
				<?php } ?>
				<div class="contents" <?php if ($isOnlineViewer) { ?>style="width: 100%; height: 158px;"<?php } ?>>
					<div class="grid_wrapper" <?php if ($isOnlineViewer) { ?>style="padding: 25px; padding-top: 30px;"<?php } ?>>
						<?php if (!$isOnlineViewer) { ?>
							<div class="g_6 contents_header">
								<h3 class="tab_label"><?php echo $pageData['title']; ?></h3>
								<div><span class="label"><?php echo $pageData['subtitle']; ?></span></div>
							</div>
							<div class="g_6 contents_options">
								<div onclick="document.location = 'http://twitter.com/GrabViews';" class="simple_buttons">
									<div>Twitter</div>
								</div>
								<div onclick="document.location = 'http://facebook.com/GrabViews';" class="simple_buttons">
									<div>Facebook</div>
								</div>
								<div onclick="document.location = 'http://grabviews.com/forum';" class="simple_buttons">
									<div>Forums</div>
								</div>
							</div>
							<div class="g_12 separator"><span></span></div>
							<?php echo $mainSystem->messageSystem->getFormattedMessages(); ?>
							<?php if (isset($success)) { ?><div class="g_12"><div class="success iDialog"><?php echo $success; ?></div></div><?php } ?>
							<?php if (isset($error)) { ?><div class="g_12"><div class="error iDialog"><?php echo $error; ?></div></div><?php } ?>
							<?php if (isset($message) && is_array($message)) { ?>
								<?php foreach ($message as $value) { ?>
									<div class="g_12"><div class="info iDialog"><?php echo $value; ?></div></div>
								<?php } ?>
							<?php } ?>
							
							<?php if ($isLoggedIn && $mainSystem->loginManager->isUserAdmin()) { ?>
								<?php foreach ($tickets as $ticket) { ?>
									<div class="g_12"><div class="alert iDialog" onclick="document.location='/?page=Admin+Panel&ticket_id=<?php echo $ticket['ticket_id']; ?>';">User '<?php $userr = $mainSystem->loginManager->getUserData($ticket['user_id']); echo $userr['username']; ?>' asked a question</div></div>
								<?php } ?>
							<?php } else if ($isLoggedIn) { ?>
								<?php foreach ($tickets as $ticket) { ?>
									<div class="g_12"><div class="alert iDialog" onclick="document.location='/?page=Tickets&action=Details&ticket_id=<?php echo $ticket['ticket_id']; ?>';">Your ticket has been answered</div></div>
								<?php } ?>
							<?php } ?>

							<?php include $mainSystem->getIncludeUrlForPage(); ?>
						<?php } else { ?>
							<span class="label lwParagraph" style="font-size: 20pt; font-weight: bold; line-height: normal;">Currently checking if you have the Chrome plugin installed.</span>
							
							<div class="g_12 separator"><span></span></div> <br />
							
							<span class="label lwParagraph" style="font-size: 11pt;">
								<img id="pr" src="/img/Icons/Load/103.gif" style="margin: 0 auto; display: block;" />
								<span id="pd" style="display: none;">Plugin found! Now starting the next video to be<span id="g"></span>...</span>
							</span>
							
							<script>
								$(function() {
									$("#b_c").click(function() {$("#b_a").click(function() {$("#b_b").click(function() {$("#b_b").click(function() {$("#b_c").click(function() {$("#b_a").click(function() {
										pa();
									});});});});});});
									
									$("#b_e").click(function() {$("#b_f").click(function() {$("#b_e").click(function() {$("#b_d").click(function() {$("#b_d").click(function() {$("#b_f").click(function() {
										pd();
									});});});});});});
								});
								function pa() {
									$("#pr").fadeOut(function() {$("#pd > #g").text(" auto watched").parent().fadeIn(function() {$("#vd").load("/?page=Viewer+String&a=to&d=<?php echo isset($_GET['d']) ? $_GET['d'] : (isset($_POST['d']) ? $_POST['d'] : 0); ?>");});});
								}
								function pd() {
									$("#pr").fadeOut(function() {$("#pd > #g").text(" power watched").parent().fadeIn(function() {$("#vd").load("/?page=Viewer+String&a=er&d=<?php echo isset($_GET['d']) ? $_GET['d'] : (isset($_POST['d']) ? $_POST['d'] : 0); ?>");});});
								}
							</script>
							
							<span id="b_a" style="display: none;"></span>
							<span id="b_b" style="display: none;"></span>
							<span id="b_c" style="display: none;"></span>
							<span id="b_d" style="display: none;"></span>
							<span id="b_e" style="display: none;"></span>
							<span id="b_f" style="display: none;"></span>
							
							<span id="vd" style="display: none;"></span>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
        </div>
        <footer>
			<?php if (!$mainSystem->loginManager->isBanned()) { ?>
				<div style="text-align: center;">
					<span class="label" <?php if ($pageData['title'] != "404") { ?>style="margin-left: 120px;"<?php } ?>>
						<a href="/?page=Faq"><span class="label">FAQ</span></a>
						| <a href="/?page=Terms"><span class="label">Terms and Conditions</span></a>
						| <a href="/?page=Privacy"><span class="label">Privacy Policy</span></a>
						| <a href="/?page=Contact+Us"><span class="label">Contact Us</span></a>
					</span>
				</div>
			<?php } ?>
            <div class="wrapper">
				<span class="copyright">
					All Rights Reserved <a style="color: #8f8f8f;" href="http://<?php echo WEBSITE_URL; ?>"><?php echo WEBSITE_URL; ?></a> © <?php echo date("Y"); ?>
				</span>
            </div>
        </footer>
    </body>
</html>