<?php
/*
 * Server side verification of browserid assertions
 *
 * @author <gunther@keryx.se>
 */

session_start();

/**
 * Fire PHP
 */
require_once('FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);

// Prepare data
// Ajax (assertion) data sent as POST
$assertion = filter_input(INPUT_POST, 'assertion', FILTER_SANITIZE_ENCODED, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);

if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off" ) {
    $audience = "https://";
} else {
    $audience = "http://";
}
$audience .= urlencode($_SERVER['SERVER_NAME']);

$data = "assertion={$assertion}&audience={$audience}";

// Do curl
$url = 'https://browserid.org/verify';
$ch  = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
$response = curl_exec($ch);
curl_close($ch);

// Check response
$response = json_decode($response);
$firephp->log($response);
/*
$response->status     - should be "okay"
$response->email
$response->audience
$response->issuer
$response->expires
*/

if ( $response->status === "okay" ) {
	$_SESSION['user'] = $response->email;
    echo "Assertion okay";
    
    // TODO local check for account
    /*
     * Check that user has an account
     *   if not send to registration page
     *   if (s)he has - update DB
     *   and
     *   set a session variable concerning type of user admin/teacher/workbook/textbook/webonly/loggedin
     *   constant values                                32      16        8        4       2     1
     */
    
    exit;
}

// Assertion not OK - Why...?
echo "Assertion failed. Reason: " . $response->status;

/*
 * How to register
 *  1. Get a BrowserID
 *  2. Log in
 *  3. Chose what type of account you want
 *  4. (Grant admin access manually only)
 *  5. Answer question from book (or buy web only access...)
 *  6. Set personal info (real names required, Github, JSFiddle) and join group
 *  7. Teachers must OK students joining
 */
