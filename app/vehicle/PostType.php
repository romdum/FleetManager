<?php

namespace FleetManager\Vehicle;

use FleetManager\FleetManager;
use function \FleetManager\number_format;
use FleetManager\Util;

require_once 'UI.php';
require_once 'Taxonomy.php';
require_once 'Shortcodes.php';
require_once 'Util.php';

/**
 * Class use to create and manage the vehicle post type
 */
class PostType
{
    const POST_TYPE_NAME = 'vehicle';

    public function __construct()
    {
        add_action( 'init', array( $this, 'addVehiclePostType' ), 10, 0 );

        add_filter( 'manage_edit-vehicle_columns', array( $this,'customVehicleColumns' ), 10, 1 ) ;
        add_action( 'manage_vehicle_posts_custom_column', array( $this, 'loadCustomVehicleColumns' ), 10, 2 );

        add_action( 'save_post', array( $this, 'saveCustomFields' ), 10, 2 );

        if( FleetManager::$settings->getSetting( 'VehiclePostType', 'deletePics' ) )
            add_action( 'before_delete_post', array( $this, 'removePics' ), 10, 1 );

        new Taxonomy();
        new UI( $this );
        new Shortcodes( $this );
    }

    /**
     * Method call when a post is saved. It will save all custom metabox field
     * in the database (table post_meta).
     * @param  int      $postId
     * @param  \WP_Post  $post
     */
    function saveCustomFields( $postId, $post )
    {
        if( $post->post_type !== self::POST_TYPE_NAME )
            return;

	    $vehicle = new Vehicle( $postId );

	    // Sauvegarde des photos du véhicule
	    foreach( $vehicle->getPics() as $index => $value )
	    	if( isset( $_POST[$index] ) )
	            update_post_meta( $postId, $index, htmlspecialchars( $_POST[$index] ) );

	    // Sauvegarde des infos du véhicules
	    $infos = $vehicle->getArrayInfos();
        foreach( $infos as $info )
            if( isset( $_POST[$info['id']] ) )
                update_post_meta( $postId, $info['id'], htmlspecialchars( $_POST[$info['id']] ) );

	    if( ! isset ( $_POST['FM_isSold'] ) )
		    update_post_meta( $postId, 'FM_isSold', 'off' );

	    $post->post_content = isset( $_POST['content'] ) ? htmlspecialchars( $_POST['content'] ) : '';

	    if( isset( $_POST['FM_type'] ) )
		    wp_set_post_terms( $postId, term_exists( htmlspecialchars( $_POST['FM_type'] ), 'vehicle_type' ), 'vehicle_type' );

	    if( isset( $_POST['FM_brand'] ) )
		    wp_set_post_terms( $postId, term_exists( htmlspecialchars( $_POST['FM_brand'] ), 'vehicle_brand' ), 'vehicle_brand' );
    }

    public function removePics( $postId )
    {
    	$postType = ( get_post( $postId ) )->post_type;

    	if( $postType === self::POST_TYPE_NAME )
	    {
	        global $wpdb;
	        $pics = $wpdb->get_results("
				SELECT meta_value 
				FROM wr_postmeta 
				WHERE meta_key LIKE 'FM_image%'
				AND meta_value NOT LIKE '%noVehicleImage.png'
				AND post_id = $postId;
			");

	        foreach( $pics as $pic )
		    {
		        $picId = $wpdb->get_var( $wpdb->prepare("
		            SELECT ID FROM wr_posts
		            WHERE post_type = 'attachment'
		            AND guid = '%s';
		        ", $pic->meta_value ) );
		        wp_delete_attachment( $picId );
		    }
	    }
    }

    /**
     * Method use to add and remove columns in vehicle list.
     * @param  array $columns
     * @return array
     */
    function customVehicleColumns( $columns )
    {
        unset( $columns['date'] );
        $newColumns = [
            'FM_brand' => __('Marque'),
            'FM_km'    => __('Kilométrage'),
            'FM_year'  => __('Année'),
            'FM_price' => __('Prix'),
        ];

        return array_merge( $columns, $newColumns );
    }

    /**
     * This method will load the custom columns data.
     * @param  array $column
     * @param  int   $postId
     */
    function loadCustomVehicleColumns( $column, $postId )
    {
        switch ( $column )
        {
            case 'FM_year' :
                echo get_post_meta( $postId , 'FM_year' , true );
                break;
            case 'FM_price' :
                echo number_format( get_post_meta( $postId , 'FM_price' , true ), ' €', 2 );
                break;
            case 'FM_km' :
                echo number_format( get_post_meta( $postId , 'FM_km' , true ), ' km' );
                break;
            case 'FM_brand' :
            	$brand = get_the_terms( get_post( $postId ), 'vehicle_brand' )[0];
                echo isset( $brand->name ) ? $brand->name : '';
                break;
        }
    }

    /**
     * Method to create the post type.
     */
    public function addVehiclePostType()
    {
	    register_post_type( self::POST_TYPE_NAME,
			[
				'labels' => [
					'name' => __('Véhicules'),
					'singular_name' => __('Véhicule'),
                    'all_items' => __('Tous les véhicules'),
                    'add_new_item' => __('Ajouter un véhicule'),
                    'edit_item' => __('Éditer le véhicule'),
                    'new_item' => __('Nouveau véhicule'),
                    'view_item' => __('Voir le véhicule'),
                    'search_items' => __('Rechercher parmi les véhicules'),
                    'not_found' => __('Pas de véhicule trouvé'),
                    'not_found_in_trash'=> __('Pas de véhicule dans la corbeille')
				],
                'capability_type' => 'post',
                'supports' => [
                    'title'
                ],
                'public' => true,
                'menu_icon' => UI::getCarDashicon(),
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'menu_position' => 4,
                'rewrite' => ['slug' => 'vehicules'],
                'has_archive' => true
		  	] );
    }
}
