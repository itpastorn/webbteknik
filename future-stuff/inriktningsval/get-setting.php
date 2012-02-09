<?php
/**
 * Läs in settings/konfiguration och returnera dem vid behov
 *
 * Tag bort behovet av globala variabler
 * men skapar ett beroende av denna funktion (dock skapar detta "tight coupling")
 */

/**
 * Läs konfiguration
 * 
 * @todo Ska man kunna läsa djuparrayer direkt? En andra parameter?
 * @todo Ska man kunna läsa från flera olika källor - i så fall "lazily"?
 * 
 * @param string $sname Namnet på den setting du vill läsa
 * @return mixed Värdet på den setting du läst
 */
function get_setting($sname)
{
    static $settings;
    if ( empty($settings) ) {

        // Fingera läsa settings från källa (XML, parse_ini, JSON, etc)
        
        $settings['dsn'] = array(
            'phptype'  => 'mysql',
            'hostspec' => 'localhost',
            'database' => 'kursval',
            'username' => 'valarbetare',
            'password' => 'eratebralav'
        );
        $settings['dbtime'] = 'Europe/Stockholm';
    }
    
    if ( array_key_exists($sname, $settings) ) {
        return $settings[$sname];
    } else {
        return null;
    }
    
}
