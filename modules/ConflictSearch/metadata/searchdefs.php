<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch SearchView Metadata
 * 
 * Defines the search form fields and layout for the ConflictSearch module
 */

$searchdefs['ConflictSearch'] = array(
    'templateMeta' => array(
        'maxColumns' => '3',
        'maxColumnsBasic' => '4',
        'widths' => array(
            'label' => '10',
            'field' => '30'
        )
    ),
    'layout' => array(
        'basic_search' => array(
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%'
            ),
            'search_term' => array(
                'name' => 'search_term',
                'default' => true,
                'width' => '10%'
            ),
            'search_status' => array(
                'name' => 'search_status',
                'type' => 'enum',
                'options' => 'conflict_search_status_dom',
                'default' => true,
                'width' => '10%'
            ),
            'current_user_only' => array(
                'name' => 'current_user_only',
                'label' => 'LBL_CURRENT_USER_FILTER',
                'type' => 'bool',
                'default' => false,
                'width' => '10%'
            )
        ),
        'advanced_search' => array(
            'name' => array(
                'name' => 'name',
                'default' => true,
                'width' => '10%'
            ),
            'search_term' => array(
                'name' => 'search_term',
                'default' => true,
                'width' => '10%'
            ),
            'search_type' => array(
                'name' => 'search_type',
                'type' => 'enum',
                'options' => 'conflict_search_type_dom',
                'default' => true,
                'width' => '10%'
            ),
            'search_status' => array(
                'name' => 'search_status',
                'type' => 'enum',
                'options' => 'conflict_search_status_dom',
                'default' => true,
                'width' => '10%'
            ),
            'modules_searched' => array(
                'name' => 'modules_searched',
                'default' => true,
                'width' => '10%'
            ),
            'total_matches_found' => array(
                'name' => 'total_matches_found',
                'type' => 'int',
                'default' => true,
                'width' => '10%'
            ),
            'high_confidence_matches' => array(
                'name' => 'high_confidence_matches',
                'type' => 'int',
                'default' => true,
                'width' => '10%'
            ),
            'confidence_threshold' => array(
                'name' => 'confidence_threshold',
                'type' => 'int',
                'default' => true,
                'width' => '10%'
            ),
            'performed_by_name' => array(
                'name' => 'performed_by_name',
                'type' => 'relate',
                'default' => true,
                'width' => '10%'
            ),
            'assigned_user_id' => array(
                'name' => 'assigned_user_id',
                'type' => 'enum',
                'function' => 'get_user_array',
                'default' => true,
                'width' => '10%'
            ),
            'date_entered' => array(
                'name' => 'date_entered',
                'default' => true,
                'width' => '10%'
            ),
            'date_modified' => array(
                'name' => 'date_modified',
                'default' => true,
                'width' => '10%'
            ),
            'current_user_only' => array(
                'name' => 'current_user_only',
                'label' => 'LBL_CURRENT_USER_FILTER',
                'type' => 'bool',
                'default' => false,
                'width' => '10%'
            ),
            'favorites_only' => array(
                'name' => 'favorites_only',
                'label' => 'LBL_FAVORITES_FILTER',
                'type' => 'bool',
                'default' => false,
                'width' => '10%'
            )
        )
    )
);