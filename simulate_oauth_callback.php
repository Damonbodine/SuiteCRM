<?php
/**
 * Simulate OAuth Callback for Testing
 * This helps test the OAuth callback functionality without going through the full OAuth flow
 */

if (!defined('sugarEntry') || !sugarEntry) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

header('Content-Type: text/html; charset=UTF-8');
echo "<html><head><title>Simulate OAuth Callback</title></head><body>";
echo "<h1>🧪 Simulate OAuth Callback</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .warning{color:orange} .info{color:blue}</style>";

try {
    global $current_user, $db;
    
    if (!$current_user || !$current_user->id) {
        echo "<p class='error'>❌ You must be logged into SuiteCRM to test the OAuth callback</p>";
        echo "<p><a href='index.php' style='background:#007bff;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🏠 Login to SuiteCRM</a></p>";
        echo "</body></html>";
        exit;
    }
    
    echo "<h2>👤 Current User</h2>";
    echo "<p><strong>User:</strong> {$current_user->user_name} (ID: {$current_user->id})</p>";
    echo "<p><strong>Admin:</strong> " . (is_admin($current_user) ? "Yes" : "No") . "</p>";
    
    if (isset($_GET['action']) && $_GET['action'] === 'simulate') {
        echo "<h2>🎭 Simulating OAuth Callback</h2>";
        
        // Create a fake OAuth response
        $fakeTokens = [
            'access_token' => 'ya29.fake_access_token_' . bin2hex(random_bytes(20)),
            'refresh_token' => 'fake_refresh_token_' . bin2hex(random_bytes(15)),
            'expires_in' => 3600,
            'token_type' => 'Bearer',
            'scope' => 'https://www.googleapis.com/auth/gmail.readonly'
        ];
        
        echo "<h3>📝 Fake Token Data</h3>";
        echo "<p><strong>Access Token:</strong> " . substr($fakeTokens['access_token'], 0, 30) . "...</p>";
        echo "<p><strong>Refresh Token:</strong> " . substr($fakeTokens['refresh_token'], 0, 20) . "...</p>";
        echo "<p><strong>Expires In:</strong> {$fakeTokens['expires_in']} seconds</p>";
        
        // Test ExternalOAuthConnection creation directly
        echo "<h3>💾 Testing Connection Storage</h3>";
        
        try {
            require_once('modules/ExternalOAuthConnection/ExternalOAuthConnection.php');
            
            // Remove old connections first
            $userId = $current_user->id;
            $cleanupQuery = "UPDATE external_oauth_connections 
                           SET deleted = 1, date_modified = NOW() 
                           WHERE assigned_user_id = '{$userId}' 
                           AND name LIKE '%Gmail%' 
                           AND deleted = 0";
            $db->query($cleanupQuery);
            echo "<p class='info'>🧹 Cleaned up old connections</p>";
            
            // Create new connection
            $connection = new ExternalOAuthConnection();
            $connection->id = create_guid();
            $connection->name = 'Gmail OAuth Connection (Test)';
            $connection->assigned_user_id = $userId;
            $connection->created_by = $userId;
            $connection->type = 'system';
            
            // Store tokens (without encryption for testing)
            $connection->access_token = $fakeTokens['access_token'];
            $connection->refresh_token = $fakeTokens['refresh_token'];
            $connection->expires_in = $fakeTokens['expires_in'];
            $connection->access_token_expires = date('Y-m-d H:i:s', time() + $fakeTokens['expires_in']);
            $connection->token_type = $fakeTokens['token_type'];
            
            echo "<p><strong>Connection ID:</strong> {$connection->id}</p>";
            echo "<p><strong>Expires At:</strong> {$connection->access_token_expires}</p>";
            
            // Attempt to save
            $result = $connection->save();
            
            if ($result) {
                echo "<p class='success'>✅ Connection saved successfully!</p>";
                
                // Verify it was saved
                $verifyQuery = "SELECT id, name, assigned_user_id, type, access_token_expires, deleted 
                              FROM external_oauth_connections 
                              WHERE id = '{$connection->id}'";
                $verifyResult = $db->query($verifyQuery);
                if ($verifyResult && $row = $db->fetchByAssoc($verifyResult)) {
                    echo "<p class='success'>✅ Connection verified in database</p>";
                    echo "<p><strong>DB Record:</strong> {$row['name']} (Deleted: {$row['deleted']})</p>";
                } else {
                    echo "<p class='error'>❌ Connection not found in database after save</p>";
                }
                
                // Test the email view detection
                echo "<h3>📧 Testing Email View Detection</h3>";
                require_once('custom/modules/Emails/views/view.list.php');
                $emailView = new CustomEmailsViewList();
                
                $reflection = new ReflectionClass($emailView);
                $hasConnectionMethod = $reflection->getMethod('hasGmailConnection');
                $hasConnectionMethod->setAccessible(true);
                
                $hasConnection = $hasConnectionMethod->invoke($emailView);
                
                if ($hasConnection) {
                    echo "<p class='success'>✅ Email view detects valid Gmail connection!</p>";
                    echo "<p>🎉 <strong>This means the OAuth flow should work when you go to the Emails module</strong></p>";
                } else {
                    echo "<p class='error'>❌ Email view does not detect Gmail connection</p>";
                    echo "<p>This suggests there might be an issue with the connection detection logic</p>";
                }
                
            } else {
                echo "<p class='error'>❌ Failed to save connection</p>";
            }
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Exception during connection creation: " . $e->getMessage() . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
        
    } else {
        echo "<h2>🎯 OAuth Callback Simulation</h2>";
        echo "<p>This will create a fake OAuth connection to test if the storage and detection works correctly.</p>";
        echo "<p><a href='?action=simulate' style='background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🎭 Simulate OAuth Success</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<br><p><a href='index.php?module=Emails&action=index' style='background:#007bff;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>📧 Test Emails Module</a>";
echo " <a href='debug_oauth_success.php' style='background:#6c757d;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🔍 OAuth Debug</a>";
echo " <a href='index.php' style='background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🏠 Home</a></p>";

echo "</body></html>";
?>