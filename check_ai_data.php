<?php
/**
 * Check AI data in SuiteCRM database before cleanup
 * This script only reads - it won't modify anything
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

// Initialize SuiteCRM environment
chdir(__DIR__);
require_once('config.php');
require_once('include/utils.php');
require_once('include/TimeDate.php');
require_once('include/database/DBManagerFactory.php');

echo "=== AI DATA INSPECTION ===\n";
echo "Database: {$sugar_config['db_name']}\n";
echo "Host: {$sugar_config['db_host_name']}\n";
echo "User: {$sugar_config['db_user_name']}\n\n";

try {
    $db = DBManagerFactory::getInstance();
    
    // 1. Check if AI fields exist in cases table
    echo "1. CHECKING CASES TABLE AI FIELDS:\n";
    echo "-----------------------------------\n";
    
    $fieldsQuery = "SHOW COLUMNS FROM cases LIKE 'ai_%'";
    $fieldsResult = $db->query($fieldsQuery);
    
    $aiFields = array();
    while ($row = $db->fetchByAssoc($fieldsResult)) {
        $aiFields[] = $row['Field'];
        echo "Found AI field: " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    if (empty($aiFields)) {
        echo "• No AI fields found in cases table\n";
    }
    
    // 2. Check cases with AI data
    if (!empty($aiFields)) {
        echo "\n2. CASES WITH AI DATA:\n";
        echo "----------------------\n";
        
        $conditions = array();
        foreach ($aiFields as $field) {
            $conditions[] = "$field IS NOT NULL AND $field != ''";
        }
        $whereClause = implode(' OR ', $conditions);
        
        $casesQuery = "SELECT name, " . implode(', ', $aiFields) . " 
                       FROM cases 
                       WHERE ($whereClause) AND deleted = 0 
                       LIMIT 5";
        
        $casesResult = $db->query($casesQuery);
        $caseCount = 0;
        
        while ($row = $db->fetchByAssoc($casesResult)) {
            $caseCount++;
            echo "Case: " . $row['name'] . "\n";
            foreach ($aiFields as $field) {
                if (!empty($row[$field])) {
                    $value = strlen($row[$field]) > 50 ? substr($row[$field], 0, 50) . '...' : $row[$field];
                    echo "  $field: $value\n";
                }
            }
            echo "\n";
        }
        
        if ($caseCount == 0) {
            echo "• No cases found with AI data\n";
        } else {
            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM cases WHERE ($whereClause) AND deleted = 0";
            $countResult = $db->query($countQuery);
            $countRow = $db->fetchByAssoc($countResult);
            echo "Total cases with AI data: " . $countRow['total'] . "\n";
        }
    }
    
    // 3. Check AI-related case updates
    echo "\n3. AI-RELATED CASE UPDATES:\n";
    echo "---------------------------\n";
    
    $updatesQuery = "SELECT c.name as case_name, cu.name as update_name, 
                            LEFT(cu.description, 100) as description_preview,
                            cu.date_entered
                     FROM aop_case_updates cu
                     LEFT JOIN cases c ON cu.case_id = c.id
                     WHERE (cu.name LIKE '%AI%' OR cu.name LIKE '%ai%'
                            OR cu.description LIKE '%AI WINNABILITY%'
                            OR cu.description LIKE '%Assessment:%'
                            OR cu.description LIKE '%Winnability%')
                       AND cu.deleted = 0
                     ORDER BY cu.date_entered DESC
                     LIMIT 5";
    
    $updatesResult = $db->query($updatesQuery);
    $updateCount = 0;
    
    while ($row = $db->fetchByAssoc($updatesResult)) {
        $updateCount++;
        echo "Case: " . ($row['case_name'] ?: 'Unknown') . "\n";
        echo "Update: " . $row['update_name'] . "\n";
        echo "Date: " . $row['date_entered'] . "\n";
        echo "Preview: " . $row['description_preview'] . "...\n\n";
    }
    
    if ($updateCount == 0) {
        echo "• No AI-related case updates found\n";
    } else {
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM aop_case_updates 
                       WHERE (name LIKE '%AI%' OR name LIKE '%ai%'
                              OR description LIKE '%AI WINNABILITY%'
                              OR description LIKE '%Assessment:%'
                              OR description LIKE '%Winnability%')
                         AND deleted = 0";
        $countResult = $db->query($countQuery);
        $countRow = $db->fetchByAssoc($countResult);
        echo "Total AI case updates: " . $countRow['total'] . "\n";
    }
    
    // 4. Check for the specific case causing issues
    echo "\n4. CHECKING SPECIFIC PROBLEM CASE:\n";
    echo "----------------------------------\n";
    
    $problemCaseQuery = "SELECT id, name, status, 
                                ai_suggested_status, ai_confidence_score, ai_last_analysis
                         FROM cases 
                         WHERE name LIKE '%AI Test Case%' OR name LIKE '%Contract Dispute%'
                         AND deleted = 0";
    
    $problemResult = $db->query($problemCaseQuery);
    $problemCount = 0;
    
    while ($row = $db->fetchByAssoc($problemResult)) {
        $problemCount++;
        echo "Found problem case:\n";
        echo "  ID: " . $row['id'] . "\n";
        echo "  Name: " . $row['name'] . "\n"; 
        echo "  Status: " . $row['status'] . "\n";
        echo "  AI Suggested Status: " . ($row['ai_suggested_status'] ?: 'None') . "\n";
        echo "  AI Confidence: " . ($row['ai_confidence_score'] ?: 'None') . "\n";
        echo "  Last Analysis: " . ($row['ai_last_analysis'] ?: 'None') . "\n";
        
        // Check updates for this specific case
        $caseUpdatesQuery = "SELECT name, LEFT(description, 200) as description_preview, date_entered
                             FROM aop_case_updates 
                             WHERE case_id = '" . $row['id'] . "' 
                               AND deleted = 0 
                             ORDER BY date_entered DESC 
                             LIMIT 3";
        
        $caseUpdatesResult = $db->query($caseUpdatesQuery);
        echo "  Recent updates:\n";
        while ($updateRow = $db->fetchByAssoc($caseUpdatesResult)) {
            echo "    - " . $updateRow['name'] . " (" . $updateRow['date_entered'] . ")\n";
            echo "      " . $updateRow['description_preview'] . "...\n";
        }
        echo "\n";
    }
    
    if ($problemCount == 0) {
        echo "• No problem cases found with that name pattern\n";
    }
    
    echo "\n=== INSPECTION COMPLETE ===\n";
    echo "This data shows what will be cleaned up.\n";
    echo "No data was modified by this inspection.\n\n";
    
} catch (Exception $e) {
    echo "Error during inspection: " . $e->getMessage() . "\n";
}
?>