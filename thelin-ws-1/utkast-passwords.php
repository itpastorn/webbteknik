<?php
/**
 * Saltade hashade lösenord
 */

// password-security.php

// Skapa lösenord med SHA512 som algoritm

$salt = uniqid('', true); // Fler än 16 slumpmässiga tecken
$user_submitted_password = "hejsan"; // svagt lösen
$password = crypt(
    $user_submitted_password,
    '$6$' . $salt . '$'
);
// http://php.net/crypt

// Anslutning (PDO-objekt) förutsätts
// Sedan lösen _inklusive_ salt skapats så lägg in data i DB
$stmt = $dbh->prepare("
    INSERT INTO users (username, password)
    VALUES (:username, :password)
");
$stmt->bind(":username", $username); // Från formulär
$stmt->bind(":password", $password); // Från crypt
$stmt->execute();


// Testa lösenord (inloggning)
// Hämta lösen (det riktiga) ur databasen
$stmt = $dbh->prepare("SELECT password FROM users WHERE username = :username");
$stmt->bind(":username", $username); // från formulär
$stmt->execute();
$userdata = $stmt->fetch(); // $userdata är nu en array eller false

if ( empty($userdata) ) {
    // No such user...
}

// Lägg märke till att lösenordet innehåller saltet. därför är det parameter till crypt()
if ( crypt($user_submitted_password, $userdata['password']) == $userdata['password'] ) {
    // Hurra inloggad
}

/*
 * Alternativ lösning som inte funkar på Windows som saknar crypt()
 */
$stmt = $dbh->prepare(
    "SELECT password FROM users WHERE username = :username AND ENCRYPT(:upassword, password) = password"
);
$stmt->bind(":username", $username); // från formulär
$stmt->bind(":upassword", $user_submitted_Password); // från formulär
$stmt->execute();
$userdata = $stmt->fetch(); // $userdata är nu en array eller false

?>
// Lösenordslängd i DB 106 tecken
// Databasens struktur - tabell users
username varchar(20) not null PRIMARY_KEY
password varchar(106) not null

CREATE TABLE users (
    username varchar(20) not null PRIMARY_KEY,
    password varchar(106) not null,
) CHARACTER SET utf8 COLLATE utf8_swedish_ci ENGINE = InnoDB



