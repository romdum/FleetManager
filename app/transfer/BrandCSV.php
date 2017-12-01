<?php

namespace FleetManager\Transfer;

use FleetManager\FleetManager;
use FleetManager\Util;

class BrandCSV extends Brand implements DataParser
{
	function exportData()
	{
        $terms = parent::getData();

        $exportTxt = '"' . Util::strCSVFormat( 'Nom' ) . '"' . ';' . "\n\r";

        foreach( $terms as $term )
            $exportTxt .= '"' . Util::strCSVFormat( $term ) . '"' . ';' . "\n\r";

        echo $exportTxt;

		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename=fleetmanager_export.csv');

		die();
	}

	function importData()
	{
		$data = $this->getData();
		$dataFormat = ['taxonomy', 'name', 'slug', 'parent'];

		$data = preg_split( "/(\n|\n\r|\r\n|\r)/", $data ); // explode if found \r, \n\r, \r\n or \n
		$data = array_map( function( $v ){ return explode( ';', $v ); }, $data );
		$csvData = [];
		foreach( $data as $i => $csv )
			foreach( $csv as $k => $d )
				$csvData[$i][$dataFormat[$k]] = $d;

		foreach( $csvData as $csv )
		{
			if( $this->isValidCSV( $csv ) && ! term_exists( $csv['name'], $csv['taxonomy'] ) )
			{
				if( $csv['parent'] !== 0 )
				{
					$parent = get_term_by( 'slug', $csv['parent'], $csv['taxonomy'] );
					$csv['parent'] = $parent !== false ? $parent->term_id : 0;
				}

				$response = wp_insert_term(
					$csv['name'],
					$csv['taxonomy'],
					['description' => $csv['name'], 'slug' => $csv['slug'], 'parent' => $csv['parent']]
				);

				if( ! is_wp_error( $response ) )
					FleetManager::$logger->log( $csv['name'] . ' term imported.' );
			}
		}

		FleetManager::$notice->setNotice('Import terminé.','success');
	}

	/**
	 * Check if the import CSV is valid
	 * @param $arr : Csv parsed in an array
	 * @return bool
	 */
	private function isValidCSV( $arr )
	{
		return isset( $arr['name'] ) && isset( $arr['taxonomy'] ) && isset( $arr['slug'] ) && isset( $arr['parent'] );
	}
}