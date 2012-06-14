<?php
/*
 * Sign in usig BrowserID test
 * 
 * @link https://developer.mozilla.org/en/BrowserID/Quick_Setup
 * @author <gunther@keryx.se>
 */

session_start();

/**
 * All needed files
 */
require_once '../includes/loadfiles.php';


if ( isset($_SESSION['user']) ) {
    echo $_SESSION['user'];
    echo " is logged in<br>";
}
?>
<a href="#" id="browserid" title="Logga in med BrowserID">  
  <img src="img/sign-in-green.png" alt="Logga in">  
</a>
<script src="https://browserid.org/include.js" type="text/javascript"></script> 
<script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
<script>
"use strict";
$("#browserid").click( function () {
   navigator.id.get(gotAssertion);
   return false;
});
function gotAssertion(assertion) {
    if ( assertion !== null ) {
        $.ajax({
            type : 'POST',
            url  : 'api/login.php',
            data : { assertion: assertion },
            success : function (res, status, xhr) {
                res = JSON.parse(res);
                if ( res.email == null ) {
                    // Fail or logout
                    // loggedOut();
                    console.log("Login fail: " +  res.reason);
                } else {
                    loggedIn(res);
                }
            },
            error : function (res, status, xhr) {
                res = JSON.parse(res);
                alert("Login fel: " +  res.reason);
                
            }
        });
    } else {
        // loggedOut();
        console.log("Assertion was null - loggedOut()")
    }
}
// Runs if assertion has been verified as OK
// Which also means that $_SESSION has been set and DB updated with login data
function loggedIn(res) {
    console.log(res.email + " has now logged in");
    // What URL are you on? Special? Stay. Otherwise load personal start page.
}
</script>