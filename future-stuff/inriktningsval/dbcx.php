<?php
/**
 * Filen innehåller funktionen dbcx (database connection), samt hjälpfunktioner för databasuppkoppling.
 *
 * @author     Lars Gunther <gunther@keryx.se>
 * @copyright  Lars Gunther <gunther@keryx.se>
 * @license    Creative Commons Attribution-Noncommercial-Share Alike 3.0 http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package    testing
 * @filesource
 *
 * @todo Felhantering
 * @todo Använd registry och inte global $dsn
 * @todo AppRegistry för tidszon
 */

if ( get_magic_quotes_runtime() ) {
    // Default exception handler får fånga denna
    throw new Exception('Magic quotes runtime är på. Applikationens databasfunktioner kräver att de är av.');
    // Alla applikationer som använder databasen får själva hantera magic_quotes_gpc
}

/**
 * Vi behöver inställningarna och funktionen get_setting
 */
require_once "get-setting.php";

/**
 * Denna funktion används för att instansiera eller hämta en databaskoppling
 *
 * Syftet med att använda factory-pattern (kring en singleton-instans av ett utökat PDO objekt)
 * är att:
 * 1. Snyggare kod control-skripten ;-)
 * 2. Ställa in alla per-connection inställningar
 * Klassen keryxDB_cx fungerar enligt följande:
 * 1. Om objektet redan finns, returnera det.
 * 2. Annars, instansiera singleton, ställ in värden och returnera objektet.
 *
 * @example $dbh = dbcx();
 * @todo Tidszon etc funkar ännu bara i MySQL
 * @return object Databasobjekt
 * @throws Exception vid fel.
 */
function dbcx() {
    /**
     * Felmeddelanden
     */
    $E_BAD_PARAMETER = 'dbcx anropad med felaktig parameter: %s.';
    $E_UNSUP_DRIVER  = 'Stöd saknas ännu i applikationen för denna driver: %s';
    /**
     * PDO-objektet för uppkopplingen
     * @var object
     */
    static $db;

    // Anrop 2 och framåt? Returnera DB-objektet
    if ( !is_null($db) ) {
        return $db;
    }
    $dsn = get_setting("dsn");
    
    // Kontroll av "parametrar"
    $dsnstr = "{$dsn['phptype']}:host={$dsn['hostspec']};dbname={$dsn['database']}";
    $dbuser = $dsn['username'];
    $dbpass = $dsn['password'];
    try {
        $db = new PDO($dsnstr, $dbuser, $dbpass);
        if ( empty($db) ) {
             throw new Exception("PDO kunde inte instansieras, uppkoppling misslyckad.");
        }
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ( $db->getAttribute(PDO::ATTR_DRIVER_NAME) != 'mysql' ) {
            throw new Exception(sprintf($E_UNSUP_DRIVER, $db->getAttribute(PDO::ATTR_DRIVER_NAME)));
        }
        // http://php.net/manual/en/ref.pdo-mysql.php
        // http://stackoverflow.com/questions/578665/php-pdo-buffered-query-problem
        // $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

        // Returnerna associativa arrayer från fetch som standard
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Teckenkodning för förbindelsen
        // Se http://dev.mysql.com/doc/refman/5.1/en/charset-connection.html
        
        // TODO: Flytta till config

        // Använd följande rad för UTF-8
        $charset_sql = "SET NAMES 'UTF8' COLLATE 'utf8_swedish_ci'";
        $db->query($charset_sql);

        // Databasförbindelsens inställningar
        // Tidszon per connection kräver MySQL 4.1 eller senare och data i MySQL:s tidszonstabeller
        $dbtime = get_setting('dbtime');
        $ts_sql = "SET time_zone = '$dbtime'";
        $svar   = $db->query($ts_sql);

        // Lite inställningar för MySQL, se http://dev.mysql.com/doc/refman/5.0/en/server-sql-mode.html
        // Tolerera INGA fel under utveckling, bli generösare under drift.
        // Mina ändringar från default: STRICT_TRANS_TABLES -> STRICT_ALL_TABLES, NO_ZERO_DATE
        // NO_AUTO_CREATE_USER och NO_ENGINE_SUBSTITUTION är på som standard, men vanliga
        // PHP-skript borde aldrig utföra operationer där åtgärder de reglerar förekommer
        // För maximal portabilitet, överväg ANSI (ej implementerat här)
        $mode_sql = "SET SESSION sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE'";
        $svar     = $db->query($mode_sql);
    }
    catch(Exception $e) {
        // Som sagt: Felhanteringen är inte klar.
        echo "<pre>";
        var_dump($e);
        echo "<hr />";
        var_dump($db);
        echo "<hr />";
        var_dump($dsn);
        echo "<hr />";
        exit;
    }
    return $db;
}
