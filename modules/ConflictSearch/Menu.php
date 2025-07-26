<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch Menu Configuration
 * 
 * Defines the menu items and actions available for the ConflictSearch module
 */

global $mod_strings;

// Main module menu items
$module_menu = array(
    array(
        'index.php?module=ConflictSearch&action=EditView&return_module=ConflictSearch&return_action=DetailView',
        $mod_strings['LNK_NEW_CONFLICT_SEARCH'],
        'ConflictSearch',
        'ConflictSearch'
    ),
    array(
        'index.php?module=ConflictSearch&action=index&return_module=ConflictSearch&return_action=DetailView',
        $mod_strings['LNK_CONFLICT_SEARCH_LIST'],
        'ConflictSearch',
        'ConflictSearch'
    )
);

// Only show import option if user has admin access
if (is_admin($GLOBALS['current_user'])) {
    $module_menu[] = array(
        'index.php?module=Import&action=Step1&import_module=ConflictSearch&return_module=ConflictSearch&return_action=index',
        $mod_strings['LNK_IMPORT_CONFLICT_SEARCHES'],
        'Import',
        'ConflictSearch'
    );
}