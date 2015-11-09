<?php

class Messages
{
	const INFO = 2;
	const SUCC = 4;
	const WARN = 8;
	const ERRO = 16;		const LINK = 32;	const LOGG = 64;
		private static $mainSystem;
	private static $messages = array();
	
	public function __construct($mainSystem)
	{		self::$mainSystem = $mainSystem;		
		$this->addMessage("To be able to obtain views, you have to download the viewer and view other peoples videos!", self::INFO);		$this->addMessage("Please <u>Click Here</u> to update to the latest version of the viewer!|/?page=Get+Credits", self::WARN | self::LINK | self::LOGG);
	}
	
	public static function addMessage($message = "", $type = self::INFO)
	{
		self::$messages[] = array('message' => $message, 'type' => $type);
	}
	
	public static function clearMessages()
	{
		self::$messages = array();
	}
	
	public static function getMessages()
	{
		return self::$messages;
	}
	
	public static function getFormattedMessages()
	{
		global $html;
		
		foreach (self::$messages as $message)
		{			if ((($message['type'] & self::LOGG) && self::$mainSystem->loggedIn) || !($message['type'] & self::LOGG))			{				$msg = explode("|", $message['message']);								if ($message['type'] & self::LINK)					$html .= "<a href='{$msg[1]}'>";								if ($message['type'] & self::INFO)				{					$html .= "<div class='g_12'><div class='info iDialog'>{$msg[0]}</div></div>";				} else if ($message['type'] & self::SUCC)				{					$html .= "<div class='g_12'><div class='success iDialog'>{$msg[0]}</div></div>";				} else if ($message['type'] & self::WARN)				{					$html .= "<div class='g_12'><div class='alert iDialog'>{$msg[0]}</div></div>";				} else if ($message['type'] & self::ERRO)				{					$html .= "<div class='g_12'><div class='error iDialog'>{$msg[0]}</div></div>";				}								if ($message['type'] & self::LINK)					$html .= "</a>";			}
		}
		
		return $html;
	}
}