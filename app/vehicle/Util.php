<?php

namespace FleetManager\Vehicle;


class Util
{
	/**
	 * Return all fuel type
	 * return array
	 */
	public static function getFuelType()
	{
		return \FleetManager\Util::getTermsName('vehicle_fuel_type');
	}
}