<?php
if (!defined('sugarEntry') || !sugarEntry) {
    define('sugarEntry', true);
}

// SuiteCRM bootstrap
chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

header('Content-Type: text/html; charset=UTF-8');
echo "<html><head><title>OAuth Table Structure Debug</title></head><body>";
echo "<h1>🔍 OAuth Table Structure Analysis</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ddd;padding:8px;text-align:left} tr:nth-child(even){background:#f2f2f2}</style>";

try {
    global $db, $current_user;
    
    echo "<h2>📊 Current User Information</h2>";
    if ($current_user && $current_user->id) {
        echo "<p class='success'>✅ Current User ID: " . $current_user->id . "</p>";
        echo "<p class='success'>✅ Current User Name: " . ($current_user->user_name ?? 'Unknown') . "</p>";
    } else {
        echo "<p class='error'>❌ No current user found</p>";
        exit;
    }
    
    echo "<h2>🗄️ External OAuth Connections Table Structure</h2>";
    
    // Check if table exists
    $tableCheck = $db->query("SHOW TABLES LIKE 'external_oauth_connections'");
    if (!$tableCheck || !$db->fetchByAssoc($tableCheck)) {
        echo "<p class='error'>❌ Table 'external_oauth_connections' does not exist</p>";
        exit;
    }
    
    echo "<p class='success'>✅ Table 'external_oauth_connections' exists</p>";
    
    // Get table structure
    echo "<h3>📋 Table Columns</h3>";
    $descResult = $db->query("DESCRIBE external_oauth_connections");
    
    if ($descResult) {
        echo "<table>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        $columns = [];
        while ($row = $db->fetchByAssoc($descResult)) {
            $columns[] = $row['Field'];
            echo "<tr>";
            echo "<td><strong>" . htmlspecialchars($row['Field']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h3>🔍 Detected Column Names</h3>";
        echo "<p><strong>Available columns:</strong> " . implode(', ', $columns) . "</p>";
        
        // Check for user ID field variations
        $userIdFields = array_filter($columns, function($col) {
            return stripos($col, 'user') !== false || stripos($col, 'assigned') !== false;
        });
        
        if (!empty($userIdFields)) {
            echo "<p class='success'>✅ User ID field(s) found: <strong>" . implode(', ', $userIdFields) . "</strong></p>";
        } else {
            echo "<p class='error'>❌ No user ID field found</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Could not describe table structure</p>";
    }
    
    echo "<h2>📊 Current OAuth Connections</h2>";
    
    // Try different possible user field names
    $possibleUserFields = ['user_id', 'assigned_user_id', 'created_by', 'modified_user_id'];
    $correctUserField = null;
    
    foreach ($possibleUserFields as $field) {
        if (in_array($field, $columns)) {
            $correctUserField = $field;
            break;
        }
    }
    
    if ($correctUserField) {
        echo "<p class='success'>✅ Using user field: <strong>$correctUserField</strong></p>";
        
        $userId = $db->quote($current_user->id);
        $query = "SELECT * FROM external_oauth_connections WHERE $correctUserField = $userId AND deleted = 0";
        
        echo "<p><strong>Query:</strong> <code>$query</code></p>";
        
        $result = $db->query($query);
        
        if ($result) {
            $connections = [];
            while ($row = $db->fetchByAssoc($result)) {
                $connections[] = $row;
            }
            
            if (empty($connections)) {
                echo "<p class='error'>❌ No OAuth connections found for current user</p>";
            } else {
                echo "<p class='success'>✅ Found " . count($connections) . " OAuth connection(s)</p>";
                
                echo "<table>";
                echo "<tr><th>ID</th><th>Name</th><th>Provider</th><th>User Field</th><th>Has Access Token</th><th>Expires</th><th>Created</th></tr>";
                
                foreach ($connections as $conn) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars(substr($conn['id'], 0, 8)) . "...</td>";
                    echo "<td>" . htmlspecialchars($conn['name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($conn['provider'] ?? $conn['type'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($conn[$correctUserField]) . "</td>";
                    echo "<td>" . (!empty($conn['access_token']) ? '✅ Yes' : '❌ No') . "</td>";
                    echo "<td>" . htmlspecialchars($conn['access_token_expires'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($conn['date_created'] ?? $conn['date_entered'] ?? 'N/A') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        } else {
            echo "<p class='error'>❌ Query failed: " . $db->lastError() . "</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Could not determine correct user field</p>";
        
        // Show all records to help debug
        echo "<h3>🔧 All OAuth Records (Debug)</h3>";
        $allQuery = "SELECT * FROM external_oauth_connections WHERE deleted = 0 LIMIT 10";
        $allResult = $db->query($allQuery);
        
        if ($allResult) {
            echo "<table>";
            $headerShown = false;
            while ($row = $db->fetchByAssoc($allResult)) {
                if (!$headerShown) {
                    echo "<tr>";
                    foreach (array_keys($row) as $key) {
                        echo "<th>" . htmlspecialchars($key) . "</th>";
                    }
                    echo "</tr>";
                    $headerShown = true;
                }
                
                echo "<tr>";
                foreach ($row as $value) {
                    $displayValue = $value;
                    if (strlen($displayValue) > 50) {
                        $displayValue = substr($displayValue, 0, 47) . "...";
                    }
                    echo "<td>" . htmlspecialchars($displayValue) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<h2>🔧 Recommended Fix</h2>";
    if ($correctUserField && $correctUserField !== 'user_id') {
        echo "<div style='background:#fff3cd;padding:15px;border:1px solid #ffeaa7;border-radius:4px'>";
        echo "<h3>⚠️ Column Name Mismatch Found</h3>";
        echo "<p>The OAuth logout is failing because the code is looking for <code>user_id</code> but the actual column is <code><strong>$correctUserField</strong></code>.</p>";
        echo "<p><strong>Solution:</strong> Update the controller code to use the correct column name.</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Fatal Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<p><a href='index.php?module=Emails&action=index' style='background:#007bff;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>Back to Emails</a></p>";

echo "</body></html>";
?>