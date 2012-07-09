<?php
/*
 * Sign in usig BrowserID test
 * 
 * @link https://developer.mozilla.org/en/BrowserID/Quick_Setup
 * @link https://developer.mozilla.org/en/BrowserID/Advanced_Features
 * @author <gunther@keryx.se>
 */

session_start();

/**
 * All needed files
 */
require_once '../includes/loadfiles.php';

// user::setSessionData();

$note    = "";      // Message why page is shown
$ref     = "false"; // Where to be redirected if sign in is ok
$curuser = "";      // Information about possible logged in user (users may switch login)

if ( user::validate(user::LOGGEDIN) ) {
    $curuser  = '<p><strong>' . htmlspecialchars($_SESSION['user']) . "</strong> är inloggad</p>";
    $curuser .= "<ul><li>Logga in på nytt om du vill <strong>byta användare</strong>. (Främst för admins.)</li>";
    $curuser .= "<li><a href=\"edituser.php\">Redigera användardata</a> om du vill ansöka om högre behörighet.</li></ul>\n";
    if ( isset($_GET['nopriv']) ) {
        $note = "<h2>Sidan kräver högre nivå på din behörighet</h2>\n";
    }
} else {
    $note = "<h2>Sidan kräver inloggning</h2>\n";
}
if ( isset($_GET['ref']) ) {
    $ref  = '"' . htmlspecialchars($_GET['ref']) . '"';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - webbteknik.nu</title>
  <link rel="stylesheet" href="css/webbteknik-nu.css" />
  <link href='http://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
</head>
<body>
  <h1>webbteknik.nu &ndash; login</h1>
<?php
echo $note;
echo $curuser;
?>
  <p class="signinbox">
    <a href="#" id="browserid" title="Logga in med BrowserID">  
      <img src="img/sign-in-green.png" alt="Logga in">  
    </a>
  </p>
  <p>
    För att bevara din bekvämlighet, trygghet och integritet, så använder denna webbplats
    <a href="https://browserid.org/">BrowserID</a>.
  </p>
  <p>
    Om du inte redan har det, så kommer du först få skaffa just ett BrowserID-konto,
    när du klickar på logga in-knappen. BrowserID är ett
    <a lang="en" href="http://en.wikipedia.org/wiki/Single_sign-on">single sign on-system</a>.
    Det används för att <dfn>autentisera</dfn> dig som användare.
    Därefter kommer du få skapa ett användarkonto på den här specifika webbplatsen.
  </p>
  <p>
  </p>
  <hr class="todo" />
  <pre>
    TODO <strong>Capability detect</strong> det som krävs av webbläsaren
     - JavaScript enabled
     - strict mode support (non current browsers general fail)
     - classList
     - JSON
     - More ES 5.1 - perhaps
     - getElementsByClassName
     - querySelector
    
     - DOM 2 events
    
     - Canvas
     - SVG
     - HTML5 video
     - drag and drop
    
     - CSS transitions, transforms, 3D transforms, animation
     - gradients
     - media queries
  </pre>
  <script src="https://browserid.org/include.js" type="text/javascript"></script> 
  <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
  <script>
    "use strict";
    var ref = <?php echo $ref; ?>;
  </script>
  <script src="script/sign-in.js"></script>
</body>
</html>