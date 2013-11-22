<?php
/**
 * Kontrollerar om en talföljd är korrekt enligt Luhn-algoritmen
 *
 * Kontrollerar att det är ett heltal med regexp
 *
 * $param int $number Talet som ska testas
 * @return bool
 */
function luhncheck($number) {
    // Kontrollera att ett positivt minst tvåsiffrigt heltal skickats (integer eller string)
    if ( !preg_match('/^[0-9]{2,}$/', $number) ) {
        trigger_error('Parameter $number must be a positive integer larger than 9', E_USER_WARNING);
        return false;
    }
    // Utnyttja PHP:s dynamiska typsystem, hantera $number omväxlande som sträng och heltal
    // Eftersom vi vet att det är heltal som vi jobbar med kan vi använda ASCII/ISO-8859-1-funktioner
    $control = substr($number, -1);
    $check   = strrev(substr($number, 0, -1));
    $sum     = 0;
    for ( $i = 0, $len = strlen($check); $i < $len; $i++ ) {
        // Modulus för att få alternerande 2 och 1 som faktor
        $factor  = 2 - ($i % 2);
        $current = $factor * $check[$i];
        if ( $current > 9) {
            $current = $current - 9;
        }
        $sum += $current;
    }
    return 10 - $sum % 10 == $control;
}
// OK
echo luhncheck(5607111316) ? "Japp" : "Nepp"; // Personnummer
echo "<br>";
echo luhncheck(2581091887735027) ? "Japp" : "Nepp"; // Kreditkort
echo "<hr>";
// Inte OK
echo luhncheck(4607111316) ? "Japp" : "Nepp";
echo "<br>";
echo luhncheck(2581091887735022) ? "Japp" : "Nepp";
