<?php

require_once ('/log4php/Logger.php');

class LoggerService{
	
	private static $logger;
	
	private static function init(){
		Logger::configure("LoggerConfig.xml");
		LoggerService::$logger = Logger::getLogger('myLogger');
	}
	
	public static function info($message){
		if(!LoggerService::$logger)
			LoggerService::init();
		
		LoggerService::$logger->info($message);
	}
	
	public static function warn($message){
		if(!LoggerService::$logger)
			LoggerService::init();
		
		LoggerService::$logger->warn($message);
	}
	
	public static function debug($message){
		if(!LoggerService::$logger)
			LoggerService::init();
		
		LoggerService::$logger->debug($message);
	}
	
	public static function error($message){
		if(!LoggerService::$logger)
			LoggerService::init();
	
			LoggerService::$logger->error($message);
	}

}
?>