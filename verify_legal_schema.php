<?php
// Legal AI Email Schema Verification Script
// This script verifies that the legal AI email schema enhancements were applied correctly

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

global $db;

echo "<h2>Legal AI Email Schema Verification</h2>\n";
echo "<p>Verifying database schema enhancements for legal AI email analysis...</p>\n";

$errors = [];
$warnings = [];
$success = [];

// Test 1: Verify the base table exists
echo "<h3>1. Verifying base table exists...</h3>\n";
try {
    $result = $db->query("SHOW TABLES LIKE 'ai_email_analysis'");
    if ($db->getRowCount($result) > 0) {
        $success[] = "✅ Base table 'ai_email_analysis' exists";
        echo "<p style='color: green;'>✅ Base table 'ai_email_analysis' exists</p>\n";
    } else {
        $errors[] = "❌ Base table 'ai_email_analysis' does not exist";
        echo "<p style='color: red;'>❌ Base table 'ai_email_analysis' does not exist</p>\n";
    }
} catch (Exception $e) {
    $errors[] = "❌ Error checking base table: " . $e->getMessage();
    echo "<p style='color: red;'>❌ Error checking base table: " . $e->getMessage() . "</p>\n";
}

// Test 2: Verify new legal columns exist
echo "<h3>2. Verifying new legal columns...</h3>\n";
$expectedColumns = [
    'case_related' => 'TINYINT(1)',
    'case_number' => 'VARCHAR(50)',
    'client_name' => 'VARCHAR(100)',
    'legal_category' => 'VARCHAR(50)',
    'urgency_level' => 'VARCHAR(20)',
    'privilege_status' => 'VARCHAR(20)',
    'sentiment' => 'VARCHAR(20)',
    'confidence_score' => 'DECIMAL(3,2)',
    'key_people' => 'TEXT',
    'action_items' => 'TEXT',
    'deadline_detected' => 'DATETIME',
    'follow_up_required' => 'TINYINT(1)',
    'response_needed' => 'TINYINT(1)',
    'response_urgency' => 'VARCHAR(20)',
    'opposing_counsel' => 'VARCHAR(100)',
    'court_name' => 'VARCHAR(100)',
    'hearing_date' => 'DATETIME',
    'document_type' => 'VARCHAR(50)',
    'billable_time_detected' => 'DECIMAL(4,2)',
    'ethical_flags' => 'TEXT'
];

try {
    $result = $db->query("DESCRIBE ai_email_analysis");
    $existingColumns = [];
    
    while ($row = $db->fetchByAssoc($result)) {
        $existingColumns[$row['Field']] = $row['Type'];
    }
    
    foreach ($expectedColumns as $columnName => $expectedType) {
        if (isset($existingColumns[$columnName])) {
            $success[] = "✅ Column '$columnName' exists";
            echo "<p style='color: green;'>✅ Column '$columnName' exists ({$existingColumns[$columnName]})</p>\n";
        } else {
            $errors[] = "❌ Column '$columnName' is missing";
            echo "<p style='color: red;'>❌ Column '$columnName' is missing</p>\n";
        }
    }
} catch (Exception $e) {
    $errors[] = "❌ Error checking columns: " . $e->getMessage();
    echo "<p style='color: red;'>❌ Error checking columns: " . $e->getMessage() . "</p>\n";
}

// Test 3: Verify indexes exist
echo "<h3>3. Verifying performance indexes...</h3>\n";
$expectedIndexes = [
    'idx_legal_category',
    'idx_urgency_level', 
    'idx_case_related',
    'idx_deadline_detected',
    'idx_response_needed',
    'idx_hearing_date',
    'idx_user_category',
    'idx_user_urgency'
];

try {
    $result = $db->query("SHOW INDEX FROM ai_email_analysis");
    $existingIndexes = [];
    
    while ($row = $db->fetchByAssoc($result)) {
        $existingIndexes[] = $row['Key_name'];
    }
    
    foreach ($expectedIndexes as $indexName) {
        if (in_array($indexName, $existingIndexes)) {
            $success[] = "✅ Index '$indexName' exists";
            echo "<p style='color: green;'>✅ Index '$indexName' exists</p>\n";
        } else {
            $warnings[] = "⚠️ Index '$indexName' is missing (performance may be affected)";
            echo "<p style='color: orange;'>⚠️ Index '$indexName' is missing (performance may be affected)</p>\n";
        }
    }
} catch (Exception $e) {
    $warnings[] = "⚠️ Error checking indexes: " . $e->getMessage();
    echo "<p style='color: orange;'>⚠️ Error checking indexes: " . $e->getMessage() . "</p>\n";
}

// Test 4: Test data insertion with new fields
echo "<h3>4. Testing data insertion with new fields...</h3>\n";
try {
    $testId = create_guid();
    $testEmailId = 'schema_test_' . time();
    $userId = '1'; // Admin user fallback
    
    // Clean up any existing test records first
    $db->query("DELETE FROM ai_email_analysis WHERE email_message_id LIKE 'schema_test_%'");
    
    $insertQuery = "INSERT INTO ai_email_analysis 
        (id, email_message_id, user_id, category, priority_score, ai_summary,
         case_related, case_number, client_name, legal_category, urgency_level,
         privilege_status, sentiment, confidence_score, response_needed,
         analysis_date, expires_at, deleted) 
        VALUES 
        (?, ?, ?, ?, ?, ?,
         ?, ?, ?, ?, ?,
         ?, ?, ?, ?,
         NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 0)";
    
    $params = [
        $testId, $testEmailId, $userId, 'legal', 8, 'Test legal email analysis',
        1, 'CR-2025-TEST', 'Test Client', 'court', 'urgent',
        'privileged', 'neutral', 0.95, 1
    ];
    
    $result = $db->pQuery($insertQuery, $params);
    
    if ($result) {
        // Verify the record was inserted
        $checkQuery = "SELECT * FROM ai_email_analysis WHERE id = ?";
        $checkResult = $db->pQuery($checkQuery, [$testId]);
        
        if ($checkResult && $row = $db->fetchByAssoc($checkResult)) {
            $success[] = "✅ Data insertion test successful";
            echo "<p style='color: green;'>✅ Data insertion test successful</p>\n";
            echo "<p>Test record details:</p>\n";
            echo "<ul>\n";
            echo "<li>Case Number: {$row['case_number']}</li>\n";
            echo "<li>Legal Category: {$row['legal_category']}</li>\n";
            echo "<li>Urgency Level: {$row['urgency_level']}</li>\n";
            echo "<li>Privilege Status: {$row['privilege_status']}</li>\n";
            echo "<li>Confidence Score: {$row['confidence_score']}</li>\n";
            echo "</ul>\n";
            
            // Clean up test record
            $db->pQuery("DELETE FROM ai_email_analysis WHERE id = ?", [$testId]);
            echo "<p style='color: gray;'>Test record cleaned up</p>\n";
        } else {
            $errors[] = "❌ Data insertion test failed - record not found after insert";
            echo "<p style='color: red;'>❌ Data insertion test failed - record not found after insert</p>\n";
        }
    } else {
        $errors[] = "❌ Data insertion test failed - insert query failed";
        echo "<p style='color: red;'>❌ Data insertion test failed - insert query failed</p>\n";
    }
} catch (Exception $e) {
    $errors[] = "❌ Data insertion test error: " . $e->getMessage();
    echo "<p style='color: red;'>❌ Data insertion test error: " . $e->getMessage() . "</p>\n";
}

// Test 5: Verify constraints (if any)
echo "<h3>5. Testing data constraints...</h3>\n";
try {
    // Test confidence score constraint (should be between 0 and 1)
    $testId = create_guid();
    $testEmailId = 'constraint_test_' . time();
    
    $invalidInsertQuery = "INSERT INTO ai_email_analysis 
        (id, email_message_id, user_id, confidence_score, analysis_date, expires_at, deleted) 
        VALUES 
        (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 0)";
    
    // Try to insert invalid confidence score (should fail)
    try {
        $db->pQuery($invalidInsertQuery, [$testId, $testEmailId, '1', 1.5]);
        $warnings[] = "⚠️ Confidence score constraint may not be enforced (1.5 was accepted)";
        echo "<p style='color: orange;'>⚠️ Confidence score constraint may not be enforced</p>\n";
        // Clean up if it was inserted
        $db->pQuery("DELETE FROM ai_email_analysis WHERE id = ?", [$testId]);
    } catch (Exception $e) {
        $success[] = "✅ Confidence score constraint is working (rejected invalid value)";
        echo "<p style='color: green;'>✅ Confidence score constraint is working</p>\n";
    }
} catch (Exception $e) {
    $warnings[] = "⚠️ Error testing constraints: " . $e->getMessage();
    echo "<p style='color: orange;'>⚠️ Error testing constraints: " . $e->getMessage() . "</p>\n";
}

// Test 6: Performance check
echo "<h3>6. Basic performance check...</h3>\n";
try {
    $startTime = microtime(true);
    
    // Run a complex query using the new indexes
    $perfQuery = "SELECT COUNT(*) as total_legal,
                  SUM(CASE WHEN legal_category = 'court' THEN 1 ELSE 0 END) as court_emails,
                  SUM(CASE WHEN urgency_level = 'urgent' THEN 1 ELSE 0 END) as urgent_emails,
                  SUM(CASE WHEN response_needed = 1 THEN 1 ELSE 0 END) as needs_response
                  FROM ai_email_analysis 
                  WHERE deleted = 0";
    
    $result = $db->query($perfQuery);
    $endTime = microtime(true);
    
    $queryTime = round(($endTime - $startTime) * 1000, 2); // Convert to milliseconds
    
    if ($result && $row = $db->fetchByAssoc($result)) {
        $success[] = "✅ Performance test completed in {$queryTime}ms";
        echo "<p style='color: green;'>✅ Performance test completed in {$queryTime}ms</p>\n";
        echo "<p>Query results:</p>\n";
        echo "<ul>\n";
        echo "<li>Total records: {$row['total_legal']}</li>\n";
        echo "<li>Court emails: {$row['court_emails']}</li>\n";
        echo "<li>Urgent emails: {$row['urgent_emails']}</li>\n";
        echo "<li>Needs response: {$row['needs_response']}</li>\n";
        echo "</ul>\n";
        
        if ($queryTime > 1000) {
            $warnings[] = "⚠️ Query took longer than expected ({$queryTime}ms) - consider checking indexes";
            echo "<p style='color: orange;'>⚠️ Query performance may need optimization</p>\n";
        }
    }
} catch (Exception $e) {
    $warnings[] = "⚠️ Performance test error: " . $e->getMessage();
    echo "<p style='color: orange;'>⚠️ Performance test error: " . $e->getMessage() . "</p>\n";
}

// Final Summary
echo "<h3>Verification Summary</h3>\n";
echo "<div style='background: #f0f0f0; padding: 15px; border-radius: 5px;'>\n";

if (count($success) > 0) {
    echo "<h4 style='color: green;'>✅ Successful Checks (" . count($success) . ")</h4>\n";
    echo "<ul>\n";
    foreach ($success as $item) {
        echo "<li>$item</li>\n";
    }
    echo "</ul>\n";
}

if (count($warnings) > 0) {
    echo "<h4 style='color: orange;'>⚠️ Warnings (" . count($warnings) . ")</h4>\n";
    echo "<ul>\n";
    foreach ($warnings as $item) {
        echo "<li>$item</li>\n";
    }
    echo "</ul>\n";
}

if (count($errors) > 0) {
    echo "<h4 style='color: red;'>❌ Errors (" . count($errors) . ")</h4>\n";
    echo "<ul>\n";
    foreach ($errors as $item) {
        echo "<li>$item</li>\n";
    }
    echo "</ul>\n";
    echo "<p style='color: red; font-weight: bold;'>⚠️ Please run the legal_ai_email_schema_update.sql script to fix these errors.</p>\n";
} else {
    echo "<h4 style='color: green;'>🎉 All Critical Tests Passed!</h4>\n";
    echo "<p style='color: green;'>The legal AI email schema enhancement is ready for use.</p>\n";
}

echo "</div>\n";

// Show next steps
echo "<h3>Next Steps</h3>\n";
echo "<ol>\n";
if (count($errors) > 0) {
    echo "<li style='color: red;'>Fix schema errors by running the SQL update script</li>\n";
    echo "<li>Re-run this verification script</li>\n";
} else {
    echo "<li style='color: green;'>✅ Schema is ready</li>\n";
}
echo "<li>Implement LegalAIAnalysisService for AI integration</li>\n";
echo "<li>Update AIEmailAnalyzer to use new legal fields</li>\n";
echo "<li>Test with real email data</li>\n";
echo "</ol>\n";

echo "<p><em>Verification completed at " . date('Y-m-d H:i:s') . "</em></p>\n";
?>