<?php

namespace FleetManager\Tests;

use FleetManager\Vehicle\Shortcodes;
use PHPUnit\Util\Test;

require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/tests/TestUtil.php';

require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/app/main/Settings.php';
require_once getenv( 'WP_DEVELOP_DIR' ) . 'src/wp-content/plugins/FleetManager/app/main/Util.php';

class Shortcodes_Test extends \WP_UnitTestCase
{
	private $shortcodes;

	public function __construct()
	{
		parent::__construct();

		$this->shortcodes = new Shortcodes();
	}

	/**
	 * Test FM_photo_url shortcode without a Loop.
	 *
	 * @covers Shortcodes::getPhotoUrl
	 * @test
	 */
	public function getPhotoUrl()
	{
		$postId = TestUtil::addVehicleToDb();

		$this->assertEmpty( do_shortcode( '[FM_photo_url]' ) );
		$this->assertEmpty( do_shortcode( '[FM_photo_url photo_id="1"]' ) );
		$this->assertEmpty( do_shortcode( "[FM_photo_url photo_id='-1' post_id='$postId']" ) );
		$this->assertEmpty( do_shortcode( "[FM_photo_url photo_id='1' post_id='-1']" ) );
		$this->assertEmpty( do_shortcode( "[FM_photo_url photo_id='A' post_id='$postId']" ) );
		$this->assertEmpty( do_shortcode( "[FM_photo_url photo_id='' post_id='$postId']" ) );
		$this->assertEmpty( do_shortcode( "[FM_photo_url photo_id=null post_id='$postId']" ) );

		$this->assertEquals( TestUtil::IMAGE1, do_shortcode( "[FM_photo_url post_id='$postId']" ) );
		$this->assertEquals( do_shortcode( "[FM_photo_url post_id='$postId']" ), TestUtil::IMAGE1 );
		$this->assertEquals( do_shortcode( "[FM_photo_url photo_id='1' post_id='$postId']" ), TestUtil::IMAGE1 );
		$this->assertEquals( do_shortcode( "[FM_photo_url photo_id='2' post_id='$postId']" ), TestUtil::IMAGE2 );
	}

	/**
	 * Test FM_photo_url shortcode within a Loop.
	 *
	 * @covers Shortcodes::getPhotoUrl
	 * @test
	 */
	public function getPhotoUrlInLoop()
	{
		global $post;
		$postId = TestUtil::addVehicleToDb();
		$post = get_post( $postId ); // same as loop instance

		$this->assertEquals( TestUtil::IMAGE1, do_shortcode( '[FM_photo_url]' ) );
		$this->assertEquals( TestUtil::IMAGE1, do_shortcode( "[FM_photo_url post_id='$postId']" ) );
		$this->assertEquals( TestUtil::IMAGE1, do_shortcode( "[FM_photo_url photo_id='1' post_id='$postId']" ) );
		$this->assertEquals( TestUtil::IMAGE2, do_shortcode( "[FM_photo_url photo_id='2' post_id='$postId']" ) );

		$this->assertEmpty( do_shortcode( "[FM_photo_url photo_id='-1' post_id='$postId']" ) );
		$this->assertEmpty( do_shortcode( '[FM_photo_url photo_id="1" post_id="-1"]' ) );
		$this->assertEmpty( do_shortcode( "[FM_photo_url post_id='A']" ) );
		$this->assertEmpty( do_shortcode( "[FM_photo_url photo_id='A']" ) );
	}

	/**
	 * Test FM_info shortcode without a loop.
	 *
	 * @covers Shortcodes::getInfo
	 * @test
	 */
	public function getInfo()
	{
		$postId = TestUtil::addVehicleToDb();

		$this->assertEquals( TestUtil::COLOR, do_shortcode( "[FM_info info_name='color' post_id='$postId']" ) );
		$this->assertEquals( TestUtil::YEAR, do_shortcode( "[FM_info info_name='year' post_id='$postId']" ) );

		$this->assertEmpty( do_shortcode( "[FM_info post_id='$postId']" ) );
		$this->assertEmpty( do_shortcode( "[FM_info]" ) );
		$this->assertEmpty( do_shortcode( "[FM_info info_name='color']" ) );
		$this->assertEmpty( do_shortcode( "[FM_info info_name='notExist' post_id='$postId']" ) );
	}

	/**
	 * Test FM_info shortcode within a loop.
	 *
	 * @covers Shortcodes::getInfo
	 * @test
	 */
	public function getInfoInLoop()
	{
		global $post;
		$postId = TestUtil::addVehicleToDb();
		$post = get_post( $postId ); // same as loop instance

		$this->assertEquals( TestUtil::COLOR, do_shortcode( "[FM_info info_name='color' post_id='$postId']" ) );
		$this->assertEquals( TestUtil::YEAR, do_shortcode( "[FM_info info_name='year' post_id='$postId']" ) );
		$this->assertEquals( TestUtil::COLOR, do_shortcode( "[FM_info info_name='color']" ) );

		$this->assertEmpty( do_shortcode( "[FM_info post_id='$postId']" ) );
		$this->assertEmpty( do_shortcode( "[FM_info]" ) );
		$this->assertEmpty( do_shortcode( "[FM_info info_name='notExist' post_id='$postId']" ) );
	}

	/**
	 * Test FM_option shortcode without a loop
	 *
	 * @covers Shortcodes::getOption
	 * @test
	 */
	public function getOption()
	{
		$this->markTestIncomplete( 'Shortcodes::getOption test not implements yet.' );
	}

	/**
	 * Test FM_option shortcode without a loop
	 *
	 * @covers Shortcodes::getOption
	 * @test
	 */
	public function getOptionInLoop()
	{
		$this->markTestIncomplete( 'Shortcodes::getOptionInLoop test not implements yet.' );
	}

	public function addVehicleToDb()
	{
		return wp_insert_post([
			'post_title'   => 'test',
			'post_content' => 'test',
			'post_type'    => 'vehicle',
			'meta_input'   => [
				'FM_image1' => 'url/to/image/1.jpg',
				'FM_image2' => 'url/to/image/2.jpg',
				'FM_year'   => '2006',
				'FM_km'     => '200000',
				'FM_color'  => 'red'
			]
		]);
	}
}