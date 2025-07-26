<?php
/**
 * Test OAuth Fixes
 * Specifically tests the SQL quoting and user context fixes
 */

if (!defined('sugarEntry') || !sugarEntry) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

header('Content-Type: text/html; charset=UTF-8');
echo "<html><head><title>OAuth Fixes Test</title></head><body>";
echo "<h1>🔧 OAuth Fixes Test</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .warning{color:orange} .info{color:blue} code{background:#f8f8f8;padding:2px 4px;border-radius:3px} .test-box{background:#f9f9f9;border:1px solid #ddd;padding:15px;margin:10px 0;border-radius:4px}</style>";

try {
    global $current_user, $db;
    
    echo "<h2>🧪 Test 1: Database State Token Storage</h2>";
    echo "<div class='test-box'>";
    
    if ($current_user && $current_user->id && $db) {
        // Test the fixed SQL query
        $testState = bin2hex(random_bytes(16));
        $stateId = create_guid();
        $userId = $current_user->id;
        
        echo "<p><strong>Test State:</strong> <code>{$testState}</code></p>";
        echo "<p><strong>User ID:</strong> {$userId}</p>";
        
        // Test the corrected insert query
        $insertQuery = "INSERT INTO oauth_states (id, user_id, state_token, created_time) 
                       VALUES ('{$stateId}', '{$userId}', '{$testState}', NOW())";
        
        echo "<p><strong>SQL Query:</strong></p>";
        echo "<code style='display:block;background:#f8f8f8;padding:10px;margin:10px 0'>{$insertQuery}</code>";
        
        $result = $db->query($insertQuery);
        if ($result) {
            echo "<p class='success'>✅ State token stored successfully in database</p>";
            
            // Test retrieval
            $selectQuery = "SELECT * FROM oauth_states WHERE state_token = '{$testState}' LIMIT 1";
            $selectResult = $db->query($selectQuery);
            if ($selectResult && $row = $db->fetchByAssoc($selectResult)) {
                echo "<p class='success'>✅ State token retrieved successfully</p>";
                echo "<p><strong>Retrieved:</strong> " . $row['state_token'] . "</p>";
                echo "<p><strong>User ID:</strong> " . $row['user_id'] . "</p>";
                echo "<p><strong>Created:</strong> " . $row['created_time'] . "</p>";
            } else {
                echo "<p class='error'>❌ Failed to retrieve state token</p>";
            }
            
            // Clean up test data
            $db->query("DELETE FROM oauth_states WHERE id = '{$stateId}'");
            echo "<p class='info'>🧹 Cleaned up test data</p>";
            
        } else {
            echo "<p class='error'>❌ Failed to store state token: " . $db->lastError() . "</p>";
        }
    } else {
        echo "<p class='error'>❌ User or database not available</p>";
    }
    echo "</div>";
    
    echo "<h2>🧪 Test 2: ExternalOAuthConnection Creation</h2>";
    echo "<div class='test-box'>";
    
    if ($current_user && $current_user->id) {
        // Test ExternalOAuthConnection creation
        require_once('modules/ExternalOAuthConnection/ExternalOAuthConnection.php');
        
        echo "<p><strong>Current User:</strong> {$current_user->user_name} (ID: {$current_user->id})</p>";
        echo "<p><strong>User is Admin:</strong> " . (is_admin($current_user) ? "Yes" : "No") . "</p>";
        
        // Test connection creation (without saving)
        $connection = new ExternalOAuthConnection();
        $connection->id = create_guid();
        $connection->name = 'Test Gmail OAuth Connection';
        $connection->assigned_user_id = $current_user->id;
        $connection->created_by = $current_user->id;
        $connection->type = 'system';
        $connection->access_token = 'test_access_token_12345';
        $connection->refresh_token = 'test_refresh_token_67890';
        $connection->token_type = 'Bearer';
        $connection->expires_in = 3600;
        $connection->access_token_expires = date('Y-m-d H:i:s', time() + 3600);
        
        echo "<p class='info'>✅ ExternalOAuthConnection object created successfully</p>";
        echo "<p><strong>Connection ID:</strong> {$connection->id}</p>";
        echo "<p><strong>Connection Type:</strong> {$connection->type}</p>";
        echo "<p><strong>Assigned User ID:</strong> {$connection->assigned_user_id}</p>";
        
        // Test save operation
        try {
            $result = $connection->save();
            if ($result) {
                echo "<p class='success'>✅ ExternalOAuthConnection saved successfully</p>";
                
                // Clean up test connection
                $connection->mark_deleted($connection->id);
                echo "<p class='info'>🧹 Cleaned up test connection</p>";
            } else {
                echo "<p class='error'>❌ Failed to save ExternalOAuthConnection</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Exception during save: " . $e->getMessage() . "</p>";
        }
        
    } else {
        echo "<p class='error'>❌ Current user not available for testing</p>";
    }
    echo "</div>";
    
    echo "<h2>🧪 Test 3: User Context Resolution</h2>";
    echo "<div class='test-box'>";
    
    // Test user context methods
    echo "<p><strong>Global \$current_user:</strong> " . (isset($GLOBALS['current_user']) && $GLOBALS['current_user']->id ? $GLOBALS['current_user']->id : "Not set") . "</p>";
    echo "<p><strong>Session User ID:</strong> " . ($_SESSION['authenticated_user_id'] ?? "Not set") . "</p>";
    echo "<p><strong>Local \$current_user:</strong> " . ($current_user && $current_user->id ? $current_user->id : "Not set") . "</p>";
    
    if ($current_user && $current_user->id) {
        echo "<p class='success'>✅ User context is properly available</p>";
    } else {
        echo "<p class='warning'>⚠️ User context may be missing during OAuth callback</p>";
    }
    echo "</div>";
    
    echo "<h2>🎯 Results Summary</h2>";
    echo "<div class='test-box'>";
    echo "<p>If all tests above passed, the OAuth fixes should resolve the issues you encountered:</p>";
    echo "<ul>";
    echo "<li>✅ Fixed SQL quoting for state token storage</li>";
    echo "<li>✅ Fixed ExternalOAuthConnection save operation</li>";
    echo "<li>✅ Enhanced user context resolution</li>";
    echo "</ul>";
    echo "<p><strong>Next Step:</strong> Try the OAuth flow again in the Emails module</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Test Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<br><p><a href='index.php?module=Emails&action=index' style='background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>📧 Try OAuth Flow</a>";
echo " <a href='test_state_token.php' style='background:#007bff;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🧪 State Token Test</a>";
echo " <a href='index.php' style='background:#6c757d;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🏠 Home</a></p>";

echo "</body></html>";
?>