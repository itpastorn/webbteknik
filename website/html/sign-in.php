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
    $curuser  = '<p class="important greenfade"><strong>' . htmlspecialchars($_SESSION['user']) . "</strong> är inloggad</p>";
    $curuser .= "<ul><li>Logga in på nytt om du vill <strong>byta användare</strong>. (Främst för admins.)</li></ul>";
    if ( isset($_GET['nopriv']) ) {
        $note  = "<h2>Du har inte behörighet att gå till sidan</h2>\n";
        $note .= "<p>Det kan bero på att du inte har registrerat bokinköp " .
        		 "eller anslutit dig till en undervisningsgrupp.\n";
        $note .= "Det gör du genom att <a href=\"edituser.php\">redigera dina användardata</a>.</p>\n";
    }
    $signinheader = "Byt användare";
} else {
    $note = "<h2>Sidan kräver inloggning</h2>\n";
    $signinheader = "Logga in";
}
if ( isset($_GET['ref']) ) {
    $ref  = '"' . htmlspecialchars($_GET['ref']) . '"';
}

// Preparing for mod_rewrite, set base-element
// TODO: Make this generic!
$baseref = dirname(htmlspecialchars($_SERVER['SCRIPT_NAME'])) . "/";
if ( "//" == $baseref ) {
    $baseref = "/";
}
//   <base href="<?php echo $baseref; ? >" />

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - webbteknik.nu</title>
  <link rel="stylesheet" href="css/webbteknik-nu.css" />
  <link rel="stylesheet" href="css/persona.css" />
  <link href='http://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
</head>
<body>
  <h1>webbteknik.nu &ndash; login</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
<?php
echo $note;
echo $curuser;
?>
  <h2><?php echo $signinheader; ?></h2>
  <p class="signinbox">
    Testar din webbläsare. Var god vänta.
  </p>
  <p>
    För din trygghet och integritet, så använder denna webbplats
    <a href="https://browserid.org/">Persona (BrowserID)</a>.
  </p>
  <p>Har du ett BrowserID-konto? Annars, så kommer du först få skaffa det.</p>
  <ol>
    <li>Du skickas i så fall dit när du klickar på logga in-knappen (sign in) ovan.</li>
    <li>Därefter måste du kontrollera din email för en bekräftelse.</li>
    <li>När du gjort det måste du klicka på sign-in knappen ovan en gång till.</li>
  </ol>
  <p>
    Persona (tidigate kallat BrowserID) är ett
    <a lang="en" href="http://en.wikipedia.org/wiki/Single_sign-on">single sign on-system</a>.
    Det används för att <dfn>autentisera</dfn> dig som användare. I framtiden kommer du förhoppningsvis
    få nytta av ditt Persona-konto på fler webbplatser än denna.
  </p>
  <p>
    Efter att ha loggat in, så måste du redigera dina användaruppgifter på den här specifika webbplatsen.
    <em>Om det är första gången</em> du använder Persona, så kommer du alltså behöva
    utföra båda stegen.
  </p>
  <h2>Instruktionsvideo</h2>
  <video class="halfsize" controls tabindex="0">
    <source type="video/webm" src="media/registrera-konto.webm"></source>
    <source type="video/mp4" src="media/registrera-konto.mp4"></source>
  </video>
  <hr class="todo" />
  <h2 id="required_tech_section">Tekniska krav</h2>
  <p>
    På den här webbplatsen används flera nya tekniker. Här ser du om din webbläsare stödjer dem.
  </p>
  <p id="nojsatall">
    Först och främst måste du aktivera JavaScript. Utan det kan du varken logga in eller gör något
    annat på den här webbplatsen. Dessutom måste din webbläsare stödja alla tekniker i tabellen nedan.
  </p>
  <table id="required_tech" class="techtests">
    <caption>Nödvändiga tekniker</caption>
    <tr>
      <th>Teknik</th>
      <th>Stöd</th>
    </tr>
  </table>
  <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
  <script src="script/featuredetect.js"></script>
  <script src="https://login.persona.org/include.js" type="text/javascript"></script> 
  <script>
    "use strict";
    var ref = <?php echo $ref; ?>;
  </script>
  <script src="script/sign-in.js"></script>
</body>
</html>