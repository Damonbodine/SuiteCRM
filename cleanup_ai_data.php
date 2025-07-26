<?php
/**
 * Clean up AI analysis data from SuiteCRM database
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('config.php');
require_once('include/database/DBManagerFactory.php');

echo "Starting AI data cleanup...\n";

try {
    $db = DBManagerFactory::getInstance();
    
    // 1. Clear AI analysis data from cases table
    echo "Clearing AI data from cases table...\n";
    $query = "UPDATE cases SET 
                ai_suggested_status = NULL,
                ai_confidence_score = NULL,
                ai_last_analysis = NULL,
                ai_analysis_factors = NULL,
                ai_status_needs_review = 0
              WHERE ai_suggested_status IS NOT NULL 
                 OR ai_confidence_score IS NOT NULL 
                 OR ai_last_analysis IS NOT NULL";
    
    $result = $db->query($query);
    if ($result) {
        echo "✓ Cleared AI fields from cases table\n";
    } else {
        echo "• No AI data found in cases table or fields don't exist\n";
    }
    
    // 2. Remove AI-related case updates
    echo "Removing AI case updates...\n";
    
    // First, get count of AI case updates
    $countQuery = "SELECT COUNT(*) as count FROM aop_case_updates 
                   WHERE name LIKE '%AI%' 
                      OR description LIKE '%AI WINNABILITY ANALYSIS%'
                      OR description LIKE '%Assessment:%'
                      OR description LIKE '%Winnability%'";
    
    $countResult = $db->query($countQuery);
    $countRow = $db->fetchByAssoc($countResult);
    $aiUpdateCount = $countRow['count'];
    
    if ($aiUpdateCount > 0) {
        echo "Found $aiUpdateCount AI-related case updates, removing...\n";
        
        // Delete AI case updates
        $deleteQuery = "DELETE FROM aop_case_updates 
                        WHERE name LIKE '%AI%' 
                           OR description LIKE '%AI WINNABILITY ANALYSIS%'
                           OR description LIKE '%Assessment:%'
                           OR description LIKE '%Winnability%'";
        
        $deleteResult = $db->query($deleteQuery);
        if ($deleteResult) {
            echo "✓ Removed $aiUpdateCount AI case updates\n";
        } else {
            echo "✗ Failed to remove AI case updates\n";
        }
    } else {
        echo "• No AI case updates found\n";
    }
    
    // 3. Clear any error messages from tracker or system logs
    echo "Clearing system error messages...\n";
    
    $trackerQuery = "DELETE FROM tracker WHERE action LIKE '%ai%' OR action LIKE '%AI%'";
    $trackerResult = $db->query($trackerQuery);
    if ($trackerResult) {
        echo "✓ Cleared AI entries from tracker\n";
    }
    
    // 4. Clear any notifications
    echo "Clearing notifications...\n";
    $notifQuery = "DELETE FROM notifications WHERE name LIKE '%AI%' OR description LIKE '%AI%'";
    $notifResult = $db->query($notifQuery);
    if ($notifResult) {
        echo "✓ Cleared AI notifications\n";
    }
    
    echo "\n=== AI DATA CLEANUP COMPLETE ===\n";
    echo "Please:\n";
    echo "1. Log out of SuiteCRM completely\n";
    echo "2. Clear your browser cache and cookies\n";
    echo "3. Log back in\n";
    echo "4. Try accessing the Cases page again\n\n";
    
} catch (Exception $e) {
    echo "Error during cleanup: " . $e->getMessage() . "\n";
    echo "This might be normal if the AI fields don't exist in the database.\n";
}
?>