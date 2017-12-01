<?php

namespace FleetManager;

use FleetManager\Vehicle\Vehicle;

class LastVehicleWidget extends Widget
{
	const SLUG = 'FM_LastVehicleWidget';
	const TITLE = 'Les derniers véhicules publiés';
	const NB_VEHICLE = 5;

	public function __construct()
	{
		parent::__construct( self::SLUG, self::TITLE );
	}

	public function display()
	{
		foreach( $this->getLastVehicle() as $vehicle )
		{
			$args['postId'] = $vehicle->getPostId();
			$args['creationDate'] = get_the_date( 'j F Y', $args['postId'] );
			$args['link'] = get_the_permalink( $args['postId'] );
			$args['title'] = get_post_field( 'post_title', $args['postId'] );
			$args['author'] = get_the_author_meta( 'login', get_post_field( 'post_author', $args['postId'] ) );

			include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/lastVehiclePublish.php';
		}
	}

	/**
	 * @return array[\FleetManager\Vehicle\Vehicle]
	 */
	private function getLastVehicle()
	{
		$q = new \WP_Query([
			'post_type' => 'vehicle',
			'post_per_page' => self::NB_VEHICLE
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