<?php
/**
 * Plugin Name: WP Fleet Manager
 * Description: Plugin de gestion de parc automobiles
 * Version: 0.0.1
 * Author: Romain DUMINIL
 * Author URI: http://duminil.eu
 */

namespace FleetManager;

require_once 'vendor/autoload.php';
require_once 'app/socialnetwork/Facebook.php';

require_once 'app/vehicle/PostType.php';
require_once 'app/vehicle/Vehicle.php';

require_once 'app/main/Util.php';
require_once 'app/main/helpers.php';
require_once 'app/main/Settings.php';
require_once 'app/main/Notice.php';
require_once 'app/main/Logger.php';
require_once 'app/main/Install.php';

require_once 'app/transfer/Transfer.php';

require_once 'app/widget/Widget.php';
require_once 'app/widget/LastVehicleWidget.php';
require_once 'app/widget/VehicleSoldWidget.php';

class FleetManager
{
    public static $settings;
    public static $PLUGIN_URL;
    public static $PLUGIN_PATH;
    public static $notice;
	public static $logger;

	public function __construct()
    {
        self::$PLUGIN_URL = plugin_dir_url( __FILE__ );
        self::$PLUGIN_PATH = plugin_dir_path( __FILE__ );

	    self::$settings = new Settings();

	    new Vehicle\PostType();

	    if( is_admin() )
	    {
	        self::$notice = new Notice();
	        self::$logger = new Logger();

		    if( self::$settings->getSetting( 'SocialNetwork', 'facebook', 'enabled' ) )
			    new SocialNetwork\Facebook();
	    }

	    register_activation_hook( __FILE__, array( '\FleetManager\Install', 'activate' ) );
	    register_deactivation_hook( __FILE__, array( '\FleetManager\Install', 'deactivate' ) );
	    register_uninstall_hook( __FILE__, array( '\FleetManager\Install', 'uninstall' ) );

	    add_action( 'init', array( $this, 'loadLanguages' ), 10, 0 );

	    add_action( 'admin_notices', array( self::$notice, 'displayNotice' ), 10, 0 );

	    add_action( 'admin_enqueue_scripts', array( $this, 'loadStyles' ), 10, 0 );
	    add_action( 'admin_enqueue_scripts', array( $this, 'loadScripts' ), 10, 0 );
	    add_action( 'admin_enqueue_scripts', array( $this, 'loadTransferScript' ), 10, 1 );

	    add_action( 'admin_post_FM_transfer', array( 'FleetManager\Transfer\Transfer', 'transferPostRequest' ), 10, 0 );

	    add_action( 'wp_dashboard_setup', array( $this, 'loadWidgets' ), 10, 0 );
    }

    public function loadStyles()
    {
        wp_register_style( 'animationCss', self::$PLUGIN_URL . 'ressources/css/animation.css' );
        wp_enqueue_style( 'animationCss' );
        wp_register_style( 'utilCss', self::$PLUGIN_URL . 'ressources/css/util.css' );
        wp_enqueue_style( 'utilCss' );
    }

    function loadScripts()
    {
        wp_enqueue_script( 'jquery-ui', 'http://code.jquery.com/ui/1.12.1/jquery-ui.min.js', [], null, true );

		wp_register_script( 'FM_mainScript', FleetManager::$PLUGIN_URL . 'ressources/js/main.js', array( 'jquery' ) );
		wp_localize_script( 'FM_mainScript', 'util', array(
			'isAdmin'     => is_admin(),
			'currentPage' => get_current_screen(),
		) );
		wp_enqueue_script( 'FM_mainScript' );
    }

    function loadTransferScript( $currentPage )
    {
	    if ( 'settings_page_fleetmanager_settings_page' !== $currentPage )
		    return;

	    wp_register_script( 'FM_transferScript', FleetManager::$PLUGIN_URL . 'ressources/js/transfer.js', array( 'jquery' ) );
	    wp_enqueue_script( 'FM_transferScript' );
    }

    function loadLanguages()
    {
        // FIXME: ne fonctionne pas / je n'arrive pas à voir la version anglaise du plugin
        load_plugin_textdomain( 'fleetmanager', false, self::$PLUGIN_URL . 'ressources/lang' );
    }

    function loadWidgets()
    {
	    new LastVehicleWidget();
	    new VehicleSoldWidget();
    }
}

new FleetManager();
