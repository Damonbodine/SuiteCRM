<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch vardefs
 * 
 * Field definitions for the ConflictSearch module.
 * This module tracks attorney conflict of interest searches across multiple modules.
 */

$dictionary['ConflictSearch'] = array(
    'table' => 'conflict_search',
    'audited' => true,
    'unified_search' => true,
    'full_text_search' => false,
    'unified_search_default_enabled' => false,
    'duplicate_merge' => false,
    'comment' => 'Attorney Conflict of Interest Search records',
    'fields' => array(
        
        // Standard SugarBean fields
        'id' => array(
            'name' => 'id',
            'vname' => 'LBL_ID',
            'type' => 'id',
            'required' => true,
            'reportable' => true,
            'comment' => 'Unique identifier'
        ),
        
        'name' => array(
            'name' => 'name',
            'vname' => 'LBL_NAME',
            'type' => 'name',
            'dbType' => 'varchar',
            'len' => '255',
            'unified_search' => true,
            'full_text_search' => array('boost' => 3),
            'required' => true,
            'importable' => 'required',
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'enabled',
            'massupdate' => 0,
            'no_default' => false,
            'comments' => 'Name of the conflict search',
            'help' => 'The Name used to refer to this conflict search',
            'duplicate_merge_dom_value' => '3',
            'audited' => true,
            'reportable' => true,
            'size' => '20'
        ),
        
        'date_entered' => array(
            'name' => 'date_entered',
            'vname' => 'LBL_DATE_ENTERED',
            'type' => 'datetime',
            'group' => 'created_by_name',
            'comment' => 'Date record created',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
            'studio' => array(
                'portaleditview' => false
            )
        ),
        
        'date_modified' => array(
            'name' => 'date_modified',
            'vname' => 'LBL_DATE_MODIFIED',
            'type' => 'datetime',
            'group' => 'modified_by_name',
            'comment' => 'Date record last modified',
            'enable_range_search' => true,
            'options' => 'date_range_search_dom',
            'studio' => array(
                'portaleditview' => false
            )
        ),
        
        'modified_user_id' => array(
            'name' => 'modified_user_id',
            'rname' => 'user_name',
            'id_name' => 'modified_user_id',
            'vname' => 'LBL_MODIFIED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'group' => 'modified_by_name',
            'dbType' => 'id',
            'reportable' => true,
            'comment' => 'User who last modified record',
            'massupdate' => false,
            'studio' => array(
                'portaleditview' => false
            )
        ),
        
        'modified_by_name' => array(
            'name' => 'modified_by_name',
            'vname' => 'LBL_MODIFIED_NAME',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'rname' => 'user_name',
            'table' => 'users',
            'id_name' => 'modified_user_id',
            'module' => 'Users',
            'link' => 'modified_user_link',
            'duplicate_merge' => 'disabled',
            'massupdate' => false,
            'studio' => array(
                'portaleditview' => false
            )
        ),
        
        'created_by' => array(
            'name' => 'created_by',
            'rname' => 'user_name',
            'id_name' => 'created_by',
            'vname' => 'LBL_CREATED',
            'type' => 'assigned_user_name',
            'table' => 'users',
            'isnull' => 'false',
            'dbType' => 'id',
            'group' => 'created_by_name',
            'comment' => 'User who created record',
            'massupdate' => false,
            'studio' => array(
                'portaleditview' => false
            )
        ),
        
        'created_by_name' => array(
            'name' => 'created_by_name',
            'vname' => 'LBL_CREATED',
            'type' => 'relate',
            'reportable' => false,
            'link' => 'created_by_link',
            'rname' => 'user_name',
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'created_by',
            'module' => 'Users',
            'duplicate_merge' => 'disabled',
            'importable' => 'false',
            'massupdate' => false,
            'studio' => array(
                'portaleditview' => false
            )
        ),
        
        'description' => array(
            'name' => 'description',
            'vname' => 'LBL_DESCRIPTION',
            'type' => 'text',
            'comment' => 'Full text of the note',
            'rows' => 6,
            'cols' => 80,
            'studio' => array(
                'portaleditview' => true,
                'editview' => true
            )
        ),
        
        'deleted' => array(
            'name' => 'deleted',
            'vname' => 'LBL_DELETED',
            'type' => 'bool',
            'default' => '0',
            'reportable' => false,
            'comment' => 'Record deletion indicator'
        ),
        
        'assigned_user_id' => array(
            'name' => 'assigned_user_id',
            'rname' => 'user_name',
            'id_name' => 'assigned_user_id',
            'vname' => 'LBL_ASSIGNED_TO_ID',
            'group' => 'assigned_user_name',
            'type' => 'relate',
            'table' => 'users',
            'module' => 'Users',
            'reportable' => true,
            'isnull' => 'false',
            'dbType' => 'id',
            'audited' => true,
            'comment' => 'User ID assigned to record',
            'duplicate_merge' => 'disabled'
        ),
        
        'assigned_user_name' => array(
            'name' => 'assigned_user_name',
            'link' => 'assigned_user_link',
            'vname' => 'LBL_ASSIGNED_TO_NAME',
            'rname' => 'user_name',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'table' => 'users',
            'id_name' => 'assigned_user_id',
            'module' => 'Users',
            'duplicate_merge' => 'disabled'
        ),
        
        // ConflictSearch specific fields
        'search_term' => array(
            'name' => 'search_term',
            'vname' => 'LBL_SEARCH_TERM',
            'type' => 'varchar',
            'len' => '255',
            'required' => true,
            'comment' => 'The term or entity name being searched for conflicts',
            'audited' => true,
            'unified_search' => true,
            'full_text_search' => array('boost' => 2),
            'duplicate_merge' => 'enabled',
            'merge_filter' => 'enabled',
            'importable' => 'required',
            'studio' => array(
                'editview' => true,
                'detailview' => true,
                'listview' => true
            )
        ),
        
        'search_type' => array(
            'name' => 'search_type',
            'vname' => 'LBL_SEARCH_TYPE',
            'type' => 'enum',
            'options' => 'conflict_search_type_dom',
            'len' => '50',
            'default' => 'comprehensive',
            'comment' => 'Type of conflict search performed',
            'audited' => true,
            'studio' => array(
                'editview' => true,
                'detailview' => true,
                'listview' => true
            )
        ),
        
        'modules_searched' => array(
            'name' => 'modules_searched',
            'vname' => 'LBL_MODULES_SEARCHED',
            'type' => 'varchar',
            'len' => '255',
            'default' => 'Contacts,Accounts,Cases',
            'comment' => 'Comma-separated list of modules searched',
            'audited' => true,
            'studio' => array(
                'editview' => true,
                'detailview' => true,
                'listview' => false
            )
        ),
        
        'confidence_threshold' => array(
            'name' => 'confidence_threshold',
            'vname' => 'LBL_CONFIDENCE_THRESHOLD',
            'type' => 'int',
            'len' => '3',
            'default' => '75',
            'comment' => 'Minimum confidence percentage for matches to be included',
            'audited' => true,
            'studio' => array(
                'editview' => true,
                'detailview' => true,
                'listview' => false
            )
        ),
        
        'total_matches_found' => array(
            'name' => 'total_matches_found',
            'vname' => 'LBL_TOTAL_MATCHES_FOUND',
            'type' => 'int',
            'len' => '11',
            'default' => '0',
            'comment' => 'Total number of potential conflicts found',
            'audited' => true,
            'studio' => array(
                'editview' => false,
                'detailview' => true,
                'listview' => true
            )
        ),
        
        'high_confidence_matches' => array(
            'name' => 'high_confidence_matches',
            'vname' => 'LBL_HIGH_CONFIDENCE_MATCHES',
            'type' => 'int',
            'len' => '11',
            'default' => '0',
            'comment' => 'Number of high confidence matches (90%+)',
            'audited' => true,
            'studio' => array(
                'editview' => false,
                'detailview' => true,
                'listview' => true
            )
        ),
        
        'medium_confidence_matches' => array(
            'name' => 'medium_confidence_matches',
            'vname' => 'LBL_MEDIUM_CONFIDENCE_MATCHES',
            'type' => 'int',
            'len' => '11',
            'default' => '0',
            'comment' => 'Number of medium confidence matches (70-89%)',
            'audited' => true,
            'studio' => array(
                'editview' => false,
                'detailview' => true,
                'listview' => false
            )
        ),
        
        'low_confidence_matches' => array(
            'name' => 'low_confidence_matches',
            'vname' => 'LBL_LOW_CONFIDENCE_MATCHES',
            'type' => 'int',
            'len' => '11',
            'default' => '0',
            'comment' => 'Number of low confidence matches (below 70%)',
            'audited' => true,
            'studio' => array(
                'editview' => false,
                'detailview' => true,
                'listview' => false
            )
        ),
        
        'search_status' => array(
            'name' => 'search_status',
            'vname' => 'LBL_SEARCH_STATUS',
            'type' => 'enum',
            'options' => 'conflict_search_status_dom',
            'len' => '50',
            'default' => 'pending',
            'comment' => 'Current status of the conflict search',
            'audited' => true,
            'studio' => array(
                'editview' => false,
                'detailview' => true,
                'listview' => true
            )
        ),
        
        'search_results' => array(
            'name' => 'search_results',
            'vname' => 'LBL_SEARCH_RESULTS',
            'type' => 'longtext',
            'comment' => 'JSON-encoded detailed search results',
            'audited' => false,
            'studio' => array(
                'editview' => false,
                'detailview' => false,
                'listview' => false
            )
        ),
        
        'execution_time' => array(
            'name' => 'execution_time',
            'vname' => 'LBL_EXECUTION_TIME',
            'type' => 'decimal',
            'len' => '10,4',
            'precision' => 10,
            'scale' => 4,
            'default' => '0.0000',
            'comment' => 'Time taken to execute the search in seconds',
            'audited' => true,
            'studio' => array(
                'editview' => false,
                'detailview' => true,
                'listview' => false
            )
        ),
        
        'performed_by' => array(
            'name' => 'performed_by',
            'vname' => 'LBL_PERFORMED_BY',
            'type' => 'relate',
            'rname' => 'user_name',
            'id_name' => 'performed_by',
            'table' => 'users',
            'module' => 'Users',
            'dbType' => 'id',
            'link' => 'performed_by_link',
            'comment' => 'User who performed the conflict search',
            'audited' => true,
            'studio' => array(
                'editview' => false,
                'detailview' => true,
                'listview' => true
            )
        ),
        
        'performed_by_name' => array(
            'name' => 'performed_by_name',
            'vname' => 'LBL_PERFORMED_BY_NAME',
            'type' => 'relate',
            'reportable' => false,
            'source' => 'non-db',
            'rname' => 'user_name',
            'table' => 'users',
            'id_name' => 'performed_by',
            'module' => 'Users',
            'link' => 'performed_by_link',
            'duplicate_merge' => 'disabled',
            'studio' => array(
                'editview' => false,
                'detailview' => true,
                'listview' => true
            )
        ),
        
        'search_criteria' => array(
            'name' => 'search_criteria',
            'vname' => 'LBL_SEARCH_CRITERIA',
            'type' => 'text',
            'comment' => 'Additional search criteria and parameters used',
            'audited' => true,
            'rows' => 4,
            'cols' => 80,
            'studio' => array(
                'editview' => true,
                'detailview' => true,
                'listview' => false
            )
        ),
        
        // Relationship links
        'assigned_user_link' => array(
            'name' => 'assigned_user_link',
            'type' => 'link',
            'relationship' => 'conflict_search_assigned_user',
            'vname' => 'LBL_ASSIGNED_TO_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db',
            'duplicate_merge' => 'enabled',
            'rname' => 'user_name',
            'id_name' => 'assigned_user_id',
            'table' => 'users'
        ),
        
        'modified_user_link' => array(
            'name' => 'modified_user_link',
            'type' => 'link',
            'relationship' => 'conflict_search_modified_user',
            'vname' => 'LBL_MODIFIED_BY_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db'
        ),
        
        'created_by_link' => array(
            'name' => 'created_by_link',
            'type' => 'link',
            'relationship' => 'conflict_search_created_by',
            'vname' => 'LBL_CREATED_BY_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db'
        ),
        
        'performed_by_link' => array(
            'name' => 'performed_by_link',
            'type' => 'link',
            'relationship' => 'conflict_search_performed_by',
            'vname' => 'LBL_PERFORMED_BY_USER',
            'link_type' => 'one',
            'module' => 'Users',
            'bean_name' => 'User',
            'source' => 'non-db'
        )
    ),
    
    'indices' => array(
        array(
            'name' => 'conflict_searchpk',
            'type' => 'primary',
            'fields' => array('id')
        ),
        array(
            'name' => 'idx_conflict_search_name',
            'type' => 'index',
            'fields' => array('name')
        ),
        array(
            'name' => 'idx_conflict_search_assigned',
            'type' => 'index',
            'fields' => array('assigned_user_id')
        ),
        array(
            'name' => 'idx_conflict_search_term',
            'type' => 'index',
            'fields' => array('search_term')
        ),
        array(
            'name' => 'idx_conflict_search_status',
            'type' => 'index',
            'fields' => array('search_status')
        ),
        array(
            'name' => 'idx_conflict_search_performed_by',
            'type' => 'index',
            'fields' => array('performed_by')
        ),
        array(
            'name' => 'idx_conflict_search_date_entered',
            'type' => 'index',
            'fields' => array('date_entered')
        ),
        array(
            'name' => 'idx_conflict_search_deleted',
            'type' => 'index',
            'fields' => array('deleted')
        )
    ),
    
    'relationships' => array(
        'conflict_search_assigned_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'ConflictSearch',
            'rhs_table' => 'conflict_search',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'conflict_search_modified_user' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'ConflictSearch',
            'rhs_table' => 'conflict_search',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'
        ),
        'conflict_search_created_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'ConflictSearch',
            'rhs_table' => 'conflict_search',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        ),
        'conflict_search_performed_by' => array(
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'ConflictSearch',
            'rhs_table' => 'conflict_search',
            'rhs_key' => 'performed_by',
            'relationship_type' => 'one-to-many'
        )
    ),
    
    // Enable optimistic locking for concurrent access
    'optimistic_locking' => true
);

// Use VardefManager to create the complete vardef structure
VardefManager::createVardef(
    'ConflictSearch',
    'ConflictSearch',
    array('default', 'assignable', 'security_groups')
);