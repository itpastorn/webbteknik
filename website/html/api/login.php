<?php
/*
 * Server side verification of browserid assertions
 *
 * This file is part of the Ajax API
 *
 * @todo Rework userdata to be an instance of the user class and not StdObject
 * @todo Perhaps always reload userdata from DB on page load to keep DRY?
 * @todo Check that user is not suspended
 * @todo Log all log-ins in DB (logged in since... logged in n times... )
 * 
 * @author <gunther@keryx.se>
 */

session_start();

header('Content-type: application/json');

/**
 * All needed files
 */
require_once '../../includes/loadfiles.php';

// Database settings and connection
$dbx = config::get('dbx');
// init
keryxDB2_cx::get($dbx);

// Prepare data
// Ajax (assertion) data sent as POST
$assertion = filter_input(
    INPUT_POST, 'assertion', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH
);

$FIREPHP->log('Assertion: ' . $assertion);
$FIREPHP->log('Assertion length: ' . strlen($assertion));

if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off" ) {
    $audience = "http://";
} else {
    $audience = "https://";
}
$audience .= urlencode($_SERVER['SERVER_NAME']);
$FIREPHP->log('audience: ' . $audience);

$data = new StdClass();
$data->assertion = $assertion;
$data->audience  = $audience;

// Do curl
$url = 'https://verifier.login.persona.org/verify';
$ch  = curl_init();
// curl_setopt($ch, CURLOPT_POST, true);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt_array($ch, array(
    CURLOPT_URL            => $url,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($data),
    CURLOPT_HEADER         => false,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_HTTPHEADER => array('Content-Type: application/json')
));
$response = curl_exec($ch);
curl_close($ch);

// Check response
if ( empty($response) ) {
    $FIREPHP->log('Response is empty - assertion failed');
    header("HTTP/1.0 401 Authentication is possible but has failed");
    echo '{"reason" : "Assertion failed, verifying server returned empty content"}';
    exit;
}
$response = json_decode($response);
$FIREPHP->log('Response decoded: ' . $response);
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
    // TODO - this is not DRY
    $stmt = $dbh->prepare(
        'SELECT email, firstname, lastname, privileges, user_since FROM users WHERE email = :email'
    );
    $stmt->bindParam(':email', $response->email);
    $stmt->execute();
    $userdata = $stmt->fetch(PDO::FETCH_OBJ);

    if ( empty($userdata) ) {
        // Non registered user
        $userdata             = new StdClass();
        $userdata->email      = $response->email;
        $userdata->firstname  = null;
        $userdata->lastname   = null;
        $userdata->privileges = 1;
    }
    $_SESSION['user']     = $response->email;
    $_SESSION['userdata'] = $userdata;
    echo json_encode($_SESSION['userdata']); // Send to receiving script
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
