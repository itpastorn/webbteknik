<?php
/*
 * Server side verification of browserid assertions
 *
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
    
    // TODO local check
    /*
     * Check that user has an account
     *   if not send to registration page
     *   if (s)he has - update DB
     */
    
    exit;
}

// Assertion not OK - Why...?
echo "Assertion failed. Reason: " . $response->status;
