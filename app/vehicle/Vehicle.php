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
	protected $postId;

	public $isSold = ['id' => 'FM_isSold', 'label' => 'Vendu :', 'type' => 'checkbox'];
	public $type = ['id' => 'FM_type', 'label' => 'Type :'];
	public $brand = ['id' => 'FM_brand', 'label' => 'Marque :'];
	public $model = ['id' => 'FM_model', 'label' => 'Modèle' . ' :'];
	public $year = ['id' => 'FM_year', 'label' => 'Année :', 'type' => 'number'];
	public $price = ['id' => 'FM_price', 'label' => 'Prix :', 'type' => 'number'];
	public $km = ['id' => 'FM_km', 'label' => 'Kilométrage :', 'type' => 'number'];
	public $doorNbr = ['id' => 'FM_doorNbr', 'label' => 'Nombre de portes :', 'type' => 'number'];
	public $chf = ['id' => 'FM_chf', 'label' => 'Puissance fiscale :', 'type' => 'number'];
	public $ch = ['id' => 'FM_ch', 'label' => 'Puissance din :', 'type' => 'number'];
	public $gearbox = ['id' => 'FM_gearbox', 'label' => 'Boîte de vitesse :', 'type' => ['man' => 'Manuelle', 'auto' => 'Automatique']];
	public $fuel = ['id' => 'FM_fuel', 'label' => 'Energie :'];
	public $circulation = ['id' => 'FM_circulation', 'label' => 'Mise en circulation :', 'type' => 'date'];
	public $color = ['id' => 'FM_color', 'label' => 'Couleur :', 'type' => 'text'];
	public $warranty = ['id' => 'FM_warranty', 'label' => 'Garantie :', 'type' => 'text'];
	public $width = ['id' => 'FM_width', 'label' => 'Longueur :', 'type' => 'text'];
	public $conso = ['id' => 'FM_conso', 'label' => 'Consommation :', 'type' => 'text'];
	public $trunk = ['id' => 'FM_trunk', 'label' => 'Volume du coffre :', 'type' => 'text'];

	protected $pics = [];

	/**
	 * Vehicle constructor.
	 *
	 * @param $postId
	 */
	public function __construct( $postId )
	{
		$this->postId = $postId;

		if( isset( $postId ) )
		{
			$infos = $this->getArrayInfos();
			$postMeta = get_post_meta( $postId );
			$parent = get_the_terms( get_post( $postId ), 'vehicle_brand' );
			$parent = $parent !== false ? $parent[0]->slug : '';

			foreach( $infos as $info )
				$this->{substr( $info['id'], 3 )}['value'] = isset( $postMeta[$info['id']] ) ? $postMeta[$info['id']][0] : '';

			$this->fuel['type'] = VehicleUtil::getFuelType();

			$this->model['value'] = isset( $postMeta['FM_model'] ) ? $postMeta['FM_model'][0] : '';
			$this->model['type'] = Util::getTermsName('vehicle_brand', $parent ? $parent : 'no_brand_given' );

			$this->brand['type'] = Util::getTermsName('vehicle_brand', 'parent');

			$type = get_the_terms( get_post( $postId ), 'vehicle_type' );
			$this->type['value'] = $type !== false ? $type[0]->slug : '';
			$this->type['type'] = Util::getTermsName('vehicle_type');

			$picsNbr = FleetManager::$settings->getSetting( 'VehiclePostType','default','photoNbr' );
			$picsNbr = $picsNbr === null ? 5 : $picsNbr;

			for( $i = 1; $i <= $picsNbr; $i++ )
			{
				$picsUrl = get_post_meta( $postId, 'FM_image' . $i, true );
				$this->pics['FM_image' . $i] = ! empty( $picsUrl ) ? $picsUrl : FleetManager::$PLUGIN_URL . 'ressources/img/noVehicleImage.png';
			}
		}
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
		return $this->isSold['value'] === 'on';
	}

	public function getPostId()
	{
		return $this->postId;
	}
}