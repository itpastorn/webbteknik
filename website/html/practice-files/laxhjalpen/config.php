<?php
/**
 * Konfigurationsfil för kontaktformulärsskriptet
 *
 * Genom att ändra på värdena i denna fil, så kan kontaktformuläret anpassas
 * för olika webbplatser och språk.
 *
 * @author Lars Gunther
 * @licence Creative Commons Attribution-Share Alike 3.0 Unported {@link http://creativecommons.org/licenses/by-sa/3.0/}
 */

/**
 * Mejladress  till den som ska ta emot meddelandet (din egen adress)
 *
 * Lämna blank för att förhindra att din mejlbox spammas under testning
 * Måste ha ett värde för att kontaktformuläret ska fungera på riktigt
 * Bara en mejladress här. (INTE: "foo bar" <foo.bar@example.com>)
 * @var string
 */
$recipient = "";

// -------------------------------------------------------------------------
// Standardvärden för mallens variabler.
// -------------------------------------------------------------------------

// Följande variabler används i mallen, men deras värde
// kommer möjligen ändras av data som användarna fyller i.

/**
 * Namnet på den som fyller i formuläret
 *
 * Lämnas lämpligen blankt. Använd placeholder i mallen i stället för ett värde här.
 * @var string
 */
$uname = "";

/**
 * Mejladress för att användaren ska kunna få svar.
 *
 * Lämnas lämpligen blankt. Använd placeholder i mallen i stället för ett värde här.
 * @var string
 */
$umail = "";

/**
 * Default ämne
 *
 * Lämnas lämpligen blankt. Använd placeholder i mallen i stället för ett värde här.
 * @var string
 */
$msubject = "";

/**
 * Default meddelande
 *
 * Lämnas lämpligen blankt. Använd placeholder i mallen i stället för ett värde här.
 */
$mmessage = "";


// In addition to the variables above, the following are used in the template
// Any value set at this point will be overridden and they are included only
// to help authors in their template implementations

// $uname_extra
// $umail_extra
// $msubject_extra
// $mmessage_extra

// The following variables will not be used per se in the template, but their values might
// end up in it. Please note that advice about size is linked to the tests and it will be confusing if
// the messages do not match the rules.

/**
 * Meddelande som säger att ett värde är obligatoriskt
 *
 * Om ett värde är felaktigt, så visas felmmeddelandet i stället
 * @var string
 */
$required_msg = '<strong class="required">*</strong>';

/**
 * Felmeddelande för namnet på användaren
 * @var string
 */
$uname_error = '<strong class="error">Obligatorisk uppgift, måste vara ditt namn (2-100 tecken).</strong>';

/**
 * Felmeddelande för namnet på användarens mejladress
 * @var string
 */
$umail_error = '<strong class="error">Måste vara en fungerande mejladress.</strong>';

/**
 * Felmeddelande för namnet på ärenderaden
 * @var string
 */
$msubject_error = '<strong class="error">Måste vara mellan 5 och 150 tecken. ' .
    'Bara bokstäver, siffror, symboler och skiljetecken.</strong>';

/**
 * Felmeddelande om meddelandet är för kort, långt eller innehåller otillåtna tecken
 * @var string
 */
$mmessage_error = '<strong class="error">Måste vara mellan 25 och 2500 tecken. ' .
    'Får bara innehålla bokstäver, siffror, symboler och skiljetecken.</strong>';


/**
 * List över alla obligatoriska fält, utöver ärende och själva meddelandet
 * Tillåtna värden är "uname", "umail", "get_in_touch"
 * @var array
 */
$required_fields = array(
    // "uname"
    "uname", "umail"
);
 
/**
 * Sökväg till katalogen där mallarna finns
 * @var string
 */
$path_to_templates = "./";

/**
 * Mallen för kontaktformuläret
 * @var string
 */
$form_template = "ÄNDRA HÄR/kontakt-mall.html";

/**
 * Mallen för resultatsidan
 * @var string
 */
$success_template = "ÄNDRA HÄR/skickat-mall.html";

/**
 * Mall för mejlet som ska skickas
 *
 * Denna mall kommer funka på samma sätt med variabler som HTML-mallarna
 * men detta skript stödjer enbart "plain text"
 * Tre mallvariabler används:
 * $smtpsafe_subject  - ärendet, säkrat mot SMTP-injektion
 * $smtpsafe_replyto  - avsändaren, säkrad mot SMT-injektion
 * smtpsafe_message   - meddelandet, säkrat mot SMTP-injektion
 * (Om användaren skriver HTML-kod så kommer den fungera som ren text)
 * Notera: Syntaxen för NOWDOC från PHP 5.3 används
 * @var string
 */
$mail_template = <<<'MT'
Meddelande från Läxhälpens kontaktsida

Ärende: "{$smtpsafe_subject}"

Från: {$smtpsafe_replyto}

Meddelande:
------------------------------
{$smtpsafe_message}
------------------------------
MT;

/**
 * Meddelande att visa om användaren inte bockat för svar-önskas-rutan
 * @var string
 */
$get_in_touched_is_checked_message = "Avsändaren vill bli kontaktad.";


