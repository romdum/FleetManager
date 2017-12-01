<?php

namespace FleetManager\Vehicle;

use \FleetManager\FleetManager;

/**
 * Class to create taxonomies on vehicle post type and their default value.
 */
class Taxonomy
{
    public function __construct()
    {
        add_action( 'init', array( $this, 'addTaxonomyToVehicle' ), 10, 0 );
    }

    /**
     * Register all taxonomies on vehicle post type.
     */
    public function addTaxonomyToVehicle()
    {
        register_taxonomy( 'vehicle_type', PostType::POST_TYPE_NAME, [
            'hierarchical' => true,
            'meta_box_cb' => false,
            'labels' => [
                'name' => __('Types'),
                'singular_name' => __('Type'),
                'all_items' => __('Tous les ') . __('types'),
                'edit_item' => __('Éditer le ') . __('type'),
                'view_item' => __('Voir le ') . __('type'),
                'update_item' => __('Mettre à jour le ') . __('type'),
                'add_new_item' => __('Ajouter un ') . __('type'),
                'new_item_name' => __('Nouveau '). __('type'),
                'search_items' => __('Rechercher parmi les '). __('types'),
                'popular_items' => __('Types') . __(' les plus utilisés')
            ],
            'query_var' => true,
            'rewrite' => true]
        );

        register_taxonomy_for_object_type( 'vehicle_type', PostType::POST_TYPE_NAME );

        register_taxonomy( 'vehicle_brand', PostType::POST_TYPE_NAME, [
            'hierarchical' => true,
            'meta_box_cb' => false,
            'labels' => [
                'name' => __('Marques') . ' / ' . __('Modèles'),
                'singular_name' => __('Marque') . ' / ' . __('Modèle'),
                'all_items' => __('Toutes les ') . __('marques') . ' / ' . __('modèle'),
                'edit_item' => __('Éditer la ') . __('marque') . ' / ' . __('modèle'),
                'view_item' => __('Voir la ') . __('marque') . ' / ' . __('modèle'),
                'update_item' => __('Mettre à jour la ') . __('marque') . ' / ' . __('modèle'),
                'add_new_item' => __('Ajouter une ') . __('marques') . ' / ' . __('modèle'),
                'new_item_name' => __('Nouvelle ') . __('marque') . ' / ' . __('modèle'),
                'search_items' => __('Rechercher parmi les ') . __('marques') . ' / ' . __('modèles'),
                'popular_items' => __('Marques') . ' / ' . __('modèle') . __(' les plus utilisées')
            ],
            'query_var' => true,
            'rewrite' => true]
        );

        register_taxonomy_for_object_type( 'vehicle_brand', PostType::POST_TYPE_NAME );


        register_taxonomy( 'vehicle_option', PostType::POST_TYPE_NAME, [
            'hierarchical' => true,
            'labels' => [
                'name' => __('Options'),
                'singular_name' => __('Option'),
                'all_items' => __('Toutes les ') . __('options'),
                'edit_item' => __('Éditer l\'') . __('option'),
                'view_item' => __('Voir l\''). __('option'),
                'update_item' => __('Mettre à jour l\''). __('option'),
                'add_new_item' => __('Ajouter une '). __('option'),
                'new_item_name' => __('Nouvelle '). __('option'),
                'search_items' => __('Rechercher parmi les '). __('options'),
                'popular_items' => __('Options') . __(' les plus utilisées')
            ],
            'query_var' => true,
            'rewrite' => true]
        );

        register_taxonomy_for_object_type( 'vehicle_option', PostType::POST_TYPE_NAME );

        register_taxonomy( 'vehicle_fuel_type', PostType::POST_TYPE_NAME, [
            'hierarchical' => true,
            'query_var' => true,
            'show_ui' => false,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'rewrite' => true]
        );

        register_taxonomy_for_object_type( 'vehicle_fuel_type', PostType::POST_TYPE_NAME );

        $this->addDefaultVehicleTaxonomies();
    }

	/**
     * Method to create default taxonomies values on vehicle post type.
     * The default values are located in options table.
	 * @deprecated since 2017/11/06, use importer
     */
    public function addDefaultVehicleTaxonomies()
    {
        $terms = FleetManager::$settings->getSetting( 'VehiclePostType','default','vehicleTaxonomies' );
		$terms = $terms ? $terms : [];

        foreach( $terms as $term )
        {
        	if( is_object( $term ) )
	        {
		        $parent = get_term_by( 'slug', $term->parent, $term->taxonomy );
				$parent = isset( $parent->term_id ) ? $parent->term_id : 0;
	        }
			else
			{
				$parent = 0;
			}

            if( ! term_exists( $term->name, $term->taxonomy ) )
            {
	            wp_insert_term(
		            $term->name,
		            $term->taxonomy,
		            ['description' => $term->description, 'slug' => $term->slug, 'parent' => $parent]
	            );
            }
        }
    }
}
