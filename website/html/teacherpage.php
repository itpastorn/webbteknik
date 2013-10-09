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

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

user::setSessionData();

user::requires(user::TEACHER);



// TODO A dispatch class that loads appropriate modules

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
    
    // TODO: The following if-section is repeated verbatim to 95 % below - Remove this code duplication!
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

require 'templates/teacherpage.php';