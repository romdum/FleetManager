<?php

namespace FleetManager\Transfer;

use \FleetManager\Vehicle\PostType;

require_once 'DataToTransfer.php';

class Vehicle implements DataToTransfer
{
	private $type;

	public function __construct( $type, $format )
	{
		$this->type = $type;

		if( $format === DataParser::CSV && $type === Transfer::EXPORT )
			$this->exportData();
		if( $format === DataParser::CSV && $type === Transfer::IMPORT )
			$this->importData();
	}

	public function getData()
	{
		if( $this->type === Transfer::EXPORT )
		{
			$data = [];

			foreach( get_posts( ['post_type' => PostType::POST_TYPE_NAME] ) as $post )
				$data[$post->ID] = new \FleetManager\Vehicle\Vehicle( $post->ID );

			return $data;
		}
		else if( $this->type === Transfer::IMPORT )
		{
			// TODO: récupérer les données
		}
	}
}