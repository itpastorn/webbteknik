<?php
/**
 * Saltade hashade lösenord
 * 
 * Visar *principen* för hur man kan skapa lösenord
 * Visar *principen* för hur man kan testa lösenord
 */

// Förslag på filnamn: password-security.php

// Skapa lösenord med SHA512 som algoritm

// Unikt salt
$salt = uniqid('', true); // Fler än 16 slumpmässiga tecken
// Vi låtsas att detta kommer från användaren och genomgått grundläggande indatafiltrering
$user_submitted_password = "hejsan"; // svagt lösen
// Vi låtsas också att detta önskade användarnamn kommer från användaren och redan filtrerats
$username = "testuser";

// Skapa lösenordet med crypt()
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
$stmt->bindParam(":username", $username);
$stmt->bindParam(":password", $password); // Från crypt
$stmt->execute();

// -------------------------------------------------------------------------------------------------

// Testa lösenord (inloggning)

// Vi låtsas på nytt att detta önskade användarnamn kommer från användaren och redan filtrerats
$username = "testuser";
// Ditto för lösenordet
$user_submitted_password = "hejsan";

// Hämta lösen (det riktiga) ur databasen
// Du skulle också kunna passa på att hämta mer info att använda om inloggningen lyckats
$stmt = $dbh->prepare("SELECT password FROM users WHERE username = :username");
$stmt->bind(":username", $username);
$stmt->execute();
$userdata = $stmt->fetch(); // $userdata är nu en array eller false

if ( empty($userdata) ) {
    // Användaren existerar inte alls i databasen
    // Vad göra? Visa lämpligt felmeddelande och avbryt
    // .........
    exit;
}

// Lägg märke till att lösenordet innehåller saltet. Därför är det parameter till crypt()
if ( crypt($user_submitted_password, $userdata['password']) == $userdata['password'] ) {
    // Hurra inloggad
    // Ställ in sessionsvariabler
    // Ev. omdirigera med header("Location: ....")
    
}

/*
 * Alternativ lösning som inte funkar på Windows som saknar stöd för crypt() i operativsystemet
 * Annars är denna måhända något elegantare
 */
$stmt = $dbh->prepare(
    "SELECT password FROM users WHERE username = :username AND ENCRYPT(:upassword, password) = password"
);
$stmt->bind(":username", $username);
$stmt->bind(":upassword", $user_submitted_Password);
$stmt->execute();
$userdata = $stmt->fetch(); // $userdata är nu en array eller false

?>
-- SQL för matchande DB
-- Lösenordslängd i DB 106 tecken
-- Databasens struktur - tabell users

username varchar(20) not null PRIMARY_KEY
password varchar(106) not null

CREATE TABLE users (
    username varchar(20) not null PRIMARY_KEY,
    password varchar(106) not null,
) CHARACTER SET utf8 COLLATE utf8_swedish_ci ENGINE = InnoDB



