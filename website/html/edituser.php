<?php
/**
 * Edit user data
 * 
 * @todo Pre-approved accounts
 * @todo Notification if changed by admin
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 */

session_start();
require_once '../includes/loadfiles.php';

user::requires(user::LOGGEDIN);

// Current privileges
$checked[1]  = "";
$checked[3]  = "";
$checked[7]  = "";
$checked[15] = "";
$checked[31] = "";

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

$userdata = $_SESSION['userdata'];
$tocagree = true;
if ( empty($userdata->user_since) ) {
    if ( !isset($_POST['tocagree'])) {
        // User has not agreed to TOC - show them
        // Agreing to TOC is a non Ajax function
        $tocagree = false;
    } else {
        // New user, that has agreed to TOC
        $stmt = $dbh->prepare('INSERT INTO users (email, user_since) VALUES (:email, NOW())');
        $stmt->bindParam(":email", $_SESSION['user']);
        $stmt->execute();
        $checked[1] = "checked";
        $_SESSION['userdata']->user_since = strftime("%F %H:%M:%s");
    }
} else {
    // Reload userdata to keep sync with DB if changed by admin
    // TODO - this is not DRY
    $stmt = $dbh->prepare(
        'SELECT email, firstname, lastname, privileges, user_since FROM users WHERE email = :email'
    );
    $stmt->bindParam(':email', $_SESSION['user']);
    $stmt->execute();
    $userdata = $stmt->fetch(PDO::FETCH_OBJ);
    $_SESSION['userdata'] = $userdata;
    $checked[$userdata->privileges] = "checked";
    //var_dump($userdata);
}
// If new name has been submitted
if ( isset($_POST['firstname']) ) {
    $names = filter_input_array(INPUT_POST, user::nameSanitizeRules());
    $names = filter_var_array($names, user::nameRules());
    if ( empty($names['firstname']) || empty($names['lastname']) ) {
        // Bad data
        $userdata->firstname = $names['firstname'] ? $names['firstname'] : "(Inte ok)";
        $userdata->lastname  = $names['lastname']  ? $names['lastname']  : "(Inte ok)";
    } else {
        $stmt = $dbh->prepare("UPDATE users SET firstname = :firstname, lastname = :lastname WHERE email = :email");
        $names['email'] = $_SESSION['user'];
        $stmt->execute($names);
        $_SESSION['userdata']->firstname = $names['firstname'];
        $_SESSION['userdata']->lastname  = $names['lastname'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Redigera din användare - webbteknik.nu</title>
  <link rel="stylesheet" href="css/webbteknik-nu.css" />
  <link href='http://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
</head>
<body>
  <h1>webbteknik.nu &ndash; Redigera din användare</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
<?php
if ( $tocagree ) :
?>
  <form action="edituser.php" method="post">
    <fieldset>
      <legend>Basfakta</legend>
      <p>
        På denna webbplats måste du använda ditt riktiga för- och efternamn.
        Läs mer på våra <a href="userterms.php" class="nonimplemented">användarvillkor</a>.
      </p>
      <p>
        <label for="firstname">Förnamn:</label>
        <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($userdata->firstname); ?>" required />
      </p>
      <p>
        <label for="lastname">Efternamn:</label>
        <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($userdata->lastname); ?>" required />
      </p>
      <p>
        <span class="labeldummysincecssalignmentisnearimpossible"></span>
        <input type="submit" value="Skicka" />
      </p>
    </fieldset>
  </form>
  <form action="edituser.php" method="post">
    <fieldset>
      <legend>Önskad åtkomst</legend>
<?php
    if ( user::validate(user::ADMIN) ):
?>
      <p>
        <strong>Du är redan administratör.</strong>
      </p>
<?php
    endif; // admin
?>
      <p>
        Har du en övningsbok, så ingår alla lärobokens privilegier.
      </p>
      <p>
        <input type="radio" name="priv" value="1" id="guest" <?php echo $checked[1]; ?>>
        <label for="guest">Inga privilegier alls</label>
      </p>
      <p>
        <input type="radio" name="priv" value="3" id="webonly" disabled class="nonimplemented" <?php echo $checked[3]; ?>>
        <label for="webonly">Bara webb</label>
      </p>
      <p>
        <input type="radio" name="priv" value="7" id="textbook" <?php echo $checked[7]; ?>>
        <label for="textbook">Lärobok</label>
      </p>
      <p>
        <input type="radio" name="priv" value="15" id="workbook" <?php echo $checked[15]; ?>>
        <label for="workbook">Övningsbok (kan ännu inte väljas)</label>
      </p>
      <p>
        <input type="radio" name="priv" value="31" id="teacher" <?php echo $checked[31]; ?>>
        <label for="teacher">Lärare (kan ännu inte väljas)</label>
      </p>
      <input type="hidden" name="origlevel" id="origlevel" value="<?php echo $userdata->privileges; ?>" />
    </fieldset>
  </form>
  <form action="edituser.php" method="post">
    <fieldset>
      <legend>Skola och undervisningsgrupp</legend>
      <p>
        Här kommer elever med arbetsbok kunna ansluta sig till en undervisningsgrupp.
        Lärare kommer kunna skapa undervisningsgrupper.
      </p>
    </fieldset>
  </form>
<?php
else: // tocagree - not - show TOC
?>
  <form action="edituser.php" method="post">
    <fieldset>
      <legend>Skapa användare</legend>
      <p>
        För att använda denna webbplats, så måste du gå med på våra användarvillkor.
      </p>
      <p>
        <input type="checkbox" id="tocagree" name="tocagree" value="1" required />
        <label for="tocagree">Jag godkänner villkoren</label>
      </p>
      <p>
        <input type="submit" value="Skapa användare" />
      </p>
    </fieldset>
    <fieldset>
      <legend>Användarvillkor</legend>
      <p>
        Lorem ipsum&hellip;
      </p>
      <p>
        I stora drag:
      </p>
      <ul>
        <li>När du köpt en bok, så får du tillgång till webbplatsen i ett år. (Skolor: Det räcker att köpa en ny övningsbok.)</li>
        <li>Du får inte kopiera materialet.</li>
        <li>Du får inte dela din inloggning med någon annan.</li>
        <li>Du får inte skapa flera inloggningar om du inte är admin eller lärare (som kan få ha ett konto där de låtsas vara elever).</li>
        <li>Du måste uppge ditt korrekta för- och efternamn. (Din email är ditt kontonamn, du behöver ingen <i>nick</i>.)</li>
      </ul>
    </fieldset>
  </form>
<?php
endif; // tocagree
?>
  <p><a href="./">Startsidan</a></p>
<?php
if ( user::validate(user::TEXTBOOK) ):
?>
  <p>
    <strong>Test: <a href="videos-test.php">Kolla filmer</strong>
  </p>
  <p><a href="statistik.php" class="nonimplemented">Statistiksidan &ndash; se hur långt du kommit</a></p>
<?php
endif;
if ( user::validate(user::TEACHER) ):
?>
    <p><a href="sign-in.php?ref=edituser.php">Logga in som en annan användare</a></p>
<?php
endif;
?>
  <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
  <script src="script/edituser.js"></script>
</body>
</html>
<!--
Om man går neråt: Vill du verkligen nedgradera...?
Automatisk nedgradering om man inte förnyar prenumerationen....
-->