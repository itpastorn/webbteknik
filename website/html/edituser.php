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

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

user::setSessionData();
user::requires(user::LOGGEDIN);

$current_privileges = array(); // Set by reference on next line
// No redirect since user already is on the page to chose a book => 3d param = false
$currentbook = acl::currentBookChoice($dbh, $current_privileges, false);

// Higlight form if user has not chosen what book to work with
$choosebook = '';
$chooseinfo = '';
if ( filter_has_var(INPUT_GET, 'choosebook') ) {
    $choosebook = ' class="yellowfade"';
    $chooseinfo = '<p><strong>Du måste välja vilken bok du vill jobba med innan du går vidare.</strong></p>';
}

require "data/books.php";
// All textbooks in the DB
$allbooks = data_books::loadAll($dbh);

// The books the user already has access to
$userbooks = array_intersect_key($allbooks, array_flip($current_privileges));

// The books the user may want access to
$missingbooks = array_diff_key($allbooks, array_flip($current_privileges));

// What books have privilege questions?
$stmt = $dbh->query(<<<SQL
      SELECT DISTINCT pq.bookID, bk.booktitle FROM privilege_questions AS pq
      NATURAL JOIN books AS bk
      ORDER BY bk.courseID DESC
SQL
);
$questionbooks = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$missingbooks  = array_intersect_key($missingbooks, $questionbooks);


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

$bookchoice = ''; // Enable highlight when choice has been made as visual feedback
if ( filter_has_var(INPUT_POST, 'bookchoice') ) {
    $bookchoice = filter_input(
        INPUT_POST, 'bookchoice', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH
    );
    if ( !array_key_exists($bookchoice, $userbooks) ) {
        trigger_error("Manipulation attempt - user has not got the rights to that book", E_USER_ERROR);
    }
    $sql  = "UPDATE users SET currentbook = :bookchoice WHERE email = :email";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":bookchoice", $bookchoice);
    $stmt->bindParam(":email", $_SESSION['user']);
    $is_ok = $stmt->execute();
    if ( $is_ok ) {
        $_SESSION['currentbook'] = $currentbook = $bookchoice;
        $GLOBALS['FIREPHP']->log('Currentbook set to: ' . $_SESSION['currentbook']);
        $chooseinfo = '<p class="greenfade">Bokval gjort. Navigera vidare i menyn.</p>';
    } else {
        $GLOBALS['FIREPHP']->log($is_ok . ' Currentbook could not be set. Tried "' . $bookchoice . '" for ' . $_SESSION['user']);
        $chooseinfo = '<p class="error yellowfade">Uppdatering misslyckades. Kontakta admin.</p>';
        $bookchoice = 'FAIL';
    }
}

// Quick and dirty test to see if name is in DB
$db_name_set = false;
if ( $userdata->firstname ) {
    $db_name_set = true;
}
// HTML safe - move to class
$first_name = $userdata->firstname;
$last_name  = $userdata->lastname;

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
<?php
require "../includes/snippets/mainmenu.php";

echo <<<BOOKCHOICE
  <form action="edituser/" method="post">
    <fieldset{$choosebook}>
      <legend>Välj bok att jobba med</legend>
      $chooseinfo
      <div class="explanation">
        Här väljer du vilken bok som du vill jobba med, vars videos, uppgifter, länkar, etc. kommer att visas.
      </div>

BOOKCHOICE;
if ( count($userbooks) > 1 ):
    foreach ( $userbooks as $bk ) :
        $checked = '';
        if ( $bk->id === $currentbook ) {
            $checked = "checked";
        }
        // Visual feedback when making a new choice
        $update_feedback = '';
        if ( $bk->id === $bookchoice ) {
             $update_feedback = ' class="greenfade"';
        }
        echo <<<TEXT
      <p{$update_feedback}>
        <input type="radio" name="bookchoice" value="{$bk->id}" id="bc_{$bk->id}" required $checked />
        <label for="bc_{$bk->id}">{$bk->name}</lable>.
      </p>

TEXT;
  endforeach;
  echo <<<BOOKCHOICE
      <p>
        <input type="submit" value="Välj bok" />
      </p>

BOOKCHOICE;
elseif ( count($userbooks) == 1 ):
  $bk = current($userbooks);
  echo <<<BOOKCHOICE
      <p>
        Du kan för närvarande bara jobba med boken <i>{$bk->name}</i>.
        Vill du ha tillgång till en annan bok så <a href="./edituser/#privileges">ansök om privilegier nedan</a>.
      </p>

BOOKCHOICE;
else:
  echo <<<BOOKCHOICE
      <p>
        Du kan för närvarande inte jobba med någon bok. Ansök om privilegier nedan.
      </p>

BOOKCHOICE;
endif;

echo <<<BOOKCHOICE
    </fieldset>
  </form>

BOOKCHOICE;


// Only show terms of service in not agreed upon
if ( !$tosagree ) :
    echo <<<TOS
  <form action="edituser/" method="post" class="nonajax">
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
 
  <form action="edituser/" method="post">
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
      <!--p>
        Kommer snart: Kontonamn på Github och JSBin (frivilliga uppgifter).
      </p-->
      <p>
        <span class="labeldummysincecssalignmentisnearimpossible"></span>
        <input type="submit" value="Skicka" />
      </p>
    </fieldset>
  </form>
BASEFACTS;

    if ( $db_name_set ):

        echo <<<EDITUSERFORMSTART
        
  <form action="edituser/#privileges" method="post" id="privileges">
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

           foreach ( $userbooks as $bk ) :
               echo <<<TEXT
                 <p>
                   Du har tillgång till <i>{$bk->name}</i>.
                 </p>

TEXT;
           endforeach;

           foreach ( $missingbooks as $bk ) :
               echo <<<INPUT
                 <p>
                   <input type="checkbox" name="bookID" value="{$bk->id}" id="textbook_{$bk->id}" autocomplete="off" />
                   <label for="textbook_{$bk->id}">{$bk->name}</label>
                 </p>

INPUT;
          endforeach;
?>
        <p>
          Lärarbehörighet (inkluderar alla böcker): Mejla gunther at keryx punkt se.</strong>
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

  <script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
  <script src="script/edituser.js"></script>
</body>
</html>
