<?php
/**
 * destroy session data.
 * no need to use session_unset() in PHP5
 **/
session_destroy();

/**
 * If you wish to kill the session, then you must
 * delete the  session cookie. An http request is needed to effectively
 * set the cookie to permanent inactive status; only the browser can remove the cookie.
 **/
$session_name = session_name(); 
if ( isset( $_COOKIE[ $session_name ] ) ) {
    if ( setcookie(session_name(), '', time()-3600, '/') ) {
        header("Location: index.php");
        exit();    
    }
    else
    {
        // setcookie() fails when there is output sent prior to calling this function.
    }
}
?>