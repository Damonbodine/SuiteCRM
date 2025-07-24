<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch Dashlet Registration
 * 
 * Registers the ConflictSearch dashlet for use on SuiteCRM dashboards
 */

global $app_strings;

$dashletMeta['ConflictSearchDashlet'] = array(
    'module' => 'ConflictSearch',
    'title' => '⚖️ Attorney Conflict Search',
    'description' => 'Comprehensive conflict of interest search across Contacts, Accounts, and Cases. Features quick search form, recent search history, and direct access to advanced search capabilities. Essential for law firms and legal professionals.',
    'icon' => 'icon-search',
    'category' => 'Legal & Compliance',
    'height' => 315,
    'width' => 400
);
?>