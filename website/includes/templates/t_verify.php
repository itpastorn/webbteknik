<?php
/**
 * Template for page where reported jobs are verified/disqualified
 * by teacher
 */
 
// TH first-child ingen min-width
// En tabell per grupp (med gruppnamn)
// Om allt har verifierats i en grupp -> Ingen tabell, bara status

// OM elev har sett klart en film men underkänns = se om den, vad ska då nollställas? Undersök

// TODO - move to groups class
$groupidrules = data_groups::getFilterValidateRule('id');

$group   = false;
$groupid = false;
$whylist = ''; // Why a list of groups will be shown
if ( filter_has_var(INPUT_GET, 'groupid') ) {
    $groupid = filter_input(INPUT_GET, 'groupid', FILTER_VALIDATE_REGEXP, $groupidrules);
    if ( !$groupid ) {
        $whylist = 'nosuchgroup';
    } else {
        $group = data_groups::loadOne($groupid, $dbh);
        if ( !$groupid ) {
            $whylist = 'nosuchgroup';
        }
    }
} else {
    $whylist = 'requestlist';
}


$sql = <<<SQL
-- Does the group exist at all? If so fetch group info
SQL;
// If not $whylist = 'nosuch group';

$sql = <<<SQL
-- Has teacher access to this group
-- Teaching
-- Colleague on same school
-- Is admin
SQL;
// If not $whylist = 'forbidden';

// Ajax
if ( filter_has_var(INPUT_POST, 'upid') &&  $groupid) {
    $upid     = filter_input(INPUT_POST, 'upid', FILTER_VALIDATE_INT);
    $approved = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    if ( 'approve' == $approved ) {
    $sql = <<<SQL
        UPDATE userprogress
        SET approved = NOW()
        WHERE upID = :upid
SQL;
    } elseif ( 'fail' == $approved ) {
    $sql = <<<SQL
        UPDATE userprogress
        SET status = 'begun', approved = NOW()
        WHERE upID = :upid
SQL;
    } else {
        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad request', true, 400);
        exit('Status must be either approve or fail.');
    }
    try {
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam('upid', $upid);
        $stmt->execute();
    }
    catch(Exception $e) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
        echo "Database operation failed. {$e->getMessage()}";
        exit;
    }
    if ( $stmt->rowCount() ) {
        if ( 'approve' == $approved ) {
            exit("jobok. Database updated. Approvalstatus set to {$approved} on upID {$upid}");
        } else {
            exit("jobfail. Database updated. Student must redo assignment for upID {$upid}");
        }
    } else {
        // Since SQL is using "NOW()" we really should never get here
        exit("nochange. Database not updated. No change on upID {$upid}.");
    }
} elseif (filter_has_var(INPUT_POST, 'upid')) {
    switch ( $whylist ) {
    case 'requestlist':
        // Fallthrough intended
    case 'nosuchgroup':
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not found', true, 404);
        exit('Group does not exist or no group set in URL.'); 
        break;
    case 'forbidden':
        header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized', true, 401);
        exit('You may not access students belonging to that group.');
        break;
    default:
        trigger_error('E_USER_ERROR', "Defensive programming error on " . __LINE__ . " in ". __FILE__);
    }
    
}

if ( !$groupid ) {
    switch ( $whylist ) {
    case 'requestlist':
        // 200 OK
        break;
    case 'nosuchgroup':
        header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not found', true, 404);
        break;
    case 'forbidden':
        header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized', true, 401);
        break;
    default:
        trigger_error('E_USER_ERROR', "Defensive programming error on " . __LINE__ . " in ". __FILE__);
    }
    // Visa lista på lärarens grupper
    
    // + Visa lista på kollegors grupper (initialt dold, fälls ut med knapp eller hashurl)

}

$bookid = $group->getTextbook($dbh);

$sql = <<<SQL
    SELECT jl.*, up.upID, up.percentage_complete, up.status, up.lastupdate, up.email, users.firstName, users.lastName
    FROM joblist AS jl
    INNER JOIN userprogress AS up USING (joblistID)
    INNER JOIN belonging_groups AS bg ON ( bg.email = up.email)
    INNER JOIN teaching_groups AS tg ON ( tg.groupID = bg.groupID)
    INNER JOIN users ON (bg.email = users.email)
    WHERE bg.groupID = :groupid
      AND jl.bookID = :bookid
      AND up.approved IS NULL
      AND ( up.status = 'finished' OR up.status = 'skipped' )
    ORDER BY bg.groupID, users.email, jl.bookID, jl.chapter ASC, jl.joborder ASC
SQL;
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':groupid', $groupid);
$stmt->bindParam(':bookid', $bookid);
$stmt->execute();

$tdata                 = new StdClass();
$tdata->groupid        = $groupid;
$tdata->jobs_to_verify = array();
while ( $job = $stmt->fetch() ) {
    $tdata->jobs_to_verify[] = array(
        'name'                => "{$job['firstName']} {$job['lastName']} ({$job['email']})",
        'bookid'              => $job['bookID'],
        'jobdescription'      => "{$job['what_to_do']} {$job['where_to_do_it']}",
        'percentage_complete' => $job['percentage_complete'],
        'status'              => $job['status'],
        'lastupdate'          => $job['lastupdate'],
        'upid'                => $job['upID'],
    );
}
$statustext = array(
  'skipped'  => 'överhoppad',
  'finished' => 'klar',
);
$listreason = array(
    'requestlist' => '',
    'nosuchgroup' => '<p class="error">Efterfrågad grupp finns inte.</p>',
    'forbidden'   => '<p class="error">Du har inte behörighet att komma åt den gruppen.</p>',
);
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Bekräfta uppgifter - webbteknik.nu</title>
  <?php require "../includes/snippets/dochead.php"; ?>
</head>
<body class="wide">
  <h1>Bekräfta uppgifter - webbteknik.nu</h1>
<?php 
require "../includes/snippets/mainmenu.php";

echo "<h2>Bekräfta elevers jobb för grupp {$tdata->groupid}</h2>\n";
if ( empty($tdata->jobs_to_verify) ) :
    echo "<p>Inga uppgifter att kontrollera för gruppen.</p>\n";
else :
?>  
  
  <table class="verifyjobs blackborder jobreport" id="jobstoverify" data-groupid="<?php echo $tdata->groupid; ?>">
    <tr>
      <th>Namn</th>
      <th>Bok</th>
      <th>Uppgift</th>
      <th>Status</th>
      <th>procent sett (videos)</th>
      <th>Rapporttid</th>
      <th>Godkänn</th>
      <th>Underkänn</th>
    </tr>
<?php
foreach ( $tdata->jobs_to_verify as $job ) {
    echo <<<TR
    <tr>
      <td>{$job['name']}</td>
      <td>{$job['bookid']}</td>
      <td>{$job['jobdescription']}</td>
      <td class="job_status">{$statustext[$job['status']]}</td>
      <td>{$job['percentage_complete']}</td>
      <td class="job_lastupdate">{$job['lastupdate']}</td>
      <td class="approve" data-upid="{$job['upid']}"></td>
      <td class="fail" data-upid="{$job['upid']}"></td>
    </tr>
TR;
}
?>
  </table>
<?php
endif;

require "snippets/footer.php";
?>
  <script>
    (function () {
        function setstatus () {
            var groupid = $("#jobstoverify").data("groupid");
            // Relevant data is set on the td element that contains the button
            var data_elem     = $(this).parent();
            var table_row     = data_elem.parent();
            var id_to_set     = data_elem.data("upid");
            var status_to_set = data_elem.attr("class");
            console.log("Trying to set status " + status_to_set + " on upid " + id_to_set + " /groupid:" + groupid);
            $.ajax({
                url: "teacherpage/verify/" + groupid + "/",
                method: "post",
                data: { upid: id_to_set, status: status_to_set},
                success: function(data, textStatus, jqXHR) {
                    console.log("Successfully set status");
                    console.log(data);
                    $("td[data-upid='" + id_to_set + "'] > button") .
                        attr("disabled", "disabled") .
                        parent().parent().hide("slow");
                },
                error: function(data, textStatus, jqXHR) {
                    console.log("Status not set. Reason:" + data.responseText);
                    console.log(data);
                }
            });
            // Ajax
               // context - the row!
               // success - set new status and new report time + disable button that was clicked
               // fail - report error
               
               // Server side - check if teacher has student in group or is admin

               // FIXME - what if teacher want to set status of previously approved stuff
               //         e.g. if (s)he finds out that the student has cheated ==> On students individual page
        }
        var approvals = $(".approve").html("<button>Godkänn</button>").find("button").click(setstatus);
        var fails     = $(".fail").html("<button>Underkänn</button>").find("button").click(setstatus);
    }());
  </script>
</body>
</html>
