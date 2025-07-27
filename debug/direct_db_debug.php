<?php

// Direct MySQL connection using config values
$config = array(
    'host' => 'db',
    'username' => 'suiteuser',
    'password' => 'suitepass',  
    'database' => 'suitecrm'
);

try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['database']}", 
                   $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATABASE DEBUGGING FOR CONFLICTSEARCH MODULE ===\n\n";
    
    // 1. Check user preferences for tab configurations
    echo "1. USER PREFERENCES - Tab Configurations:\n";
    echo "================================================\n";
    $stmt = $pdo->query("SELECT assigned_user_id, category, contents FROM user_preferences WHERE category = 'Home' OR contents LIKE '%tab%' OR contents LIKE '%ConflictSearch%' LIMIT 10");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($results)) {
        echo "No user preferences found related to tabs or ConflictSearch\n";
    } else {
        foreach ($results as $row) {
            echo "User: " . $row['assigned_user_id'] . "\n";
            echo "Category: " . $row['category'] . "\n";
            echo "Contents: " . substr($row['contents'], 0, 300) . "...\n";
            echo "---\n";
        }
    }
    
    // 2. Check Display Modules configuration
    echo "\n2. DISPLAY MODULES Configuration:\n";
    echo "==================================\n";
    $stmt = $pdo->query("SELECT * FROM config WHERE category = 'display_tabs' OR name LIKE '%tab%' OR name LIKE '%module%'");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($results)) {
        echo "No display module configurations found\n";
    } else {
        foreach ($results as $row) {
            echo "Category: " . $row['category'] . ", Name: " . $row['name'] . ", Value: " . $row['value'] . "\n";
        }
    }
    
    // 3. Check if ConflictSearch exists in ACL tables
    echo "\n3. ACL CONFIGURATION for ConflictSearch:\n";
    echo "=========================================\n";
    $stmt = $pdo->query("SELECT * FROM acl_actions WHERE category = 'ConflictSearch'");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($results)) {
        echo "*** NO ACL ACTIONS FOUND FOR CONFLICTSEARCH - THIS COULD BE THE ISSUE! ***\n";
    } else {
        foreach ($results as $row) {
            echo "Action: " . $row['name'] . ", ACL Type: " . $row['acltype'] . "\n";
        }
    }
    
    // 4. Check ACL roles access
    echo "\n4. ACL ROLES ACCESS for ConflictSearch:\n";
    echo "=======================================\n";
    $stmt = $pdo->query("SELECT ar.*, aa.name as action_name FROM acl_roles_actions ar 
              JOIN acl_actions aa ON ar.action_id = aa.id 
              WHERE aa.category = 'ConflictSearch'");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($results)) {
        echo "*** NO ROLE ACCESS CONFIGURED FOR CONFLICTSEARCH - THIS COULD BE THE ISSUE! ***\n";
    } else {
        foreach ($results as $row) {
            echo "Role: " . $row['role_id'] . ", Action: " . $row['action_name'] . ", Access: " . $row['access_override'] . "\n";
        }
    }
    
    // 5. Check if module is in the modules table
    echo "\n5. MODULES TABLE Entry:\n";
    echo "=======================\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'modules'");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($results)) {
        echo "No modules table found in database\n";
    } else {
        $stmt = $pdo->query("SELECT * FROM modules WHERE module_name = 'ConflictSearch'");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($results)) {
            echo "*** CONFLICTSEARCH NOT FOUND IN MODULES TABLE - THIS COULD BE THE ISSUE! ***\n";
        } else {
            foreach ($results as $row) {
                echo "Module: " . $row['module_name'] . ", Enabled: " . $row['enabled'] . "\n";
            }
        }
    }
    
    // 6. Check global tab configuration
    echo "\n6. GLOBAL TAB CONFIGURATION:\n";
    echo "=============================\n";
    $stmt = $pdo->query("SELECT * FROM config WHERE name = 'display_tabs' OR name = 'hide_tabs' OR name LIKE '%ConflictSearch%'");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($results)) {
        echo "No global tab configuration found\n";
    } else {
        foreach ($results as $row) {
            echo "Name: " . $row['name'] . ", Value: " . $row['value'] . "\n";
        }
    }
    
    // 7. Check user-specific hidden tabs
    echo "\n7. USER-SPECIFIC HIDDEN TABS:\n";
    echo "==============================\n";
    $stmt = $pdo->query("SELECT * FROM user_preferences WHERE contents LIKE '%hide%' AND contents LIKE '%tab%'");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($results)) {
        echo "No user-specific hidden tab preferences found\n";
    } else {
        foreach ($results as $row) {
            echo "User: " . $row['assigned_user_id'] . "\n";
            echo "Category: " . $row['category'] . "\n";
            if (strpos($row['contents'], 'ConflictSearch') !== false) {
                echo "*** CONTAINS CONFLICTSEARCH REFERENCE ***\n";
            }
            echo "Contents: " . $row['contents'] . "\n";
            echo "---\n";
        }
    }
    
    // 8. Check all tables for ConflictSearch references
    echo "\n8. ALL CONFLICTSEARCH REFERENCES IN DATABASE:\n";
    echo "==============================================\n";
    $tables = ['config', 'user_preferences', 'acl_actions', 'acl_roles_actions'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT * FROM $table WHERE 1=1");
        $columns = array();
        for ($i = 0; $i < $stmt->columnCount(); $i++) {
            $col = $stmt->getColumnMeta($i);
            $columns[] = $col['name'];
        }
        
        $whereClause = implode(' LIKE "%ConflictSearch%" OR ', $columns) . ' LIKE "%ConflictSearch%"';
        $stmt = $pdo->query("SELECT * FROM $table WHERE $whereClause");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($results)) {
            echo "Found ConflictSearch references in table: $table\n";
            foreach ($results as $row) {
                print_r($row);
            }
        }
    }
    
    echo "\n=== ANALYSIS COMPLETE ===\n";
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
?>