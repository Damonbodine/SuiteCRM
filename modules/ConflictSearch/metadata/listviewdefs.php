<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch ListView Metadata
 * 
 * Defines the columns and layout for the ConflictSearch list view
 */

$listViewDefs['ConflictSearch'] = array(
    'NAME' => array(
        'width' => '25%',
        'label' => 'LBL_LIST_NAME',
        'default' => true,
        'link' => true
    ),
    'SEARCH_TERM' => array(
        'type' => 'varchar',
        'label' => 'LBL_LIST_SEARCH_TERM',
        'width' => '20%',
        'default' => true
    ),
    'SEARCH_TYPE' => array(
        'type' => 'enum',
        'label' => 'LBL_LIST_SEARCH_TYPE',
        'width' => '10%',
        'default' => true
    ),
    'SEARCH_STATUS' => array(
        'type' => 'enum',
        'label' => 'LBL_LIST_STATUS',
        'width' => '10%',
        'default' => true
    ),
    'TOTAL_MATCHES_FOUND' => array(
        'type' => 'int',
        'label' => 'LBL_LIST_TOTAL_MATCHES',
        'width' => '8%',
        'default' => true,
        'align' => 'right'
    ),
    'HIGH_CONFIDENCE_MATCHES' => array(
        'type' => 'int',
        'label' => 'LBL_LIST_HIGH_CONFIDENCE',
        'width' => '8%',
        'default' => true,
        'align' => 'right'
    ),
    'PERFORMED_BY_NAME' => array(
        'type' => 'relate',
        'link' => 'performed_by_link',
        'label' => 'LBL_LIST_PERFORMED_BY',
        'width' => '10%',
        'default' => true
    ),
    'DATE_ENTERED' => array(
        'type' => 'datetime',
        'label' => 'LBL_LIST_DATE_ENTERED',
        'width' => '10%',
        'default' => true
    ),
    'ASSIGNED_USER_NAME' => array(
        'width' => '10%',
        'label' => 'LBL_LIST_ASSIGNED_USER',
        'module' => 'Employees',
        'id' => 'ASSIGNED_USER_ID',
        'default' => false
    ),
    'MODULES_SEARCHED' => array(
        'type' => 'varchar',
        'label' => 'LBL_MODULES_SEARCHED',
        'width' => '15%',
        'default' => false
    ),
    'CONFIDENCE_THRESHOLD' => array(
        'type' => 'int',
        'label' => 'LBL_CONFIDENCE_THRESHOLD',
        'width' => '8%',
        'default' => false,
        'align' => 'right'
    ),
    'EXECUTION_TIME' => array(
        'type' => 'decimal',
        'label' => 'LBL_EXECUTION_TIME',
        'width' => '8%',
        'default' => false,
        'align' => 'right'
    ),
    'MEDIUM_CONFIDENCE_MATCHES' => array(
        'type' => 'int',
        'label' => 'LBL_MEDIUM_CONFIDENCE_MATCHES',
        'width' => '8%',
        'default' => false,
        'align' => 'right'
    ),
    'LOW_CONFIDENCE_MATCHES' => array(
        'type' => 'int',
        'label' => 'LBL_LOW_CONFIDENCE_MATCHES',
        'width' => '8%',
        'default' => false,
        'align' => 'right'
    ),
    'DESCRIPTION' => array(
        'type' => 'text',
        'label' => 'LBL_DESCRIPTION',
        'width' => '20%',
        'default' => false
    ),
    'DATE_MODIFIED' => array(
        'type' => 'datetime',
        'label' => 'LBL_DATE_MODIFIED',
        'width' => '10%',
        'default' => false
    ),
    'CREATED_BY_NAME' => array(
        'type' => 'relate',
        'link' => 'created_by_link',
        'label' => 'LBL_CREATED',
        'width' => '10%',
        'default' => false
    )
);