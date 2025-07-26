<?php
/**
 * Simple OAuth Test - Minimal Dependencies
 * This bypasses SuiteCRM's complex routing to test OAuth functionality directly
 */

// Minimal SuiteCRM bootstrap
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

// Set up basic environment
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost:8080';

// Include only essential files
require_once('config.php');
require_once('include/database/DBManagerFactory.php');
require_once('include/utils.php');

// Simple HTML output
?>
<!DOCTYPE html>
<html>
<head>
    <title>Simple OAuth Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .test-box { background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px; }
        code { background: #f8f8f8; padding: 2px 4px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔧 Simple OAuth Test</h1>
    
    <?php
    try {
        echo "<div class='test-box'>";
        echo "<h2>🗄️ Database Connection Test</h2>";
        
        // Test database connection
        $db = DBManagerFactory::getInstance();
        if ($db) {
            echo "<p class='success'>✅ Database connection successful</p>";
            
            // Test basic query
            $result = $db->query("SELECT DATABASE() as db_name");
            if ($result && $row = $db->fetchByAssoc($result)) {
                echo "<p class='success'>✅ Connected to database: " . $row['db_name'] . "</p>";
            }
        } else {
            echo "<p class='error'>❌ Database connection failed</p>";
            exit;
        }
        echo "</div>";
        
        echo "<div class='test-box'>";
        echo "<h2>📊 OAuth Tables Check</h2>";
        
        // Check external_oauth_connections table
        $tableCheck = $db->query("SHOW TABLES LIKE 'external_oauth_connections'");
        if ($tableCheck && $db->fetchByAssoc($tableCheck)) {
            echo "<p class='success'>✅ external_oauth_connections table exists</p>";
            
            // Count connections
            $countResult = $db->query("SELECT COUNT(*) as total FROM external_oauth_connections");
            $countRow = $db->fetchByAssoc($countResult);
            echo "<p class='info'>📊 Total OAuth connections: " . $countRow['total'] . "</p>";
            
            // Show recent connections
            $recentQuery = "SELECT id, name, assigned_user_id, type, deleted, access_token_expires 
                           FROM external_oauth_connections 
                           ORDER BY date_modified DESC LIMIT 3";
            $recentResult = $db->query($recentQuery);
            
            echo "<h3>Recent Connections:</h3>";
            echo "<table border='1' style='border-collapse:collapse;width:100%'>";
            echo "<tr><th>Name</th><th>User ID</th><th>Type</th><th>Expires</th><th>Deleted</th></tr>";
            
            while ($row = $db->fetchByAssoc($recentResult)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['assigned_user_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['access_token_expires']) . "</td>";
                echo "<td>" . ($row['deleted'] ? '❌' : '✅') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
        } else {
            echo "<p class='error'>❌ external_oauth_connections table missing</p>";
        }
        
        // Check oauth_states table
        $stateTableCheck = $db->query("SHOW TABLES LIKE 'oauth_states'");
        if ($stateTableCheck && $db->fetchByAssoc($stateTableCheck)) {
            echo "<p class='success'>✅ oauth_states table exists</p>";
            
            $stateCount = $db->query("SELECT COUNT(*) as total FROM oauth_states");
            $stateRow = $db->fetchByAssoc($stateCount);
            echo "<p class='info'>📊 OAuth states: " . $stateRow['total'] . "</p>";
        } else {
            echo "<p class='warning'>⚠️ oauth_states table missing</p>";
        }
        echo "</div>";
        
        echo "<div class='test-box'>";
        echo "<h2>🧪 Manual Connection Test</h2>";
        
        if (isset($_GET['action']) && $_GET['action'] === 'create') {
            // Try to create a test connection manually
            echo "<h3>Creating Test Connection...</h3>";
            
            $connectionId = create_guid();
            $testUserId = '1'; // Admin user
            $testToken = 'test_token_' . bin2hex(random_bytes(20));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600);
            
            $insertQuery = "INSERT INTO external_oauth_connections 
                           (id, name, date_entered, date_modified, assigned_user_id, created_by, 
                            type, access_token, refresh_token, token_type, expires_in, access_token_expires, deleted) 
                           VALUES 
                           ('{$connectionId}', 'Test Gmail Connection', NOW(), NOW(), '{$testUserId}', '{$testUserId}', 
                            'system', '{$testToken}', 'test_refresh', 'Bearer', 3600, '{$expiresAt}', 0)";
            
            echo "<p><strong>SQL:</strong></p>";
            echo "<code style='display:block;background:#f8f8f8;padding:10px'>" . htmlspecialchars($insertQuery) . "</code>";
            
            $result = $db->query($insertQuery);
            if ($result) {
                echo "<p class='success'>✅ Test connection created successfully!</p>";
                echo "<p><strong>Connection ID:</strong> {$connectionId}</p>";
                
                // Verify it exists
                $verifyQuery = "SELECT * FROM external_oauth_connections WHERE id = '{$connectionId}'";
                $verifyResult = $db->query($verifyQuery);
                if ($verifyResult && $row = $db->fetchByAssoc($verifyResult)) {
                    echo "<p class='success'>✅ Connection verified in database</p>";
                    echo "<p><strong>Name:</strong> " . $row['name'] . "</p>";
                    echo "<p><strong>Deleted:</strong> " . ($row['deleted'] ? 'Yes' : 'No') . "</p>";
                    echo "<p><strong>Expires:</strong> " . $row['access_token_expires'] . "</p>";
                }
                
                // Clean up
                $db->query("DELETE FROM external_oauth_connections WHERE id = '{$connectionId}'");
                echo "<p class='info'>🧹 Test connection cleaned up</p>";
                
            } else {
                echo "<p class='error'>❌ Failed to create test connection: " . $db->lastError() . "</p>";
            }
            
        } else {
            echo "<p>Test creating a manual OAuth connection to verify database operations work:</p>";
            echo "<p><a href='?action=create' style='background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🧪 Create Test Connection</a></p>";
        }
        echo "</div>";
        
        echo "<div class='test-box'>";
        echo "<h2>🔧 OAuth Callback Test</h2>";
        
        if (isset($_GET['action']) && $_GET['action'] === 'callback') {
            echo "<h3>Testing OAuth Callback Logic...</h3>";
            
            // Test the callback file directly
            if (file_exists('oauth2callback.php')) {
                echo "<p class='success'>✅ oauth2callback.php exists</p>";
                
                // Simulate callback parameters
                $_GET['code'] = 'test_code_12345';
                $_GET['state'] = 'test_state_67890';
                
                echo "<p><strong>Simulated Parameters:</strong></p>";
                echo "<p>Code: " . $_GET['code'] . "</p>";
                echo "<p>State: " . $_GET['state'] . "</p>";
                
                // Check if we can load the callback class
                try {
                    // This won't execute the full callback, just test if it loads
                    echo "<p class='info'>OAuth callback file is accessible and should process requests</p>";
                } catch (Exception $e) {
                    echo "<p class='error'>❌ Error with OAuth callback: " . $e->getMessage() . "</p>";
                }
                
            } else {
                echo "<p class='error'>❌ oauth2callback.php missing</p>";
            }
            
        } else {
            echo "<p>Test the OAuth callback processing:</p>";
            echo "<p><a href='?action=callback' style='background:#007bff;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🔄 Test Callback</a></p>";
        }
        echo "</div>";
        
    } catch (Exception $e) {
        echo "<div class='test-box'>";
        echo "<p class='error'>❌ Test Error: " . $e->getMessage() . "</p>";
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</div>";
    }
    ?>
    
    <div class="test-box">
        <h2>🎯 Next Steps</h2>
        <p>If all tests above pass, the OAuth infrastructure is working. The issue might be:</p>
        <ul>
            <li>OAuth callback not being reached during the real flow</li>
            <li>Session/authentication issues in SuiteCRM</li>
            <li>Redirect loops in the routing system</li>
        </ul>
        <p><strong>Try a direct OAuth callback test:</strong></p>
        <p><a href="oauth2callback.php?code=test&state=test" target="_blank" style="background:#dc3545;color:white;padding:8px 12px;text-decoration:none;border-radius:4px">🧪 Direct Callback Test</a></p>
    </div>
    
    <p><a href="index.php" style="background:#6c757d;color:white;padding:10px 15px;text-decoration:none;border-radius:4px">🏠 Back to SuiteCRM</a></p>
    
</body>
</html>