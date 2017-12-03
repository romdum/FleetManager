<?php

namespace FleetManager;
use FleetManager\Vehicle\Vehicle;

/**
 * Class to manage FleetManager settings (located in options table).
 */
class Settings
{
    private $settings;
    private $tmpSettings;
    
    const NAME = 'FM_settings';

    public function __construct()
    {
    	$this->settings = json_decode( get_option( self::NAME ), '' );

        add_action( 'admin_menu', array( $this, 'add_plugin_page' ), 10, 0 );
        add_action( 'admin_post_FM_save_settings', array( $this, 'saveSettings' ), 10, 0 );
    }

    public function getSetting( ...$names )
    {
	    $settings = Util::object_to_array( $this->settings );

	    $result = $settings;
	    for( $i = 0; $i < count( $names ); $i++ )
		    if( isset( $result[$names[$i]] ) )
		        $result = $result[$names[$i]];
		    else
		    	return null;

	    return $result;
    }

    public function setSetting( $value, ...$names )
    {
        $settings = Util::object_to_array( json_decode( get_option( self::NAME ), '' ) );
        
        $cmdToExe = '$settings';
        for( $i = 0; $i < count( $names ); $i++ )
            $cmdToExe .= '[$names[' . $i . ']]';
        $cmdToExe .= ' = $value;';

        eval( $cmdToExe );

	    update_option( self::NAME, json_encode( $settings ) );
        FleetManager::$notice->setNotice('Options sauvegardées.', 'success');
    }
    
    private function testSettingFormat( $setting = null )
    {
        $setting = isset( $setting ) ? $setting : $this->settings;

	    if( ! is_array( $setting ) && is_object( $setting ) )
		    return get_object_vars( $setting );

	    return [];
    }
    
    public function add_plugin_page()
    {
        add_options_page(
            'Fleet Manager',
            'Fleet Manager',
            'administrator',
            'fleetmanager_settings_page',
            array( $this, 'create_admin_page' )
        );
    }
    
    public function create_admin_page()
    {
        $settings['socialNetwork'][] = [
            'id'    => 'FM_facebook', 
            'type'  => 'checkbox',
            'value' => $this->getSetting( 'SocialNetwork', 'facebook', 'enabled' ) ? 'checked' : '',
            'class' => 'setting',
            'label' => 'Auto-publier sur Facebook'
        ];
        $settings['socialNetwork'][] = [
            'id'    => 'FM_fb_appid',
            'type'  => 'input',
            'value' => htmlspecialchars( $this->getSetting( 'SocialNetwork', 'facebook', 'appId' ) ),
            'class' => 'setting',
            'label' => 'Facebook App ID'
        ];
        $settings['socialNetwork'][] = [
            'id'    => 'FM_fb_appsecret',
            'type'  => 'input',
            'value' => htmlspecialchars( $this->getSetting( 'SocialNetwork', 'facebook', 'appSecret' ) ),
            'class' => 'setting',
            'label' => 'Facebook App Secret'
        ];
        $settings['main'][] = [
            'id'    => 'FM_logger',
            'type'  => 'checkbox',
            'value' => $this->getSetting( 'Logger', 'enabled' ) ? 'checked' : '',
            'class' => 'setting',
            'label' => 'Activer les logs'
        ];

        $vehicleInfo = (new Vehicle(0))->getArrayInfos();

        foreach( $vehicleInfo as $info )
        	$settings['vehicle'][] = [
		        'id'    => $info['id'],
		        'type'  => 'checkbox',
		        'value' => $this->getSetting( 'VehiclePostType', 'display', $info['id'] ) !== false ? 'checked' : '',
		        'class' => 'setting',
		        'label' => $info['label']
	        ];

        include FleetManager::$PLUGIN_PATH . 'ressources/views/settingsPage.php';
    }
    
    protected function displayOption( $optionArgs )
    {
        foreach( $optionArgs as $args )
        {
            if( $args['type'] === 'checkbox' )
                include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/checkboxTag.php';
            else if( $args['type'] === 'input' )
                include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/inputTag.php';
        }
    }
    
    public function saveSettings()
    {
        check_admin_referer( 'FM_save_settings' );

        $this->setSetting( isset( $_POST['FM_facebook'] ) ? true : false, 'SocialNetwork', 'facebook', 'enabled' );
        $this->setSetting( htmlspecialchars( $_POST['FM_fb_appid'] ), 'SocialNetwork', 'facebook', 'appId' );
        $this->setSetting( htmlspecialchars( $_POST['FM_fb_appsecret'] ), 'SocialNetwork', 'facebook', 'appSecret' );
        $this->setSetting( isset( $_POST['FM_logger'] ) ? true : false, 'Logger', 'enabled' );

	    $vehicleInfo = (new Vehicle(0))->getArrayInfos();

	    foreach( $vehicleInfo as $info )
            $this->setSetting( isset( $_POST[$info['id']] ) ? true : false, 'VehiclePostType', 'display', $info['id'] );
		    
        
        wp_redirect( admin_url( "options-general.php?page=fleetmanager_settings_page" ) );
    }
}
