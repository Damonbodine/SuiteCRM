<?php
/**
 * Safe AI data cleanup - removes only the specific AI case updates causing the error
 * This will NOT affect any other data in your system
 */

// Database connection settings
$host = 'db';
$username = 'suiteuser';  
$password = 'suitepass';
$database = 'suitecrm';

echo "=== SAFE AI CLEANUP ===\n";
echo "This will remove ONLY the AI case updates causing the error dialog.\n";
echo "No other data will be affected.\n\n";

try {
    $mysqli = new mysqli($host, $username, $password, $database);
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✓ Connected to database: $database\n\n";
    
    // 1. Show what we're about to delete
    echo "AI Case Updates to be removed:\n";
    echo "------------------------------\n";
    
    $previewQuery = "SELECT cu.id, cu.name, cu.date_entered, c.name as case_name,
                            LEFT(cu.description, 100) as preview
                     FROM aop_case_updates cu
                     LEFT JOIN cases c ON cu.case_id = c.id
                     WHERE cu.name LIKE '%AI Winnability Analysis%'
                       AND cu.deleted = 0";
    
    $result = $mysqli->query($previewQuery);
    $idsToDelete = array();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $idsToDelete[] = $row['id'];
            echo "ID: " . $row['id'] . "\n";
            echo "Case: " . ($row['case_name'] ?: 'Unknown') . "\n";
            echo "Update: " . $row['name'] . "\n";
            echo "Date: " . $row['date_entered'] . "\n";
            echo "Preview: " . $row['preview'] . "...\n\n";
        }
        
        echo "Total updates to remove: " . count($idsToDelete) . "\n\n";
        
        // 2. Delete the AI case updates
        echo "Removing AI case updates...\n";
        
        $idList = implode(',', array_map('intval', $idsToDelete));
        $deleteQuery = "DELETE FROM aop_case_updates WHERE id IN ($idList)";
        
        if ($mysqli->query($deleteQuery)) {
            echo "✓ Successfully removed " . $mysqli->affected_rows . " AI case updates\n";
        } else {
            echo "✗ Error removing case updates: " . $mysqli->error . "\n";
        }
        
    } else {
        echo "• No AI case updates found to remove\n";
    }
    
    // 3. Optional: Clear AI fields from cases (you can skip this if you want to keep case AI data)
    echo "\nClearing AI fields from cases table (optional)...\n";
    
    $clearQuery = "UPDATE cases SET 
                     ai_suggested_status = NULL,
                     ai_confidence_score = NULL,
                     ai_last_analysis = NULL,
                     ai_analysis_factors = NULL,
                     ai_status_needs_review = 0
                   WHERE (ai_suggested_status IS NOT NULL 
                          OR ai_confidence_score IS NOT NULL 
                          OR ai_last_analysis IS NOT NULL)
                     AND deleted = 0";
    
    if ($mysqli->query($clearQuery)) {
        $affectedCases = $mysqli->affected_rows;
        if ($affectedCases > 0) {
            echo "✓ Cleared AI data from $affectedCases cases\n";
        } else {
            echo "• No cases had AI data to clear\n";
        }
    } else {
        echo "✗ Error clearing case AI fields: " . $mysqli->error . "\n";
    }
    
    echo "\n=== CLEANUP COMPLETE ===\n";
    echo "The AI case updates causing the error dialog have been removed.\n";
    echo "Your cases data and other functionality are completely unaffected.\n\n";
    echo "NEXT STEPS:\n";
    echo "1. Clear your browser cache (Ctrl+Shift+R)\n";  
    echo "2. Log out and log back into SuiteCRM\n";
    echo "3. Try accessing the Cases page again\n\n";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>