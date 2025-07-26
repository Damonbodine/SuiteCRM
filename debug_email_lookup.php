<?php
/**
 * Debug email lookup issue
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<!DOCTYPE html><html><head><title>Debug Email Lookup</title></head><body>";
echo "<h1>🔍 Debug Email Lookup Issue</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .info{color:blue} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ddd;padding:8px;text-align:left} tr:nth-child(even){background:#f2f2f2}</style>";

try {
    global $db, $current_user;
    
    // Get the email ID from the URL
    $lookupEmailId = $_GET['email_id'] ?? '19847884fb541899';
    echo "<h2>Looking up email ID: " . htmlspecialchars($lookupEmailId) . "</h2>";
    
    echo "<h2>Current User Context</h2>";
    if ($current_user && $current_user->id) {
        echo "<p class='success'>✅ Current User ID: " . $current_user->id . "</p>";
    } else {
        echo "<p class='error'>❌ No current user context</p>";
    }
    
    echo "<h2>Database Query Test</h2>";
    
    // Test exact query from view_full_email.php
    $query = "SELECT * FROM ai_email_analysis 
              WHERE email_message_id = ? 
              AND user_id = ? 
              AND deleted = 0 
              LIMIT 1";
    
    $userId = $current_user->id ?? '1';
    echo "<p class='info'>Query: " . htmlspecialchars($query) . "</p>";
    echo "<p class='info'>Parameters: email_id='" . htmlspecialchars($lookupEmailId) . "', user_id='" . htmlspecialchars($userId) . "'</p>";
    
    $result = $db->pQuery($query, array($lookupEmailId, $userId));
    
    if ($result && ($email = $db->fetchByAssoc($result))) {
        echo "<p class='success'>✅ Email found!</p>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        foreach ($email as $key => $value) {
            echo "<tr><td><strong>" . htmlspecialchars($key) . "</strong></td><td>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>❌ Email not found with exact query</p>";
        
        // Try broader searches
        echo "<h3>Broader Search Tests</h3>";
        
        // Test 1: Just email_message_id
        $test1 = $db->pQuery("SELECT * FROM ai_email_analysis WHERE email_message_id = ? LIMIT 1", array($lookupEmailId));
        if ($test1 && $db->fetchByAssoc($test1)) {
            echo "<p class='info'>✓ Email exists for any user</p>";
        } else {
            echo "<p class='error'>✗ Email doesn't exist at all</p>";
        }
        
        // Test 2: Similar email IDs
        $test2 = $db->pQuery("SELECT email_message_id FROM ai_email_analysis WHERE email_message_id LIKE ? LIMIT 5", array($lookupEmailId . '%'));
        echo "<p class='info'>Similar email IDs:</p><ul>";
        while ($test2 && ($row = $db->fetchByAssoc($test2))) {
            echo "<li>" . htmlspecialchars($row['email_message_id']) . "</li>";
        }
        echo "</ul>";
        
        // Test 3: All emails for user
        $test3 = $db->pQuery("SELECT email_message_id, subject, sender_name, sender_email FROM ai_email_analysis WHERE user_id = ? AND deleted = 0 ORDER BY date_entered DESC LIMIT 10", array($userId));
        echo "<p class='info'>Recent emails for user $userId:</p>";
        echo "<table>";
        echo "<tr><th>Email ID</th><th>Subject</th><th>Sender Name</th><th>Sender Email</th></tr>";
        while ($test3 && ($row = $db->fetchByAssoc($test3))) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['email_message_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['subject'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['sender_name'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['sender_email'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h2>Column Check</h2>";
    $columns = $db->query("SHOW COLUMNS FROM ai_email_analysis");
    echo "<p>Available columns:</p><ul>";
    while ($columns && ($col = $db->fetchByAssoc($columns))) {
        echo "<li><strong>" . $col['Field'] . "</strong> - " . $col['Type'] . "</li>";
    }
    echo "</ul>";
    
    echo "<h2>Actions</h2>";
    echo "<p><a href='add_email_metadata.php'>🗄️ Add Email Metadata Columns</a></p>";
    echo "<p><a href='debug_existing_data.php'>📊 View All Data</a></p>";
    echo "<p><a href='test_email_fetch.php'>🧪 Test Email Fetch</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>