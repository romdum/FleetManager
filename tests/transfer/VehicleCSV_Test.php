<?php

namespace FleetManager\Tests\Transfer;

use FleetManager\Tests\TestUtil;
use FleetManager\Transfer\DataParser;
use FleetManager\Transfer\Transfer;
use FleetManager\Transfer\VehicleCSV;
use FleetManager\Vehicle\PostType;

require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/tests/TestUtil.php';

require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/FleetManager.php';
require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/app/transfer/Transfer.php';

class VehicleCSV_Test extends \WP_UnitTestCase
{
	public function __construct()
	{
		parent::__construct();

	}

	/**
	 * @covers VehicleCSV::exportData
	 * @test
	 */
	public function exportData()
	{
		$this->markTestIncomplete();
		$_POST['dataToTransfer'] = PostType::POST_TYPE_NAME;
		$_POST['formatToExport'] = DataParser::CSV;

		( new VehicleCSV( Transfer::EXPORT, DataParser::CSV ) )->exportData();
	}
}