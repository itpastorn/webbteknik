<?php
/*
 * Behandlar inriktnings- och paketval
 *
 * @author Lars Gunther <gunther@keryx.se>
 */

error_reporting(E_ALL);
ini_set("display_errors", "on");

/**
 * Databasanslutning
 */
require_once("dbcx.php");
$dbh = dbcx();

// Hur har man kommit hit?
/*
  Via GET? I så fall visa lagrade uppgifter
  Via POST? I så fall ska de lagras
*/

if ( !empty($_POST) ) {
    $testcode = $_POST['verified_pkod'];
} elseif ( isset($_GET['kod']) ) {
    $testcode = $_GET['kod'];
} else {
    // Felaktigt anrop
    echo("<h1 style='font: 3em sans-serif'>Du har kommit hit på fel sätt</h1>\n");
    echo("<p style='font: 2em sans-serif'>Du måste antingen ange kod eller komma från formuläret</p>\n");
}

/*
 Emulera för tester:
     aaaa = ok kod
     bbbb = finns men redan lagrat svar
     ccc# = helt felaktig
 */
// $_POST['verified_pkod'] = "bbbb";

// Rätt mönster för koder
$ok_verified_pkod = "/^[a-z]{4}/";
if ( !preg_match($ok_verified_pkod, $testcode) ) {
    $userdata['kod'] = "Ogiltigt kodmönster";
} else {
    // Kolla att koden matchar en användare som faktiskt finns
    $stmt = $dbh->prepare("SELECT * FROM elever WHERE kod = :kod");
    $stmt->bindParam(":kod", $testcode);
    $stmt->execute();
    $userdata = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( empty($userdata) ) {
        $felfri = false;
        $userdata['kod'] = "Felaktig kod, matchar ingen person i databasen";
    } else {
        $userdata['kod'] = $testcode;
        $elev            = "{$userdata['fornamn']} {$userdata['efternamn']}, {$userdata['klass']}";
    }
}

// Snabb felkontroll

if ( !empty($_POST) ) {
    // Fält att behandla - resten ignoreras
    $ok_inriktningar = array("design", "produktion", "it", "samhall");
    $ok_paket1       = array("prod1", "it1", "ark1", "des1");
    $ok_paket2       = array("civing", "it2", "prod2", "sam2");
    
    // Ändra provisoriskt för testning
    // $ok_paket2       = array("civing", "it2", "prod2", "sam3");
    
    $ok_kommentar_max = 500;
    
    if ( $userdata['kod'] !== $testcode) {
        $felfri = false;
    } else {
        // Kolla att man inte redan valt - här är värdet hämtat ur DB
        if ( $userdata['inriktning'] ) {
            // Val redan gjort
            echo "<h1>Du har redan gjort ditt val!</h1>";
            echo <<<HTML
              <p><a href="val.php?kod={$testcode}">Se ditt val</a></p>
              <p>Ångrar du ditt val? Kontakta din klassföreståndare.</p>
HTML;
            exit;
        }
    }

    // TODO: Filtrera kommentar bättre
    $_POST['kommentar'] = strip_tags($_POST['kommentar']);
    
    $felfri = true;
    if ( !in_array($_POST['inriktningar'], $ok_inriktningar) ) {
        $felfri = false;
        $userdata['inriktning'] = "Ogiltigt val";
    } else {
        $userdata['inriktning'] = $_POST['inriktningar'];
    }
    
    if ( !in_array($_POST['paket1'], $ok_paket1) ) {
        $felfri = false;
        $userdata['paket1'] = "Ogiltigt val";
    } else {
        $userdata['paket1'] = $_POST['paket1'];
    }
    
    if ( !in_array($_POST['paket2'], $ok_paket2) ) {
        $felfri = false;
        $userdata['paket2'] = "Ogiltigt val";
    } else {
        $userdata['paket2'] = $_POST['paket2'];
    }
    
    // TODO Kolla kommentar noggrannare
    if ( mb_strlen($_POST['kommentar'], "utf-8") > $ok_kommentar_max ) {
        $felfri = false;
        $userdata['kommentar'] = "För lång kommentar. Max {$ok_kommentar_max} tecken.";
    } else {
        $userdata['kommentar'] = $_POST['kommentar'];
    }
    
    // TODO: Snygg felhantering
    if ( !$felfri ) {
        echo "<pre>";
        var_dump($_POST);
        var_dump($userdata);
        exit("<h1 style='font: 3em sans-serif'>Du har fyllt i felaktiga uppgifter. Backa och försök igen.</h1>\n");
    } else {
        $sql = "UPDATE elever SET inriktning=:inriktning, paket1=:paket1, paket2=:paket2, kommentar=:kommentar WHERE kod = :kod";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":inriktning", $userdata['inriktning']);
        $stmt->bindParam(":paket1", $userdata['paket1']);
        $stmt->bindParam(":paket2", $userdata['paket2']);
        $stmt->bindParam(":kod", $userdata['kod']);
        $stmt->bindParam(":kommentar", $userdata['kommentar']);
        if ( $testcode !== "test" ) {
            $stmt->execute();
            // Annars "dry run"
        }
    }
}

if ( $userdata['kod'] !== $testcode) {
    echo "<h1>{$userdata['kod']}</h1>";
    exit;
}

// Skapa sida för utskrift
$sql = "SELECT name FROM inriktning_paket WHERE inr_pak_ID = :inriktning";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":inriktning", $userdata['inriktning']);
$stmt->execute();
$userdata['inriktningsnamn'] = $stmt->fetchColumn();

if ( empty($userdata['inriktningsnamn']) ) {
    echo "<h1>Inga val gjorda ännu</h1>";
    echo "<p>Inga val gjorda ännu för <strong>{$elev}</strong></p>";
    exit;
}

$sql = <<<SQL
    SELECT k.kurskod, k.kursnamn, k.poang FROM kurser AS k 
    INNER JOIN block_kurser AS bk
    ON bk.kurskod = k.kurskod
    WHERE bk.inr_pak_ID = :inriktning
SQL;
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":inriktning", $userdata['inriktning']);
$stmt->execute();
$userdata['inriktningskurser'] = '';
foreach ( $stmt->fetchAll() as $row) {
    $userdata['inriktningskurser'] .= "<li><strong>{$row['kursnamn']}</strong>";
    $userdata['inriktningskurser'] .= " ({$row['kurskod']}) om {$row['poang']} poäng.</li>";
}

$sql = <<<SQL
    SELECT k.kurskod, k.kursnamn, k.poang FROM kurser AS k 
    INNER JOIN block_kurser AS bk
    ON bk.kurskod = k.kurskod
    WHERE bk.inr_pak_ID = :paket
SQL;
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":paket", $userdata['paket1']);
$stmt->execute();
$userdata['paket1_kurser'] = '';
foreach ( $stmt->fetchAll() as $row) {
    $userdata['paket1_kurser'] .= "<li><strong>{$row['kursnamn']}</strong>";
    $userdata['paket1_kurser'] .= " ({$row['kurskod']}) om {$row['poang']} poäng.</li>";
}

$stmt->bindParam(":paket", $userdata['paket2']);
$stmt->execute();
$userdata['paket2_kurser'] = '';
foreach ( $stmt->fetchAll() as $row) {
    $userdata['paket2_kurser'] .= "<li><strong>{$row['kursnamn']}</strong>";
    $userdata['paket2_kurser'] .= " ({$row['kurskod']}) om {$row['poang']} poäng.</li>";
}
$pnum              = $userdata['personnummer'];
$inriktningsnamn   = $userdata['inriktningsnamn'];
$inriktningskurser = $userdata['inriktningskurser'];
$paket1_kurser     = $userdata['paket1_kurser'];
$paket2_kurser     = $userdata['paket2_kurser'];
$kommentar         = nl2br(htmlspecialchars($userdata['kommentar']));

// Om lapp med underskrift lämnats så bör denna vara true
$verified = false;

// Sidmall
?>
<!DOCTYPE html>
<html lang="sv">
 <head>
  <meta charset="utf-8" />
  <title>För utskrift: Inriktnings- och fördjupningskursval på Teknikprogrammet, NE, 2012</title>
  <link href="inr-val.css" rel="stylesheet" />
 </head>
 <body class="utskrift">
   <h1>Inriktnings- och fördjupningskursval på Teknikprogrammet, NE, 2012</h1>
   <p>Elev: <strong><?php echo $elev; ?></strong> (<?php echo $pnum; ?>)</p>
   <h2>Inriktning: <i><?php echo $inriktningsnamn; ?></i></h2>
   <p>Med följande kurser:</p>
   <ul>
     <?php echo $inriktningskurser; ?>
   </ul>
   <h2>Programfördjupning paket 1</h2>
   <ul>
     <?php echo $paket1_kurser; ?>
   </ul>

   <h2>Programfördjupning paket 2</h2>
   <ul>
     <?php echo $paket2_kurser; ?>
   </ul>

   <h2>Kommentar</h2>
   <div id="kommentar">
   <?php echo $kommentar; ?>
   </div>
   <?php if ( $verified ): ?>
   <h2>Detta är dina lämnade uppgifter</h2>
   <p>Har något blivit fel, kontakta din klassföreståndare.</p>
   <?php else: ?>
   <h2>Underskrifter</h2>
   <table class="printlayout">
     <tr>
       <th>Elev:</th>
       <th class="spacer">
       <th>Målsman:</th>
       <th class="spacer">
       <th>Målsman:</th>
     </tr>
     <tr>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
     </tr>
     <tr>
       <td>Ort och datum:</td>
       <td></td>
       <td>Ort och datum:</td>
       <td></td>
       <td>Ort och datum:</td>
     </tr>
     <tr>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
       <td></td>
     </tr>
   </table>
   <?php endif; ?>
   
  </body>
</html>
