<?php

namespace FleetManager;
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
        add_action( "admin_post_FM_save_settings", array( $this, 'saveSettings' ), 10, 0 );
    }

    public function getSetting( ...$names )
    {
        if( is_array( $names[0] ) )
            $names = $names[0];
        else // first call of the function
            $this->tmpSettings = $this->settings;

        $setting = $this->testSettingFormat( $this->tmpSettings );

        if( isset( $names[1] ) && isset( $setting[$names[0]] ) )
        {
            $this->tmpSettings = $setting[$names[0]];
            unset( $names[0] );
            $names = array_values( $names );
            return $this->getSetting( $names );
        }
        else if( ! isset( $names[1] ) && isset( $setting[$names[0]] ) )
        {
            return $setting[$names[0]];
        }
        return null;
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
        $settings['main'][] = [
            'id'    => 'FM_logger',
            'type'  => 'checkbox',
            'value' => $this->getSetting( 'Logger', 'enabled' ) ? 'checked' : '',
            'class' => 'setting',
            'label' => 'Activer les logs'
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
        $this->setSetting( isset( $_POST['FM_logger'] ) ? true : false, 'Logger', 'enabled' );

        wp_redirect( admin_url( "options-general.php?page=fleetmanager_settings_page" ) );
    }
}