<?php

namespace FleetManager;
use FleetManager\Vehicle\Vehicle;

class SettingsUI
{
	private $settings;

	/**
	 * SettingsUI constructor.
	 *
	 * @param Settings $settings
	 */
	public function __construct( $settings )
	{
		$this->settings = $settings;
		add_action( 'admin_menu', array( $this, 'createSettingPage' ), 10, 0 );
		add_action( 'admin_post_FM_save_settings', array( $this, 'saveSettings' ), 10, 0 );
	}

	/**
	 * Create the setting page.
	 */
	public function createSettingPage()
	{
		add_options_page(
			FleetManager::PLUGIN_NAME,
			FleetManager::PLUGIN_NAME,
			'administrator',
			'fleetmanager_settings_page',
			array( $this, 'displaySettingPage' )
		);
	}

	/**
	 * Display the setting page (callback of createSettingPage function).
	 */
	public function displaySettingPage()
	{
		$settings['socialNetwork'][] = [
			'id'    => 'FM_facebook',
			'type'  => 'checkbox',
			'value' => $this->settings->getSetting( 'SocialNetwork', 'facebook', 'enabled' ) ? 'checked' : '',
			'class' => 'setting',
			'label' => 'Auto-publier sur Facebook'
		];
		$settings['socialNetwork'][] = [
			'id'    => 'FM_fb_appid',
			'type'  => 'input',
			'value' => htmlspecialchars( $this->settings->getSetting( 'SocialNetwork', 'facebook', 'appId' ) ),
			'class' => 'setting',
			'label' => 'Facebook App ID'
		];
		$settings['socialNetwork'][] = [
			'id'    => 'FM_fb_appsecret',
			'type'  => 'input',
			'value' => htmlspecialchars( $this->settings->getSetting( 'SocialNetwork', 'facebook', 'appSecret' ) ),
			'class' => 'setting',
			'label' => 'Facebook App Secret'
		];
		$settings['main'][] = [
			'id'    => 'FM_logger',
			'type'  => 'checkbox',
			'value' => $this->settings->getSetting( 'Logger', 'enabled' ) ? 'checked' : '',
			'class' => 'setting',
			'label' => 'Activer les logs'
		];

		$vehicleInfo = (new Vehicle(0))->getArrayInfos();

		foreach( $vehicleInfo as $info )
			$settings['vehicle'][] = [
				'id'    => $info['id'],
				'type'  => 'checkbox',
				'value' => $this->settings->getSetting( 'VehiclePostType', 'display', $info['id'] ) !== false ? 'checked' : '',
				'class' => 'setting',
				'label' => $info['label']
			];

		include FleetManager::$PLUGIN_PATH . 'ressources/views/settingsPage.php';
	}

	/**
	 * Display option template.
	 *
	 * @param array $optionArgs
	 */
	protected function displayOption( $optionArgs )
	{
		foreach( $optionArgs as $args )
			if( $args['type'] === 'checkbox' )
				include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/checkboxTag.php';
			else if( $args['type'] === 'input' )
				include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/inputTag.php';
	}

	/**
	 * Post request made when settings are save (on setting page).
	 */
	public function saveSettings()
	{
		check_admin_referer( 'FM_save_settings' );

		$this->settings->setSetting( isset( $_POST['FM_facebook'] ) ? true : false, 'SocialNetwork', 'facebook', 'enabled' );
		$this->settings->setSetting( htmlspecialchars( $_POST['FM_fb_appid'] ), 'SocialNetwork', 'facebook', 'appId' );
		$this->settings->setSetting( htmlspecialchars( $_POST['FM_fb_appsecret'] ), 'SocialNetwork', 'facebook', 'appSecret' );
		$this->settings->setSetting( isset( $_POST['FM_logger'] ) ? true : false, 'Logger', 'enabled' );

		$vehicleInfo = (new Vehicle(0))->getArrayInfos();

		foreach( $vehicleInfo as $info )
			$this->settings->setSetting( isset( $_POST[$info['id']] ) ? true : false, 'VehiclePostType', 'display', $info['id'] );

		wp_redirect( admin_url( "options-general.php?page=fleetmanager_settings_page" ) );
	}
}