<?php

namespace FleetManager\Vehicle;

use FleetManager\Util;
use FleetManager\Vehicle\Util as VehicleUtil;
use FleetManager\FleetManager;

/**
 * Class Vehicle
 */
class Vehicle
{
	const IS_SOLD     = 'isSold';
	const TYPE        = 'type';
	const BRAND       = 'brand';
	const MODEL       = 'model';
	const YEAR        = 'year';
	const PRICE       = 'price';
	const KM          = 'km';
	const DOOR_NBR    = 'doorNbr';
	const CHF         = 'chf';
	const CH          = 'ch';
	const GEARBOX     = 'gearbox';
	const FUEL        = 'fuel';
	const CIRCULATION = 'circulation';
	const COLOR       = 'color';
	const WARRANTY    = 'warranty';
	const WIDTH       = 'width';
	const CONSO       = 'conso';
	const CO2         = 'co2';
	const TRUNK       = 'trunk';

	const PREFIX      = 'FM_';
	const PICS        = 'image';

	protected $postId;

	protected $pics = [];

	/**
	 * Vehicle constructor.
	 *
	 * @param $postId
	 */
	public function __construct( $postId )
	{
		$this->postId = $postId;

		$this->initIds();

		$this->initLabels();
		$this->initTypes();

		if( isset( $postId ) )
		{
			$this->initValues();
			$this->initPics();
		}
	}

	public function initIds()
	{
		foreach( $this->getProperties() as $id )
			$this->{$id}['id'] = self::PREFIX . $id;
	}

	private function initTypes()
	{
		foreach( $this->getProperties() as $property ) // all field are text type by default
			$this->{$property}['type'] = 'text';

		$parent = get_the_terms( get_post( $this->postId ), 'vehicle_brand' );
		$parent = $parent !== false && ! is_wp_error( $parent ) ? $parent[0]->slug : '';

		$this->{self::IS_SOLD}['type']     = 'checkbox';
		$this->{self::TYPE}['type']        = Util::getTermsName('vehicle_type');
		$this->{self::BRAND}['type']       = Util::getTermsName('vehicle_brand', 'parent');
		$this->{self::MODEL}['type']       = Util::getTermsName('vehicle_brand', $parent ? $parent : 'no_brand_given' );
		$this->{self::YEAR}['type']        = 'number';
		$this->{self::PRICE}['type']       = 'number';
		$this->{self::KM}['type']          = 'number';
		$this->{self::DOOR_NBR}['type']    = 'number';
		$this->{self::CHF}['type']         = 'number';
		$this->{self::CH}['type']          = 'number';
		$this->{self::GEARBOX}['type']     = ['man' => __( 'Manuelle' ), 'auto' => __( 'Automatique' )];
		$this->{self::FUEL}['type']        = VehicleUtil::getFuelType();
		$this->{self::CIRCULATION}['type'] = 'date';
	}

	private function initLabels()
	{
		$this->{self::IS_SOLD}['label']     = __( 'Vendu' ) . ' :';
		$this->{self::TYPE}['label']        = __( 'Type' ) . ' :';
		$this->{self::BRAND}['label']       = __( 'Marque' ) . ' :';
		$this->{self::MODEL}['label']       = __( 'Modèle' ) . ' :';
		$this->{self::YEAR}['label']        = __( 'Année' ) . ' :';
		$this->{self::PRICE}['label']       = __( 'Prix' ) . ' :';
		$this->{self::KM}['label']          = __( 'Kilométrage' ) . ' :';
		$this->{self::DOOR_NBR}['label']    = __( 'Nombre de portes' ) . ' :';
		$this->{self::CHF}['label']         = __( 'Puissance fiscale' ) . ' :';
		$this->{self::CH}['label']          = __( 'Puissance din' ) . ' :';
		$this->{self::GEARBOX}['label']     = __( 'Boîte de vitesse' ) . ' :';
		$this->{self::FUEL}['label']        = __( 'Energie' ) . ' :';
		$this->{self::CIRCULATION}['label'] = __( 'Mise en circulation' ) . ' :';
		$this->{self::COLOR}['label']       = __( 'Couleur' ) . ' :';
		$this->{self::WARRANTY}['label']    = __( 'Garantie' ) . ' :';
		$this->{self::WIDTH}['label']       = __( 'Longueur' ) . ' :';
		$this->{self::CONSO}['label']       = __( 'Consommation' ) . ' :';
		$this->{self::CO2}['label']         = __( 'Emission de CO2' ) . ' :';
		$this->{self::TRUNK}['label']       = __( 'Volume du coffre' ) . ' :';
	}

	private function initPics()
	{
		$picsNbr = 5;
		if( Util::classLoaded( 'FleetManager\FleetManager' ) )
			$picsNbr = FleetManager::$settings->getSetting( 'VehiclePostType','default','photoNbr' );

		for( $i = 1; $i <= $picsNbr; $i++ )
		{
			$picsUrl = get_post_meta( $this->postId, self::PREFIX . self::PICS . $i, true );
			if( empty( $picsUrl ) )
				if( Util::classLoaded( 'FleetManager\FleetManager' ) )
					$this->pics[self::PREFIX . self::PICS . $i] = FleetManager::$PLUGIN_URL . 'ressources/img/noVehicleImage.png';
				else
					$this->pics[self::PREFIX . self::PICS . $i] = '';
			else
				$this->pics[self::PREFIX . self::PICS . $i] = $picsUrl;
		}
	}

	public function initValues()
	{
		$postMeta = get_post_meta( $this->postId );

		foreach( $this->getProperties() as $id )
			$this->{$id}['value'] = isset( $postMeta[self::PREFIX . $id] ) ? $postMeta[self::PREFIX . $id][0] : '';

		$this->{self::MODEL}['value'] = isset( $postMeta[self::PREFIX . self::MODEL] ) ? $postMeta[self::PREFIX . self::MODEL][0] : '';

		$type = get_the_terms( get_post( $this->postId ), 'vehicle_type' );
		$this->{self::TYPE}['value'] = $type !== false && ! is_wp_error( $type ) ? $type[0]->slug : '';
	}

	/**
	 * Return all public class attributes
	 * @return array
	 */
	public function getArrayInfos()
	{
		return Util::object_to_array( $this );
	}

	/**
	 * Return array of all pics.
	 * Return example: ['FM_image1' => 'url_image_1', 'FM_image2' => 'url_image_2']
	 * @return array
	 */
	public function getPics()
	{
		return $this->pics;
	}

	/**
	 * Return true if the vehicle is sold.
	 * @return bool
	 */
	public function isSold()
	{
		return $this->{self::IS_SOLD}['value'] === 'on';
	}

	public function getPostId()
	{
		return $this->postId;
	}

	public static function getProperties( $withPrefix = false )
	{
		$consts = ( new \ReflectionClass( 'FleetManager\Vehicle\Vehicle' ) )->getConstants();

		if( $withPrefix )
			return array_diff( array_map( function( $v ){return self::PREFIX . $v;}, $consts ), [self::PREFIX, self::PICS] );
		else
			return array_diff( $consts, [self::PREFIX, self::PICS] );
	}
}