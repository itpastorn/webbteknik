<?php
/**
 * Kasta tärning, uppgift 9D
 */

/**
 * Simulerar kast av två tärningar
 *
 * @return array
 */
function roll_dice()
{
    for ( $i = 0; $i < 2; $i++ ) {
        $dices[] = mt_rand(1, 6);
    }
    return $dices;
}

header("Content-type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <title>Uppgift 9D</title>
  <style>
    body {
        font-family: sans-serif;
        margin: auto;
        max-width: 600px;
    }
  </style>
</head>
<body>
  <h1>Uppgift 9D</h1>
<?php

for ( $i = 0; $i < 5; $i++ ) {
    // Samma namn som i funktionen, men inte samma variabel pga scope
    $dices = roll_dice();
    echo "<p>Första tärningen blev {$dices[0]} och den andra tärningen blev {$dices[1]}.</p>\n";
}
?>
</body>
</html>
