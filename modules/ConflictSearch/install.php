<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch Module Installation Script
 * 
 * Creates necessary database tables and configuration for the ConflictSearch module
 */

function install_conflict_search_module() {
    global $db;
    
    echo "Installing ConflictSearch Module...\n";
    
    try {
        // Create the main ConflictSearch table
        $sql = "CREATE TABLE IF NOT EXISTS conflict_search (
            id CHAR(36) NOT NULL PRIMARY KEY,
            name VARCHAR(255) NULL,
            date_entered DATETIME NULL,
            date_modified DATETIME NULL,
            modified_user_id CHAR(36) NULL,
            created_by CHAR(36) NULL,
            description TEXT NULL,
            deleted TINYINT(1) DEFAULT 0 NULL,
            assigned_user_id CHAR(36) NULL,
            search_term VARCHAR(255) NULL,
            search_type VARCHAR(50) DEFAULT 'comprehensive' NULL,
            modules_searched TEXT NULL,
            confidence_threshold INT DEFAULT 75 NULL,
            total_matches_found INT DEFAULT 0 NULL,
            high_confidence_matches INT DEFAULT 0 NULL,
            medium_confidence_matches INT DEFAULT 0 NULL,
            low_confidence_matches INT DEFAULT 0 NULL,
            search_status VARCHAR(50) DEFAULT 'pending' NULL,
            execution_time DECIMAL(10,4) DEFAULT 0.0000 NULL,
            performed_by CHAR(36) NULL,
            search_criteria TEXT NULL,
            KEY idx_conflict_search_name (name),
            KEY idx_conflict_search_assigned_user (assigned_user_id),
            KEY idx_conflict_search_status (search_status),
            KEY idx_conflict_search_date_entered (date_entered),
            KEY idx_conflict_search_performed_by (performed_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $db->query($sql);
        echo "✓ ConflictSearch table created successfully\n";
        
        // Create audit table
        $auditSql = "CREATE TABLE IF NOT EXISTS conflict_search_audit (
            id CHAR(36) NOT NULL PRIMARY KEY,
            parent_id CHAR(36) NOT NULL,
            date_created DATETIME NULL,
            created_by VARCHAR(36) NULL,
            field_name VARCHAR(100) NULL,
            data_type VARCHAR(100) NULL,
            before_value_text TEXT NULL,
            after_value_text TEXT NULL,
            before_value_string VARCHAR(255) NULL,
            after_value_string VARCHAR(255) NULL,
            KEY idx_conflict_search_audit_parent_id (parent_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        
        $db->query($auditSql);
        echo "✓ ConflictSearch audit table created successfully\n";
        
        // Insert ACL data
        insert_conflict_search_acl();
        
        // Create custom field for relationship tracking
        create_conflict_search_relationships();
        
        echo "✓ ConflictSearch module installed successfully!\n";
        echo "Note: Please run Quick Repair and Rebuild to complete the installation.\n";
        
        return true;
        
    } catch (Exception $e) {
        echo "✗ Error installing ConflictSearch module: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * Insert ACL data for ConflictSearch module
 */
function insert_conflict_search_acl() {
    global $db;
    
    // Define ACL actions for ConflictSearch
    $aclActions = [
        ['id' => create_guid(), 'name' => 'ConflictSearch', 'category' => 'module', 'acltype' => 'module', 'aclaccess' => 90],
        ['id' => create_guid(), 'name' => 'access', 'category' => 'ConflictSearch', 'acltype' => 'module', 'aclaccess' => 89],
        ['id' => create_guid(), 'name' => 'view', 'category' => 'ConflictSearch', 'acltype' => 'module', 'aclaccess' => 90],
        ['id' => create_guid(), 'name' => 'list', 'category' => 'ConflictSearch', 'acltype' => 'module', 'aclaccess' => 90],
        ['id' => create_guid(), 'name' => 'edit', 'category' => 'ConflictSearch', 'acltype' => 'module', 'aclaccess' => 90],
        ['id' => create_guid(), 'name' => 'delete', 'category' => 'ConflictSearch', 'acltype' => 'module', 'aclaccess' => 90],
        ['id' => create_guid(), 'name' => 'import', 'category' => 'ConflictSearch', 'acltype' => 'module', 'aclaccess' => 90],
        ['id' => create_guid(), 'name' => 'export', 'category' => 'ConflictSearch', 'acltype' => 'module', 'aclaccess' => 90]
    ];
    
    foreach ($aclActions as $action) {
        $checkSql = "SELECT id FROM acl_actions WHERE name = '{$action['name']}' AND category = '{$action['category']}'";
        $existing = $db->query($checkSql);
        
        if ($db->getRowCount($existing) == 0) {
            $insertSql = "INSERT INTO acl_actions (id, name, category, acltype, aclaccess, deleted) 
                         VALUES ('{$action['id']}', '{$action['name']}', '{$action['category']}', 
                                '{$action['acltype']}', {$action['aclaccess']}, 0)";
            $db->query($insertSql);
        }
    }
    
    echo "✓ ACL actions created for ConflictSearch module\n";
}

/**
 * Create relationships for ConflictSearch module
 */
function create_conflict_search_relationships() {
    global $db;
    
    // Add ConflictSearch to relationships
    $relationships = [
        'conflict_search_created_by' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'ConflictSearch',
            'rhs_table' => 'conflict_search',
            'rhs_key' => 'created_by',
            'relationship_type' => 'one-to-many'
        ],
        'conflict_search_modified_user' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'ConflictSearch',
            'rhs_table' => 'conflict_search',
            'rhs_key' => 'modified_user_id',
            'relationship_type' => 'one-to-many'
        ],
        'conflict_search_assigned_user' => [
            'lhs_module' => 'Users',
            'lhs_table' => 'users',
            'lhs_key' => 'id',
            'rhs_module' => 'ConflictSearch',
            'rhs_table' => 'conflict_search',
            'rhs_key' => 'assigned_user_id',
            'relationship_type' => 'one-to-many'
        ]
    ];
    
    foreach ($relationships as $relName => $relData) {
        $checkSql = "SELECT id FROM relationships WHERE relationship_name = '$relName'";
        $existing = $db->query($checkSql);
        
        if ($db->getRowCount($existing) == 0) {
            $id = create_guid();
            $insertSql = "INSERT INTO relationships 
                         (id, relationship_name, lhs_module, lhs_table, lhs_key, rhs_module, rhs_table, rhs_key, join_table, join_key_lhs, join_key_rhs, relationship_type, relationship_role_column, relationship_role_column_value, reverse, deleted) 
                         VALUES 
                         ('$id', '$relName', '{$relData['lhs_module']}', '{$relData['lhs_table']}', '{$relData['lhs_key']}', '{$relData['rhs_module']}', '{$relData['rhs_table']}', '{$relData['rhs_key']}', NULL, NULL, NULL, '{$relData['relationship_type']}', NULL, NULL, 0, 0)";
            $db->query($insertSql);
        }
    }
    
    echo "✓ Relationships created for ConflictSearch module\n";
}

// Run installation if called directly
if (isset($_REQUEST['install_conflict_search']) && $_REQUEST['install_conflict_search'] == '1') {
    install_conflict_search_module();
}