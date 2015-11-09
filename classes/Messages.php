<?php

class Messages
{
	const INFO = 2;
	const SUCC = 4;
	const WARN = 8;
	const ERRO = 16;
	
	private static $messages = array();
	
	public function __construct($mainSystem)
	{
		$this->addMessage("To be able to obtain views, you have to download the viewer and view other peoples videos!", self::INFO);
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
		{
		}
		
		return $html;
	}
}