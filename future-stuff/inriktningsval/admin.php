<?php
/*
 * Administrera inriktnings- och paketval
 *
 * @author Lars Gunther <gunther@keryx.se>
 */

// Inloggad?



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
  <title>Administrera inriktnings- och fördjupningskursval på Teknikprogrammet, NE, 2012</title>
  <link href="inr-val.css" rel="stylesheet" />
 </head>
 <body class="admin">
   <h1>Administrera inriktnings- och fördjupningskursval på Teknikprogrammet, NE, 2012</h1>
   <form action="" method="post">
   <h2>Te1A</h2>
   <table>
     <tr>
       <th>Namn</th>
       <th>Personnummmer</th>
       <th>Inriktning</th>
       <th>Skriv ut</th>
       <th>Tag bort val</th>
       <th>Bekräfta</th><!-- kan ej längre ångra -->
     </tr>
     <tr>
       <td>Allan Andersson</td>
       <td>950809-1122</td>
       <td>Design-IT-produktion</td>
       <td><a href="val.php?kod=aaaa">aaaa</a></td>
       <td><input type="checkbox" name="regret_aaaa" value="aaaa" /></td>
       <td><input type="checkbox" name="confirm_aaaa" value="aaaa" /></td>
     </tr>
     <tr>
       <td>Allan Andersson</td>
       <td>950809-1122</td>
       <td>Design-IT-produktion</td>
       <td><a href="val.php?kod=aaaa">aaaa</a></td>
       <td><input type="checkbox" name="regret_aaaa" value="aaaa" /></td>
       <td><input type="checkbox" name="confirm_aaaa" value="aaaa" /></td>
     </tr>
     <tr>
       <td>Allan Andersson</td>
       <td>950809-1122</td>
       <td>Design-IT-produktion</td>
       <td><a href="val.php?kod=aaaa">aaaa</a></td>
       <td><input type="checkbox" name="regret_aaaa" value="aaaa" /></td>
       <td><input type="checkbox" name="confirm_aaaa" value="aaaa" /></td>
     </tr>
   </table>

   <h2>Te1B</h2>
   <table>
     <tr>
       <th>Namn</th>
       <th>Personnummmer</th>
       <th>Inriktning</th>
       <th>Skriv ut</th>         <!-- Visa bara om val gjorts -->
       <th>Tag bort val</th>     <!-- Eleven har ångrat sig -->
       <th>Bekräfta</th>         <!-- Bekräftat med underskrift, kan ej längre ångra -->
     </tr>
     <tr>
       <td>Beda Bengtsson</td>
       <td>950309-4422</td>
       <td>Design-IT-produktion</td>
       <td><a href="val.php?kod=aaaa">aaaa</a></td>
       <td><input type="checkbox" name="regret_aaaa" value="aaaa" /></td>
       <td><input type="checkbox" name="confirm_aaaa" value="aaaa" /></td>
     </tr>
     <tr>
       <td>Beda Bengtsson</td>
       <td>950309-4422</td>
       <td>Design-IT-produktion</td>
       <td><a href="val.php?kod=aaaa">aaaa</a></td>
       <td><input type="checkbox" name="regret_aaaa" value="aaaa" /></td>
       <td><input type="checkbox" name="confirm_aaaa" value="aaaa" /></td>
     </tr>
     <tr>
       <td>Beda Bengtsson</td>
       <td>950309-4422</td>
       <td>Design-IT-produktion</td>
       <td><a href="val.php?kod=aaaa">aaaa</a></td>
       <td><input type="checkbox" name="regret_aaaa" value="aaaa" /></td>
       <td><input type="checkbox" name="confirm_aaaa" value="aaaa" /></td>
     </tr>
   </table>
   </form>

   <h2>Statistik</h2>
   <p>TODO</p>

  </body>
</html>
