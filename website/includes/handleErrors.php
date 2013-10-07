<?php
/**
 * Felhantering för vanliga PHP-fel.
 *
 * Felen samlas i en array i stället för att visas direkt.
 * Arrayen kan skrivas ut som HTML.
 *
 * @author Lars Gunther
 * @version 0.01
 */
class handleErrors 
{
     /**
      * Error stack
      * @var $errorStack
      */
     protected static $errorStack = array();

     /**
      * Error names
      * @var errorNames
      */
     protected static $errorNames = array();

     /**
      * Första gången ska en del saker göras som inte behöver upprepas
      * @var $notFirstRun;
      */
     protected static $notFirstRun;

     /**
      * Felhanteraren
      *
      * Denna metod fångar fel och lägger dem i arrayen $errorStack
      */
     public static function handler($num, $str, $file, $line, $context)
     {
        if ( empty(self::$notFirstRun) ) {
            // Skapa en array som innehåller alla slags felkonstanter
            // Stirra inte på koden här, det är vodoo!
            self::$notFirstRun = true;
            $consts = get_defined_constants(true);
            $consts = $consts['Core']; // Bara PHP:s grundkonstanter kvar
            // Filtrera bort alla konstanter som inte börjar med "E_"
            $filtered = array_flip(
                array_filter(
                    array_keys($consts),
                    'self::filter_error_consts'
                )
            );
            $filtered = array_intersect_key($consts, $filtered);
            self::$errorNames = array_flip($filtered);
            // Här slutar vodoo-koden
        }
        if ( error_reporting() == 0 ) {
            // Fel hanteras inte om visning av fel stängts av
            return;
        }
        // Lägg på felet i slutet av arrayen
        self::$errorStack[] = array($num, $str, basename($file), $line, $context);
        return true;
    }

    /**
     * Denna funktion returnerar felmeddelandena som HTML-kod.
     *
     * En ol-lista skapas med ett felmeddelande per list item.
     *
     * @param string $heading Överskrift till felmeddelandena.
     * @return string Formatterade felmeddelanden.
     */
    public static function messagesAsHTML($heading = "<h4>Felmeddelanden:</h4>\n")
    {
        $message  = $heading;
        $message .= "<ol>\n";
        foreach ( self::$errorStack as $error ) {
            $type = self::$errorNames[$error[0]];
            $message .= <<<LI
            <li>
              <strong>{$type}</strong>
              på rad <em>{$error[3]}</em>
              i filen <em>{$error[2]}</em>
              Felmeddelande: {$error[1]}
            </li>
LI;
        }
        $message .= "</ol>\n";
        return $message;
    }
    /**
     * Denna funktion loggar felen via FirePHP
     *
     * @return bool Success
     */
    public static function messagesFirePHP()
    {
        foreach ( self::$errorStack as $error ) {
            $type = self::$errorNames[$error[0]];
            $GLOBALS['FIREPHP']->log("{$type} : {$error[3]} in file {$error[2]}. Message: {$error[1]}");
        }
        return true;
    }

     /**
      * Hjälpfunktion för att filtrera ut felkonstanter
      *
      * Denna används som callback till array_filter
      *
      * @param string $constnamne Namn på konstanten
      * @return bool
      */
     private static function filter_error_consts($constnamne)
     {
         // Notera att vi skiljer på 0 (noll) och false
         return 0 === strpos($constnamne, 'E_');
     }

}
