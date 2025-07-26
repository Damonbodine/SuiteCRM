<?php
// Debug Email Connection Issues

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<h1>Debug Email Connection Issues</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

try {
    global $current_user, $db;
    
    // Test 1: Current User Info
    echo "<h2>Test 1: Current User Info</h2>";
    if ($current_user && $current_user->id) {
        echo "<p>✅ Current User ID: " . $current_user->id . "</p>";
        echo "<p>✅ Current User Name: " . $current_user->user_name . "</p>";
        echo "<p>✅ Current User Email: " . ($current_user->email1 ?? 'Not set') . "</p>";
    } else {
        echo "<p>❌ No current user found or user not logged in</p>";
        echo "<p>You may need to log into SuiteCRM first</p>";
    }
    
    // Test 2: Check all OAuth connections
    echo "<h2>Test 2: All OAuth Connections</h2>";
    $query = "SELECT id, name, type, created_by, assigned_user_id, access_token_expires, deleted 
              FROM external_oauth_connections 
              ORDER BY date_modified DESC";
    $result = $db->query($query);
    
    $totalConnections = 0;
    echo "<table border='1' style='width:100%; font-size:12px;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Created By</th><th>Assigned User</th><th>Token Expires</th><th>Deleted</th></tr>";
    while ($row = $db->fetchByAssoc($result)) {
        $totalConnections++;
        echo "<tr>";
        echo "<td>" . substr($row['id'], 0, 8) . "...</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['type'] . "</td>";
        echo "<td>" . $row['created_by'] . "</td>";
        echo "<td>" . $row['assigned_user_id'] . "</td>";
        echo "<td>" . $row['access_token_expires'] . "</td>";
        echo "<td>" . ($row['deleted'] ? 'YES' : 'NO') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p>Total OAuth connections: $totalConnections</p>";
    
    // Test 3: Test the hasGmailConnection method specifically
    echo "<h2>Test 3: Gmail Connection Detection Logic</h2>";
    
    $userId = $current_user->id ?? 'NONE';
    echo "<p>Current User ID for query: '$userId'</p>";
    
    // Test the exact query from hasGmailConnection method
    $query = "SELECT id, name, type, created_by, assigned_user_id 
              FROM external_oauth_connections 
              WHERE (assigned_user_id = '$userId' OR type = 'system') 
              AND name LIKE '%Gmail%' 
              AND deleted = 0 
              LIMIT 1";
    
    echo "<p><strong>Query being used:</strong></p>";
    echo "<pre>$query</pre>";
    
    $result = $db->query($query);
    if ($result && $row = $db->fetchByAssoc($result)) {
        echo "<p>✅ Gmail connection found!</p>";
        echo "<ul>";
        echo "<li>ID: " . $row['id'] . "</li>";
        echo "<li>Name: " . $row['name'] . "</li>";
        echo "<li>Type: " . $row['type'] . "</li>";
        echo "<li>Created By: " . $row['created_by'] . "</li>";
        echo "<li>Assigned User: " . $row['assigned_user_id'] . "</li>";
        echo "</ul>";
    } else {
        echo "<p>❌ No Gmail connection found with current logic</p>";
        
        // Try broader search
        echo "<h3>Broader Search:</h3>";
        $query2 = "SELECT id, name, type, created_by, assigned_user_id 
                   FROM external_oauth_connections 
                   WHERE name LIKE '%Gmail%' 
                   AND deleted = 0";
        
        $result2 = $db->query($query2);
        $gmailConnections = 0;
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Created By</th><th>Assigned User</th></tr>";
        while ($row2 = $db->fetchByAssoc($result2)) {
            $gmailConnections++;
            echo "<tr>";
            echo "<td>" . substr($row2['id'], 0, 8) . "...</td>";
            echo "<td>" . $row2['name'] . "</td>";
            echo "<td>" . $row2['type'] . "</td>";
            echo "<td>" . $row2['created_by'] . "</td>";
            echo "<td>" . $row2['assigned_user_id'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p>Total Gmail connections found: $gmailConnections</p>";
    }
    
    // Test 4: Test AIEmailAnalyzer directly
    echo "<h2>Test 4: AIEmailAnalyzer Direct Test</h2>";
    try {
        require_once('custom/include/AIEmailAnalyzer.php');
        $analyzer = new AIEmailAnalyzer();
        
        // Use reflection to access private method
        $reflection = new ReflectionClass($analyzer);
        $method = $reflection->getMethod('getGmailOAuthConnection');
        $method->setAccessible(true);
        $connection = $method->invoke($analyzer);
        
        if ($connection) {
            echo "<p>✅ AIEmailAnalyzer found Gmail connection</p>";
            echo "<ul>";
            echo "<li>Connection ID: " . substr($connection['id'], 0, 12) . "...</li>";
            echo "<li>Name: " . $connection['name'] . "</li>";
            echo "<li>Token Expires: " . $connection['access_token_expires'] . "</li>";
            echo "<li>Token Status: " . (strtotime($connection['access_token_expires']) > time() ? 'Valid' : 'Expired') . "</li>";
            echo "</ul>";
            
            // Test email fetching
            echo "<h3>Testing Email Fetch:</h3>";
            try {
                $fetchMethod = $reflection->getMethod('fetchGmailEmails');
                $fetchMethod->setAccessible(true);
                $emails = $fetchMethod->invoke($analyzer, $connection);
                
                echo "<p>Gmail API fetch result: " . (is_array($emails) ? count($emails) . " emails" : "Failed") . "</p>";
                
                if (!empty($emails)) {
                    echo "<p>✅ Successfully fetched emails from Gmail</p>";
                    echo "<h4>Sample Email:</h4>";
                    $sample = $emails[0];
                    echo "<ul>";
                    echo "<li>ID: " . $sample['id'] . "</li>";
                    echo "<li>Subject: " . ($sample['subject'] ?? 'No Subject') . "</li>";
                    echo "<li>From: " . ($sample['from'] ?? 'Unknown') . "</li>";
                    echo "<li>Snippet: " . substr($sample['snippet'] ?? '', 0, 100) . "...</li>";
                    echo "</ul>";
                } else {
                    echo "<p>⚠️ No emails returned from Gmail API</p>";
                }
                
            } catch (Exception $e) {
                echo "<p>❌ Error fetching emails: " . $e->getMessage() . "</p>";
            }
            
        } else {
            echo "<p>❌ AIEmailAnalyzer could not find Gmail connection</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error testing AIEmailAnalyzer: " . $e->getMessage() . "</p>";
    }
    
    // Test 5: Test processUnanalyzedEmails
    echo "<h2>Test 5: Process Unanalyzed Emails Test</h2>";
    if (isset($analyzer)) {
        try {
            $result = $analyzer->processUnanalyzedEmails();
            echo "<p>processUnanalyzedEmails() result: " . ($result ? 'SUCCESS' : 'FAILED') . "</p>";
            
            // Check analysis results
            $query = "SELECT COUNT(*) as count FROM ai_email_analysis WHERE deleted = 0";
            $result = $db->query($query);
            $row = $db->fetchByAssoc($result);
            echo "<p>Email analysis records in database: " . $row['count'] . "</p>";
            
        } catch (Exception $e) {
            echo "<p>❌ Error in processUnanalyzedEmails: " . $e->getMessage() . "</p>";
        }
    }
    
    // Test 6: Check the view logic
    echo "<h2>Test 6: Email List View Logic Test</h2>";
    
    // Test the hasGmailConnection method from the view
    require_once('custom/modules/Emails/views/view.list.php');
    
    try {
        $emailView = new CustomEmailsViewList();
        
        // Use reflection to test hasGmailConnection
        $reflection = new ReflectionClass($emailView);
        $method = $reflection->getMethod('hasGmailConnection');
        $method->setAccessible(true);
        $hasConnection = $method->invoke($emailView);
        
        echo "<p>Email view hasGmailConnection() result: " . ($hasConnection ? 'TRUE' : 'FALSE') . "</p>";
        
        if ($hasConnection) {
            echo "<p>✅ View should show AI email list</p>";
        } else {
            echo "<p>⚠️ View will show Gmail setup page instead</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error testing email view: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<h2>Fatal Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php?module=Emails&action=index'>Go to Emails Module</a></p>";
echo "<p><a href='index.php'>Back to SuiteCRM</a></p>";
?>