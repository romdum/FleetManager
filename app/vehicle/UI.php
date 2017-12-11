<?php

namespace FleetManager\Vehicle;

use \FleetManager\Util;
use \FleetManager\FleetManager;

/**
 * Class to manage all user interface
 */
class UI
{
	/**
	 * UI constructor.
	 */
    public function __construct()
    {
        add_action( 'wp_ajax_displayModels', array( $this, 'displayModels' ), 10, 0 );
        
        add_action( 'admin_enqueue_scripts', array( $this, 'loadScripts' ), 10, 1 );
        add_action( 'admin_enqueue_scripts', array( $this, 'loadStyles' ), 10, 0 );

        add_action( 'add_meta_boxes', array( $this, 'addCustomFields' ), 10, 0 );
    }

	/**
	 * Method load stylesheet.
	 */
    public function loadStyles()
    {
        wp_register_style( 'fleetManagerMainCss', FleetManager::$PLUGIN_URL . 'ressources/css/vehiclePostType.css' );
        wp_enqueue_style( 'fleetManagerMainCss' );
    }

	/**
	 * Method load javascript files.
	 * @param $currentPage
	 */
    public function loadScripts( $currentPage )
    {
	    if ( 'post.php' !== $currentPage && 'post-new.php' !== $currentPage )
		    return;

		if( ! (new Vehicle( $GLOBALS['post']->ID ))->isSold() )
		{
			wp_enqueue_media();

			wp_register_script( 'fleetManagetUploadFileScript', FleetManager::$PLUGIN_URL . 'ressources/js/imageUploader.js', array( 'jquery' ) );
			wp_localize_script( 'fleetManagetUploadFileScript', 'util', ['pluginUrl' => FleetManager::$PLUGIN_URL] );
			wp_enqueue_script( 'fleetManagetUploadFileScript' );
		}

	    wp_register_script( 'fleetManagerDisplayModel', FleetManager::$PLUGIN_URL . 'ressources/js/displayModel.js', array( 'jquery' ) );
        wp_localize_script( 'fleetManagerDisplayModel', 'displayModelUtil', [
            'pluginUrl' => FleetManager::$PLUGIN_URL,
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'displayModelNonce' ),
        ]);
        wp_enqueue_script( 'fleetManagerDisplayModel' );
    }

    /**
     * Method to create custom metabox.
     */
    function addCustomFields()
    {
        add_meta_box(
            'vehicleInfo',
            __('Informations du véhicules'),
            array( $this, 'displayInfoMetabox' ),
            PostType::POST_TYPE_NAME,
            'normal',
            'high'
        );
        $displayPhoto = FleetManager::$settings->getSetting( 'VehiclePostType','display','photo' );
        if( $displayPhoto === null || $displayPhoto )
        {
            add_meta_box(
                'vehicleImage',
                __('Photos'),
                array( $this, 'displayImageMetabox' ),
                PostType::POST_TYPE_NAME,
                'normal',
                'default'
            );
        }
    }

    /**
     * Method to display information vehicle metabox.
     * @param  \WP_Post $post
     */
    public function displayInfoMetabox( $post )
    {
        $postId = $post->ID;
		$vehicle = new Vehicle( $postId );

		foreach( $vehicle->getArrayInfos() as $args )
        {
            $args['class'] = 'vehicleInfo';
            $displayInfo = FleetManager::$settings->getSetting( 'VehiclePostType','display',$args['id'] );
            if( $displayInfo === null || $displayInfo )
            {
                if( is_array( $args['type'] ) )
                {
	                include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/selectTag.php';
                }
                else if( $args['type'] === 'checkbox' )
	            {
	            	$args['value'] = $args['value'] === 'on' ? 'checked' : '';
	                include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/checkboxTag.php';
                }
                else
	            {
	                include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/inputTag.php';
                }
            }
        }
        include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/metabox/infoArea.php';
    }

    /**
     * Method to display pics vehicle metabox.
     * @param  \WP_Post $post
     */
    public function displayImageMetabox( $post )
    {
        $postId = $post->ID;
        $vehicle = new Vehicle( $postId );

        foreach( $vehicle->getPics() as $index => $url )
            include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/metabox/image.php';
    }
    
    /**
     * Ajax 
     */
    public function displayModels()
    {
        check_ajax_referer( 'displayModelNonce', 'nonce' );
        
        $parentSlug = isset( $_POST['parentSlug'] ) ? $_POST['parentSlug'] : 'no_brand_given';
        echo json_encode( Util::getTermsName( 'vehicle_brand', $parentSlug ) );
        wp_die();
    }
    
    /**
     * Method to get vehicle dashicon in SVG format.
     * Source: https://github.com/encharm/Font-Awesome-SVG-PNG
     * @return string
     */
    public static function getCarDashicon()
    {
        return Util::loadSVG('<svg width="20" height="20" viewBox="0 0 2048 1792" xmlns="http://www.w3.org/2000/svg"><path fill="black" d="M480 1088q0-66-47-113t-113-47-113 47-47 113 47 113 113 47 113-47 47-113zm36-320h1016l-89-357q-2-8-14-17.5t-21-9.5h-768q-9 0-21 9.5t-14 17.5zm1372 320q0-66-47-113t-113-47-113 47-47 113 47 113 113 47 113-47 47-113zm160-96v384q0 14-9 23t-23 9h-96v128q0 80-56 136t-136 56-136-56-56-136v-128h-1024v128q0 80-56 136t-136 56-136-56-56-136v-128h-96q-14 0-23-9t-9-23v-384q0-93 65.5-158.5t158.5-65.5h28l105-419q23-94 104-157.5t179-63.5h768q98 0 179 63.5t104 157.5l105 419h28q93 0 158.5 65.5t65.5 158.5z"/></svg>');
    }
}
