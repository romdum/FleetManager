<?php

namespace FleetManager;

abstract class Widget
{
	public function __construct( $slug, $title )
	{
		wp_add_dashboard_widget(
			$slug,
			$title,
			array( $this, 'display' )
		);
	}

	public abstract function display();
}