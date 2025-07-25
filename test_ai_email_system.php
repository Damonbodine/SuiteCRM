<?php
// Test AI Email System Integration

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<h1>AI Email System Test</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

try {
    global $current_user, $db;
    
    // Test 1: Check database table exists
    echo "<h2>Test 1: Database Table Check</h2>";
    $query = "DESCRIBE ai_email_analysis";
    $result = $db->query($query);
    
    if ($result) {
        echo "<p>✅ ai_email_analysis table exists</p>";
        
        // Show table structure
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        while ($row = $db->fetchByAssoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>❌ ai_email_analysis table not found</p>";
    }
    
    // Test 2: Check OAuth connections
    echo "<h2>Test 2: OAuth Connections Check</h2>";
    $query = "SELECT id, name, type, created_by FROM external_oauth_connections WHERE deleted = 0";
    $result = $db->query($query);
    
    $connections = 0;
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Created By</th></tr>";
    while ($row = $db->fetchByAssoc($result)) {
        $connections++;
        echo "<tr>";
        echo "<td>" . substr($row['id'], 0, 8) . "...</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['type'] . "</td>";
        echo "<td>" . $row['created_by'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if ($connections > 0) {
        echo "<p>✅ Found $connections OAuth connection(s)</p>";
    } else {
        echo "<p>❌ No OAuth connections found</p>";
    }
    
    // Test 3: AIEmailAnalyzer class loading
    echo "<h2>Test 3: AIEmailAnalyzer Class</h2>";
    try {
        require_once('custom/include/AIEmailAnalyzer.php');
        $analyzer = new AIEmailAnalyzer();
        echo "<p>✅ AIEmailAnalyzer class loaded successfully</p>";
        
        // Test Gmail connection detection
        $reflection = new ReflectionClass($analyzer);
        $method = $reflection->getMethod('getGmailOAuthConnection');
        $method->setAccessible(true);
        $connection = $method->invoke($analyzer);
        
        if ($connection) {
            echo "<p>✅ Gmail OAuth connection found: " . $connection['name'] . "</p>";
            echo "<p>Token expires: " . $connection['access_token_expires'] . "</p>";
        } else {
            echo "<p>❌ No Gmail OAuth connection found for current user</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Error loading AIEmailAnalyzer: " . $e->getMessage() . "</p>";
    }
    
    // Test 4: Email fetching (if connection exists)
    if (isset($connection) && $connection) {
        echo "<h2>Test 4: Gmail Email Fetching</h2>";
        
        try {
            // Test fetching Gmail emails
            $reflection = new ReflectionClass($analyzer);
            $method = $reflection->getMethod('fetchGmailEmails');
            $method->setAccessible(true);
            
            echo "<p>🔄 Attempting to fetch Gmail emails...</p>";
            $emails = $method->invoke($analyzer, $connection);
            
            if (!empty($emails)) {
                echo "<p>✅ Successfully fetched " . count($emails) . " emails from Gmail</p>";
                echo "<h3>Sample Email:</h3>";
                $sampleEmail = $emails[0];
                echo "<ul>";
                echo "<li><strong>ID:</strong> " . $sampleEmail['id'] . "</li>";
                echo "<li><strong>Subject:</strong> " . ($sampleEmail['subject'] ?? 'No Subject') . "</li>";
                echo "<li><strong>From:</strong> " . ($sampleEmail['from'] ?? 'Unknown') . "</li>";
                echo "<li><strong>Snippet:</strong> " . substr($sampleEmail['snippet'] ?? '', 0, 100) . "...</li>";
                echo "</ul>";
            } else {
                echo "<p>⚠️ No emails fetched (this might be normal if inbox is empty or token expired)</p>";
            }
            
        } catch (Exception $e) {
            echo "<p>❌ Error fetching emails: " . $e->getMessage() . "</p>";
            echo "<p>This might be due to expired tokens or API rate limits</p>";
        }
    }
    
    // Test 5: Database analysis data
    echo "<h2>Test 5: Existing Analysis Data</h2>";
    $query = "SELECT COUNT(*) as count FROM ai_email_analysis WHERE deleted = 0";
    $result = $db->query($query);
    $row = $db->fetchByAssoc($result);
    
    echo "<p>Analysis records in database: " . $row['count'] . "</p>";
    
    if ($row['count'] > 0) {
        $query = "SELECT email_message_id, category, priority_score, ai_summary, analysis_date 
                  FROM ai_email_analysis 
                  WHERE deleted = 0 
                  ORDER BY analysis_date DESC 
                  LIMIT 5";
        $result = $db->query($query);
        
        echo "<h3>Recent Analysis Records:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Email ID</th><th>Category</th><th>Priority</th><th>Summary</th><th>Date</th></tr>";
        while ($row = $db->fetchByAssoc($result)) {
            echo "<tr>";
            echo "<td>" . substr($row['email_message_id'], 0, 15) . "...</td>";
            echo "<td>" . $row['category'] . "</td>";
            echo "<td>" . $row['priority_score'] . "</td>";
            echo "<td>" . substr($row['ai_summary'], 0, 50) . "...</td>";
            echo "<td>" . $row['analysis_date'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test 6: Manual email processing test
    if (isset($connection) && $connection) {
        echo "<h2>Test 6: Manual Email Processing Test</h2>";
        
        try {
            echo "<p>🔄 Running processUnanalyzedEmails()...</p>";
            $result = $analyzer->processUnanalyzedEmails();
            
            if ($result) {
                echo "<p>✅ Email processing completed successfully</p>";
            } else {
                echo "<p>⚠️ Email processing returned false (check logs for details)</p>";
            }
            
            // Check if any new analysis records were created
            $query = "SELECT COUNT(*) as count FROM ai_email_analysis WHERE deleted = 0";
            $result = $db->query($query);
            $row = $db->fetchByAssoc($result);
            echo "<p>Total analysis records after processing: " . $row['count'] . "</p>";
            
        } catch (Exception $e) {
            echo "<p>❌ Error during email processing: " . $e->getMessage() . "</p>";
        }
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