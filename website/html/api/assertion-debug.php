<?php
header("content-type: text/plain");
$a = "eyJhbGciOiJSUzI1NiJ9.eyJwdWJsaWMta2V5Ijp7ImFsZ29yaXRobSI6IkRTIiwieSI6ImRmYmVhNzBhYzUyMjgxZmM4MTRhNjc0ODk4MWE3YmRhYWMxMzYyODIyZTRhZDBmOWE5OTc0MzhiNmEzNmJhNGQ3ZDcwYzU1MjFmN2NjYjg1MzQ2YjRhMDM2NjA5NDM1YWI4ODBjM2RkYjM5NThkNjNjZDc2ZWY0MTY4OWFkMzFlZWRkY2I5MGRlMDFlMTVmODM2Nzk2MjlkZGEwNGFmODE2ZGE3YmNjMTA1MmQ0ZjNmNTdjNmFiOGIwNDE5MDM1YmNlMTUyZDYxYzZhMjQ2MmM0YzIxNzBkMzQ0MjYzOGU2MDRmMWQ5ODFjMTM5NGQwYzBjNDBlYjc5MTJmNzk1MTAiLCJwIjoiZmY2MDA0ODNkYjZhYmZjNWI0NWVhYjc4NTk0YjM1MzNkNTUwZDlmMWJmMmE5OTJhN2E4ZGFhNmRjMzRmODA0NWFkNGU2ZTBjNDI5ZDMzNGVlZWFhZWZkN2UyM2Q0ODEwYmUwMGU0Y2MxNDkyY2JhMzI1YmE4MWZmMmQ1YTViMzA1YThkMTdlYjNiZjRhMDZhMzQ5ZDM5MmUwMGQzMjk3NDRhNTE3OTM4MDM0NGU4MmExOGM0NzkzMzQzOGY4OTFlMjJhZWVmODEyZDY5YzhmNzVlMzI2Y2I3MGVhMDAwYzNmNzc2ZGZkYmQ2MDQ2MzhjMmVmNzE3ZmMyNmQwMmUxNyIsInEiOiJlMjFlMDRmOTExZDFlZDc5OTEwMDhlY2FhYjNiZjc3NTk4NDMwOWMzIiwiZyI6ImM1MmE0YTBmZjNiN2U2MWZkZjE4NjdjZTg0MTM4MzY5YTYxNTRmNGFmYTkyOTY2ZTNjODI3ZTI1Y2ZhNmNmNTA4YjkwZTVkZTQxOWUxMzM3ZTA3YTJlOWUyYTNjZDVkZWE3MDRkMTc1ZjhlYmY2YWYzOTdkNjllMTEwYjk2YWZiMTdjN2EwMzI1OTMyOWU0ODI5YjBkMDNiYmM3ODk2YjE1YjRhZGU1M2UxMzA4NThjYzM0ZDk2MjY5YWE4OTA0MWY0MDkxMzZjNzI0MmEzODg5NWM5ZDViY2NhZDRmMzg5YWYxZDdhNGJkMTM5OGJkMDcyZGZmYTg5NjIzMzM5N2EifSwicHJpbmNpcGFsIjp7ImVtYWlsIjoiZ3VudGhlckBrZXJ5eC5zZSJ9LCJpYXQiOjEzNTIzODYxOTE2NTEsImV4cCI6MTM1MjQ3MjU5MTY1MSwiaXNzIjoibG9naW4ucGVyc29uYS5vcmcifQ.5ROM84nJt5jmmg71am4vV-v9fr7flgprXl8gh46jTd5XmkC5yYbyR3PS_rYB2sJdJKlom9LPcqOtLz8yVp_2iI8kDLFhalhybesWbU-R9NDXNWS_d2AkswlgviYfLsnxN7Qh_CFK2mNTyIU9w-D0cXI8-KbguzHnzRf8qv9YTVGmx15ztLgtGnEkOv7S35JvMK2biiMJ-_xL1YwtBrdMDlLsmEB39U1_Zx7cpwhFvVTpTLUoyK_4u3QM-4-a_Y29Q0jOda1Ed-0SscpQ-Aus4wooWFwjYEfirhG0JQF9E3F0LcxhmNhzGxzkgTDWDRoOj1Hoik5kjs4oVHiWFV-V~eyJhbGciOiJEUzEyOCJ9.eyJleHAiOjEzNTIzOTEzNTg3MzQsImF1ZCI6Imh0dHA6Ly93dC5ib29rIn0.mR-wkE9qgOheMrNZXk_xJ3M_harcqa8Dpd7za03cMMLF_gbdxeI4wA";
$e = urlencode($a);

echo "Debugging";
flush();
$len = strlen($a);
$i2  = 0;
for ( $i = 0; $i < $len; $i++ ) {
    if ( $a[$i] != $e[$i2] ) {
        echo $i . ": " . $a[$i] . " -> " . $e[$i2] . "\n";
        $i2++;
        flush();
    }
    if ( $a[$i] != $e[$i2] ) {
        echo $i . ": " . $a[$i] . " -> " . $e[$i2] . "\n";
        $i2++;
        flush();
    }
    if ( $a[$i] != $e[$i2] ) {
        echo $i . ": " . $a[$i] . " -> " . $e[$i2] . "\n";
        flush();
    }
    $i2++;
}
        


