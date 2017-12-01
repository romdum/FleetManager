<?php

namespace FleetManager\SocialNetwork;

use \Facebook\Facebook as FbAPI;
use \Facebook\Authentication\AccessToken as FbTokenAPI;

use \FleetManager\Util;
use \FleetManager\FleetManager;


/**
 * Class Facebook use to publish automatically new car on a Facebook page / user
 */
class Facebook
{
    const SESSION_TOKEN = 'fb_access_token';

    private static $APP_ID;
    private static $APP_SECRET;

	/**
	 * Facebook API Graph object
	 * @var \Facebook\Facebook
	 */
    private $fb;

	/**
	 * AccessToken API Graph object
	 * @var \Facebook\Authentication\AccessToken
	 */
    private $accessToken;

	/**
	 * Facebook constructor.
	 */
    public function __construct()
    {
        Util::session_start();
        self::$APP_ID = FleetManager::$settings->getSetting( 'SocialNetwork', 'facebook', 'appId' );
        self::$APP_SECRET = FleetManager::$settings->getSetting( 'SocialNetwork', 'facebook', 'appSecret' );
        
        $this->createFacebook();

        add_action( 'wp_ajax_getAccessToken', array( $this, 'getAccessToken' ), 10, 0 );
        
        if( $this->tokenExpired() || $this->sessionExpired() )
            add_action( 'init', array( $this, 'loadScripts' ), 10, 0 );

        add_filter( 'views_edit-vehicle', array( $this, 'addConnectionButton' ), 10, 1 );

        add_action( 'save_post', array( $this, 'publish'), 10, 3 );
    }

	/**
	 * Method use when user publish or update a post. It will
	 * use Facebook API and publish on the user wall.
	 *
	 * @param int $postId The post ID.
	 * @param \WP_Post $post The post object.
	 * @param bool $update Whether this is an existing post being updated or not.
	 * @param bool $retry Retry to connect a second time
	 */
    public function publish( $postId, $post, $update, $retry = false )
    {
        try
        {
            if( $this->accessToken === null )
            	throw new \Exception('FleetManager\SocialNetwork\Facebook->accessToken var is null');

            $resp = $this->fb->post( '/me/feed',
                [
                    'link' => get_permalink( $postId ),
                    'message' => $post->post_title
                ],
                $this->accessToken->getValue()
            );

	        $fbOldId = get_post_meta( $postId, 'FM_facebookId', true );
	        if( $update )
	        {
		        try
	            {
		            $this->fb->delete( $fbOldId, [], $this->accessToken->getValue() );
	            }
	            catch( \Exception $exception )
	            {
		            FleetManager::$logger->log('Vehicle deletion failed. Caused by: ', 'error' );
		            FleetManager::$logger->log( $exception->getMessage(), 'error' );
	            }
            }
	        update_post_meta( $postId, 'FM_facebookId', json_decode( $resp->getBody() )->id );

	        FleetManager::$logger->log('Vehicle ' . $postId . ' publish on Facebook. Response:' );
            FleetManager::$logger->log( $resp->getBody() );
        }
        catch ( \Exception $e )
        {
	        $this->accessToken = null;
	        $_SESSION['token_timeout'] = null;
	        $_SESSION[self::SESSION_TOKEN] = null;

	        FleetManager::$notice->setNotice( 'Une erreur est survenue lors de la publication sur Facebook.' );
	        FleetManager::$logger->log('Vehicle ' . $postId . ' publication failed. Caused by: ', 'error' );
	        FleetManager::$logger->log( $e->getMessage(), 'error' );

	        if( $retry === false )
				$this->publish( $postId, $post, $update, true);
        }
    }

	/**
	 * Method load javascript files
	 */
    public function loadScripts()
    {
        wp_register_script( 'FM_facebookScript', FleetManager::$PLUGIN_URL . 'ressources/js/facebookScript.js', array( 'jquery' ) );
        wp_localize_script( 'FM_facebookScript', 'FB_util', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'facebookScript' ),
        ) );
        wp_enqueue_script( 'FM_facebookScript' );
    }

    public function addConnectionButton( $view )
    {
        include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/facebookLogin.php';
        return $view;
    }

	/**
	 * Method to instanciate Facebook and AccessToken objects
	 */
    public function createFacebook()
    {
        $this->fb = new FbAPI([
          'app_id' => self::$APP_ID,
          'app_secret' => self::$APP_SECRET,
          'default_graph_version' => 'v2.10',
        ]);

        if( isset( $_SESSION[self::SESSION_TOKEN] ) )
            $this->accessToken = new FbTokenAPI( $_SESSION[self::SESSION_TOKEN], time() * 60 * 60 * 2 );
    }

    /**
     * Ajax: Méthode utilisé pour récupérer le token d'accès Facebook
     */
    public function getAccessToken()
    {
        check_ajax_referer( 'facebookScript', 'nonce' );

        if( isset( $_POST ) && isset( $_POST['accessToken'] ) && isset( $_POST['expiresIn'] ) )
        {
            $_CLEAN['accessToken'] = htmlspecialchars( $_POST['accessToken'] );
            $_SESSION['token_timeout'] = time() + $_POST['expiresIn'];
            $_SESSION[self::SESSION_TOKEN] = $_CLEAN['accessToken'];
            $this->accessToken = new FbTokenAPI( $_CLEAN['accessToken'], time() + $_POST['expiresIn'] );
            echo $_CLEAN['accessToken'];
        }
        else
        {
            echo 'error : Ajax doesnt return access token';
        }

        wp_die();
    }

    /**
     * Check if the session token is expired.
     *
     * @return boolean  true if session is expire.
     */
    private function sessionExpired()
    {
        return ! isset( $_SESSION['token_timeout'] )
            || ( isset( $_SESSION['token_timeout'] ) && $_SESSION['token_timeout'] < time() );
    }
    
    /**
     * Check if the token attribute is expired. 
     * @return boolean  true if token expired.
     */
    private function tokenExpired()
    {
        return ( isset( $this->accessToken ) && $this->accessToken->isExpired() ) || ! isset( $this->accessToken );
    }
}
