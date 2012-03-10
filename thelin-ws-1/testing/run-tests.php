<?php
/**
 * After installation run these tests to ensure that all needed modules are avilable, versions are up to date, etc
 * 
 * This script will also generate some information that can be useful
 *
 * @author Lars Gunther
 */
 
 
// PHP version



// PDO-MySQL


// SHA512 test
if ( empty(CRYPT_SHA512) ) {
    trigger_error(E_USER_ERROR, "SHA512 is not supported on this system");
}

// Sessions - where are they stored?


// Timezone setting


// Max upoad size

// Server name

// Server dir


