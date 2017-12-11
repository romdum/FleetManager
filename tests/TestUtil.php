<?php

namespace FleetManager\Tests;

use FleetManager\Vehicle\PostType;
use FleetManager\Vehicle\Vehicle;

require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/app/vehicle/PostType.php';
require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/app/vehicle/Vehicle.php';

class TestUtil
{
	const TITLE       = 'TITLE';
	const CONTENT     = 'CONTENT';
	const DOOR_NBR    = 5;
	const TYPE        = 'voiture';
	const BRAND       = 'citroen';
	const MODEL       = 'berlingo';
	const CH          = 69;
	const CHF         = 5;
	const KM          = 100000;
	const PRICE       = 5870;
	const CIRCULATION = '21/12/2012';
	const YEAR        = 2000;
	const IS_SOLD     = 'on';
	const GEARBOX     = 'man';
	const CONSO       = '8L / 100';
	const FUEL        = 'sp95';
	const COLOR       = 'red';
	const WARRANTY    = 'no';
	const WIDTH       = 'very long';
	const CO2         = 'nothing';
	const TRUNK       = '800L';
	const IMAGE1      = 'url/to/image/1.jpg';
	const IMAGE2      = 'url/to/image/2.jpg';

	public static function addVehicleToDb()
	{
		$metas = [
			Vehicle::PREFIX . Vehicle::DOOR_NBR     => self::DOOR_NBR,
			Vehicle::PREFIX . Vehicle::TYPE         => self::TYPE,
			Vehicle::PREFIX . Vehicle::BRAND        => self::BRAND,
			Vehicle::PREFIX . Vehicle::MODEL        => self::MODEL,
			Vehicle::PREFIX . Vehicle::CH           => self::CH,
			Vehicle::PREFIX . Vehicle::CHF          => self::CHF,
			Vehicle::PREFIX . Vehicle::KM           => self::KM,
			Vehicle::PREFIX . Vehicle::PRICE        => self::PRICE,
			Vehicle::PREFIX . Vehicle::CIRCULATION  => self::CIRCULATION,
			Vehicle::PREFIX . Vehicle::YEAR         => self::YEAR,
			Vehicle::PREFIX . Vehicle::IS_SOLD      => self::IS_SOLD,
			Vehicle::PREFIX . Vehicle::GEARBOX      => self::GEARBOX,
			Vehicle::PREFIX . Vehicle::CONSO        => self::CONSO,
			Vehicle::PREFIX . Vehicle::FUEL         => self::FUEL,
			Vehicle::PREFIX . Vehicle::COLOR        => self::COLOR,
			Vehicle::PREFIX . Vehicle::WARRANTY     => self::WARRANTY,
			Vehicle::PREFIX . Vehicle::WIDTH        => self::WIDTH,
			Vehicle::PREFIX . Vehicle::CO2          => self::CO2,
			Vehicle::PREFIX . Vehicle::TRUNK        => self::TRUNK,
			Vehicle::PREFIX . Vehicle::PICS . '1'   => self::IMAGE1,
			Vehicle::PREFIX . Vehicle::PICS . '2'   => self::IMAGE2,
		];

		foreach( $metas as $k => $meta ) // set $_POST var because hook save_post is called.
			$_POST[$k] = $meta;

		return wp_insert_post([
			'post_title'   => self::TITLE,
			'post_content' => self::CONTENT,
			'post_type'    => PostType::POST_TYPE_NAME,
			'meta_input'   => $metas
		]);
	}
}