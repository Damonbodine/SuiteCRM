<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch SearchFields Metadata
 * 
 * Defines searchable fields and their query types for the ConflictSearch module
 */

$searchFields['ConflictSearch'] = array(
    'name' => array(
        'query_type' => 'default'
    ),
    'search_term' => array(
        'query_type' => 'default'
    ),
    'search_type' => array(
        'query_type' => 'default',
        'operator' => '=',
        'options' => 'conflict_search_type_dom',
        'template_var' => 'SEARCH_TYPE_OPTIONS'
    ),
    'search_status' => array(
        'query_type' => 'default',
        'operator' => '=',
        'options' => 'conflict_search_status_dom',
        'template_var' => 'SEARCH_STATUS_OPTIONS'
    ),
    'modules_searched' => array(
        'query_type' => 'default'
    ),
    'total_matches_found' => array(
        'query_type' => 'default',
        'db_field' => array('total_matches_found')
    ),
    'high_confidence_matches' => array(
        'query_type' => 'default',
        'db_field' => array('high_confidence_matches')
    ),
    'medium_confidence_matches' => array(
        'query_type' => 'default',
        'db_field' => array('medium_confidence_matches')
    ),
    'low_confidence_matches' => array(
        'query_type' => 'default',
        'db_field' => array('low_confidence_matches')
    ),
    'confidence_threshold' => array(
        'query_type' => 'default',
        'db_field' => array('confidence_threshold')
    ),
    'execution_time' => array(
        'query_type' => 'default',
        'db_field' => array('execution_time')
    ),
    'performed_by_name' => array(
        'query_type' => 'default',
        'db_field' => array('users.user_name'),
        'vname' => 'LBL_PERFORMED_BY'
    ),
    'assigned_user_id' => array(
        'query_type' => 'default'
    ),
    'assigned_user_name' => array(
        'query_type' => 'default',
        'db_field' => array('users.user_name'),
        'vname' => 'LBL_ASSIGNED_TO'
    ),
    'current_user_only' => array(
        'query_type' => 'default',
        'db_field' => array('assigned_user_id'),
        'my_items' => true,
        'vname' => 'LBL_CURRENT_USER_FILTER',
        'type' => 'bool'
    ),
    'favorites_only' => array(
        'query_type' => 'format',
        'operator' => 'subquery',
        'checked_only' => true,
        'subquery' => "SELECT favorites.parent_id FROM favorites
                        WHERE favorites.deleted = 0
                            and favorites.parent_type = 'ConflictSearch'
                            and favorites.assigned_user_id = '{1}'",
        'db_field' => array('id')
    ),
    'description' => array(
        'query_type' => 'default'
    ),
    'search_criteria' => array(
        'query_type' => 'default'
    ),
    'date_entered' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true
    ),
    'start_range_date_entered' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true
    ),
    'end_range_date_entered' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true
    ),
    'date_modified' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true
    ),
    'start_range_date_modified' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true
    ),
    'end_range_date_modified' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_date_field' => true
    ),
    'range_total_matches' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_numeric_field' => true
    ),
    'start_range_total_matches' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_numeric_field' => true
    ),
    'end_range_total_matches' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_numeric_field' => true
    ),
    'range_high_confidence' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_numeric_field' => true
    ),
    'start_range_high_confidence' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_numeric_field' => true
    ),
    'end_range_high_confidence' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_numeric_field' => true
    ),
    'range_confidence_threshold' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_numeric_field' => true
    ),
    'start_range_confidence_threshold' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_numeric_field' => true
    ),
    'end_range_confidence_threshold' => array(
        'query_type' => 'default',
        'enable_range_search' => true,
        'is_numeric_field' => true
    )
);