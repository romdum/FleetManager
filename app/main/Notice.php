<?php

namespace FleetManager;

class Notice
{
    const SESSION = 'FLEETMANAGER_NOTICE';

    const NOTICE_SUCCESS = 'success';
    const NOTICE_ERROR   = 'error';

    public function __construct()
    {
        Util::session_start();
    }
    
    public function setNotice( $message, $messageType = 'error' )
    {
        $_SESSION[self::SESSION] = $message . '||' . $messageType;

        return $this;
    }
    
    public function displayNotice()
    {
        if( isset( $_SESSION[self::SESSION] ) && ! empty( $_SESSION[self::SESSION] ) )
        {
            $args['message'] = explode( '||', $_SESSION[self::SESSION] )[0];
            $args['messageType'] = explode( '||', $_SESSION[self::SESSION] )[1];
            
            include FleetManager::$PLUGIN_PATH . 'ressources/views/templates/notice.php';
            
            $_SESSION[self::SESSION] = null;
        }
    }
}