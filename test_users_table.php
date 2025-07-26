<?php
// Test users table structure

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<h1>Users Table Structure Test</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

try {
    global $db;
    
    // Test 1: Show table structure
    echo "<h2>Test 1: All Table Columns</h2>";
    $query = "SHOW COLUMNS FROM users";
    $result = $db->query($query);
    
    echo "<p>All columns in users table:</p>";
    echo "<ul>";
    while ($row = $db->fetchByAssoc($result)) {
        echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
    }
    echo "</ul>";
    
    // Test 2: Check email_addresses table
    echo "<h2>Test 2: Email Addresses Table</h2>";
    $query = "SHOW TABLES LIKE '%email%'";
    $result = $db->query($query);
    
    echo "<p>Email-related tables:</p>";
    echo "<ul>";
    while ($row = $db->fetchByAssoc($result)) {
        foreach ($row as $tableName) {
            echo "<li>$tableName</li>";
        }
    }
    echo "</ul>";
    
    // Test 3: Check email_addr_bean_rel table
    echo "<h2>Test 3: Email Address Relationships</h2>";
    $query = "SELECT * FROM email_addr_bean_rel WHERE bean_module = 'Users' AND deleted = 0 LIMIT 3";
    $result = $db->query($query);
    
    echo "<table border='1'>";
    echo "<tr><th>Bean ID</th><th>Email Address ID</th><th>Primary</th></tr>";
    while ($row = $db->fetchByAssoc($result)) {
        echo "<tr>";
        echo "<td>" . ($row['bean_id'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['email_address_id'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['primary_address'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test 4: Sample actual email addresses
    echo "<h2>Test 4: Actual Email Addresses</h2>";
    $query = "SELECT ea.email_address, eabr.bean_id 
              FROM email_addresses ea 
              JOIN email_addr_bean_rel eabr ON ea.id = eabr.email_address_id 
              WHERE eabr.bean_module = 'Users' AND eabr.deleted = 0 AND ea.deleted = 0 
              LIMIT 3";
    $result = $db->query($query);
    
    echo "<table border='1'>";
    echo "<tr><th>User ID</th><th>Email Address</th></tr>";
    while ($row = $db->fetchByAssoc($result)) {
        echo "<tr>";
        echo "<td>" . ($row['bean_id'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['email_address'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<p><a href='index.php'>Back to SuiteCRM</a></p>";
?>