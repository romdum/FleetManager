<?php

namespace FleetManager;

class Install
{
	public static function activate()
	{
		FleetManager::$settings->createDefaultOption();
	}

	public static function deactivate()
	{

	}

	public static function uninstall()
	{
		delete_option( Settings::NAME );

		FleetManager::$logger->removeAll();
	}
}