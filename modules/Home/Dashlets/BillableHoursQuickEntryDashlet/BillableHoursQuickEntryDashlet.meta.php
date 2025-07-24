<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * Metadata for Billable Hours Quick Entry Dashlet
 * Registers the dashlet in SuiteCRM's dashlet system
 */

global $app_strings, $current_language;

$dashletMeta['BillableHoursQuickEntryDashlet'] = array(
    'title'       => 'LBL_TITLE',
    'description' => 'LBL_DESCRIPTION', 
    'icon'        => 'clock-o',
    'category'    => 'tools'
);