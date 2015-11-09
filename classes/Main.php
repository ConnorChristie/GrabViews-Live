<?php
require "Messages.php";
require "Database.php";require "LoginManager.php";
require "VideoActions.php";

class Main
{
	public $messageSystem;
	public $databaseSystem;
	public $loginManager;
	public $videoAction;
	
	public $user;
	public $loggedIn;
	
	private $page;
	
	private $pages = array(
		"Home"     => array("php" => "Home.php", "title" => "Home", "subtitle" => "Main Page", "icon" => "iconic_HOME", "tab" => true, "auth" => false, "admin" => false),
		"Login"    => array("php" => "Login.php", "title" => "Login", "subtitle" => "Authentication", "icon" => "iconic_LOCK_STROKE", "tab" => true, "auth" => false, "admin" => false),
		"Register" => array("php" => "Register.php", "title" => "Register", "subtitle" => "Gain Access", "icon" => "iconic_LOCK_FILL", "tab" => true, "auth" => false, "admin" => false),
		
		"Account"     => array("php" => "Account.php", "title" => "Account", "subtitle" => "User Account", "icon" => "", "tab" => false, "auth" => true, "admin" => false),
		"Admin Panel" => array("php" => "Admin Panel.php", "title" => "Admin Panel", "subtitle" => "Admin Control Panel", "icon" => "", "tab" => false, "auth" => true, "admin" => true),
		"Get Credits" => array("php" => "Get Credits.php", "title" => "Get Credits", "subtitle" => "Get Credits", "icon" => "iconic_PLUS", "tab" => true, "auth" => true, "admin" => false),
		"Videos"      => array("php" => "Videos.php", "title" => "Videos", "subtitle" => "Active Videos", "icon" => "iconic_MOVIE", "tab" => true, "auth" => true, "admin" => false),
		"Disabled"    => array("php" => "Disabled.php", "title" => "Disabled", "subtitle" => "Disabled Videos", "icon" => "iconic_MOON", "tab" => true, "auth" => true, "admin" => false),
		"Referrals"   => array("php" => "Referrals.php", "title" => "Referrals", "subtitle" => "Your Referrals", "icon" => "iconic_USER", "tab" => true, "auth" => true, "admin" => false),
		"Tickets"     => array("php" => "Tickets.php", "title" => "Tickets", "subtitle" => "Support Tickets", "icon" => "iconic_TAG", "tab" => true, "auth" => true, "admin" => false),
		
		"Viewer"        => array("title" => "Online Viewer"),
		"Viewer String" => array("title" => "Viewer String"),
		"Faq"        => array("php" => "Faq.php", "title" => "FAQ", "subtitle" => "Frequently Asked Questions", "icon" => "", "tab" => false, "auth" => false, "admin" => false),
		"Terms"      => array("php" => "Terms.php", "title" => "Terms and Conditions", "subtitle" => "Terms and Conditions", "icon" => "", "tab" => false, "auth" => false, "admin" => false),
		"Privacy"    => array("php" => "Privacy.php", "title" => "Privacy Policy", "subtitle" => "Privacy Policy", "icon" => "", "tab" => false, "auth" => false, "admin" => false),
		"Contact Us" => array("php" => "Contact Us.php", "title" => "Contact Us", "subtitle" => "Contact Us", "icon" => "", "tab" => false, "auth" => false, "admin" => false),
		
		"404" => array("title" => "404"),
		"Logout" => array("title" => "Logout", "auth" => true)
	);
	public function __construct()
	{
		$this->databaseSystem = new Database();
		$this->loginManager = new LoginManager($this->getMysqli());
		
		$this->loggedIn = $this->loginManager->isLoggedIn($this->user);
		$this->messageSystem = new Messages($this);
		
		$this->videoAction = new VideoAction($this);		
		$this->databaseSystem->where("id", "51");
		$obj = $this->databaseSystem->get("videos", 1);		
		$this->page = isset($_GET['page']) ? ucwords(strtolower($_GET['page'])) : "Home";
		
		if (array_key_exists($this->page, $this->pages))
		{
			$this->page = $this->pages[$this->page];
			
			if (isset($this->page['auth']) && $this->page['auth'] && !$this->loggedIn)
			{
				$this->page = $this->pages["404"];
				
				return;
			}
			
			if ($this->loggedIn && isset($this->page['admin']) && $this->page['admin'] && !$this->loginManager->isUserAdmin())
			{
				$this->page = $this->pages["404"];
				
				return;
			}
		}
		
		if ($this->page['title'] == "Logout" && !$this->loginManager->isBanned()) $this->loginManager->doLogout();
		if ($this->page['title'] != "Home" && $this->loginManager->isBanned()) header("Location: /");
		
		$this->checkMinimal();
		$this->checkViewer();
		$this->checkActions();
	}
	
	public function getPages()
	{
		return $this->pages;
	}
	
	public function getPageData()
	{
		return $this->page;
	}
	
	public function getIncludeUrlForPage()
	{
		return "pages/{$this->page['php']}";
	}
	
	public function getMysqli()
	{
		return $this->databaseSystem->_mysqli;
	}
	
	private function checkMinimal()
	{
		if (isset($_GET['minimal']) && $this->page['title'] == "Login" && $_GET['minimal'] == "True")
		{
			if (!$this->loggedIn)
			{
				if (isset($_POST['username']) && $_POST['username'] != "" && isset($_POST['password']) && $_POST['password'] != "")
				{
					$username = $_POST['username'];
					$password = $_POST['password'];
					$remember = isset($_POST['remember']);

					$return = $this->loginManager->doLogin($username, $password, $remember);
					
					echo (isset($_SESSION['is_banned']) && $_SESSION['is_banned']) ? "Banned" : $return;
				} else
				{
					echo "Please enter a Username or Email and a Password";
				}
			} else
			{
				echo "You are already logged in";
			}
			
			exit();
		} else if (isset($_POST['check']) && $_POST['check'] == "Login")
		{
			echo $this->loggedIn ? "Yes" : "No";
			
			exit();
		}
	}
	
	private function checkViewer()
	{
		if ($this->page['title'] == "Viewer String")
		{
			require_once "classes/onlineViewer.php";
			
			$onlineViewer = new OnlineViewer($this);
			echo "<span id='vw' style='display: none;'>" . $onlineViewer->getViewer() . "</span>";

			exit();
		}
	}
	
	private function checkActions()
	{
		if (isset($_GET['action']) && $_GET['action'] == "Verify")
		{
			$hash = $_GET['vk'];
			
			if ($hash != "")
			{
				$this->databaseSystem->where("hash", $hash)->where("is_verify", 1);
				$verifies = $this->databaseSystem->get("verifies", 10);
				
				if (count($verifies) != 0)
				{
					$this->databaseSystem->where("id", $verifies[0]['user_id']);
					$this->databaseSystem->update("users", array("`group`" => 2));
					
					$this->databaseSystem->where("id", $verifies[0]['id']);
					$this->databaseSystem->delete("verifies", 1);
					
					header("Location: /?page=Login&from=Verify");
				} else
				{
					$error = "Could not verify the Validation Key";
				}
			} else
			{
				header("Location: /");
			}
		}
	}
}