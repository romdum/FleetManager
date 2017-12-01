<?php

namespace FleetManager;

/**
 * Class Logger
 */
class Logger
{
	const LOG_TYPE = ['error', 'info'];
	const DAYS_NUMBER_TO_SAVE = 1;

	protected static $LOG_PATH;

	public function __construct()
	{
		if( ! FleetManager::$settings->getSetting( 'Logger', 'enabled' ) )
			return;

		//$this->cleanLog();

		self::$LOG_PATH = FleetManager::$PLUGIN_PATH . 'logs/';

		if( ! is_dir( self::$LOG_PATH ) )
			mkdir( self::$LOG_PATH );

		foreach( self::LOG_TYPE as $logFile )
			if( ! file_exists( self::$LOG_PATH . $logFile . date( 'Ymd' ) . '.log' ) )
				file_put_contents( self::$LOG_PATH . $logFile . date( 'Ymd' ) . '.log' ,'[ FleetManager Log ' . ucfirst( $logFile ) . ' ]' . "\n\r" );
	}

	public function log( $message, $type = 'info' )
	{
		if( ! FleetManager::$settings->getSetting( 'Logger', 'enabled' ) )
			return;

		if( ! in_array( $type, self::LOG_TYPE ) )
			$type = 'info';

		file_put_contents(
			self::$LOG_PATH . $type . date( 'Ymd' ) . '.log',
			$this->getPrefixMessage() . ' ' . $message . "\n\r",
			FILE_APPEND
		);
	}

	private function cleanLog()
	{
		if( ! FleetManager::$settings->getSetting( 'Logger', 'enabled' ) )
			return;

		foreach( scandir( self::$LOG_PATH ) as $logFile )
			if( is_file( $logFile ) && $this->isTooOld( $logFile ) && pathinfo( $logFile, PATHINFO_EXTENSION ) === 'log' )
				unlink( $logFile );
	}

	private function isTooOld( $filePath )
	{
		return filemtime( $filePath ) < time() - 60 * 60 * 24 * self::DAYS_NUMBER_TO_SAVE;
	}

	private function getPrefixMessage()
	{
		return date( '[d/m/Y H:i:s]' );
	}
}