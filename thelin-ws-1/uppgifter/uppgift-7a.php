<?php
/**
 * Injektionssäkrad användardata, enligt uppgift 7A
 */

mb_internal_encoding('UTF-8');

$words[] = trim(filter_input(INPUT_GET, 'word1', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW));
$words[] = trim(filter_input(INPUT_GET, 'word2', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW));
$words[] = trim(filter_input(INPUT_GET, 'word3', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW));

foreach ( $words as $w ) {
    $lengths[] = mb_strlen($w);
}

header("Content-type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <title>Uppgift 7A</title>
  <style>
    body {
        font-family: sans-serif;
        margin: auto;
        max-width: 600px;
    }
  </style>
</head>
<body>
  <h1>Uppgift 7A</h1>
<?php
$i = 0;
foreach ( $words as $w ) {
    echo '<p>Ordet "' . htmlspecialchars($w, ENT_QUOTES) . "\" innehåller {$lengths[$i]} bokstäver.</p>\n";
    $i++;
}
?>
</body>
</html>

<?php
// Testa enligt följande mönster
// http://host/path/to/uppgift-7a.php?word1=hej&word2=%3Ci%20onclick=%22alert%28%27injektion%27%29%22%3Etjosan&word3=%3Cem%3Ebl%C3%A4rgh
