<?php
if (!defined('sugarEntry') || !sugarEntry) {
    define('sugarEntry', true);
}

// SuiteCRM bootstrap
chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

// Set headers for web output
header('Content-Type: text/html; charset=UTF-8');
echo "<html><head><title>OAuth Debug Status</title></head><body>";
echo "<h1>🔍 OAuth Authentication Debug Status</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .warning{color:orange} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ddd;padding:8px;text-align:left} tr:nth-child(even){background:#f2f2f2}</style>";

try {
    global $db, $current_user;
    
    echo "<h2>📊 Current User Information</h2>";
    if ($current_user && $current_user->id) {
        echo "<p class='success'>✅ Current User ID: " . $current_user->id . "</p>";
        echo "<p class='success'>✅ Current User Name: " . ($current_user->user_name ?? 'Unknown') . "</p>";
        echo "<p class='success'>✅ Current User Email: " . ($current_user->email1 ?? 'Unknown') . "</p>";
    } else {
        echo "<p class='error'>❌ No current user found - authentication required</p>";
        exit;
    }
    
    echo "<h2>🗄️ Database Connection Status</h2>";
    if ($db) {
        echo "<p class='success'>✅ Database connection active</p>";
        $result = $db->query("SELECT DATABASE() as db_name");
        if ($result && $row = $db->fetchByAssoc($result)) {
            echo "<p class='success'>✅ Connected to database: " . $row['db_name'] . "</p>";
        }
    } else {
        echo "<p class='error'>❌ No database connection</p>";
        exit;
    }
    
    echo "<h2>📱 OAuth Connections Table Status</h2>";
    
    // Check if the external_oauth_connections table exists
    $tableCheck = $db->query("SHOW TABLES LIKE 'external_oauth_connections'");
    if (!$tableCheck || !$db->fetchByAssoc($tableCheck)) {
        echo "<p class='error'>❌ Table 'external_oauth_connections' does not exist</p>";
        echo "<p>Creating table now...</p>";
        
        $createTable = "
        CREATE TABLE external_oauth_connections (
            id VARCHAR(36) PRIMARY KEY,
            user_id VARCHAR(36) NOT NULL,
            provider VARCHAR(50) NOT NULL,
            name VARCHAR(100) NOT NULL,
            access_token TEXT,
            refresh_token TEXT,
            access_token_expires DATETIME,
            scope TEXT,
            is_active TINYINT(1) DEFAULT 1,
            date_created DATETIME DEFAULT CURRENT_TIMESTAMP,
            date_modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted TINYINT(1) DEFAULT 0
        )";
        
        if ($db->query($createTable)) {
            echo "<p class='success'>✅ Table created successfully</p>";
        } else {
            echo "<p class='error'>❌ Failed to create table: " . $db->lastError() . "</p>";
        }
    } else {
        echo "<p class='success'>✅ Table 'external_oauth_connections' exists</p>";
    }
    
    echo "<h2>🔑 Current User's OAuth Connections</h2>";
    
    $userId = $db->quote($current_user->id);
    $query = "SELECT 
                id,
                provider, 
                name, 
                access_token_expires,
                is_active,
                date_created,
                date_modified,
                CASE 
                    WHEN access_token IS NOT NULL AND access_token != '' THEN 'Yes' 
                    ELSE 'No' 
                END as has_access_token,
                CASE 
                    WHEN refresh_token IS NOT NULL AND refresh_token != '' THEN 'Yes' 
                    ELSE 'No' 
                END as has_refresh_token,
                CASE 
                    WHEN access_token_expires > NOW() THEN 'Valid' 
                    ELSE 'Expired' 
                END as token_status
              FROM external_oauth_connections 
              WHERE assigned_user_id = $userId 
              AND deleted = 0 
              ORDER BY date_modified DESC";
    
    $result = $db->query($query);
    
    if (!$result) {
        echo "<p class='error'>❌ Query failed: " . $db->lastError() . "</p>";
    } else {
        $connections = [];
        while ($row = $db->fetchByAssoc($result)) {
            $connections[] = $row;
        }
        
        if (empty($connections)) {
            echo "<p class='warning'>⚠️ No OAuth connections found for current user</p>";
            echo "<p>This explains why emails aren't refreshing - no Gmail connection exists.</p>";
        } else {
            echo "<p class='success'>✅ Found " . count($connections) . " OAuth connection(s)</p>";
            echo "<table>";
            echo "<tr><th>Provider</th><th>Name</th><th>Has Access Token</th><th>Has Refresh Token</th><th>Token Status</th><th>Expires</th><th>Active</th><th>Created</th><th>Modified</th></tr>";
            
            foreach ($connections as $conn) {
                $statusClass = $conn['token_status'] === 'Valid' ? 'success' : 'error';
                $activeClass = $conn['is_active'] ? 'success' : 'error';
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($conn['provider']) . "</td>";
                echo "<td>" . htmlspecialchars($conn['name']) . "</td>";
                echo "<td class='" . ($conn['has_access_token'] === 'Yes' ? 'success' : 'error') . "'>" . $conn['has_access_token'] . "</td>";
                echo "<td class='" . ($conn['has_refresh_token'] === 'Yes' ? 'success' : 'error') . "'>" . $conn['has_refresh_token'] . "</td>";
                echo "<td class='$statusClass'>" . $conn['token_status'] . "</td>";
                echo "<td>" . htmlspecialchars($conn['access_token_expires'] ?? 'N/A') . "</td>";
                echo "<td class='$activeClass'>" . ($conn['is_active'] ? 'Yes' : 'No') . "</td>";
                echo "<td>" . htmlspecialchars($conn['date_created'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($conn['date_modified'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<h2>📧 AI Email Analysis Table Status</h2>";
    
    // Check if ai_email_analysis table exists
    $emailTableCheck = $db->query("SHOW TABLES LIKE 'ai_email_analysis'");
    if (!$emailTableCheck || !$db->fetchByAssoc($emailTableCheck)) {
        echo "<p class='error'>❌ Table 'ai_email_analysis' does not exist</p>";
    } else {
        echo "<p class='success'>✅ Table 'ai_email_analysis' exists</p>";
        
        // Check for recent analysis
        $emailQuery = "SELECT COUNT(*) as count FROM ai_email_analysis WHERE user_id = $userId AND deleted = 0";
        $emailResult = $db->query($emailQuery);
        if ($emailResult && $emailRow = $db->fetchByAssoc($emailResult)) {
            $emailCount = $emailRow['count'];
            echo "<p class='success'>✅ Found $emailCount analyzed emails for current user</p>";
            
            if ($emailCount > 0) {
                // Get most recent analysis
                $recentQuery = "SELECT email_message_id, analysis_date, ai_summary 
                               FROM ai_email_analysis 
                               WHERE user_id = $userId AND deleted = 0 
                               ORDER BY analysis_date DESC 
                               LIMIT 5";
                $recentResult = $db->query($recentQuery);
                echo "<h3>Recent Email Analyses:</h3>";
                echo "<table>";
                echo "<tr><th>Email ID</th><th>Analysis Date</th><th>Summary</th></tr>";
                while ($recent = $db->fetchByAssoc($recentResult)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars(substr($recent['email_message_id'], 0, 20)) . "...</td>";
                    echo "<td>" . htmlspecialchars($recent['analysis_date']) . "</td>";
                    echo "<td>" . htmlspecialchars(substr($recent['ai_summary'], 0, 50)) . "...</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
        }
    }
    
    echo "<h2>🔧 Configuration Status</h2>";
    
    // Check for secure config
    if (file_exists('custom/Extension/application/Ext/Include/ai_secure_config.php')) {
        echo "<p class='success'>✅ AI secure config file exists</p>";
        
        // Try to load it and check for credentials
        try {
            require_once('custom/Extension/application/Ext/Include/ai_secure_config.php');
            if (class_exists('AISecurity')) {
                echo "<p class='success'>✅ AISecurity class loaded</p>";
                
                // Test credential retrieval (without exposing actual values)
                $hasGoogleClientId = !empty(AISecurity::getApiCredential('google_client_id'));
                $hasGoogleClientSecret = !empty(AISecurity::getApiCredential('google_client_secret'));
                $hasOpenAIKey = !empty(AISecurity::getApiCredential('openai_api_key'));
                
                echo "<p class='" . ($hasGoogleClientId ? 'success' : 'error') . "'>" . 
                     ($hasGoogleClientId ? '✅' : '❌') . " Google Client ID configured</p>";
                echo "<p class='" . ($hasGoogleClientSecret ? 'success' : 'error') . "'>" . 
                     ($hasGoogleClientSecret ? '✅' : '❌') . " Google Client Secret configured</p>";
                echo "<p class='" . ($hasOpenAIKey ? 'success' : 'error') . "'>" . 
                     ($hasOpenAIKey ? '✅' : '❌') . " OpenAI API Key configured</p>";
            } else {
                echo "<p class='error'>❌ AISecurity class not found</p>";
            }
        } catch (Exception $e) {
            echo "<p class='error'>❌ Error loading secure config: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='error'>❌ AI secure config file not found</p>";
    }
    
    echo "<h2>🌐 OAuth Callback URL Status</h2>";
    
    if (file_exists('oauth2callback.php')) {
        echo "<p class='success'>✅ OAuth callback file exists</p>";
    } else {
        echo "<p class='error'>❌ OAuth callback file missing</p>";
    }
    
    echo "<h2>📝 Recommendations</h2>";
    
    if (empty($connections)) {
        echo "<div class='error'>";
        echo "<h3>❌ No OAuth Connection Found</h3>";
        echo "<p>This is why emails aren't refreshing. You need to:</p>";
        echo "<ol>";
        echo "<li>Set up OAuth connection with Gmail</li>";
        echo "<li>Ensure Google Client ID and Secret are configured</li>";
        echo "<li>Complete the OAuth authorization flow</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        $hasValidConnection = false;
        foreach ($connections as $conn) {
            if ($conn['token_status'] === 'Valid' && $conn['is_active']) {
                $hasValidConnection = true;
                break;
            }
        }
        
        if (!$hasValidConnection) {
            echo "<div class='error'>";
            echo "<h3>⚠️ OAuth Tokens Expired or Inactive</h3>";
            echo "<p>You have connections but they're not working. You need to:</p>";
            echo "<ol>";
            echo "<li>Re-authorize your Gmail connection</li>";
            echo "<li>Check that refresh tokens are working</li>";
            echo "<li>Verify the token refresh mechanism</li>";
            echo "</ol>";
            echo "</div>";
        } else {
            echo "<div class='success'>";
            echo "<h3>✅ OAuth Connection Looks Good</h3>";
            echo "<p>If emails still aren't refreshing, check:</p>";
            echo "<ol>";
            echo "<li>Email refresh action is working</li>";
            echo "<li>Gmail API permissions</li>";
            echo "<li>Application logs for errors</li>";
            echo "</ol>";
            echo "</div>";
        }
    }
    
    echo "<h2>🔧 Quick Actions</h2>";
    echo "<p><a href='?action=test_gmail_api' style='background:#007cba;color:white;padding:10px;text-decoration:none;border-radius:4px'>Test Gmail API Connection</a></p>";
    echo "<p><a href='?action=clear_tokens' style='background:#dc3545;color:white;padding:10px;text-decoration:none;border-radius:4px'>Clear All OAuth Tokens</a></p>";
    echo "<p><a href='index.php?module=Emails&action=index' style='background:#28a745;color:white;padding:10px;text-decoration:none;border-radius:4px'>Back to Email List</a></p>";
    
    // Handle quick actions
    if (isset($_GET['action'])) {
        echo "<h2>🎯 Action Results</h2>";
        
        switch ($_GET['action']) {
            case 'test_gmail_api':
                echo "<h3>Testing Gmail API Connection...</h3>";
                testGmailAPI($db, $current_user);
                break;
                
            case 'clear_tokens':
                echo "<h3>Clearing OAuth Tokens...</h3>";
                clearOAuthTokens($db, $current_user);
                break;
        }
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Fatal Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";

/**
 * Test Gmail API connection
 */
function testGmailAPI($db, $current_user) {
    try {
        require_once('custom/include/AIEmailAnalyzer.php');
        $analyzer = new AIEmailAnalyzer();
        
        // Try to process emails
        echo "<p>Attempting to fetch Gmail emails...</p>";
        $result = $analyzer->processUnanalyzedEmails();
        
        if ($result) {
            echo "<p class='success'>✅ Gmail API test successful</p>";
        } else {
            echo "<p class='error'>❌ Gmail API test failed</p>";
        }
        
        // Check the logs for more details
        echo "<p>Check suitecrm.log for detailed error messages</p>";
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Gmail API test error: " . $e->getMessage() . "</p>";
    }
}

/**
 * Clear OAuth tokens
 */
function clearOAuthTokens($db, $current_user) {
    try {
        $userId = $db->quote($current_user->id);
        $query = "UPDATE external_oauth_connections SET deleted = 1 WHERE assigned_user_id = $userId";
        
        if ($db->query($query)) {
            echo "<p class='success'>✅ OAuth tokens cleared successfully</p>";
            echo "<p>You'll need to re-authorize Gmail access</p>";
        } else {
            echo "<p class='error'>❌ Failed to clear tokens: " . $db->lastError() . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ Error clearing tokens: " . $e->getMessage() . "</p>";
    }
}
?>