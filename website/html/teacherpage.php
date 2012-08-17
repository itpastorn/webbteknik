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
 * @todo Tomorrow: Add schools and number of bought books from publisher
 * @todo Tomorrow; ACL to videos based on privileges and what book it is related to
 * 
 * @todo Now: GET-param with group code = prefill form and scroll to it
 * @todo Now: Remove one school limit/teacher (stop using select-list)
 *            and instead start using teacher-school affiliation
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
$select_course = makeSelectElement('course_id', $all_courses, 'WEBWEU01');

$all_schools   = data_schools::loadAll($dbh);
$select_school = makeSelectElement('school_id', $all_schools);



// Preparing for mod_rewrite, set base-element
// TODO: Make this generic!
$baseref = dirname(htmlspecialchars($_SERVER['SCRIPT_NAME'])) . "/";
if ( "//" == $baseref ) {
    $baseref = "/";
}
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
    <li><a href="{$pageref}#myschool">Skolanslutning</a></li>
  </ul>
SECNAV;
?>
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
      <p>
        <!-- Should be limited to the schools where the teacher works and be a select list -->
        <label for="school">Skola</label>
        <?php echo $select_school; ?>
      </p>
      <p>
        <label for="course">Kurs</label>
        <?php echo $select_course; ?>
      </p>
      <p>
        <label for="groupnick">Smeknamn på gruppen (Hur ni pratar om gruppen i dagligt tal)</label>
        <input id="groupnick" name="groupnick" placeholder="Exempel: Webbettan" required />
      </p>
    </fieldset>
    <fieldset class="blocklabels">
      <legend>Gruppinformation</legend>
      <p>
        <!-- Should be limited to the number of books bought for one year (with some grace overlap)-->
        <label for="numstudents">Antal elever i gruppen</label>
        <input id="numstudents" name="numstudents" type="number" required />
      </p>
      <p>
        <label for="startdate">Kursstart (gruppens livslängd är 12 månader)</label>
        <input id="startdate" name="startdate" type="date" value="<?php echo $TODAY; ?>" required />
      </p>
      <p>
        <em>OBS! Man kan aldrig sätta startdatum längre in i framtiden än vad det sattes första gången.</em>
      </p>
      <p>
        <label for="groupurl">Länk till eventuell kurssida</label>
        <input id="groupurl" name="groupurl" placeholder="Länk till kursens webbplats på din skola" />
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
  <?php require "../includes/snippets/footer.php"; ?>
</body>
</html>
