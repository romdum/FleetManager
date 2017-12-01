<?php

namespace FleetManager;

class Util
{
    public static function number_format( $number, $suffix = '', $decimalNumber = 0, $decimalPoint = ',', $thousandSeparator = ' ' )
    {
        if( is_numeric( $number ) )
            return \number_format($number, $decimalNumber, $decimalPoint, $thousandSeparator) . $suffix;
        else
            return '';
    }

    public static function loadSVG( $svg )
    {
        return 'data:image/svg+xml;base64,' . base64_encode( $svg );
    }

    public static function session_start()
    {
        if( ! session_id() )
        {
            \session_start();
        }
    }   
    
    public static function array_to_object($arr) 
    {
        return is_array($arr) ? (object) array_map(__METHOD__, $arr) : $arr;
    }
    
    public static function object_to_array($obj) 
    {
        if ( is_object( $obj ) )
            $obj = get_object_vars( $obj );
        return is_array($obj) ? array_map( __METHOD__, $obj ) : $obj;
    }

	/**
	 * Method to get all taxonomy values (terms) even if they had be create
	 * by administrator and they aren't in options table.
	 * The param $only can be used to filter terms by parent terms.
	 * @param  string $taxonomyName
	 * @param  string $only
	 * @return array
	 */
	public static function getTermsName( $taxonomyName, $only = '' )
	{
		$result = [];
		foreach( get_terms( $taxonomyName, [ 'hide_empty' => false] ) as $term )
		{
			if( $term->parent === 0 && $only === 'parent' )
			{
				$result[$term->slug] = $term->name;
			}
			else if( $only !== '' && get_term_by( 'id', $term->parent, $taxonomyName ) !== false && get_term_by( 'id', $term->parent, $taxonomyName )->slug === $only )
			{
				$result[$term->slug] = $term->name;
			}
			else if( $only === '' )
			{
				$result[$term->slug] = $term->name;
			}
		}

		return $result;
	}

	public static function strCSVFormat( $str )
	{
		$str = str_replace("\n", ' ', $str);
		$str = str_replace("\r", ' ', $str);
		$str = str_replace('.', '\\.', $str);
		$str = str_replace('\'', '\\\'', $str);
		$str = str_replace('"', '\"', $str);

		return $str;
	}
}
