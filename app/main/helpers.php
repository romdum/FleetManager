<?php

namespace FleetManager;

use FleetManager\Vehicle\Taxonomy;
use FleetManager\Vehicle\Vehicle;

require_once 'Util.php';

function number_format( $number, $suffix = '', $decimalNumber = 0, $decimalPoint = ',', $thousandSeparator = ' ' )
{
    return Util::number_format($number, $suffix, $decimalNumber, $decimalPoint, $thousandSeparator);
}

function var_dump( $args )
{
    if( is_array( $args ) || is_object( $args ) )
    {
        echo '<pre>';
        \var_dump( $args );
        echo '</pre>';
    }
    else
    {
        \var_dump( $args );
    }
}

function fm_get_terms_name( $taxonomyName, $only = '' )
{
    return Util::getTermsName( $taxonomyName, $only );
}

function fm_vehicle_is_sold( $postId )
{
	return ( new Vehicle( $postId ) )->isSold();
}