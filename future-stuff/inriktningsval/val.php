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

// Rätt möänster för koder
$ok_verified_pkod = "/^[a-z]{4}/";
if ( !preg_match($ok_verified_pkod, $testcode) ) {
    $vald['kod'] = "Ogiltigt kodmönster";
} else {
    // Kolla att koden matchar en användare som faktiskt finns
    $stmt = $dbh->prepare("SELECT * FROM elever WHERE kod = :kod");
    $stmt->bindParam(":kod", $testcode);
    $stmt->execute();
    $userdata = $stmt->fetch(PDO::FETCH_ASSOC);
    if ( empty($userdata) ) {
        $felfri = false;
        $vald['kod'] = "Felaktig kod, matchar ingen person i databasen";
    } else {
        $vald['kod'] = $testcode;
    }
}

// Snabb felkontroll

if ( !empty($_POST) ) {
    // Fält att behandla - resten ignoreras
    $ok_inriktningar = array("design", "produktion", "it", "samhall");
    $ok_paket1       = array("prod1", "it1", "ark1", "des1");
    $ok_paket2       = array("civing", "it2", "prod2", "sam2");
    
    // Ändra provisoriskt för testning
    $ok_paket2       = array("civing", "it2", "prod2", "sam3");
    
    $ok_kommentar_max = 500;
    
    // TODO: Filtrera kommentar bättre
    $_POST['kommentar'] = strip_tags($_POST['kommentar']);
    
    $felfri = true;
    if ( !in_array($_POST['inriktningar'], $ok_inriktningar) ) {
        $felfri = false;
        $vald['inriktning'] = "Ogiltigt val";
    } else {
        $vald['inriktning'] = $_POST['inriktningar'];
    }
    
    if ( !in_array($_POST['paket1'], $ok_paket1) ) {
        $felfri = false;
        $vald['paket1'] = "Ogiltigt val";
    } else {
        $vald['paket1'] = $_POST['paket1'];
    }
    
    if ( !in_array($_POST['paket2'], $ok_paket2) ) {
        $felfri = false;
        $vald['paket2'] = "Ogiltigt val";
    } else {
        $vald['paket2'] = $_POST['paket2'];
    }

    if ( $vald['kod'] !== $testcode) {
        $felfri = false;
    } else {
        // Kolla att man inte redan valt
        if ( $userdata['inriktning'] ) {
            // Val redan gjort
            echo "<h1>Du har redan gjort ditt val!</h1>";
            echo <<<HTML
              <p><a href="val.php?kod={$testcode}">Se ditt val</a></p>          
HTML;
            exit;
        }
    }
    
    // TODO Kolla kommentar
    // Max 500 tecken
    // $felfri = $felfri && mb_strlen($_POST['kommentar'], "utf-8");
    
    // TODO: Snygg felhantering
    if ( !$felfri ) {
        echo "<pre>";
        var_dump($_POST);
        var_dump($vald);
        exit("<h1 style='font: 3em sans-serif'>Du har fyllt i felaktiga uppgifter. Backa och försök igen.</h1>\n");
    } else {
        $sql = "UPDATE elever SET inriktning=:inriktning, paket1=:paket1, paket2=:paket2 WHERE kod = :kod";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":inriktning", $vald['inriktning']);
        $stmt->bindParam(":paket1", $vald['paket1']);
        $stmt->bindParam(":paket2", $vald['paket2']);
        $stmt->bindParam(":kod", $vald['kod']);
        $stmt->execute();
    }
    
}
// Skapa sida för utskrift
$sql = "SELECT name FROM inriktning_paket WHERE inr_pak_id = :inriktning";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":inriktning", $userdata['inriktning']);
// TODO: Hämta underlag i DB
$elev              = "{$userdata['fornamn']} {$userdata['efternamn']}";
$pnum              = $userdata['personnummer'];
$inriktningsnamn   = "Design-IT-produktion";
$inriktningskurser = "<li>Kul kurs ett (kurskod)</li><li>Kul kurs två (kurskod)</li><li>Tråkig kurs tre (kurskod)</li>";
$paket1_kurser     = "<li>B1 Kurs ett (kurskod)</li><li>B1 Kurs två (kurskod)</li>";
$paket2_kurser     = "<li>B2 Kurs ett (kurskod)</li><li>B2 Kurs två (kurskod)</li>";
$kommentar         = nl2br(htmlspecialchars("Jag vill ha blommig falukorv till lunch."));

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
