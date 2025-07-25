<?php
// Test Gmail connection detection

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<h1>Gmail Connection Detection Test</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

try {
    global $db, $current_user;
    
    // Test 1: Check existing OAuth connections
    echo "<h2>Test 1: All OAuth Connections</h2>";
    $query = "SELECT id, name, type, assigned_user_id, created_by FROM external_oauth_connections WHERE deleted = 0";
    $result = $db->query($query);
    
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Assigned User</th><th>Created By</th></tr>";
    while ($row = $db->fetchByAssoc($result)) {
        echo "<tr>";
        echo "<td>" . ($row['id'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['name'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['type'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['assigned_user_id'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['created_by'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test 2: Check Gmail connections specifically
    echo "<h2>Test 2: Gmail Connections</h2>";
    $query = "SELECT id, name, type FROM external_oauth_connections WHERE name LIKE '%Gmail%' AND deleted = 0";
    $result = $db->query($query);
    
    $gmailCount = 0;
    while ($row = $db->fetchByAssoc($result)) {
        $gmailCount++;
        echo "<p>Gmail Connection $gmailCount: " . $row['name'] . " (Type: " . $row['type'] . ")</p>";
    }
    
    if ($gmailCount === 0) {
        echo "<p>❌ No Gmail connections found</p>";
    } else {
        echo "<p>✅ Found $gmailCount Gmail connection(s)</p>";
    }
    
    // Test 3: Test connection detection logic
    echo "<h2>Test 3: Connection Detection Logic</h2>";
    
    $currentUserId = $current_user->id ?? 'NONE';
    echo "<p>Current User ID: $currentUserId</p>";
    
    $query = "SELECT id FROM external_oauth_connections 
              WHERE (assigned_user_id = '$currentUserId' OR type = 'system') 
              AND name LIKE '%Gmail%' 
              AND deleted = 0 
              LIMIT 1";
    
    echo "<p>Query: $query</p>";
    
    $result = $db->query($query);
    $hasConnection = ($result && $db->fetchByAssoc($result));
    
    if ($hasConnection) {
        echo "<p>✅ hasGmailConnection() would return TRUE</p>";
    } else {
        echo "<p>❌ hasGmailConnection() would return FALSE</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php?module=Emails&action=index'>Test Emails Page</a></p>";
echo "<p><a href='index.php'>Back to SuiteCRM</a></p>";
?>