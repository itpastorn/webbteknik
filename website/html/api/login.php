<?php
/*
 * Server side verification of browserid assertions
 *
 * This file is part of the Ajax API
 * @author <gunther@keryx.se>
 */

session_start();

/**
 * All needed files
 */
require_once '../../includes/loadfiles.php';

$firephp = FirePHP::getInstance(true);

// Database settings and connection
$dbx = config::get('dbx');
// init
keryxDB2_cx::get($dbx);

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
//$firephp->log($response);
/*
$response->status     - should be "okay"
$response->email
$response->audience
$response->issuer
$response->expires
*/

if ( $response->status === "okay" ) {
    
    session_regenerate_id();

    $dbh  = keryxDB2_cx::get();
    $stmt = $dbh->prepare(
        'SELECT email, firstname, lastname, privileges FROM users WHERE email = :email'
    );
    $stmt->bindParam(':email', $response->email);
    $stmt->execute();
    $userdata = $stmt->fetch();
    
    if ( empty($userdata) ) {
        // Non registered user
        $userdata['email']      = $response->email;
        $userdata['firstname']  = null;
        $userdata['lastname']   = null;
        $userdata['privileges'] = 1;
    }
    $_SESSION['user'] = $response->email;
    $_SESSION['userdata'] = json_encode($userdata);
    echo $_SESSION['userdata'];
    exit;
}

// Assertion not OK - Why...?
echo '{"email" : null, "privileges": 0, "reason" : "'. $response->status . '"}';

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
