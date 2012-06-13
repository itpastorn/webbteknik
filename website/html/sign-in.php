<?php
/*
 * Sign in usig BrowserID test
 * 
 * @link https://developer.mozilla.org/en/BrowserID/Quick_Setup
 */

session_start();

/**
 * Fire PHP
 */
require_once('FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);

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
                if ( res === "Assertion okay" ) {
                    loggedIn(res);
                    console.log("res: " + res + "| status: " + status + " xhr: " + xhr);
                } else {
                    // Fail or logout
                    // loggedOut();
                    console.log("loggedOut()");
                }
            },
            error : function (res, status, xhr) {
                alert("Login fel: " +  res);
                
            }
        });
    } else {
        // loggedOut();
        console.log("loggedOut()")
    }
}
// Runs if assertion has been verified as OK
// Whicj also means that $_SESSION has been set and DB updated with login data
function loggedIn(res) {
    console.log(res);
}
</script>