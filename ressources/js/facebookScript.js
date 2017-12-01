(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/fr_FR/sdk.js#xfbml=1&version=v2.10&appId=122866341766475";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

window.fbAsyncInit = function() {
    let accessToken = "";
    let expiresIn = "";

    FB.init({
        appId      : '122866341766475',
        xfbml      : true,
        version    : 'v2.10',
        cookie     : true
    });

    FB.AppEvents.logPageView();

    // get status of facebook connection
    FB.getLoginStatus(function(response)
    {
        // the user is logged in and has authenticated your
        // app, and response.authResponse supplies
        // the user's ID, a valid access token, a signed
        // request, and the time the access token
        // and signed request each expire
        if( response.status === 'connected' && ! isTokenExpired( response.authResponse.expiresIn ) )
        {
        	console.log(response);
            accessToken = response.authResponse.accessToken;
            expiresIn = response.authResponse.expiresIn;
            ajaxSaveAccessToken( accessToken, expiresIn );
        }
        // the user isn't logged in
        else 
    	{
        	FB.login(function(response) {
        		accessToken = response.authResponse.accessToken;
        		expiresIn = response.authResponse.expiresIn;
        		
        		ajaxSaveAccessToken( accessToken, expiresIn );
        		
        	},{'scope':'publish_actions'});
    	}
    } );

    jQuery('.wp-header-end').before( jQuery('#fb_login_btn') );
};

function ajaxSaveAccessToken( accessToken, expiresIn )
{
    jQuery.post({
        url     : FB_util.ajaxurl,
        data    : {
            action  : "getAccessToken",
            nonce: FB_util.nonce,
            accessToken: accessToken,
            expiresIn: expiresIn
        },
        success : function (response)
        {
            console.log('Connecté à Facebook');
        },
        error   : function ()
        {
            console.log('error :');
            console.log(response);
        }
    });
}


function isTokenExpired( expiresIn )
{
	console.log( expiresIn );
	return false;
}