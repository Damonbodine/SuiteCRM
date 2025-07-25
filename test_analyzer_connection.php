<?php
// Simple test to debug AIEmailAnalyzer connection issue
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}
require_once('include/entryPoint.php');

echo "<h1>AIEmailAnalyzer Connection Test</h1>";

// Test 1: Check current user context
global $current_user, $db;
echo "<h2>Current User Context:</h2>";
echo "User ID: " . ($current_user->id ?? 'NONE') . "<br>";
echo "User Name: " . ($current_user->user_name ?? 'NONE') . "<br>";

// Test 2: Direct query to see what connections exist
echo "<h2>Direct Database Query:</h2>";
$query = "SELECT id, name, type, assigned_user_id, expires_in 
          FROM external_oauth_connections 
          WHERE name LIKE '%Gmail%' AND deleted = 0";
$result = $db->query($query);

echo "All Gmail connections:<br>";
while ($row = $db->fetchByAssoc($result)) {
    echo "- ID: " . $row['id'] . "<br>";
    echo "- Name: " . $row['name'] . "<br>";
    echo "- Type: " . $row['type'] . "<br>";
    echo "- Assigned User: " . ($row['assigned_user_id'] ?: 'NONE') . "<br>";
    echo "- Token Expires: " . $row['expires_in'] . "<br><br>";
}

// Test 3: Test AIEmailAnalyzer
echo "<h2>AIEmailAnalyzer Test:</h2>";
try {
    require_once('custom/include/AIEmailAnalyzer.php');
    $analyzer = new AIEmailAnalyzer();
    
    // Test the method directly
    $result = $analyzer->processUnanalyzedEmails();
    echo "processUnanalyzedEmails() result: " . ($result ? 'SUCCESS' : 'FAILED') . "<br>";
    
    // Check error log
    $logFile = '/var/www/html/suitecrm.log';
    if (file_exists($logFile)) {
        $logContent = file_get_contents($logFile);
        $lines = explode("\n", $logContent);
        $recentLines = array_slice($lines, -20); // Last 20 lines
        
        echo "<h3>Recent Log Entries:</h3>";
        echo "<pre>";
        foreach ($recentLines as $line) {
            if (strpos($line, 'AIEmailAnalyzer') !== false) {
                echo htmlspecialchars($line) . "\n";
            }
        }
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<a href='index.php?module=Emails&action=index'>Go to Emails Module</a>";
?>