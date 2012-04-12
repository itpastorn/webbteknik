<?php
/**
 * This file contains configuration varibles for the WaSP InterACT contact for demo
 *
 * L10N and other configuration is done by editing values in this script
 * @author Lars Gunther on behalf of WaSP Interact
 * @licence Creative Commons Attribution-Share Alike 3.0 Unported {@link http://creativecommons.org/licenses/by-sa/3.0/}
 */

// -------------------------------------------------------------------------
// Default values for the template variables.
// Change these to your needs or to do localization.
// -------------------------------------------------------------------------

// The following variables are actually used in the template
// Their values will, however, perhaps be overridden by user submitted data

/**
 * This will be the real name of the person filling in the form
 *
 * Value set here will be the suggestion if a fresh form is shown
 * @var string
 */
$uname = "";

/**
 * This will be the mail address of the person filling in the form
 *
 * Value set here will be the suggestion if a fresh form is shown
 * @var string
 */
$umail = "";

/**
 * This will be the default subject
 * @var string
 */
$msubject = "";

/**
 * This will be the default message
 */
$mmessage = "Skriv ditt meddelande. Minst 25 tecken.";


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
 * This will be the message that tells a user a field is required
 *
 * Applies to all fields but radiobuttons. If a field has an error, the error message will be shown instead.
 * @var string
 */
$required_msg = '<strong class="required" title="Obligatorisk uppgift">*</strong>';

/**
 * This will be the message that tells a user choice between radiobuttons is required
 *
 * If no choice is made an error message will be shown instead.
 * @var string
 */
$required_choice = '<strong class="required">Obligatorisk</strong>';

/**
 * This will be the error message that is provided if the user's name is missing or flawed
 * @var string
 */
$uname_error = '<strong class="error">Obligatorisk uppgift, måste vara ditt namn (2-100 tecken).</strong>';

/**
 * This will be the error message that is provided if the mail address is flawed
 * @var string
 */
$umail_error = '<strong class="error">Måste vara en fungerande mejladress.</strong>';

/**
 * This will be the error message that is provided if the subject is too short or too long
 * @var string
 */
$msubject_error = '<strong class="error">Måste vara mellan 5 och 150 tecken. ' .
    'Får bara innehålla bokstäver, siffror, symboler och skiljetecken.</strong>';

/**
 * This will be the error message that is provided if message is too short
 * @var string
 */
$mmessage_error = '<strong class="error">Måste vara mellan 25 och 2500 tecken. ' .
    'Får bara innehålla bokstäver, siffror, symboler och skiljetecken.</strong>';

/**
 * This will be the error message to show if the user has not chosen a category
 * @var string
 */
$mcategory_error = '<p class="error">Du måste välja kategori.</p>'; // oanvänd

/**
 * List all required fields, besides mmessage and msubject
 * Allowed values = "uname", "umail", "get_in_touch", "mcategory"
 * @var array
 */
$required_fields = array(
    // "uname"
    // "uname", "mcategory"
    "uname", "umail"
);
// oanvänd
 
/**
 * path to directory that contains the templates
 * @var string
 */
$path_to_templates = "./";

/**
 * Template that contains the form
 * @var string
 */
$form_template = "kontakt-mall.html";

/**
 * Template to show if form submission is successful
 * @var string
 */
$success_template = "skickat-mall.html";

/**
 * Template for the actaul mail body
 *
 * This template will work like the HTML-templates, but it can not contain HTML
 * since this script only works with plain text messages
 * Three template variables are suggested:
 * $smtpsafe_subject
 * (User submitted HTML will also be treated as plain text)
 * Note: In PHP 5.3 this would be a perfect place to use NOWDOC syntax but PHP 5.2 is the minimum version
 * since Linux distros and some web hosts are slow to upgrade...
 * @var string
 */
$mail_template = <<<MT
Meddelande från kontaktsidan.

Ärende: "{\$smtpsafe_subject}"

Från: {\$smtpsafe_replyto}

Meddelande:
------------------------------
{\$smtpsafe_message}
------------------------------
MT;

/**
 * Localized categories to be used used when assembling the e-mail message
 *
 * @var string
 */
$localized_categories = array(
    'complaint'  => 'Complaint',
    'suggestion' => 'Suggestion',
    'other'      => 'Other issue'
);

/**
 * The message to be displayed in the email if the user has checked the get in touch please checkbox
 * @var string
 */
$get_in_touched_is_checked_message = "Avsändaren vill bli kontaktad.";

/**
 * E-mail address of person who receives the form contents
 *
 * During testing it is highly suggested to leave this blank and
 * do dry runs in order to prevent inbox from filling up
 * @var string
 */
$recipient = "";

