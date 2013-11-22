<?php
/**
 * Uppgift 9E, Fibonaccital
 */

/**
 * Skapar en följd av fibonaccital
 *
 * @return int Nästa tal i sekvensen
 */
function nextfib() {
    static $prev, $prevprev, $current;
    if ( empty($prev) ) {
        $prev     = 1;
        $prevprev = 0;
        return 1;
    }
    $current  = $prev + $prevprev;
    $prevprev = $prev;
    $prev     = $current;
    return $current;
}

header("Content-type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <title>Uppgift 9E, Fibonaccital</title>
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
  <h1>Uppgift 9E, Fibonaccital</h1>
  <p>
<?php
for ( $i = 0; $i < 100; $i++ ) {
    // number_format krävs inte för att bli godkänd på uppgiften
    echo number_format(nextfib(), 0, ',', " "). "<br />\n";
}
?>
  </p>
</body>
</html>
