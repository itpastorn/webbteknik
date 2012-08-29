<?php
/**
 * Teacher start page, requires log in
 * 
 * This is the page controler for the course-administration page
 * 
 * 
 * Workflow
 *  1. Admin adds school (name, number of books bought, etc)
 *  2. Teacher@school associates himself to that school
 *  3. Teacher may create/edit groups - max number of students in all groups per year = # of books bought
 *  4. System generates code to share with students
 *  5. Students enter code and join the group, no need to answer questions
 * 
 * @todo Future: Handle mismatch between number of bought textbooks vs number of bought workbooks
 * 
 * @todo Tomorrow: Add schools and number of bought books from publisher / EDIT new school submissions
 * @todo Tomorrow; ACL to videos based on privileges and what book it is related to
 * 
 * @todo Now: GET-param with group code = prefill form and scroll to it
 * 
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

session_start();
require_once '../includes/loadfiles.php';

$FIREPHP->dump('_POST', $_POST);

/**
 * Groups - the main object type on this page!
 */
require "data/groups.php";

/**
 * Courses - available courses shall be listed
 */
require "data/courses.php";

/**
 * Schools - Where the teacher works
 */
require "data/schools.php";

user::setSessionData();

user::requires(user::TEACHER);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

// TODO A dispatch class that loads appropriate modules

// TODO Always use filter_has_var() ...

// TODO Show groups

// Variables for group add/modify form
$g_group_id           = ''; // For update - not yet implemented
$g_group_id_msg       = '';
$g_new_group_save_msg = '';
if ( filter_has_var(INPUT_POST, 'admingroup_form_submitted') ) {
	

    $g_group_id = trim(filter_input(INPUT_POST, 'g_group_id', FILTER_SANITIZE_STRIPPED, FILTER_FLAG_STRIP_LOW));
    if ( $g_group_id ) {
        if ( data_groups::isExistingId($g_course_id, $dbh) ) {
            // Updating existing group
            trigger_error("Can not update existing group. Not implemented yet.", E_USER_WARNING);
            $g_group_id_msg("Kan ännu inte uppdatera grupper. Funktionen ej implementerad.");
            
            // Remember to keep groupStartDate away from UPDATE
            // and not show it in form when editin existing group
            
        }
        // Bad groupID
        trigger_error("No such groupID in DB.", E_USER_WARNING);
        $g_group_id_msg("Felaktig grupp angiven, gruppen finns inte");
    } else {
        $g_group_id = ''; // Reset to string type
    }

    $g_school_id = trim(filter_input(INPUT_POST, 'g_school_id', FILTER_SANITIZE_STRIPPED, FILTER_FLAG_STRIP_LOW));
    // Extract schoolID from longer string
    $g_school_id = data_schools::checkSchoolId($g_school_id, true);


    $new_group = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);
    
    // HTML and DB use one naming scheme, Classes another (they both make sense in their context)
    $new_group['id']             = $g_group_id;
    $new_group['schoolID']       = $g_school_id;
    $new_group['courseID']       = $new_group['g_course_id'];
    $new_group['name']           = $new_group['g_group_nickname'];
    $new_group['groupMaxSize']   = $new_group['g_group_max_size'];
    $new_group['groupStartDate'] = $new_group['g_group_start_date'];
    $new_group['groupUrl']       = $new_group['g_group_url'];
    
    $new_group = data_groups::fromArray($new_group);
    
    // TODO: The following if-section is repeated verbatim to 95 % below - REmove this code duplication!
    if ( $new_group->isErrorFree() ) {
        $FIREPHP->log("Was error free");
        try {
            $new_group_saved = $new_group->save($dbh);
            if ( $new_group_saved ) {
            	// Small difference in this string, from the following (no link)
                $g_new_group_save_msg = <<<HTML
                    <p class="greenfade">Databasen uppdaterad.
                    <strong>Kursens inbjudningskod: <strong>{$new_group->getId()}</strong></strong>
HTML;
            } else {
                throw new Exception('Failed save, but no exception.');
            }
        }
        catch( Exception $e ) {
            $g_new_group_save_msg = $e->getMessage();
            $FIREPHP->log($g_new_group_save_msg);
            // Re-use variable for end user
            $g_new_group_save_msg = "<p class=\"errormsg\">Det gick inte att spara gruppen i databasen. Felorsak:\n";
            if ( $e->getCode() == 23000 ) {
                // Duplicate entry in PDO-MySQL
                $g_new_group_save_msg .= "Det finns redan en grupp med det namnet, med samma startdatum.</p>\n";
            } else {
                $g_new_group_save_msg .= "Okänt databasfel. Vänligen kontakta administratören.</p>\n";
            }
        }
    } else {
        // Could not generate an error free object
        $g_new_group_save_msg = "<p class=\"errormsg\">Det gick inte att spara gruppen i databasen.</p>\n";
        
    }
} else {
    $new_group = data_groups::fake();
}
$FIREPHP->log($new_group);

// TODO Pre-select in SELECT-lists if form has been submitted

// TODO Edit group

// TODO Workplaces (This is done - keeping todo as label)
$new_workplace_save_msg = "";
if ( filter_has_var(INPUT_POST, 'new_workplace_added') ) {

    $working_at = trim(filter_input(INPUT_POST, 's_school_id', FILTER_SANITIZE_STRIPPED, FILTER_FLAG_STRIP_LOW));
    if ( empty($working_at) ) {
        // Perhaps an old browser that used the select list?
        $working_at = trim(filter_input(INPUT_POST, 's_school_sel', FILTER_SANITIZE_STRIPPED, FILTER_FLAG_STRIP_LOW));
    }
    $working_at = data_schools::checkSchoolId($working_at, true);
    // If there is such a school we should be able to instantiate the object
    $working_at = data_schools::loadOne($working_at, $dbh);
    if ( $working_at ) {
        // School exists, lets add teacher as worker at that place
        $sql = <<<SQL
            INSERT INTO workplaces (schoolID, email, since) VALUES
            (:schoolID, :email, NOW())
SQL;
        try {
            $stmt          = $dbh->prepare($sql);
            $working_at_id = $working_at->getID();
            $stmt->bindParam(':schoolID', $working_at_id);
            $stmt->bindParam(':email', $_SESSION['user']);
            $stmt->execute();
            // Set a data attribute that equals values in select list
            $new_workplace_save_msg = <<<HTML
                <p class="greenfade">Arbetsplats sparad.
                  <a data-schoolinfostring="{$working_at->getName()},{$working_at->getPlace()} ({$working_at_id})"
                     id="goto_create_group"></a>
                </p>
HTML;
        }
        catch (Exception $e) {
            if ( $e->getCode() == 23000 ) {
            $new_workplace_save_msg = <<<HTML
                <p class="errormsg">Uppgifterna finns redan i databasen.</p>
HTML;
            } else {
                $FIREPHP->log("Databasfel i ". __FILE__ . " på rad " . __LINE__ . ": " .$e->getMessage());
                $new_workplace_save_msg = <<<HTML
                    <p class="errormsg">Uppgifterna kunde inte lagras på grund av databasfel.
                      Kontakta administratören.</p>
HTML;
            }    
        }
    } else {
        $new_workplace_save_msg = "<p class=\"errormsg\">Felaktig skol-id skickad. Använd förslagen!</p>";
    }
}

$new_school_save_msg = "";
if ( filter_has_var(INPUT_POST, 'new_school_school_added') ) {
    // TODO Add support for UPDTATING by setting id as a hidden form field
    $new_school = array(
        'id'          => false,
        'name'        => trim(filter_input(INPUT_POST, 'new_school_school_name', FILTER_UNSAFE_RAW)),
        'schoolPlace' => trim(filter_input(INPUT_POST, 'new_school_school_place', FILTER_UNSAFE_RAW)),
        'schoolUrl'   => trim(filter_input(INPUT_POST, 'new_school_school_url', FILTER_UNSAFE_RAW))
    );
    // More filtering and validation in class method
    $new_school = data_schools::fromArray($new_school);
    if ( $new_school->isErrorFree() ) {
        try {
            $new_school_saved = $new_school->save($dbh);
            if ( $new_school_saved ) {
                $new_school_save_msg = <<<HTML
                    <p class="greenfade">Databasen uppdaterad.
                    <a data-schoolID="{$new_school->getId()}" id="add_as_workplace"></a></p>
HTML;
            } else {
                throw new Exception('Failed save, but no exception.');
            }
        }
        catch( Exception $e ) {
            $new_school_save_msg = $e->getMessage();
            $FIREPHP->log($new_school_save_msg);
            // Re-use variable for end user
            $new_school_save_msg = "<p class=\"errormsg\">Det gick inte att spara skolan i databasen. Felorsak:\n";
            if ( $e->getCode() == 23000 ) {
                // Duplicate entry in PDO-MySQL
                $new_school_save_msg .= "Det finns redan en skola på den platsen med det namnet.</p>\n";
            } else {
                $new_school_save_msg .= "okänt databasfel. Vänligen kontakta administratören.</p>\n";
            }
        }
    } else {
        // Could not generate an error free object, probably the id failing
        $new_school_save_msg = "<p class=\"errormsg\">Det gick inte att spara skolan i databasen. Felorsak:\n";
        $new_school_save_msg .= "Kunde inte skapa ett felfritt skolobjekt. Vänligen kontakta administratören.</p>\n";
    }
} else {
    $new_school = data_schools::fake();
}

// TODO Tomorrow, make it possible to edit school information, if:
// You're affiliated to that school or you're an admin


// SQL injection safe

// Normal/always page view data
$all_courses   = data_courses::loadAll($dbh);
$pre_select_g_gourse = ( $new_group->courseID ) ?: 'WEBWEU01';
$g_course = makeSelectElement($all_courses, $pre_select_g_gourse);

$all_schools   = data_schools::loadAll($dbh);
$select_school = makeSelectElement($all_schools, "", true);
// TODO Filter out schools where the teacher already works


// Schools where the user works
$sql = <<<SQL
    SELECT schoolID AS id, school_name AS name, school_place AS schoolPlace, school_url AS schoolUrl
    FROM schools
    INNER JOIN workplaces USING ( schoolID)
    WHERE workplaces.email = :email
    ORDER BY name
SQL;
$workplaces = data_schools::loadAll($dbh, $sql, array('email' => $_SESSION['user']));

// TODO Pre-select school according to form entry from last subbmit
$g_schools  = makeSelectElement($workplaces, "", true, array('id' => 'null', 'name' => 'Ej i listan/lägg till'));
$wp_list    = makeListItems($workplaces, "current_workplaces");


// TODO Historical groups

$sql = data_groups::SELECT_SQL . <<<SQL
    INNER JOIN teaching_groups AS tg ON (groups.groupID = tg.groupID)
    WHERE tg.email = :email
SQL;
$params = array(':email' => $_SESSION['user']);
$cur_groups = data_groups::loadALL($dbh, $sql, $params);


// TODO Add another teacher option

// Preparing for mod_rewrite, set base-element
// TODO: Make this generic!
$baseref = dirname(htmlspecialchars($_SERVER['SCRIPT_NAME'])) . "/";
if ( "//" == $baseref ) {
    $baseref = "/";
}
// This page name
$pageref = 'teacherpage.php';


?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Lärarsida - webbteknik.nu</title>
  <base href="<?php echo $baseref; ?>" />
  <link rel="stylesheet" href="css/webbteknik-nu.css" />
  <link href='http://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
</head>
<body class="wide">
  <h1>Lärarsida - webbteknik.nu</h1>
  <?php 
    require "../includes/snippets/mainmenu.php";
    echo <<<SECNAV
  <ul class="secondarynav">
    <li><a href="{$pageref}#mygroups">Mina grupper</a></li>
    <li><a href="{$pageref}#admingroup">Skapa grupp</a></li>
    <li><a href="{$pageref}#myschools">Skolanslutning</a></li>
  </ul>
SECNAV;
?>
  <h2 id="mygroups">Läxhjälpen</h2>
  <p>
    Här finns <a href="laxhjalpen-demowebb/">Läxhjälpen &ndash; bokens demowebbplats</a>.
  </p>
  <h2 id="mygroups">Mina grupper</h2>
<?php
foreach ($cur_groups as $cgroup ):
    echo <<<CGROUP
  <h3>{$cgroup->getName()} ({$cgroup->getId()}) med {$cgroup->numStudents} anslutna elever</h3>
  <p>
    <strong>Vad som kommer:</strong> Statistik om gruppen som helhet. Min/max/medel i antal gjorda uppgifter.
  </p>
  <ul>
    <li>Visa gruppens framsteg</li>
    <li>Redigera medlemslistan</li>
    <li>Redigera gruppdata</li>
  </ul>
  <p class="unobtrusive">
    Gruppen startade {$cgroup->groupStartDate}
  </p>  

CGROUP;
endforeach;
?>
  <h2 id="admingroup">Skapa ny grupp/redigera grupp</h2>
  <?php 
  if ( empty($workplaces) ):
      echo "<p>Du kan inte skapa grupper ännu, eftersom du inte angivit någon skola där du jobbar.</p>";
  else:
  echo <<<FORMCONTENTS1
  <form method="post" action="{$pageref}#admingroup_form" id="admingroup_form">
    <fieldset class="blocklabels">
      <legend>Allmän information</legend>
      <!-- g_group_id and g_group_id_msg must exist in order to modify existing group -->
      {$g_new_group_save_msg}
      <p>
        <label for="g_school_id">
          Skola<strong class="errormsg">{$new_group->errorMessage('schoolID', true)}</strong>
        </label>
        <select name="g_school_id" id="g_school_id">
          {$g_schools}
        </select>
      </p>
      <p>
        <label for="g_course_id">
          Kurs<strong class="errormsg">{$new_group->errorMessage('courseID', true)}</strong>
        </label>
        <select name="g_course_id" id="g_course_id">
          {$g_course}
        </select>
      </p>
      <p>
        <label for="g_group_nickname">
          Smeknamn på gruppen (Hur ni pratar om gruppen i dagligt tal, inga mellanslag)
          <strong class="errormsg">{$new_group->errorMessage('name', true)}</strong>
        </label>
        <input type="text" id="g_group_nickname" name="g_group_nickname" value="{$new_group->getName()}"
               placeholder="Exempel: Webbettan-13" required />
      </p>
    </fieldset>
    <fieldset class="blocklabels">
      <legend>Gruppinformation</legend>
      <p>
        <!-- Should be limited to the number of books bought for one year (with some grace overlap)-->
        <label for="g_group_max_size">
          Max antal elever i gruppen
          <strong class="errormsg">{$new_group->errorMessage('groupMaxSize', true)}</strong>
        </label>
        <input type="number" id="g_group_max_size" name="g_group_max_size" value="{$new_group->groupMaxSize}" required />
      </p>
      <p>
        <label for="g_group_start_date">
          Kursstart (gruppens livslängd är 12 månader)
          <strong class="errormsg">{$new_group->errorMessage('groupStartDate', true)}</strong>
        </label>
        <input type="date" id="g_group_start_date" name="g_group_start_date" value="{$new_group->groupStartDate}" required />
      </p>
      <p>
        <em>OBS! Man kan aldrig sätta startdatum längre in i framtiden än vad det sattes första gången.</em>
      </p>
      <p>
        <label for="g_group_url">
          Länk till eventuell kurssida
          <strong class="errormsg">{$new_group->errorMessage('groupUrl', true)}</strong>
        </label>
        <input type="url" id="g_group_url" name="g_group_url" value="{$new_group->getUrl()}"
               placeholder="Länk till kursens webbplats på din skola" />
      </p>
      <p>
        <em>OBS! Man kan aldrig sätta startdatum längre in i framtiden än vad det sattes första gången.</em>
      </p>
    </fieldset>
    <fieldset>
      <legend>Skicka gruppdata</legend>
      <p>
        <input type="hidden" name="admingroup_form_submitted" value="yes" />
        <input type="submit" value="Skicka" />
      </p>
    </fieldset>
  </form>

FORMCONTENTS1;
endif;
    echo <<<FORMCONTENTS2
  <h2 id="myschools">Mina skolor</h2>
  <form action="{$pageref}#my_schools_form" method="post" id="my_schools_form">
    <fieldset class="blocklabels">
      <legend>Skolor där du jobbar</legend>
      <p>
        Du är registrerade som lärare på följande skolor:
      </p>
      <ul>
        {$wp_list}
      </ul>
      <p>
        Just nu kan endast admin ändra på skolors namn, eller ta bort en lärare från en skola.
        Har ni gjort en liten felstavning, så registrera ingen ny skola. Det kan åtgärdas i sinom tid.
      </p>
    </fieldset>
  </form>
  <form action="{$pageref}#add_workplace_form" method="post" id="add_workplace_form">
    <fieldset class="blocklabels">
      <legend>Lägg till ny arbetsplats</legend>
      {$new_workplace_save_msg}
      <p>
        <label for="s_school_id">Lägg till plats där du jobbar
          (du måste använda ett färdigt förslag i detta fält)</label>
        <input type="text" name="s_school_id" id="s_school_id" list="s_school_list" required autocomplete="off" /> 
          <datalist id="s_school_list">
            <select name="s_school_sel">
              {$select_school}
            </select>
        </datalist>
      </p>
      <p>
        <input type="checkbox" id="s_new_school" name="s_new_school" value="yes" /> 
        <label for="s_new_school">Skolan finns inte med i listan (markeras denna kan du lägga till den)</label>
      </p>
      <p>
        <input type="hidden" name="new_workplace_added" value="yes" />
        <input type="submit" value="Skicka" />
      </p>
    </fieldset>
  </form>
  <form action="{$pageref}#new_school_form" method="post" id="new_school_form">
    <fieldset class="blocklabels">
      <legend>Lägg till ny skola eller annan arbetsplats i systemet</legend>
      {$new_school_save_msg}
      <p>
        <label for="new_school_school_name">
          Namn på skolan
          <strong class="errormsg">{$new_school->errorMessage('name', true)}</strong>
        </label>
        <input type="text"
               placeholder="Ex. Jurtaskolan" 
               value="{$new_school->getName()}"
               {$new_school->isError('name', true)}
               name="new_school_school_name"
               id="new_school_school_name"
               maxlength="99" required />
      </p>
      <p>
        <label for="new_school_school_place">
          Ort eller annan plats för skolan
          <strong class="errormsg">{$new_school->errorMessage('schoolPlace', true)}</strong>
        </label>
        <input type="text"
               placeholder="Västra utmarken" 
               value="{$new_school->getPlace()}"
               {$new_school->isError('schoolPlace', true)}
               name="new_school_school_place"
               id="new_school_school_place"
               maxlength="49" required />
      </p>
      <p>
        <label for="new_school_school_url">
          Skolans webbplats
          <strong class="errormsg">{$new_school->errorMessage('schoolUrl', true)}</strong>
        </label>
        <input type="url" 
               placeholder="http://example.com/" 
               value="{$new_school->getUrl()}"
               {$new_school->isError('schoolUrl', true)}
               name="new_school_school_url"
               id="new_school_school_url"
               maxlength="99" />
      </p>
      <p>
        <input type="hidden" name="new_school_school_added" value="yes" />
        <input type="submit" value="Skicka" />
      </p>
      <p>
        <del>Adminstratörer måste godkänna att nya skolor läggs till. Detta kan ta ett par dagar.</del>
        <big>Just nu godkänns alla skolor direkt!</big>
        Använd <a href="contact.php" class="nonimplemented">kontaktformuläret</a> om inget hänt inom 48 timmar.
      </p>
FORMCONTENTS2;
?>
    </fieldset>
  </form>
  <?php require "../includes/snippets/footer.php"; ?>
  <script>
(function (win, doc, undefined) {  
    if ( win.location.hash !== "#new_school_form") {
        $("#new_school_form").hide().data("hidden", "hidden");
        // Remove checked set by browser history
        $("#s_new_school").removeAttr("checked");
    }
    
    $("#s_new_school").on("click", function () {
        if ( $(this).attr("checked") ) {
            $("#new_school_form").data("hidden" ,"").show().get()[0].scrollIntoView(false);
            $("#new_school_school_name").focus();
        } else {
            $("#new_school_form").data("hidden" ,"hidden").hide();
        }
    });
    
    $("#g_school").on("change", function () {
        if ( $(this).val() === "null" ) {
            $("#myschools").get()[0].scrollIntoView(true);
            $("#s_school_id").focus();
            win.location.hash = "#myschools";
        }
    });
    // Set focus on first field that has an error
    // TODO: Move to common
    if ( $(".error").length ) {
        $(".error")[0].focus();
    }
    
    // Debug use only
    $("#add_workplace_form").submit(
        function (e) {
            console.log($("#s_school_id").val());
            // return false;
        }
    );
    
    var add_as_workplace = $("#add_as_workplace");
    if ( add_as_workplace.length ) {
        add_as_workplace.attr("href", "#").html("Lägg till denna skola som arbetsplats.").on("click", function() {
            var schoolid = add_as_workplace.data("schoolid");
            console.log("setting " + schoolid + " as workplace");
            // Populate form above and submit
            $("#s_school_id").val(
                $("#new_school_school_name").val() + ", " + $("#new_school_school_place").val() + " (" + schoolid + ")"
            );
            // Hide the form we no longer need
            $("#new_school_form").hide().data("hidden", "hidden");
            // Focus attention on the form we are going to use
            $("#add_workplace_form").get()[0].scrollIntoView(true);
            win.location.hash = "add_workplace_form";
            $("#add_workplace_form").submit();
            return false;
        });
    }
    // If user does not want to use the convenience link, hide it.
    $("#new_school_name").on('focus', function (e) {
        if ( add_as_workplace.length ) {
            add_as_workplace.hide();
        }
    });
    
    var goto_create_group = $("#goto_create_group");
    if ( goto_create_group.length ) {
        goto_create_group.attr("href", "#").html("Skapa en grupp för den här skolan.").on("click", function (e) {
            e.preventDefault();
            $("#admingroup").get()[0].scrollIntoView(true);
            $("#g_school").val(goto_create_group.data('schoolinfostring'));

        });
    }
    // If user does not want to use the convenience link, hide it.
    $("#s_school_id").on('focus', function (e) {
        if ( goto_create_group.length ) {
            goto_create_group.hide();
        }
    });
    
}(window, window.document));
  </script>
  
</body>
</html>
