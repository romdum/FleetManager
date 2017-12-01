<?php

namespace FleetManager\Transfer;

use FleetManager\FleetManager;
use FleetManager\Vehicle\PostType;

require_once 'DataParser.php';
require_once 'DataToTransfer.php';
require_once 'Vehicle.php';
require_once 'VehicleCSV.php';
require_once 'Brand.php';
require_once 'BrandCSV.php';

class Transfer
{    
	const IMPORT = 'import';
    const EXPORT = 'export';

	/**
	 * Method call on transfer post request.
	 */
    public static function transferPostRequest()
    {
        check_admin_referer( 'FM_transfer' );

        foreach( $_POST as $k => $p )
        	$_POST[$k] = htmlspecialchars( $p );

        if( $_POST['dataToTransfer'] === PostType::POST_TYPE_NAME && $_POST['formatToExport'] === DataParser::CSV )
	        new VehicleCSV( isset( $_POST['export'] ) ? self::EXPORT : self::IMPORT, $_POST['formatToExport'] );
		else if( $_POST['dataToTransfer'] === Brand::NAME && $_POST['formatToExport'] === DataParser::CSV )
			new BrandCSV( isset( $_POST['export'] ) ? self::EXPORT : self::IMPORT, $_POST['formatToExport'] );

        wp_redirect( admin_url( 'options-general.php?page=fleetmanager_settings_page' ) );
    }
}