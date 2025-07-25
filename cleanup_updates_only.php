<?php
/**
 * Remove AI case updates with proper UUID handling
 */

$host = 'db';
$username = 'suiteuser';  
$password = 'suitepass';
$database = 'suitecrm';

echo "=== REMOVING AI CASE UPDATES ===\n";

try {
    $mysqli = new mysqli($host, $username, $password, $database);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✓ Connected to database\n\n";
    
    // Delete AI case updates using proper string matching
    echo "Removing AI case updates...\n";
    
    $deleteQuery = "DELETE FROM aop_case_updates 
                    WHERE (name LIKE '%AI Winnability Analysis%' 
                           OR description LIKE '%AI WINNABILITY ANALYSIS COMPLETE%')
                      AND deleted = 0";
    
    if ($mysqli->query($deleteQuery)) {
        $deletedCount = $mysqli->affected_rows;
        echo "✓ Successfully removed $deletedCount AI case updates\n";
    } else {
        echo "✗ Error: " . $mysqli->error . "\n";
    }
    
    // Verify they're gone
    echo "\nVerifying removal...\n";
    $checkQuery = "SELECT COUNT(*) as remaining FROM aop_case_updates 
                   WHERE (name LIKE '%AI Winnability Analysis%' 
                          OR description LIKE '%AI WINNABILITY ANALYSIS COMPLETE%')
                     AND deleted = 0";
    
    $result = $mysqli->query($checkQuery);
    $row = $result->fetch_assoc();
    
    if ($row['remaining'] == 0) {
        echo "✓ All AI case updates have been removed\n";
    } else {
        echo "⚠ " . $row['remaining'] . " AI case updates still remain\n";
    }
    
    echo "\n=== CLEANUP COMPLETE ===\n";
    echo "Please refresh your browser and try accessing Cases again.\n";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>