<?php
/**
 * Simple AI data check using direct MySQL connection
 */

// Database connection settings from config
$host = 'db';  // Docker service name
$username = 'suiteuser';
$password = 'suitepass';
$database = 'suitecrm';

echo "=== AI DATA INSPECTION ===\n";
echo "Connecting to MySQL...\n";

try {
    // Create connection
    $mysqli = new mysqli($host, $username, $password, $database);
    
    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }
    
    echo "✓ Connected to database: $database\n\n";
    
    // 1. Check if AI fields exist in cases table
    echo "1. CHECKING CASES TABLE STRUCTURE:\n";
    echo "----------------------------------\n";
    
    $result = $mysqli->query("SHOW COLUMNS FROM cases LIKE 'ai_%'");
    $aiFields = array();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $aiFields[] = $row['Field'];
            echo "AI field found: " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "• No AI fields found in cases table\n";
    }
    
    // 2. Check AI case updates
    echo "\n2. AI-RELATED CASE UPDATES:\n";
    echo "---------------------------\n";
    
    $query = "SELECT cu.name as update_name, 
                     cu.description,
                     cu.date_entered,
                     c.name as case_name
              FROM aop_case_updates cu
              LEFT JOIN cases c ON cu.case_id = c.id
              WHERE (cu.name LIKE '%AI%' 
                     OR cu.description LIKE '%AI WINNABILITY%'
                     OR cu.description LIKE '%Assessment:%'
                     OR cu.description LIKE '%Winnability%')
                AND cu.deleted = 0
              ORDER BY cu.date_entered DESC
              LIMIT 3";
    
    $result = $mysqli->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Case: " . ($row['case_name'] ?: 'Unknown') . "\n";
            echo "Update: " . $row['update_name'] . "\n";
            echo "Date: " . $row['date_entered'] . "\n";
            echo "Description preview: " . substr($row['description'], 0, 100) . "...\n\n";
        }
        
        // Get total count
        $countResult = $mysqli->query("SELECT COUNT(*) as total FROM aop_case_updates 
                                       WHERE (name LIKE '%AI%' 
                                              OR description LIKE '%AI WINNABILITY%'
                                              OR description LIKE '%Assessment:%'
                                              OR description LIKE '%Winnability%')
                                         AND deleted = 0");
        $countRow = $countResult->fetch_assoc();
        echo "Total AI case updates found: " . $countRow['total'] . "\n";
    } else {
        echo "• No AI-related case updates found\n";
    }
    
    // 3. Check specific problem case
    echo "\n3. CHECKING PROBLEM CASE:\n";
    echo "-------------------------\n";
    
    $query = "SELECT id, name, status 
              FROM cases 
              WHERE (name LIKE '%AI Test Case%' OR name LIKE '%Contract Dispute%')
                AND deleted = 0";
    
    $result = $mysqli->query($query);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Problem case found:\n";
            echo "  ID: " . $row['id'] . "\n";
            echo "  Name: " . $row['name'] . "\n";
            echo "  Status: " . $row['status'] . "\n";
            
            // Check updates for this case
            $caseId = $row['id'];
            $updateQuery = "SELECT name, description, date_entered 
                           FROM aop_case_updates 
                           WHERE case_id = '$caseId' AND deleted = 0 
                           ORDER BY date_entered DESC 
                           LIMIT 2";
            
            $updateResult = $mysqli->query($updateQuery);
            echo "  Recent updates:\n";
            
            if ($updateResult && $updateResult->num_rows > 0) {
                while ($updateRow = $updateResult->fetch_assoc()) {
                    echo "    - " . $updateRow['name'] . " (" . $updateRow['date_entered'] . ")\n";
                    $preview = substr($updateRow['description'], 0, 150);
                    echo "      " . $preview . "...\n";
                }
            } else {
                echo "    No updates found\n";
            }
            echo "\n";
        }
    } else {
        echo "• No problem cases found with that name pattern\n";
    }
    
    // 4. Show what cleanup queries would do
    echo "\n4. CLEANUP PREVIEW:\n";
    echo "-------------------\n";
    
    if (!empty($aiFields)) {
        echo "Cases table cleanup would affect:\n";
        $fieldList = implode(', ', $aiFields);
        $conditions = array();
        foreach ($aiFields as $field) {
            $conditions[] = "$field IS NOT NULL AND $field != ''";
        }
        $whereClause = implode(' OR ', $conditions);
        
        $countQuery = "SELECT COUNT(*) as total FROM cases WHERE ($whereClause) AND deleted = 0";
        $result = $mysqli->query($countQuery);
        $row = $result->fetch_assoc();
        echo "  - " . $row['total'] . " cases have AI data that would be cleared\n";
    }
    
    $updateCountQuery = "SELECT COUNT(*) as total FROM aop_case_updates 
                         WHERE (name LIKE '%AI%' 
                                OR description LIKE '%AI WINNABILITY%'
                                OR description LIKE '%Assessment:%'
                                OR description LIKE '%Winnability%')
                           AND deleted = 0";
    $result = $mysqli->query($updateCountQuery);
    $row = $result->fetch_assoc();
    echo "  - " . $row['total'] . " case updates would be deleted\n";
    
    echo "\n=== INSPECTION COMPLETE ===\n";
    echo "Review the data above before running cleanup.\n";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>