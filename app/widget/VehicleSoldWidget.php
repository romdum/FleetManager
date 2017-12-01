<?php

namespace FleetManager;

use FleetManager\Vehicle\Vehicle;

class VehicleSoldWidget extends Widget
{
	const SLUG  = 'FM_VehicleSentWidget';
	const TITLE = 'Véhicules vendus ce mois-ci';

	public function __construct()
	{
		parent::__construct( self::SLUG, self::TITLE );
	}

	public function display()
	{
		$vehicles = $this->getSentVehicle();

		echo count( $vehicles ) . ' véhicules vendus ce mois-ci.' . '<br>';

		foreach( $vehicles as $vehicle )
		{
			$args['postId'] = $vehicle->getPostId();
			$args['creationDate'] = get_the_modified_date( 'j F Y', $args['postId'] );
			$args['link'] = get_the_permalink( $args['postId'] );
			$args['title'] = get_post_field( 'post_title', $args['postId'] );

			include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/lastVehiclePublish.php';
		}
	}

	public function getSentVehicle()
	{
		$q = new \WP_Query([
			'post_type'  => 'vehicle',
			'column'     => 'post_modified_gmt',
			'after'      => '1 month ago',
			'meta_key'   => 'FM_isSold',
			'meta_value' => 'on',
		]);

		$vehicles = [];

		if ( $q->have_posts() )
		{
			while ( $q->have_posts() )
			{
				$q->the_post();
				$vehicles[] = new Vehicle( get_the_ID() );
			}
			wp_reset_postdata();
		}

		return $vehicles;
	}
}