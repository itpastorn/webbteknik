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

/**
 * Abstract items-class
 */
require "../includes/data/items.php";

/**
 * Data class interface
 */
require "../includes/data/data.php";

/**
 * Courses - available courses shall be listed
 */
require "../includes/data/courses.php";

/**
 * Schools - Where the teacher works
 */
require "../includes/data/schools.php";

user::setSessionData();

user::requires(user::TEACHER);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

$all_courses   = data_courses::loadAll($dbh);
$select_course = makeSelectElement($all_courses, 'WEBWEU01');

$all_schools   = data_schools::loadAll($dbh);
$select_school = makeSelectElement($all_schools, "", true);

// TODO A dispatch class that loads appropriate modules

if ( isset($_POST['new_school_school_added']) ) {
	$new_school = array(
        'id'          => false,
        'name'        => $_POST['new_school_school_name'],
        'schoolPlace' => $_POST['new_school_school_place'],
        'schoolUrl'   => $_POST['new_school_school_url'],
    );
	// More filtering and validation in class method
    $new_school = data_schools::fromArray($new_school);
} else {
    $new_school = data_schools::fake('', '');
}


// TODO load sql for affiliations
// $workplace = data_schools::loadFromSql("");


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
<p><strong>Utan skolanslutning ska inget annat kunna fungera....</strong></p>
  <h2 id="mygroups">Mina grupper</h2>
  <h3>Grupp smeknamn (inbjudningskod)</h3>
  <p>
    Statistik om gruppen som helhet. Min/max/medel i antal gjorda uppgifter.
    <br />
    Start- och slutdatum
  </p>
  <ul>
    <li>Redigera gruppdata</li>
    <li>Visa gruppens framsteg</li>
    <li>Redigera medlemslistan</li>
  </ul>
  <p>
    abc
  </p>   
  <h2 id="admingroup">Skapa ny grupp/redigera grupp</h2>
  <form method="post" action="<?php echo $pageref; ?>">
    <fieldset class="blocklabels">
      <legend>Allmän information</legend>
      <p><strong>Göm allt annat tills skola valts</strong></p>
      <p>
        <!-- Should be limited to the schools where the teacher works and be a select list -->
        <label for="g_school">Skola</label>
        <select name="g_school" id="g_school">
          <?php echo $select_school; ?>
        </select>
      </p>
      <p>
        <label for="g_course_id">Kurs</label>
        <select name="g_course_id" id="g_course_id">
          <?php echo $select_course; ?>
        </select>
      </p>
      <p>
        <label for="g_groupnick">Smeknamn på gruppen (Hur ni pratar om gruppen i dagligt tal)</label>
        <input type="text" id="g_groupnick" name="g_groupnick" placeholder="Exempel: Webbettan" required />
      </p>
    </fieldset>
    <fieldset class="blocklabels">
      <legend>Gruppinformation</legend>
      <p>
        <!-- Should be limited to the number of books bought for one year (with some grace overlap)-->
        <label for="g_numstudents">Antal elever i gruppen</label>
        <input type="number" id="g_numstudents" name="g_numstudents" required />
      </p>
      <p>
        <label for="g_startdate">Kursstart (gruppens livslängd är 12 månader)</label>
        <input type="date" id="g_startdate" name="g_startdate" value="<?php echo $TODAY; ?>" required />
      </p>
      <p>
        <em>OBS! Man kan aldrig sätta startdatum längre in i framtiden än vad det sattes första gången.</em>
      </p>
      <p>
        <label for="g_groupurl">Länk till eventuell kurssida</label>
        <input type="url" id="g_groupurl" name="g_groupurl" placeholder="Länk till kursens webbplats på din skola" />
      </p>
      <p>
        <em>OBS! Man kan aldrig sätta startdatum längre in i framtiden än vad det sattes första gången.</em>
      </p>
    </fieldset>
    <fieldset>
      <legend>Skicka gruppdata</legend>
      <p>
        <input type="submit" value="Skicka" />
      </p>
    </fieldset>
  </form>
  <h2 id="myschools">Mina skolor</h2>
  <form action="" method="post">
    <fieldset class="blocklabels">
      <legend>Skolor där du jobbar</legend>
      <p>
        Du är registrerade som lärare på följande skolor:
      </p>
    </fieldset>
  </form>
  <form action="" method="post">
    <fieldset class="blocklabels">
      <legend>Lägg till ny arbetsplats</legend>
      <p>
        <label for="s_school_id">Lägg till skola (du måste använda ett färdigt förslag i detta fält)</label>
        <input type="text" name="s_school_id" id="s_school_id" list="s_school_list" /> 
          <datalist id="s_school_list">
            <select name="s_school_id">
              <?php echo $select_school; ?>
            </select>
        </datalist>
      </p>
      <p>
        <input type="checkbox" id="s_new_school" name="s_new_school" value="yes" /> 
        <label for="s_new_school">Skolan finns inte med i listan</label>
      </p>
    </fieldset>
  </form>
  <?php echo <<<FORMCONTENTS
  <form action="{$pageref}#new_school_form" method="post" id="new_school_form">
    <fieldset class="blocklabels">
      <legend>Lägg till ny skola eller annan arbetsplats</legend>
      <p>
        <label for="new_school_school_name">Namn på skolan{$new_school->errorMessage('schoolName')}</label>
        <input type="text"
               placeholder="Ex. Jurtaskolan" 
               value="{$new_school->getName()}"
               {$new_school->isError('schoolName', true)}
               name="new_school_school_name"
               id="new_school_school_name"
               maxlength="99" required />
      </p>
      <p>
        <label for="new_school_school_place">Ort eller annan plats för skolan</label>
        <input type="text"
               placeholder="Västra utmarken" 
               value="{$new_school->getPlace()}"
               {$new_school->isError('schoolPlace', true)}
               name="new_school_school_place"
               id="new_school_school_place"
               maxlength="49" required />
      </p>
      <p>
        <label for="new_school_school_url">Skolans webbplats</label>
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
        Adminstratörer måste godkänna att nya skolor läggs till. Detta kan ta ett par dagar.
        Använd <a href="contact.php" class="nonimplemented">kontaktformuläret</a> om inget hänt inom 48 timmar.
      </p>
FORMCONTENTS;
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
    
    $("#s_new_school").on('click', function () {
    	if ( $(this).attr('checked') ) {
    	    $("#new_school_form").data("hidden" ,"").show().get()[0].scrollIntoView(false);
    	    $("#new_school_school_name").focus();
    	} else {
    	    $("#new_school_form").data("hidden" ,"hidden").hide();
    	}
    });
}(window, window.document));
  </script>
  
</body>
</html>
