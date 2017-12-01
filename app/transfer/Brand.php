<?php

namespace FleetManager\Transfer;

use FleetManager\FleetManager;
use FleetManager\Util;

class Brand implements DataToTransfer
{
	const NAME = 'brand';

	private $type;

	public function __construct( $type, $format )
	{
		$this->type = $type;
		if( $format === DataParser::CSV && $type === Transfer::EXPORT )
			$this->exportData();
		else if( $format === DataParser::CSV && $type === Transfer::IMPORT )
			$this->importData();
	}

	public function getData()
	{
		if( $this->type === Transfer::EXPORT )
			return Util::getTermsName( 'vehicle_brand' );
		else if( $this->type === Transfer::IMPORT )
			if( isset( $_FILES['importFile'] ) )
				return file_get_contents( $_FILES['importFile']['tmp_name'] );

		return [];
	}
}