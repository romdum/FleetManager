<?php

namespace FleetManager;

/**
 * Class Logger
 */
class Logger
{
	const SLUG = 'FleetManager_logs';
	const LOG_TYPE = ['error', 'info'];
	const DAYS_NUMBER_TO_SAVE = 1;

	protected static $LOG_PATH;

	public function __construct()
	{
		if( ! FleetManager::$settings->getSetting( 'Logger', 'enabled' ) )
			return;

		$this->cleanLog();

		self::$LOG_PATH = FleetManager::$PLUGIN_PATH . 'logs/';
	}

	public function log( $message, $type = 'info' )
	{
		if( ! FleetManager::$settings->getSetting( 'Logger', 'enabled' ) )
			return;

		if( ! in_array( $type, self::LOG_TYPE ) )
			$type = 'info';

		wp_insert_post([
			'post_title'   => strtoupper( $type ),
			'post_content' => $message,
			'post_type'    => self::SLUG,
		]);
	}

	private function cleanLog()
	{
		global $wpdb;

		$wpdb->query(
			$wpdb->prepare(
				"
                DELETE FROM {$wpdb->prefix}posts
				WHERE post_type = %s
		 		AND post_date < %s
			",
			self::SLUG,
			date( 'Y-m-d H:i:s', time() - 60 * 60 * 24 * self::DAYS_NUMBER_TO_SAVE ) )
		);
	}

	public function removeAll()
	{
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
			"
                DELETE FROM {$wpdb->prefix}posts
				WHERE post_type = %s
			",self::SLUG )
		);
	}
}