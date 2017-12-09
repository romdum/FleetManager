<?php

namespace FleetManager\Vehicle;

/**
 * Class to create all vehicle shortcodes.
 */
class Shortcodes
{
    function __construct()
    {
        add_shortcode( 'FM_photo_url', array( $this, 'getPhotoUrl' ) );
        add_shortcode( 'FM_brand', array( $this, 'getBrand' ) );
        add_shortcode( 'FM_info', array( $this, 'getInfo' ) );
        add_shortcode( 'FM_type', array( $this, 'getType' ) );
        add_shortcode( 'FM_option', array( $this, 'getOption' ) );
    }

    function getPhotoUrl( $atts )
    {
        $a = shortcode_atts( array(
            'photo_id' => '1',
            'post_id' => get_the_ID(),
        ), $atts );

        return get_post_meta( $a['post_id'] , 'FM_image' . $a['photo_id'] , true );
    }
    
    /**
     * Return vehicle brand
     * Example : [FM_brand post_id='3']
     * post_id parameter is optional.
     * @deprecated since 22/10/2017, use FM_info shortcode.
     * @param  $atts
     * @return string
     */
    function getBrand( $atts )
    {
        $a = shortcode_atts( ['post_id' => get_the_ID()], $atts );
		$result = get_the_terms( get_post( $a['post_id'] ), 'vehicle_brand' );
		return isset( $result[0]->name ) ? $result[0]->name : '';
    }
    
    /**
     * Return a vehicle information.
     * Example : [FM_info post_id='3' info_name='price']
     * post_id parameter is optional.
     * @param  $atts
     * @return string
     */
    function getInfo( $atts )
    {
        $a = shortcode_atts( array(
            'post_id' => get_the_ID(),
            'info_name' => null
        ), $atts );

        $vehicle = new Vehicle( $a['post_id'] );

        $postMeta = get_post_meta( $a['post_id'], 'FM_' . $a['info_name'], true );
	    if( isset( $vehicle->{$a['info_name']} ) && isset( $vehicle->{$a['info_name']}['type'] ) && is_array( $vehicle->{$a['info_name']}['type'] ) && isset( $vehicle->{$a['info_name']}['type'][$postMeta] ) )
		    $postMeta = $vehicle->{$a['info_name']}['type'][$postMeta];

	    return $postMeta !== 'none' ? $postMeta : '';
    }
    
    /**
     * Return vehicle type.
     * Example : [FM_type post_id='3']
     * post_id parameter is optional.
     * @deprecated since 2017/10/22, use FM_info shortcode
     * @param  $atts
     * @return string
     */
    function getType( $atts )
    {
        $a = shortcode_atts( ['post_id' => get_the_ID()], $atts );

        return get_the_terms( get_post( $a['post_id'] ), 'vehicle_type' )[0]->name;
    }

    function getOption( $atts )
    {
        $a = shortcode_atts( ['post_id' => get_the_ID()], $atts );

        $result = '';
        $options = get_the_terms( get_post( $a['post_id'] ), 'vehicle_option' );

        if( ! is_array( $options ) )
            return '';

        foreach( $options as $option )
            $result .= $option->name . ';';

        return $result;
    }
}
