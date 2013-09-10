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

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

// Current privileges
$current_privileges = acl::getList($_SESSION['user'], $dbh);

$userdata = $_SESSION['userdata'];
$tosagree = true;
if ( empty($userdata->user_since) ) {
    if ( !isset($_POST['tosagree'])) {
        // User has not agreed to tos - show them
        // Agreing to tos is a non Ajax function
        $tosagree = false;
    } else {
        // New user, that has agreed to tos
        $stmt = $dbh->prepare('INSERT INTO users (email, user_since) VALUES (:email, NOW())');
        $stmt->bindParam(":email", $_SESSION['user']);
        $stmt->execute();

        // FIXME next line
        $checked[1] = "checked";

        $userdata->user_since             = strftime("%F %H:%M:%s");
        $_SESSION['userdata']->user_since = $userdata->user_since;
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

    // FIXME next line
    $checked[$userdata->privileges] = "checked";
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

// Quic and dirty test to see if name is in DB
$db_name_set = false;
if ( $userdata->firstname ) {
    $db_name_set = true;
}
// HTML safe - move to class
$first_name = $userdata->firstname;
$last_name  = $userdata->lastname;

// What books have privilege questions?
$stmt = $dbh->query(<<<SQL
      SELECT DISTINCT pq.bookID, bk.booktitle FROM privilege_questions AS pq
      NATURAL JOIN books AS bk
      ORDER BY bk.courseID DESC
SQL
);
$books = $stmt->fetchAll();

$hasalready['wu1'] = "";
$hasalready['ws1'] = "";

// Preparing for mod_rewrite, set base-element
// TODO: Make this generic!
$baseref = dirname(htmlspecialchars($_SERVER['SCRIPT_NAME'])) . "/";
if ( "//" == $baseref ) {
    $baseref = "/";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Redigera din användare - webbteknik.nu</title>
  <?php require "../includes/snippets/dochead.php"; ?>
</head>
<body>
  <h1>webbteknik.nu &ndash; Redigera din användare</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
<?php
// Only show terms of service in not agreed upon
if ( !$tosagree ) :
    echo <<<TOS
  <form action="edituser.php" method="post" class="nonajax">
    <fieldset>
      <legend>Skapa användare</legend>
      <p>
        För att använda denna webbplats, så måste du gå med på våra användarvillkor.
      </p>
      <p>
        <input type="checkbox" id="tosagree" name="tosagree" value="1" required />
        <label for="tosagree">Jag godkänner villkoren</label>
      </p>
      <p>
        <input type="submit" value="Skapa användare" />
      </p>
    </fieldset>
    <fieldset>
      <legend>Användarvillkor</legend>
      <p>
        denna avdelning är inte helt klar ännu&hellip;
      </p>
      <p>
        I stora drag:
      </p>
      <ul>
        <li>När du köpt en bok, så får du tillgång till webbplatsen i ett år.
          (Skolor: Det räcker att köpa en ny övningsbok.)</li>
        <li>Du får inte kopiera materialet.</li>
        <li>Du får inte dela din inloggning med någon annan.</li>
        <li>Du får inte skapa flera inloggningar om du inte är admin eller lärare
          (som kan få ha ett konto där de låtsas vara elever).</li>
        <li>Du måste uppge ditt korrekta för- och efternamn. (Din email är ditt kontonamn,
          du behöver ingen <i>nick</i>.)</li>
      </ul>
    </fieldset>
  </form>
TOS;

else:
    // show all other forms if TOS-agreement has been made
    echo <<<BASEFACTS
 
  <form action="edituser.php" method="post">
    <fieldset>
      <legend>Basfakta</legend>
      <p>
        På denna webbplats måste du använda ditt riktiga för- och efternamn.
        Läs mer på våra <a href="userterms.php" class="nonimplemented">användarvillkor</a>.
      </p>
      <p>
        <label for="firstname">Förnamn:</label>
        <input type="text" id="firstname" name="firstname" 
               value="{$first_name}" required />
      </p>
      <p>
        <label for="lastname">Efternamn:</label>
        <input type="text" id="lastname" name="lastname"
               value="{$last_name}" required />
      </p>
	  <p>
	    Kommer snart: Kontonamn på Github och JSBin (frivilliga uppgifter).
	  </p>
      <p>
        <span class="labeldummysincecssalignmentisnearimpossible"></span>
        <input type="submit" value="Skicka" />
      </p>
    </fieldset>
  </form>
BASEFACTS;

    if ( $db_name_set ):

        echo <<<EDITUSERFORMSTART
        
  <form action="edituser.php#privileges" method="post" id="privileges">
    <fieldset>
      <legend>Önskade privilegier</legend>

EDITUSERFORMSTART;

      echo <<<MAYBECODE

      <div id="maybe_got_group_code" class="subfield blocklabels">
        <p>
          <input type="radio" id="group_code_set_yes" name="group_code_set">
          <label for="group_code_set_yes">Jag har en inbjudningskod till en grupp</label>
        </p>
        <p>
          <input type="radio" id="group_code_set_no" name="group_code_set">
          <label for="group_code_set_no">Jag har inte någon inbjudningskod</label>
        </p>
        <p id="group_code_yes">
           <label for="my_group_id">Inbjudningskod till en grupp <strong></strong></label>
           <input type="text" id="my_group_id" name="my_group_id" value="" 
                  pattern="[0-9a-z]{5}" maxlength="5" required />
           <input type="submit" value="Anslut mig till gruppen" disabled />
        </p>
      </div>

MAYBECODE;
      echo <<<GNOSTART
      <div id="group_code_no" class="subfield">

GNOSTART;
      // nested if 3d level
      if ( user::validate(user::ADMIN) ):
          echo <<<ADMINYOU
      <p>
        <strong>Du är redan administratör!</strong>
      </p>

ADMINYOU;
      elseif ( user::validate(user::TEACHER) ):
          echo <<<TEACHERYOU
      <p>
        <strong>Du har redan lärabehörighet!</strong>
      </p>

TEACHERYOU;
       else:
           echo <<<QUESTION
        <p>
          <b>Vad vill du få tillgång till?</b>
        </p>

QUESTION;
           foreach ( $books as $bk ) :
               if ( in_array($bk['bookID'], $current_privileges) ) {
               	   echo <<<TEXT
        <p>
          Du har tillgång till <i>{$bk['booktitle']}</i>.
        </p>

TEXT;
               } else {
                   echo <<<INPUT
        <p>
          <input type="checkbox" name="bookID" value="{$bk['bookID']}" id="textbook_{$bk['bookID']}" autocomplete="off" />
          <label for="textbook_{$bk['bookID']}">{$bk['booktitle']}</label>
        </p>

INPUT;
               }
          endforeach;
?>
        <p>
          Lärarbehörighet (inkluderar båda böckerna): Mejla gunther at keryx punkt se.</strong>
        </p>
        <input type="hidden" name="origlevel" id="origlevel" value="<?php echo $userdata->privileges; ?>" />
      </div>
    </fieldset>
  </form>

<?php
      endif; // admin or teacher
    endif; // db_name_set
endif; // show all other forms
?>

  <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
  <script src="script/edituser.js"></script>
</body>
</html>
