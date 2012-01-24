<?php
/**
 * Simple test of crypt() using SHA512
 *
 */

// Ensure that SHA512 is supported

if ( empty(CRYPT_SHA512) ) {
    trigger_error(E_USER_ERROR, "SHA512 is not supported on this system");
} 

// Creating a user account
// Mock submitted data
$username = "emanresu";
$password = "drowssap";

// This will generate a unique salt (23 characters, but we only use 16)
$salt = uniqid('', true);

$enc_password = crypt($password, '$6$' . $salt . '$');

// Note that the salt now is the first part of the password
// 106 characters long


// Testing password
if ( $enc_password === crypt($password, $enc_password) ) {
    // Logged in
}
