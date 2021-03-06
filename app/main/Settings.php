<?php

namespace FleetManager;

require_once 'SettingsUI.php';

/**
 * Class to manage FleetManager settings (located in options table).
 */
class Settings
{
	/**
	 * Settings like they're saved in database (JSON string).
	 * @var string
	 */
	private $settings;

	/**
	 * Option name (options.option_name)
	 */
	const NAME = 'FM_settings';

	/**
	 * Settings constructor.
	 */
    public function __construct()
    {
    	$this->settings = json_decode( get_option( self::NAME ), '' );

    	add_action( 'plugins_loaded', array( $this, 'testSettingFormat' ), 10, 0 );

		if( is_admin() )
		{
			new SettingsUI( $this );
		}
    }

	/**
	 * Return the setting, null if setting not exists, all settings if no parameter given.
	 *
	 * @example getSetting( 'setting1', 'setting1.1', 'setting1.1.1')
	 * @param array[string] ...$names
	 *
	 * @return string|array|null
	 */
	public function getSetting( ...$names )
    {
	    $settings = Util::object_to_array( $this->settings );

	    $result = $settings;
	    for( $i = 0; $i < count( $names ); $i++ )
		    if( is_string( $names[$i] ) && isset( $result[$names[$i]] ) )
		        $result = $result[$names[$i]];
		    else
		    	return null;

	    return $result;
    }

	/**
	 * Change a setting value and save it in database.
	 *
	 * @param $value
	 * @param array[string] ...$names
	 */
    public function setSetting( $value, ...$names )
    {
    	if( ! isset( $names ) || ! is_array( $names ) || empty( $names ) )
    		return;

        $settings = Util::object_to_array( json_decode( get_option( self::NAME ), '' ) );
        
        $cmdToExe = '$settings';
        for( $i = 0; $i < count( $names ); $i++ )
	        if( is_string( $names[$i] ) )
                $cmdToExe .= '[$names[' . $i . ']]';
            else
            	return;
        $cmdToExe .= ' = $value;';

        eval( $cmdToExe );

	    if( update_option( self::NAME, json_encode( $settings ) ) )
			$this->settings = $settings;

	    if( Util::classLoaded( 'FleetManager\FleetManager' ) && FleetManager::$notice !== null )
            FleetManager::$notice->setNotice('Options sauvegardées.', Notice::NOTICE_SUCCESS);
    }

	/**
	 * Method to test if JSON settings is valid.
	 * Generate a notice if they're not.
	 */
    public function testSettingFormat()
    {
        if( $this->settings === null && FleetManager::$notice !== null )
	        (FleetManager::$notice->setNotice( 'Format des paramètres incorrects.', Notice::NOTICE_ERROR ))->displayNotice();
    }

    public function createDefaultOption()
    {
	    if( get_option( Settings::NAME, null ) === null )
	    {
		    $defaultSettings = [
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

		    add_option( Settings::NAME, json_encode( $defaultSettings ) );
		    $this->settings = json_decode( json_encode( $defaultSettings ) );
	    }
    }
}
