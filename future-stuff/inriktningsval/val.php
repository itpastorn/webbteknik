<?php
/*
 * Behandlar inriktnings- och paketval
 *
 * @author Lars Gunther <gunther@keryx.se>
 */

// Snabb felkontroll

// Fält att behandla - resten ignoreras
$ok_inriktningar = array("design", "produktion", "it", "samhall");
$ok_block1       = array("prod1", "it1", "ark1", "des1");
$ok_block2       = array("civing", "it2", "prod2", "sam2");

// Ändra provisoriskt för testning
$ok_block2       = array("civing", "it2", "prod2", "sam3");

$ok_verified_pkod = "/^[a-z]{4}/";
$ok_kommentar_max = 500;

// TODO: Filtrera kommentar bättre
$_POST['kommentar'] = strip_tags($_POST['kommentar']);

$felfri = true;
$felfri = $felfri && in_array($_POST['inriktningar'], $ok_inriktningar);
$felfri = $felfri && in_array($_POST['block1'], $ok_block1);
$felfri = $felfri && in_array($_POST['block2'], $ok_block2);
$felfri = $felfri && preg_match($ok_verified_pkod, $_POST['verified_pkod']);
$felfri = $felfri && mb_strlen($_POST['kommentar']);

// Kolla att koden matchar en användare som faktiskt finns
// Emulera DB
// För testning så tillåter vi inte "bbbb";
if ( $_POST['verified_pkod'] !== "aaaa" ) {
    $felfri = false;
} else {
    $elev = "Allan Andersson, Te1A (personnumer?)";
}
// TODO: Snygg felhantering
if ( !$felfri ) {
    echo "<pre>";
    var_dump($_POST);
    exit("<h1 style='font: 3em sans-serif'>Du har fyllt i felaktiga uppgifter. Backa och försök igen.</h1>\n");
}

// TODO: Kolla så att inte uppgifter redan lagrats

// TODO: Lagra i DB

// Skapa sida för utskrift

// Hämta underlag i DB
$inriktningsnamn   = "Design-IT-produktion";
$inriktningskurser = "<li>Kul kurs ett (kurskod)</li><li>Kul kurs två (kurskod)</li><li>Tråkig kurs tre (kurskod)</li>";
$block1_kurser     = "<li>B1 Kurs ett (kurskod)</li><li>B1 Kurs två (kurskod)</li>";
$block2_kurser     = "<li>B2 Kurs ett (kurskod)</li><li>B2 Kurs två (kurskod)</li>";
$kommentar         = nl2br(htmlspecialchars($_POST['kommentar']));

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
   <p>Elev: <?php echo $elev; ?></p>
   <h2>Inriktning: <i><?php echo $inriktningsnamn; ?></i></h2>
   <p>Med följande kurser:</p>
   <ul>
     <?php echo $inriktningskurser; ?>
   </ul>
   <h2>Programfördjupning block 1</h2>
   <ul>
     <?php echo $block1_kurser; ?>
   </ul>

   <h2>Programfördjupning block 2</h2>
   <ul>
     <?php echo $block2_kurser; ?>
   </ul>

   <h2>Kommentar</h2>
   <div id="kommentar">
   <?php echo $kommentar; ?>
   </div>
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
   
  </body>
</html>
