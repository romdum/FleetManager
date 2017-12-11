<?php

namespace FleetManager\Tests;

use FleetManager\FleetManager;
use FleetManager\Util;
use FleetManager\Vehicle\UI;
use FleetManager\Vehicle\Vehicle;
use FleetManager\Vehicle\Util as VehicleUtil;

require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/tests/TestUtil.php';

require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/FleetManager.php';
require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/app/vehicle/UI.php';
require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/app/vehicle/Vehicle.php';

class UI_Test extends \WP_UnitTestCase
{
	private $fleetManager;
	private $ui;
	private $postId;

	public function __construct()
	{
		parent::__construct();

		$this->fleetManager = new FleetManager();
		$this->ui = new UI();
	}

	/**
	 * @covers UI::displayInfoMetabox
	 * @test
	 */
	public function displayInfoMetabox()
	{
		$postId = TestUtil::addVehicleToDb();

		foreach( $this->infoDataProvider() as $args )
		{
			ob_start();
			$this->ui->displayInfoMetabox( get_post( $postId ) );
			$output = ob_get_clean();

			ob_start();
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
			$expected = ob_get_clean();

			$this->assertTrue( $this->outputContains( $output, $expected ),
				'UI::displayDoorNbr failed at ' . $args['id'] . "\n\r" . $expected . $output
			);
		}
	}

	/**
	 * @covers UI::displayImageMetabox
	 * @test
	 */
	public function displayImageMetabox()
	{
		$this->markTestIncomplete( 'UI_Test::displayImageMetabox test not implements yet.' );
	}

	private function infoDataProvider()
	{
		return [
			[
				'class' => 'vehicleInfo',
				'label' => 'Nombre de portes :',
				'type'  => 'number',
				'id'    => Vehicle::PREFIX . Vehicle::DOOR_NBR,
				'name'  => Vehicle::PREFIX . Vehicle::DOOR_NBR,
				'value' => TestUtil::DOOR_NBR
			],[
				'class' => 'vehicleInfo',
				'label' => 'Année :',
				'type'  => 'number',
				'id'    => Vehicle::PREFIX . Vehicle::YEAR,
				'name'  => Vehicle::PREFIX . Vehicle::YEAR,
				'value' => TestUtil::YEAR
			],[
				'class' => 'vehicleInfo',
				'label' => 'Marque :',
				'type'  => Util::getTermsName('vehicle_brand', 'parent'),
				'id'    => Vehicle::PREFIX . Vehicle::BRAND,
				'name'  => Vehicle::PREFIX . Vehicle::BRAND,
				'value' => TestUtil::BRAND
			],[
				'class' => 'vehicleInfo',
				'label' => 'Type :',
				'type'  => Util::getTermsName('vehicle_type'),
				'id'    => Vehicle::PREFIX . Vehicle::TYPE,
				'name'  => Vehicle::PREFIX . Vehicle::TYPE,
				'value' => TestUtil::TYPE
			],[
				'class' => 'vehicleInfo',
				'label' => 'Puissance din :',
				'type'  => 'number',
				'id'    => Vehicle::PREFIX . Vehicle::CH,
				'name'  => Vehicle::PREFIX . Vehicle::CH,
				'value' => TestUtil::CH
			],[
				'class' => 'vehicleInfo',
				'label' => 'Puissance fiscale :',
				'type'  => 'number',
				'id'    => Vehicle::PREFIX . Vehicle::CHF,
				'name'  => Vehicle::PREFIX . Vehicle::CHF,
				'value' => TestUtil::CHF
			],[
				'class' => 'vehicleInfo',
				'label' => 'Kilométrage :',
				'type'  => 'number',
				'id'    => Vehicle::PREFIX . Vehicle::KM,
				'name'  => Vehicle::PREFIX . Vehicle::KM,
				'value' => TestUtil::KM
			],[
				'class' => 'vehicleInfo',
				'label' => 'Prix :',
				'type'  => 'number',
				'id'    => Vehicle::PREFIX . Vehicle::PRICE,
				'name'  => Vehicle::PREFIX . Vehicle::PRICE,
				'value' => TestUtil::PRICE
			],[
				'class' => 'vehicleInfo',
				'label' => 'Mise en circulation :',
				'type'  => 'date',
				'id'    => Vehicle::PREFIX . Vehicle::CIRCULATION,
				'name'  => Vehicle::PREFIX . Vehicle::CIRCULATION,
				'value' => TestUtil::CIRCULATION
			],[
				'class' => 'vehicleInfo',
				'label' => 'Vendu :',
				'type'  => 'checkbox',
				'id'    => Vehicle::PREFIX . Vehicle::IS_SOLD,
				'name'  => Vehicle::PREFIX . Vehicle::IS_SOLD,
				'value' => TestUtil::IS_SOLD
			],[
				'class' => 'vehicleInfo',
				'label' => 'Boîte de vitesse :',
				'type'  => ['man' => __( 'Manuelle' ), 'auto' => __( 'Automatique' )],
				'id'    => Vehicle::PREFIX . Vehicle::GEARBOX,
				'name'  => Vehicle::PREFIX . Vehicle::GEARBOX,
				'value' => TestUtil::GEARBOX
			],[
				'class' => 'vehicleInfo',
				'label' => 'Consommation :',
				'type'  => 'text',
				'id'    => Vehicle::PREFIX . Vehicle::CONSO,
				'name'  => Vehicle::PREFIX . Vehicle::CONSO,
				'value' => TestUtil::CONSO
			],[
				'class' => 'vehicleInfo',
				'label' => 'Energie :',
				'type'  => VehicleUtil::getFuelType(),
				'id'    => Vehicle::PREFIX . Vehicle::FUEL,
				'name'  => Vehicle::PREFIX . Vehicle::FUEL,
				'value' => TestUtil::FUEL
			],[
				'class' => 'vehicleInfo',
				'label' => 'Couleur :',
				'type'  => 'text',
				'id'    => Vehicle::PREFIX . Vehicle::COLOR,
				'name'  => Vehicle::PREFIX . Vehicle::COLOR,
				'value' => TestUtil::COLOR
			],[
				'class' => 'vehicleInfo',
				'label' => 'Garantie :',
				'type'  => 'text',
				'id'    => Vehicle::PREFIX . Vehicle::WARRANTY,
				'name'  => Vehicle::PREFIX . Vehicle::WARRANTY,
				'value' => TestUtil::WARRANTY
			],[
				'class' => 'vehicleInfo',
				'label' => 'Longueur :',
				'type'  => 'text',
				'id'    => Vehicle::PREFIX . Vehicle::WIDTH,
				'name'  => Vehicle::PREFIX . Vehicle::WIDTH,
				'value' => TestUtil::WIDTH
			],[
				'class' => 'vehicleInfo',
				'label' => 'Emission de CO2 :',
				'type'  => 'text',
				'id'    => Vehicle::PREFIX . Vehicle::CO2,
				'name'  => Vehicle::PREFIX . Vehicle::CO2,
				'value' => TestUtil::CO2
			],[
				'class' => 'vehicleInfo',
				'label' => 'Volume du coffre :',
				'type'  => 'text',
				'id'    => Vehicle::PREFIX . Vehicle::TRUNK,
				'name'  => Vehicle::PREFIX . Vehicle::TRUNK,
				'value' => TestUtil::TRUNK
			]
		];
	}

	private function outputContains( $output, $needle )
	{
		return strstr( $output, $needle ) !== false;
	}
}