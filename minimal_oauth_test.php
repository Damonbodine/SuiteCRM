<?php
/**
 * Minimal OAuth Test with Proper SuiteCRM Bootstrap
 */

// Prevent redirects by setting up minimal environment first
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
$_SERVER['SCRIPT_NAME'] = '/minimal_oauth_test.php';
$_SERVER['REQUEST_URI'] = '/minimal_oauth_test.php';

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

// Minimal bootstrap - include only what we need
require_once('config.php');
require_once('include/TimeDate.php');
require_once('include/database/DBManagerFactory.php');
require_once('include/utils.php');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Minimal OAuth Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .test-box { background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>🔧 Minimal OAuth Test</h1>
    
    <?php
    echo "<div class='test-box'>";
    echo "<h2>🗄️ Database Connection Test</h2>";
    
    try {
        // Test database connection
        $db = DBManagerFactory::getInstance();
        echo "<p class='success'>✅ Database connection successful</p>";
        
        // Test basic query
        $result = $db->query("SELECT 1 as test");
        if ($result) {
            echo "<p class='success'>✅ Database query successful</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
        echo "</div></body></html>";
        exit;
    }
    echo "</div>";
    
    echo "<div class='test-box'>";
    echo "<h2>📊 OAuth Database Test</h2>";
    
    try {
        // Check if external_oauth_connections table exists
        $result = $db->query("SHOW TABLES LIKE 'external_oauth_connections'");
        if ($result && $db->fetchByAssoc($result)) {
            echo "<p class='success'>✅ external_oauth_connections table exists</p>";
            
            // Count existing connections
            $countResult = $db->query("SELECT COUNT(*) as total FROM external_oauth_connections");
            if ($countResult && $countRow = $db->fetchByAssoc($countResult)) {
                echo "<p class='info'>📊 Total connections: " . $countRow['total'] . "</p>";
            }
            
            // Show structure
            $structResult = $db->query("DESCRIBE external_oauth_connections");
            echo "<h3>Table Structure:</h3>";
            echo "<ul>";
            while ($structRow = $db->fetchByAssoc($structResult)) {
                echo "<li><strong>" . $structRow['Field'] . "</strong> (" . $structRow['Type'] . ")</li>";
            }
            echo "</ul>";
            
        } else {
            echo "<p class='error'>❌ external_oauth_connections table missing</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Table check error: " . $e->getMessage() . "</p>";
    }
    echo "</div>";
    
    if (isset($_GET['action']) && $_GET['action'] === 'insert_test') {
        echo "<div class='test-box'>";
        echo "<h2>🧪 Test Connection Insert</h2>";
        
        try {
            $testId = 'test-' . uniqid();
            $testQuery = "INSERT INTO external_oauth_connections 
                         (id, name, date_entered, date_modified, assigned_user_id, created_by, type, 
                          access_token, refresh_token, token_type, expires_in, access_token_expires, deleted) 
                         VALUES 
                         ('{$testId}', 'Test Connection', NOW(), NOW(), '1', '1', 'system', 
                          'test_access_token', 'test_refresh_token', 'Bearer', 3600, 
                          DATE_ADD(NOW(), INTERVAL 1 HOUR), 0)";
            
            echo "<p><strong>Test Query:</strong></p>";
            echo "<pre style='background:#f8f8f8;padding:10px;font-size:12px'>" . htmlspecialchars($testQuery) . "</pre>";
            
            $result = $db->query($testQuery);
            if ($result) {
                echo "<p class='success'>✅ Test connection inserted successfully!</p>";
                
                // Verify it was inserted
                $verifyResult = $db->query("SELECT * FROM external_oauth_connections WHERE id = '{$testId}'");
                if ($verifyResult && $row = $db->fetchByAssoc($verifyResult)) {
                    echo "<p class='success'>✅ Connection verified in database</p>";
                    echo "<p><strong>Name:</strong> " . htmlspecialchars($row['name']) . "</p>";
                    echo "<p><strong>User ID:</strong> " . htmlspecialchars($row['assigned_user_id']) . "</p>";
                    echo "<p><strong>Expires:</strong> " . htmlspecialchars($row['access_token_expires']) . "</p>";
                    echo "<p><strong>Deleted:</strong> " . ($row['deleted'] ? 'Yes' : 'No') . "</p>";
                }
                
                // Clean up
                $db->query("DELETE FROM external_oauth_connections WHERE id = '{$testId}'");
                echo "<p class='info'>🧹 Test connection cleaned up</p>";
                
            } else {
                echo "<p class='error'>❌ Failed to insert test connection</p>";
                echo "<p><strong>Error:</strong> " . $db->lastError() . "</p>";
            }
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Insert test error: " . $e->getMessage() . "</p>";
        }
        echo "</div>";
    }
    
    echo "<div class='test-box'>";
    echo "<h2>🎯 Test Actions</h2>";
    echo "<p><a href='?action=insert_test' style='background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🧪 Test Connection Insert</a></p>";
    echo "</div>";
    
    echo "<div class='test-box'>";
    echo "<h2>📁 File Check</h2>";
    
    $files_to_check = [
        'oauth2callback.php' => 'OAuth Callback',
        'custom/modules/Emails/views/view.list.php' => 'Custom Email View',
        'modules/ExternalOAuthConnection/ExternalOAuthConnection.php' => 'OAuth Connection Model'
    ];
    
    foreach ($files_to_check as $file => $name) {
        if (file_exists($file)) {
            echo "<p class='success'>✅ {$name}: {$file}</p>";
        } else {
            echo "<p class='error'>❌ {$name}: {$file} (missing)</p>";
        }
    }
    echo "</div>";
    
    echo "<div class='test-box'>";
    echo "<h2>🔄 Direct Tests</h2>";
    echo "<p>If the database tests above work, try these direct tests:</p>";
    echo "<p><a href='oauth2callback.php?code=test&state=test' target='_blank' style='background:#007bff;color:white;padding:8px 12px;text-decoration:none;border-radius:4px'>🔗 Direct OAuth Callback</a></p>";
    echo "<p><em>This should show an error about invalid state token, which means the callback is working</em></p>";
    echo "</div>";
    ?>
    
    <p><a href="index.php" style="background:#6c757d;color:white;padding:10px 15px;text-decoration:none;border-radius:4px">🏠 Back to SuiteCRM</a></p>
    
</body>
</html>