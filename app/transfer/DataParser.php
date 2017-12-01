<?php

namespace FleetManager\Transfer;

interface DataParser
{
	const CSV  = 'csv';
	const XML  = 'xml';
	const JSON = 'json';

	function exportData();
	function importData();
}