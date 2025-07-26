<?php
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/MVC/preDispatch.php');

echo "=== DATABASE DEBUGGING FOR CONFLICTSEARCH MODULE ===\n\n";

// Get database connection
$db = DBManagerFactory::getInstance();

// 1. Check user preferences for tab configurations
echo "1. USER PREFERENCES - Tab Configurations:\n";
echo "================================================\n";
$query = "SELECT assigned_user_id, category, contents FROM user_preferences WHERE category = 'Home' OR contents LIKE '%tab%' OR contents LIKE '%ConflictSearch%' LIMIT 10";
$result = $db->query($query);
while ($row = $db->fetchByAssoc($result)) {
    echo "User: " . $row['assigned_user_id'] . "\n";
    echo "Category: " . $row['category'] . "\n";
    echo "Contents: " . substr($row['contents'], 0, 300) . "...\n";
    echo "---\n";
}

// 2. Check Display Modules configuration
echo "\n2. DISPLAY MODULES Configuration:\n";
echo "==================================\n";
$query = "SELECT * FROM config WHERE category = 'display_tabs' OR name LIKE '%tab%' OR name LIKE '%module%'";
$result = $db->query($query);
while ($row = $db->fetchByAssoc($result)) {
    echo "Category: " . $row['category'] . ", Name: " . $row['name'] . ", Value: " . $row['value'] . "\n";
}

// 3. Check if ConflictSearch exists in ACL tables
echo "\n3. ACL CONFIGURATION for ConflictSearch:\n";
echo "=========================================\n";
$query = "SELECT * FROM acl_actions WHERE category = 'ConflictSearch'";
$result = $db->query($query);
if ($db->getRowCount($result) == 0) {
    echo "No ACL actions found for ConflictSearch - THIS COULD BE THE ISSUE!\n";
} else {
    while ($row = $db->fetchByAssoc($result)) {
        echo "Action: " . $row['name'] . ", ACL Type: " . $row['acltype'] . "\n";
    }
}

// 4. Check ACL roles access
echo "\n4. ACL ROLES ACCESS for ConflictSearch:\n";
echo "=======================================\n";
$query = "SELECT ar.*, aa.name as action_name FROM acl_roles_actions ar 
          JOIN acl_actions aa ON ar.action_id = aa.id 
          WHERE aa.category = 'ConflictSearch'";
$result = $db->query($query);
if ($db->getRowCount($result) == 0) {
    echo "No role access configured for ConflictSearch - THIS COULD BE THE ISSUE!\n";
} else {
    while ($row = $db->fetchByAssoc($result)) {
        echo "Role: " . $row['role_id'] . ", Action: " . $row['action_name'] . ", Access: " . $row['access_override'] . "\n";
    }
}

// 5. Check if module is in the modules table
echo "\n5. MODULES TABLE Entry:\n";
echo "=======================\n";
$query = "SHOW TABLES LIKE 'modules'";
$result = $db->query($query);
if ($db->getRowCount($result) > 0) {
    $query = "SELECT * FROM modules WHERE module_name = 'ConflictSearch'";
    $result = $db->query($query);
    if ($db->getRowCount($result) == 0) {
        echo "ConflictSearch not found in modules table - THIS COULD BE THE ISSUE!\n";
    } else {
        while ($row = $db->fetchByAssoc($result)) {
            echo "Module: " . $row['module_name'] . ", Enabled: " . $row['enabled'] . "\n";
        }
    }
} else {
    echo "No modules table found in database\n";
}

// 6. Check global tab configuration
echo "\n6. GLOBAL TAB CONFIGURATION:\n";
echo "=============================\n";
$query = "SELECT * FROM config WHERE name = 'display_tabs' OR name = 'hide_tabs' OR name LIKE '%ConflictSearch%'";
$result = $db->query($query);
while ($row = $db->fetchByAssoc($result)) {
    echo "Name: " . $row['name'] . ", Value: " . $row['value'] . "\n";
}

// 7. Check user-specific hidden tabs
echo "\n7. USER-SPECIFIC HIDDEN TABS:\n";
echo "==============================\n";
$query = "SELECT * FROM user_preferences WHERE contents LIKE '%hide%' AND contents LIKE '%tab%'";
$result = $db->query($query);
while ($row = $db->fetchByAssoc($result)) {
    echo "User: " . $row['assigned_user_id'] . "\n";
    echo "Category: " . $row['category'] . "\n";
    if (strpos($row['contents'], 'ConflictSearch') !== false) {
        echo "*** CONTAINS CONFLICTSEARCH REFERENCE ***\n";
    }
    echo "Contents: " . $row['contents'] . "\n";
    echo "---\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
?>