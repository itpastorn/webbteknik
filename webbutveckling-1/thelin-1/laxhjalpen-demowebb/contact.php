<?php
/**
 * This is a contact form handling script to be used with WaSP Interact courses in web design
 *
 * How to use:
 *
 * Make a template, as a suggestion called "contact-template.html" and put it in a directory
 * not accessible from the web (For security reasons templates should exist outside of the web root.)
 *
 * The template should be a straight html file and encoded using UTF-8 or US-ASCII. A simple example is provided.
 * Within that template you may put php variables, which MUST be enclosed in brackets.
 * Default values for these variables can be set below.
 *
 * For most form field in the form there will be two variables: One will be the value, one will be any
 * extra messages, like "required" or error messages.
 *
 * The form can be reduced to just two fields (subject and message) and a submit button.
 * These are always required and the form will not send e-mails if they are missing.
 *
 * In order to practice more stuff it may contain more fields, as in the example.
 * If there is an "mcategory" field, it must be in the form of radio buttons.
 * The user may modify the settings below to require more fields.
 *
 * One field, with the name attribute value "get_in_touch", may be included, but has no value carrying PHP variables
 * attached. It is simply used to practice the use of a checkbox.
 *
 * After a successful submission of the form, a "thank you" template will be used ("success-template.html").
 * The user may chose alternative names for the templates in the preferences.
 *
 * This script has been written to highlight pedagogic values. It has not been tested on production sites.
 * It does not use optimal algorithms or patterns for use outside of a teaching situation.
 *
 * @author Lars Gunther on behalf of WaSP Interact
 * @licence Creative Commons Attribution-Share Alike 3.0 Unported {@link http://creativecommons.org/licenses/by-sa/3.0/}
 *
 * @todo Support placeholder attributes from HTML5
 */


// ----------------------------------------------------------------------------------------
// You should not change anything below this comment, unless you are really good at PHP ;-)
// But please read it as a learning experience
// ----------------------------------------------------------------------------------------

/**
 * Configuration
 */
require "config.php";

// Sessions are used to prevent multiple submits
session_start();

// make sure server is sending the page as UTF-8
header('Content-type: text/html; charset=UTF-8');

// Check that we are at least on PHP 5.2
if ( phpversion() < '5.2' ) {
    trigger_error('The server is running a version of PHP that is too old. 5.2+ is required.');
    $has_config_errors = true;
}

// Check that the user has not messed up any preferences

$has_config_errors = false;

if ( array_diff($required_fields, array("uname", "umail", "mcategory")) ) {
    trigger_error('Forbidden value in $required_fields.', E_USER_NOTICE);
    $has_config_errors = true;
}

if ( ! is_readable($path_to_templates . $form_template) ) {
    trigger_error('Form template not available or readable.', E_USER_NOTICE);
    $has_config_errors = true;
}

if ( ! is_readable($path_to_templates . $success_template) ) {
    trigger_error('Success template not available or readable.', E_USER_NOTICE);
    $has_config_errors = true;
}

if ( ! empty($recipient) ) {
    if ( ! filter_var($recipient, FILTER_VALIDATE_EMAIL) ) {
        trigger_error('Recipient e-mail address not functional. Leave blank for dry runs.', E_USER_NOTICE);
    }
}

// Check that the message-template does not allow SMTP-headers
// We do this by allowing the use of colon on the first line
// Note that this check is not very technically precise. It is easy to understand, though.
if ( strpos(":", $mail_template) < strpos("\n", $mail_template) ) {
    trigger_error('Mail template is not safe for usage. It must not contan a colon the first line.', E_USER_NOTICE);
}

if ( $has_config_errors ) {
    exit("<h1>Configuration errors exists. Check your settings and re-run the script.</h1>");
}

// Set additional required fields
$required_fields = array_merge(array('msubject', 'mmessage'), $required_fields);

// Initial values for fields, used with an empty form
$uname_extra     = in_array('uname', $required_fields)     ? $required_msg    : "";
$umail_extra     = in_array('umail', $required_fields)     ? $required_msg    : "";
$msubject_extra  = in_array('msubject', $required_fields)  ? $required_msg    : "";
$mmessage_extra  = in_array('mmessage', $required_fields)  ? $required_msg    : "";
$mcategory_extra = in_array('mcategory', $required_fields) ? $required_choice : "";

// variables to indicate if a radiobutton is selected
$mcat_complaint_checked  = "";
$mcat_suggestion_checked = "";
$mcat_other_checked      = "";
// And the same for the checkbox
$get_in_touch_checked = "";

// Store suggested message text to enable test of changed value
$placeholder_mmessage = $mmessage;

// Skip tests if the form is displayed for the first time
if ( ! empty($_POST) ) {

    // Copy all values from $_POST as it should not be treated as a writable array
    $unsafe = $_POST;
    $_POST  = array(); // No longer needed. Empty to avoid further usage.

    // If prevent_multiple_submits is missing it is a configuration error
    if ( empty($unsafe['prevent_multiple_submits']) ) {
        echo "<h1>Configuration error: prevent_multiple_submits field is missing in contact form.</h1>";
        exit;
    }

    // Make sure that 'prevent_multiple_submits' is aligned with the session variable, to prevent multiple submits
    // when data has been successfully entered and to prevent tampering like CSRF
    if (
        empty($_SESSION['prevent_multiple_submits']) OR
        $_SESSION['prevent_multiple_submits'] !== $unsafe['prevent_multiple_submits']
    ) {
        // In this case we simply reload the form from scratch
        header('Location: '. $_SERVER['PHP_SELF']);
        exit;
    }

    // Allow newlines in mmessage (filter will remove these further down - we need to put them back)
    // Also, harmonize newlines
    $unsafe['mmessage'] = str_replace(
        array("\r\n", "\r", "\n"),
        array("@NEWLINE@", "@NEWLINE@", "@NEWLINE@"),
        $unsafe['mmessage']
    );

    // Do initial cleanup before tests
    foreach ( $unsafe as $key => $data ) {
        if ( ! is_well_formed_utf8($data) ) {
            // Non-well formed UTF-8 is a technical error or a server configuration error
            // or perhaps a malicious attempt to manipulate the system
            // The script will not accept this and will exit at this point
            trigger_error(
                "Server or data communication error. Non well-formed UTF-8 data enountered",
                E_USER_ERROR
            );
        }
        // Strip out everything below ASCII 32 = bad chars
        // Tests will be applied further down to ensure that every field contains only appropriate characters
        // This initial filter will rip away the most common explitable or otherwise troublesome characters
        $still_unsafe[$key] = filter_var($unsafe[$key], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
    }

    // Put newlines back in mmessage and adapt to SMTP standard "\r\n"
    $still_unsafe['mmessage'] = str_replace("@NEWLINE@", "\r\n", $still_unsafe['mmessage']);

    // Check if user submitted data is flawed

    // Assume flawlesssness. As long as this array is empty, there are no errors.
    $error_fields = array();

    // Note: The following code could be rewritten in a shorter and more effectice way using
    // the filter extension. It is kept this way since it is more instructive for people who
    // are new to PHP

    // Test get_in_touch - if used (by definition it can not be required)
    if ( isset($still_unsafe['get_in_touch']) && $still_unsafe['get_in_touch'] !== 'yes' ) {
        // This is a checkbox field
        // If it has been tampered with or if the form returns bad values we simply stop executing
        echo "<h1>Bad data from checlbox field</h1>\n";
        echo "<p>This is due to a configuration error or due to tempering with the form as such.</p>";
        exit;
    }
    if ( isset($still_unsafe['get_in_touch']) ) {
        // If user wishes to be contacted he/she must submit an email adress regardless of configuration
        if ( ! in_array('umail', $required_fields) ) {
            $required_fields[] = 'umail';
        }
    } else {
        $get_in_touched_is_checked_message = "";
    }

    // Check for possible errors in the user's real name
    $testing = isset($still_unsafe['uname']) ? $still_unsafe['uname'] : null;
    // Note. The line above is PHP <= 5.2 syntax and could be rewritten in PHP 5.3+ as
    // $testing = isset($still_unsafe['uname']) ?: null;
    // The idea is to assign null when undefined, so we do not have to handle that case for all following checks
    if ( in_array('uname', $required_fields) && mb_strlen($testing, 'utf-8') < 2 ) {
        // No value at all or too short
        $error_fields[] = 'uname';
    } elseif ( mb_strlen($testing, 'utf-8') > 100 ) {
        // Too long
        $error_fields[] = 'uname';
    } elseif ( ! preg_match("/^(\\pL|\\x20|-)+$/u", $testing) ) {
        // Contains forbidden characters
        // We only allow Unicode Letters and space or hyphen
        $error_fields[] = 'uname';
    }

    $testing = isset($still_unsafe['umail']) ? $still_unsafe['umail'] : null;
    if ( empty($testing) && in_array('umail', $required_fields) ) {
        // No value at all, but it is required
        $error_fields[] = 'umail';
    } elseif ( ! empty($testing) &&
               ! filter_var($testing, FILTER_VALIDATE_EMAIL) ) {
        // There is something but not an email address
        $error_fields[] = 'umail';
    }

    // Now please let me restate that the testing methodology is quite repetitive and not effecient
    // It is - as said- kept this way for pedagogical purposes

    $testing = isset($still_unsafe['msubject']) ? $still_unsafe['msubject'] : null;
    if ( in_array('msubject', $required_fields) && mb_strlen($testing, 'utf-8') < 5 ) {
        // No value at all or too short
        $error_fields[] = 'msubject';
    } elseif ( mb_strlen($testing, 'utf-8') > 150 ) {
        // Too long
        $error_fields[] = 'msubject';
    } elseif ( ! preg_match("/^(\\pL|\\pN|\\pS|\\pP|\\x20)+$/u", $testing) ) {
        // We only allow Unicode Letters, Numbers, Symbols, Punctuation and space
        $error_fields[] = 'msubject';
    }

    // Message rules are very similar to msubject rules so we do get some code duplication
    // that could be abstracted into a function or better usage of the filter extension
    // (Still kept for pedagogic purposes...)
    $testing = isset($still_unsafe['mmessage']) ? $still_unsafe['mmessage'] : null;
    if ( in_array('mmessage', $required_fields) && mb_strlen($testing, 'utf-8') < 25 ) {
        // No value at all or too short
        $error_fields[] = 'mmessage';
    } elseif ( mb_strlen($testing, 'utf-8') > 2500 ) {
        // Too long
        $error_fields[] = 'mmessage';
    } elseif ( ! preg_match("/^(\\pL|\\pN|\\pS|\\pP|\\x20|\\xD\\xA)+$/u", $testing) ) {
        // We only allow Unicode Letters, Numbers, Symbols, Punctuation, space and newlines
        // Note that newlines have been harmonized to \r\n = \\xD\\xA
        // I prefer hex codes in this regexp for readability reasons but admit its a matter of taste
        $error_fields[] = 'mmessage';
    } elseif ( levenshtein($testing, $placeholder_mmessage) < 10 ) {
        // Levenshtein is used to make sure that the message has substantially changed from the placeholder one
        $error_fields[] = 'mmessage';
    }

    $testing = isset($still_unsafe['mcategory']) ? $still_unsafe['mcategory'] : null;
    if (
        in_array('mcategory', $required_fields) && empty($testing)
        OR
        ! in_array($testing, array(null, 'complaint', 'suggestion', 'other'))
    ) {
        // No value set but it is required or wrong value set
        $error_fields[] = 'mcategory';
    }

    // Activate error messages and log to console
    foreach ( $error_fields as $ef ) {
        $error_name = $ef . '_error';
        $extra_name = $ef . '_extra';
        // Assigning error messages to the "extra" variable
        // Note that we are using a dynamic variables here. $$ is not a bug but a feature!
        $$extra_name = $$error_name;
    }

    $successfully_sent = false;

    if ( empty($error_fields) ) {
        // Send mail and show success template

        // The first values are safe for usage in e-mail, since they have been filtered and can not contain
        // data that acts as SMTP-headers
        $smtpsafe_subject = $still_unsafe['msubject'];
        if ( ! empty($still_unsafe['mcategory']) ) {
            $smtpsafe_subject .= ' (' . $localized_categories[$still_unsafe['mcategory']] . ')';
        }

        if ( empty($still_unsafe['uname']) ) {
            $smtpsafe_replyto = "";
        } else {
            $smtpsafe_replyto = $still_unsafe['uname'];
        }
        if ( empty($still_unsafe['umail']) ) {
            $smtpsafe_replyto .= "";
        } else {
            $smtpsafe_replyto .= '<' . $still_unsafe['umail'] . '>';
        }

        $message = str_replace('{$smtpsafe_replyto}', $smtpsafe_replyto, $mail_template);
        $message = str_replace('{$get_in_touched_is_checked_message}', $get_in_touched_is_checked_message, $message);
        $message = str_replace('{$smtpsafe_subject}', $smtpsafe_subject, $message);
        $message = str_replace('{$smtpsafe_message}', $still_unsafe['mmessage'], $message);

        // Store return value from the mail function to enable error message
        $successfully_sent = mail($recipient, $smtpsafe_subject, $message, "Reply-to: {$smtpsafe_replyto}");

        $_SESSION['prevent_multiple_submits'] = null;

    }

    // In PHP 5.3 we could use the shorthand syntax, but this script is 5.2 in min-version
    $still_unsafe['uname']    = isset($still_unsafe['uname'])    ? $still_unsafe['uname']    : '';
    $still_unsafe['umail']    = isset($still_unsafe['umail'])    ? $still_unsafe['umail']    : '';
    $still_unsafe['msubject'] = isset($still_unsafe['msubject']) ? $still_unsafe['msubject'] : '';
    $still_unsafe['mmessage'] = isset($still_unsafe['mmessage']) ? $still_unsafe['mmessage'] : '';
    // The following 4 values are user submitted and thus may contain HTML, JavaScript, etc.
    // Before usage on re-shown contact page or on the success page we therefore need to escape them
    // before they can be considered totally safe for such usage
    // All other user submitted values have been tested against whitelists and are safe
    $uname    = htmlspecialchars($still_unsafe['uname']);
    $umail    = htmlspecialchars($still_unsafe['umail']);
    $msubject = htmlspecialchars($still_unsafe['msubject']);
    $mmessage = htmlspecialchars($still_unsafe['mmessage']);

    // Set selected attribute on radio button according to user choice
    if ( isset($still_unsafe['mcategory']) ) {
        switch ( $still_unsafe['mcategory'] ) {
        case 'complaint':
            $mcat_complaint_checked = 'checked="checked"';
            break;
        case 'suggestion':
            $mcat_suggestion_checked = 'checked="checked"';
            break;
        case 'other':
            $mcat_other_checked = 'checked="checked"';
            break;
        }
    }
    // Re-enable the checkbox if checked
    if ( ! empty($still_unsafe['get_in_touch']) ) {
        $get_in_touch_checked = 'checked="checked"';
    }
}

// Use form template for the first run or if the form has errors
$use_form_template = empty($still_unsafe) || ! empty($error_fields);

if ( $use_form_template ) {
    $template = $path_to_templates . $form_template;
    // The value used to prevent multiple submits
    $_SESSION['prevent_multiple_submits'] = uniqid('prevent_', true);
} elseif ( $successfully_sent ) {
    $template = $path_to_templates . $success_template;
    // Use the SMTP-formatted message in this scenario
    $mmessage = htmlspecialchars($message);
} else {
    // Form was technically OK, but message could not be delivered
    exit("<h1>Technical error. Mail could not be delivered.</h1>\n<h2>Note: All data was correctly submitted.</h2>");
}

$template = file_get_contents($template);

$template = str_replace('{$uname}', $uname, $template);
$template = str_replace('{$umail}', $umail, $template);
$template = str_replace('{$uname_extra}', $uname_extra, $template);
$template = str_replace('{$umail_extra}', $umail_extra, $template);
$template = str_replace('{$msubject}', $msubject, $template);
$template = str_replace('{$mmessage}', $mmessage, $template);
$template = str_replace('{$msubject_extra}', $msubject_extra, $template);
$template = str_replace('{$mmessage_extra}', $mmessage_extra, $template);
$template = str_replace('{$mcategory_extra}', $mcategory_extra, $template);
$template = str_replace('{$mcat_complaint_checked}', $mcat_complaint_checked, $template);
$template = str_replace('{$mcat_suggestion_checked}', $mcat_suggestion_checked, $template);
$template = str_replace('{$mcat_other_checked}', $mcat_other_checked, $template);
$template = str_replace('{$get_in_touch_checked}', $get_in_touch_checked, $template);
$template = str_replace('{$random_string}', $_SESSION['prevent_multiple_submits'], $template);


// Display the page

echo $template;


// ------------------ Auxillary functions -----------------------------------

/**
 * Check that a string is well formed UTF-8
 *
 * Useful to avoid encoding errors on suer submitted data.
 * @param string Data to be checked
 * @return bool
 */
function is_well_formed_utf8($string)
{
    if ( empty($string) ) {
        return true;
    }
    // iconv is the fastest and best way to check this but it
    // might not be installed
    // The comments in this blog post will explain what is going on
    // http://www.sitepoint.com/blogs/2006/08/09/scripters-utf-8-survival-guide-slides/
    // Please note that we are validating, not cleaning, the input
    if ( function_exists('iconv') ) {
        return iconv('UTF-8', 'UTF-8', $string) == $string;
    }
    // Falling back to slower regexp if iconv is not available
    // When the u-flag is used, the string must be well formed or
    // nothing will match
    return preg_match('/^.{1}/us', $string) == 1;
}

