<?php

namespace FleetManager;

class Install
{
	public static function activate()
	{
		if( get_option( Settings::NAME, null ) === null )
		{
			$options = [
				'VehiclePostType' => [
					'display' => [
						'photo' => true
					],
					'default' => [
						'photoNbr' => 5
					],
					'deletePics' => true
				],
				'SocialNetwork' => [
					'facebook' => [
						'enabled'   => false,
						'appId'     => 'my_app_id',
						'appSecret' => 'my_app_secret'
					]
				],
				'Logger' => [
					'enabled' => true
				]
			];

			add_option( Settings::NAME, json_encode( $options ) );
		}
	}

	public static function deactivate()
	{

	}

	public static function uninstall()
	{
		delete_option( Settings::NAME );
	}
}