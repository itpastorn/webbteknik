<?php
/**
 * Hur man ansluter till en databas
 * 
 * Följande SQL har körts som förberedelse:
 * GRANT ALL ON `blogg`.* TO 'php_demo_user'@'localhost' IDENTIFIED BY 'bad_pw';
 */

$mysqldb   = 'blogg';
$mysqluser = 'php_demo_user';
$mysqlpass = 'bad_pw';

$dbh = new PDO('mysql:host=localhost;dbname=' . $mysqldb, $mysqluser, $mysqlpass);

// var_dump($dbh);

// Drakonisk felhantering är bra för nybörjare (kan undvikas med try-catch)
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// OM vi jobbar med UTF-8 i databasen, så måste detta också gälla för uppkopplingen
$dbh->query("SET NAMES 'utf8' COLLATE 'utf8_swedish_ci'");

$sql = 'SELECT * FROM artiklar';

$stmt = $dbh->prepare($sql);
$stmt->execute();
$all_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header("Content-type: text/html; charset=utf-8");
echo "<pre>\n";
print_r($all_rows);
