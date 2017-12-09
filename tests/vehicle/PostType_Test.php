<?php

namespace FleetManager\Tests;

use FleetManager\Vehicle\PostType;
use \WP_UnitTestCase;

require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/app/vehicle/PostType.php';
require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/app/main/Util.php';

class PostType_Test extends WP_UnitTestCase
{
	private $postType;

	public function __construct()
	{
		parent::__construct();

		$this->postType = new PostType();
	}

	/**
	 * Test if a constant exist for the post type name.
	 *
	 * @test
	 */
	public function postTypeNameConst()
	{
		$this->assertNotNull(PostType::POST_TYPE_NAME );
		$this->assertNotEmpty(PostType::POST_TYPE_NAME );
	}

	/**
	 * Test method which create the vehicle post type.
	 *
	 * @covers PostType::addVehiclePostType
	 * @test
	 */
	public function addVehiclePostType()
	{
		$this->postType->addVehiclePostType();
		$this->assertTrue( post_type_exists( PostType::POST_TYPE_NAME ) );
	}

	/**
	 * Test if custom field are save in post meta table.
	 *
	 * @covers PostType::saveCustomFields
	 * @test
	 */
	public function saveCustomFields()
	{
		$this->markTestIncomplete( 'saveCustomFields test not implement yet.' );
	}

	/**
	 * Test if the pics are save in post meta table.
	 *
	 * @covers PostType::saveCustomFields
	 * @depends addVehiclePostType
	 * @test
	 */
	public function savePics()
	{
		require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/app/vehicle/Vehicle.php';

		$picsNbr = 5;
		$postId = $this->addVehicleToDb('Car', 'Beautiful car' );

		for( $i = 1; $i < $picsNbr; $i++ )
		{
			$this->assertEmpty( get_post_meta( $postId , 'FM_image' . $i , true ) );
			$_POST['FM_image' . $i] = 'fakeUrl' . $i;
		}

		$this->postType->saveCustomFields( $postId, get_post( $postId ) );

		for( $i = 1; $i < $picsNbr; $i++ )
			$this->assertTrue( get_post_meta( $postId , 'FM_image' . $i , true ) === 'fakeUrl' . $i );
	}

	/**
	 * Test to remove attachment join with vehicle in post meta table.
	 *
	 * @covers PostType::removePics
	 * @depends addVehiclePostType
	 * @test
	 */
	public function removePics()
	{
		require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/app/vehicle/Vehicle.php';

		$filePath = '/wp-content/themes/twentyseventeen/assets/images/coffee.jpg';
		$guid = wp_upload_dir()['url'] . '/' . basename( $filePath );

		$attach_id = wp_insert_attachment([
			'guid'           => $guid,
			'post_mime_type' => wp_check_filetype( basename( $filePath ), null )['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filePath ) ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		], $filePath, 0 );

		$postId = $this->addVehicleToDb( 'Super voiture', 'Super Voiture', [
			'FM_image1' => $guid
		]);

		$this->assertNotNull( get_post( $postId ) );
		$this->assertNotNull( get_post( $attach_id ) );

		$this->postType->removePics( $postId );

		$this->assertNull( get_post( $attach_id ) );
	}

	/**
	 * @param string $title
	 * @param string $content
	 * @param array $meta
	 *
	 * @return int|\WP_Error : post id or WP_Error
	 */
	public function addVehicleToDb( $title = '', $content = '', $meta = [] )
	{
		return wp_insert_post([
			'post_title'   => $title,
			'post_content' => $content,
			'post_type'    => PostType::POST_TYPE_NAME,
			'meta_input'   => $meta
		]);
	}
}