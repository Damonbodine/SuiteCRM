<?php
/**
 * Debug OAuth Success Page
 * Check what happens when we reach the success URL
 */

if (!defined('sugarEntry') || !sugarEntry) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

header('Content-Type: text/html; charset=UTF-8');
echo "<html><head><title>OAuth Success Debug</title></head><body>";
echo "<h1>🎉 OAuth Success Debug</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .warning{color:orange} .info{color:blue}</style>";

try {
    global $current_user, $db;
    
    echo "<h2>📊 Current Status</h2>";
    echo "<p><strong>Current User:</strong> " . ($current_user && $current_user->id ? "{$current_user->user_name} (ID: {$current_user->id})" : "❌ Not logged in") . "</p>";
    echo "<p><strong>OAuth Success Parameter:</strong> " . (isset($_GET['oauth_success']) ? "✅ Present" : "❌ Missing") . "</p>";
    echo "<p><strong>OAuth Error Parameter:</strong> " . (isset($_GET['oauth_error']) ? "❌ " . $_GET['oauth_error'] : "✅ None") . "</p>";
    
    if ($current_user && $current_user->id) {
        echo "<h2>🔍 OAuth Connections Check</h2>";
        
        // Check for valid OAuth connections
        $userId = $current_user->id;
        $query = "SELECT id, name, type, access_token_expires, deleted,
                         CASE WHEN access_token IS NOT NULL AND access_token != '' THEN 'Yes' ELSE 'No' END as has_token,
                         CASE WHEN access_token_expires > NOW() THEN 'Valid' ELSE 'Expired' END as token_status
                  FROM external_oauth_connections 
                  WHERE assigned_user_id = '{$userId}' 
                  AND name LIKE '%Gmail%'
                  ORDER BY date_modified DESC";
        
        $result = $db->query($query);
        if ($result) {
            $connections = [];
            while ($row = $db->fetchByAssoc($result)) {
                $connections[] = $row;
            }
            
            if (empty($connections)) {
                echo "<p class='error'>❌ No Gmail connections found for user {$userId}</p>";
            } else {
                echo "<p class='info'>Found " . count($connections) . " Gmail connection(s):</p>";
                echo "<table border='1' style='border-collapse:collapse;width:100%'>";
                echo "<tr style='background:#f0f0f0'><th>Name</th><th>Type</th><th>Has Token</th><th>Status</th><th>Expires</th><th>Deleted</th></tr>";
                
                foreach ($connections as $conn) {
                    $statusClass = $conn['token_status'] === 'Valid' && $conn['deleted'] == 0 ? 'success' : 'error';
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($conn['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($conn['type']) . "</td>";
                    echo "<td>" . $conn['has_token'] . "</td>";
                    echo "<td class='{$statusClass}'>" . $conn['token_status'] . "</td>";
                    echo "<td>" . htmlspecialchars($conn['access_token_expires']) . "</td>";
                    echo "<td>" . ($conn['deleted'] ? '❌ Yes' : '✅ No') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                // Check if we have any valid connections
                $validConnections = array_filter($connections, function($conn) {
                    return $conn['token_status'] === 'Valid' && $conn['deleted'] == 0 && $conn['has_token'] === 'Yes';
                });
                
                if (empty($validConnections)) {
                    echo "<p class='warning'>⚠️ No valid, active connections found</p>";
                } else {
                    echo "<p class='success'>✅ Found " . count($validConnections) . " valid connection(s)</p>";
                }
            }
        }
        
        echo "<h2>🧪 Test Email View Detection</h2>";
        
        // Test the hasGmailConnection method
        try {
            require_once('custom/modules/Emails/views/view.list.php');
            $emailView = new CustomEmailsViewList();
            
            $reflection = new ReflectionClass($emailView);
            $hasConnectionMethod = $reflection->getMethod('hasGmailConnection');
            $hasConnectionMethod->setAccessible(true);
            
            $hasConnection = $hasConnectionMethod->invoke($emailView);
            
            echo "<p><strong>Email View Detection:</strong> " . ($hasConnection ? "✅ Valid connection detected" : "❌ No valid connection") . "</p>";
            
            if (!$hasConnection) {
                echo "<p class='warning'>This is why you're not seeing emails - the view thinks there's no valid connection</p>";
            }
            
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error testing email view: " . $e->getMessage() . "</p>";
        }
        
        echo "<h2>🔧 Recommended Actions</h2>";
        
        if (empty($validConnections)) {
            echo "<div style='background:#fff3cd;padding:15px;border:1px solid #ffeaa7;border-radius:4px'>";
            echo "<h3>⚠️ OAuth Connection Issue</h3>";
            echo "<p>The OAuth flow completed successfully, but no valid connection was saved. This could be because:</p>";
            echo "<ul>";
            echo "<li>The OAuth callback encountered an error after redirecting you</li>";
            echo "<li>The connection was saved but then immediately marked as deleted</li>";
            echo "<li>The token exchange failed silently</li>";
            echo "</ul>";
            echo "<p><strong>Next steps:</strong></p>";
            echo "<ol>";
            echo "<li>Try the OAuth flow again</li>";
            echo "<li>Check the logs during the OAuth callback</li>";
            echo "<li>Test the OAuth callback directly</li>";
            echo "</ol>";
            echo "</div>";
        } else {
            echo "<div style='background:#d4edda;padding:15px;border:1px solid #c3e6cb;border-radius:4px'>";
            echo "<h3>✅ OAuth Connection Valid</h3>";
            echo "<p>You have valid OAuth connections. The email view should show your emails.</p>";
            echo "</div>";
        }
        
    } else {
        echo "<p class='error'>❌ You need to be logged into SuiteCRM to debug OAuth connections</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Debug Error: " . $e->getMessage() . "</p>";
}

echo "<br><p><a href='index.php?module=Emails&action=index' style='background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>📧 Back to Emails</a>";
echo " <a href='cleanup_oauth_tokens.php' style='background:#ffc107;color:black;padding:10px 15px;text-decoration:none;border-radius:4px'>🧹 OAuth Cleanup</a>";
echo " <a href='index.php' style='background:#6c757d;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🏠 Home</a></p>";

echo "</body></html>";
?>