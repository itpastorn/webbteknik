<?php
/**
 * The interface for all data classes and functions that use that interface
 *
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 */

/**
 * Data classes
 * 
 * All classes should also inherit from the abstract class data_item
 */
interface data
{
	
	/*
	 * Contains the 'SELECT ... FROM table' part of SQL-queries
	 * 
	 * Manually check for this, since there are no "abstract constants"
	 */
	// abstract const SELECT_SQL = "";
	
    /**
     * Loads an instance from DB
     * 
     * @param string $id  The school ID, matches DB primary key
     * @param object $dbh Instance of PDO
     * @return mixed The object instance or false
     */
    public static function loadOne($id, PDO $dbh);

    /**
     * Return array of objects with all available records
     * 
     * @todo set limits, interval for pagination, etc
     * 
     * @param object $dbh Instance of PDO
     * @param string $dbh Custom SQL SELECT-query
     * @return Array of instances of this class
     */
    public static function loadAll(PDO $dbh, $sql=false);

    /**
     * Saving an object
     * 
     * Should only happen if it has been validated and is error free
     * That test can be deferred to the abstract class preSaveChecks-method
     * @see data_items::preSaveChecks()
     * 
     * @param object $dbh A PDO object
     * @return bool Successfully saved or not
     */
    public function save(PDO $dbh);
    
    // Documented in abstract class items
    public function getId();
    
    // Documented in abstract class items
    public function getName();

    /**
     * Helper functions for sibling classes to verify foreign key relationships
     */
    public static function isExistingId($id, PDO $dbh);
    
    /*
     * Constructor must be private
     * 
     * Important that instantiation must come through factory-functions
     * in order to keep invalid data away
     * 
     * However, PHP does not allow private declarations in interface for constructor functions
     * so this must be enforced manually
     */
    // private function __construct();
}

/**
 * A function that makes a list of options for the select element, from an array of data-objects
 * 
 * @param array  $list         An array of objects that use the data interface
 * @param string $pre_selected The value that should be set as default (selected attribute)
 * @param bool   $fullinfo     If true, duplicate name and id both for value and text (for datalists)
 * @param array  $extra        An extra value
 * @return string HTML-code
 */
function makeSelectElement($list, $pre_selected='', $fullinfo=false, $extra=array())
{
    $select_elem = "";
    foreach ( $list as $item ) {
        if ( $item->getId() == $pre_selected ) {
            $selected = ' selected';
        } else {
            $selected = '';
        }
        $name = htmlspecialchars($item->getFullName());
        $id   = htmlspecialchars($item->getId());
        if ( $fullinfo ) {
            $id   = "{$name} ({$id})";
            $name = $id;
        }
        $select_elem .= <<<HTML
            <option value="{$id}"{$selected}>{$name}</option>

HTML;
    }
    if ( !empty($extra) ) {
        $select_elem .= <<<HTML
            <option value="{$extra['id']}">{$extra['name']}</option>
HTML;

    }
    return $select_elem;
}

/**
 * A function that makes a list of checkboxes for HTML from an array of data-objects
 * 
 * Each checkbox will come before a label element, both wrappen in a paragraph
 * 
 * @param array  $list      An array of objects that use the data interface
 * @param string $name      Used for name attribute on the select tag
 * @param array  $extra     An extra value
 * @return string HTML-code
 */
function makeCheckboxes($list, $name, $extra=false)
{
    $i          = 0;
    $checkboxes = "";
    foreach ( $list as $item ) {
        // Prepare values
        $id        = htmlspecialchars($item->getId());
        $item_name = htmlspecialchars($item->getName());
        // Make the paragraph
        $checkboxes .= <<<PARA
            <p>
              <input type="checkbox" name="{$name}[]" id="{$name}_{$i}" value="{$id}">
              <label for="{$name}_{$i}">{$item_name}</label>
            </p>

PARA;
         $i++;
    }
    if ( !empty($extra) ) {
        $checkboxes .= <<<HTML
            <p>
              <input type="checkbox" name="{$name}[]" id="{$name}_{$i}" value="{$extra['id']}">
              <label for="{$name}_{$i}">{$extra['item_name']}</label>
            </p>
HTML;
    }    
    return $checkboxes;
}

