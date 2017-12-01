<?php 

namespace FleetManager\Transfer;

use FleetManager\FleetManager;
use FleetManager\Util;

require_once 'DataParser.php';
require_once 'Vehicle.php';

class VehicleCSV extends Vehicle implements DataParser
{
	public function importData()
	{
		//TODO: import data
		FleetManager::$notice->setNotice( 'L\'import de véhicule n\'est pas encore disponible.');
	}

	public function exportData()
	{
		$vehicles = parent::getData();
        $exportTxt = '';

        foreach( array_values( $vehicles )[0] as $header => $v )
        {
	        $exportTxt .= '"' . Util::strCSVFormat( $v['label'] ) . '"' . ';';
        }
        $exportTxt .= "\n";

        foreach( $vehicles as $postId => $vehicle )
        {
            foreach( $vehicle->getArrayInfos() as $data )
            {
	            $exportTxt .= '"' . Util::strCSVFormat( $data['value'] ) . '"' . ';';
            }
            $exportTxt .= "\n\r";
        }

        echo $exportTxt;

		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename=fleetmanager_export.csv');

		die();
	}
}