<?php
/**
 * Debug existing data in ai_email_analysis table
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<!DOCTYPE html><html><head><title>Debug Existing Data</title></head><body>";
echo "<h1>🔍 Debug Existing AI Email Analysis Data</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .info{color:blue} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ddd;padding:8px;text-align:left} tr:nth-child(even){background:#f2f2f2}</style>";

try {
    global $db, $current_user;
    
    echo "<h2>Current User Context</h2>";
    if ($current_user && $current_user->id) {
        echo "<p class='success'>✅ Current User ID: " . $current_user->id . "</p>";
    } else {
        echo "<p class='error'>❌ No current user context</p>";
    }
    
    echo "<h2>All Records in ai_email_analysis Table</h2>";
    
    $query = "SELECT * FROM ai_email_analysis ORDER BY date_entered DESC";
    $result = $db->query($query);
    
    if ($result) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Email Message ID</th><th>User ID</th><th>Category</th><th>Priority</th><th>Summary</th><th>Analysis Date</th><th>Expires At</th><th>Deleted</th></tr>";
        
        $count = 0;
        while ($row = $db->fetchByAssoc($result)) {
            $count++;
            echo "<tr>";
            echo "<td>" . htmlspecialchars(substr($row['id'], 0, 8)) . "...</td>";
            echo "<td>" . htmlspecialchars($row['email_message_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['category'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['priority_score'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars(substr($row['ai_summary'] ?? '', 0, 50)) . "...</td>";
            echo "<td>" . htmlspecialchars($row['analysis_date'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['expires_at'] ?? 'NULL') . "</td>";
            echo "<td>" . ($row['deleted'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p class='info'>📊 Total records: $count</p>";
        
    } else {
        echo "<p class='error'>❌ Failed to query table: " . $db->lastError() . "</p>";
    }
    
    echo "<h2>Records for Current User</h2>";
    
    $userId = $current_user->id ?? '1'; // Fallback to admin user
    
    $userQuery = "SELECT * FROM ai_email_analysis 
                  WHERE user_id = ? 
                  AND deleted = 0 
                  AND expires_at > NOW()
                  ORDER BY analysis_date DESC";
    
    $userResult = $db->pQuery($userQuery, array($userId));
    
    if ($userResult) {
        $userCount = 0;
        echo "<table>";
        echo "<tr><th>Email Message ID</th><th>Category</th><th>Priority</th><th>Summary</th><th>Analysis Date</th></tr>";
        
        while ($row = $db->fetchByAssoc($userResult)) {
            $userCount++;
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['email_message_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['category'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['priority_score'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars(substr($row['ai_summary'] ?? '', 0, 100)) . "...</td>";
            echo "<td>" . htmlspecialchars($row['analysis_date'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<p class='info'>📊 Records for user $userId: $userCount</p>";
        
    } else {
        echo "<p class='error'>❌ Failed to query user records: " . $db->lastError() . "</p>";
    }
    
    echo "<h2>Test AIEmailAnalyzer::getAnalyzedEmails()</h2>";
    
    try {
        require_once('custom/include/AIEmailAnalyzer.php');
        $analyzer = new AIEmailAnalyzer();
        $emails = $analyzer->getAnalyzedEmails(5);
        
        echo "<p class='info'>🔄 AIEmailAnalyzer returned " . count($emails) . " emails</p>";
        
        if (!empty($emails)) {
            echo "<table>";
            echo "<tr><th>Subject</th><th>Category</th><th>Priority</th><th>Summary</th></tr>";
            
            foreach ($emails as $email) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($email['subject'] ?? 'No Subject') . "</td>";
                echo "<td>" . htmlspecialchars($email['category'] ?? 'No Category') . "</td>";
                echo "<td>" . htmlspecialchars($email['priority_score'] ?? 'No Priority') . "</td>";
                echo "<td>" . htmlspecialchars(substr($email['ai_summary'] ?? '', 0, 100)) . "...</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>❌ AIEmailAnalyzer error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<h2>Actions</h2>";
    echo "<p><a href='check_and_create_table.php'>🗄️ Check Database Setup</a></p>";
    echo "<p><a href='test_email_fetch.php'>🧪 Test Email Fetch</a></p>";
    echo "<p><a href='index.php?module=Emails&action=index'>📧 Go to Emails Module</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>