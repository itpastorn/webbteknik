<?php
/**
 * Uppgift 9E, Fibonaccital, alternativ funktion
 */

/**
 * Skapar en följd av fibonaccital
 *
 * Elegantare variant som innehåller överkurs (list)
 *
 * @return int Nästa tal i sekvensen
 */
function nextfib() {
    static $prev, $prevprev;
    if ( empty($prev) ) {
        $prev     = 1;
        $prevprev = 0;
        return 1;
    }
    list($prev, $prevprev) = array($prev + $prevprev, $prev);
    return $prev;
}

header("Content-type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <title>Uppgift 9E, Fibonaccital (elegantare funktion)</title>
  <style>
    body {
        font: large sans-serif;
        margin: auto;
        max-width: 500px;
    }
    p {
        text-align: right;
        font: x-large monospace;
    }
  </style>
</head>
<body>
  <h1>Uppgift 9E, Fibonaccital (elegantare funktion)</h1>
  <p>
<?php
for ( $i = 0; $i < 100; $i++ ) {
    echo number_format(nextfib(), 0, ',', " "). "<br />\n";
}
?>
  </p>
</body>
</html>
