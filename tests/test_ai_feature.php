<?php
/**
 * AI Status Intelligence - Interactive Testing Script
 * Run this script to test the AI feature step by step
 */

// Prevent direct web access
if (php_sapi_name() !== 'cli') {
    die("This script must be run from command line only.\n");
}

echo "=== AI Status Intelligence Testing Script ===\n\n";

// Check if we're in the right directory
if (!file_exists('config.php')) {
    die("Error: Please run this script from the SuiteCRM root directory.\n");
}

// Include SuiteCRM
define('sugarEntry', true);
require_once 'sugar_version.php';
require_once 'include/entrypoint.php';

echo "✓ SuiteCRM loaded successfully\n";

// Test 1: Check if AI files exist
echo "\n--- Test 1: File Existence Check ---\n";
$required_files = [
    'custom/include/ai/CaseStatusAnalyzer.php',
    'custom/modules/Cases/CaseAIAnalysisHook.php',
    'custom/modules/Cases/logic_hooks.php',
    'custom/modules/Cases/views/view.detail.php',
    'custom/modules/Cases/tpls/ai_status_widget.tpl',
    'custom/Extension/modules/Cases/Ext/Vardefs/ai_status_intelligence.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✓ $file\n";
    } else {
        echo "✗ $file (MISSING)\n";
    }
}

// Test 2: Database Schema Check
echo "\n--- Test 2: Database Schema Check ---\n";
try {
    global $db;
    $query = "SHOW COLUMNS FROM cases LIKE 'ai_%'";
    $result = $db->query($query);
    
    $ai_fields = [];
    while ($row = $db->fetchByAssoc($result)) {
        $ai_fields[] = $row['Field'];
        echo "✓ {$row['Field']} ({$row['Type']})\n";
    }
    
    if (count($ai_fields) === 5) {
        echo "✓ All 5 AI fields present in database\n";
    } else {
        echo "✗ Expected 5 AI fields, found " . count($ai_fields) . "\n";
    }
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

// Test 3: Load AI Analyzer Class
echo "\n--- Test 3: AI Analyzer Class Test ---\n";
try {
    require_once 'custom/include/ai/CaseStatusAnalyzer.php';
    $analyzer = new CaseStatusAnalyzer();
    echo "✓ CaseStatusAnalyzer class loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Error loading CaseStatusAnalyzer: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Check for test cases
echo "\n--- Test 4: Test Data Check ---\n";
try {
    $test_case_id = 'ai-test-case-001';
    $query = "SELECT id, name, status FROM cases WHERE id = '$test_case_id'";
    $result = $db->query($query);
    
    if ($row = $db->fetchByAssoc($result)) {
        echo "✓ Test case found: {$row['name']} (Status: {$row['status']})\n";
        
        // Count related activities
        $task_query = "SELECT COUNT(*) as count FROM tasks WHERE parent_id = '$test_case_id' AND deleted = 0";
        $task_result = $db->query($task_query);
        $task_row = $db->fetchByAssoc($task_result);
        echo "✓ Found {$task_row['count']} tasks\n";
        
        $call_query = "SELECT COUNT(*) as count FROM calls WHERE parent_id = '$test_case_id' AND deleted = 0";
        $call_result = $db->query($call_query);
        $call_row = $db->fetchByAssoc($call_result);
        echo "✓ Found {$call_row['count']} calls\n";
        
    } else {
        echo "✗ Test case not found. Please run: mysql -u [user] -p [db] < create_test_data.sql\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking test data: " . $e->getMessage() . "\n";
}

// Test 5: Run AI Analysis
echo "\n--- Test 5: AI Analysis Test ---\n";
if (isset($row)) {
    try {
        $start_time = microtime(true);
        $analysis_result = $analyzer->analyzeCase('ai-test-case-001');
        $end_time = microtime(true);
        
        $execution_time = ($end_time - $start_time) * 1000;
        echo "✓ Analysis completed in " . number_format($execution_time, 2) . " milliseconds\n";
        
        if ($analysis_result) {
            echo "✓ Analysis Result:\n";
            echo "  - Suggested Status: " . (isset($analysis_result['suggested_status']) ? $analysis_result['suggested_status'] : 'None') . "\n";
            echo "  - Confidence Score: " . (isset($analysis_result['confidence_score']) ? $analysis_result['confidence_score'] : 'N/A') . "\n";
            echo "  - Analysis Factors: " . count(isset($analysis_result['analysis_factors']) ? $analysis_result['analysis_factors'] : array()) . " factors\n";
            
            // Pretty print factors
            if (!empty($analysis_result['analysis_factors'])) {
                echo "  - Factor Details:\n";
                foreach ($analysis_result['analysis_factors'] as $factor => $data) {
                    if (is_array($data)) {
                        echo "    * $factor: " . json_encode($data) . "\n";
                    } else {
                        echo "    * $factor: $data\n";
                    }
                }
            }
        } else {
            echo "✗ Analysis returned no result\n";
        }
    } catch (Exception $e) {
        echo "✗ Analysis failed: " . $e->getMessage() . "\n";
    }
}

// Test 6: Database Update Check
echo "\n--- Test 6: Database Update Check ---\n";
if (isset($analysis_result) && $analysis_result) {
    try {
        // Update the case with AI results (simulate what the logic hook does)
        $update_query = "UPDATE cases SET 
            ai_suggested_status = '" . $db->quote($analysis_result['suggested_status']) . "',
            ai_confidence_score = " . floatval($analysis_result['confidence_score']) . ",
            ai_last_analysis = NOW(),
            ai_analysis_factors = '" . $db->quote(json_encode($analysis_result['analysis_factors'])) . "',
            ai_status_needs_review = 1
            WHERE id = 'ai-test-case-001'";
        
        $db->query($update_query);
        echo "✓ Case updated with AI analysis results\n";
        
        // Verify the update
        $verify_query = "SELECT ai_suggested_status, ai_confidence_score, ai_last_analysis, ai_status_needs_review FROM cases WHERE id = 'ai-test-case-001'";
        $verify_result = $db->query($verify_query);
        $verify_row = $db->fetchByAssoc($verify_result);
        
        echo "✓ Verification:\n";
        echo "  - AI Suggested Status: " . (isset($verify_row['ai_suggested_status']) ? $verify_row['ai_suggested_status'] : 'NULL') . "\n";
        echo "  - AI Confidence Score: " . (isset($verify_row['ai_confidence_score']) ? $verify_row['ai_confidence_score'] : 'NULL') . "\n";
        echo "  - AI Last Analysis: " . (isset($verify_row['ai_last_analysis']) ? $verify_row['ai_last_analysis'] : 'NULL') . "\n";
        echo "  - Needs Review: " . ($verify_row['ai_status_needs_review'] ? 'Yes' : 'No') . "\n";
        
    } catch (Exception $e) {
        echo "✗ Database update failed: " . $e->getMessage() . "\n";
    }
}

// Test 7: Logic Hook Test
echo "\n--- Test 7: Logic Hook Integration Test ---\n";
try {
    $case_bean = BeanFactory::getBean('Cases', 'ai-test-case-001');
    if ($case_bean) {
        echo "✓ Case bean loaded successfully\n";
        
        // Check if our logic hooks are registered
        $hooks = $case_bean->getHookArray('after_save');
        $ai_hook_found = false;
        
        foreach ($hooks as $hook) {
            if (is_array($hook)) {
                foreach ($hook as $hook_detail) {
                    if (is_array($hook_detail) && isset($hook_detail[1]) && $hook_detail[1] === 'AI Status Analysis') {
                        $ai_hook_found = true;
                        echo "✓ AI logic hook found and registered\n";
                        break 2;
                    }
                }
            }
        }
        
        if (!$ai_hook_found) {
            echo "✗ AI logic hook not found in after_save hooks\n";
        }
        
        // Test hook execution by updating case
        echo "Testing logic hook execution by updating case priority...\n";
        $old_priority = $case_bean->priority;
        $case_bean->priority = ($old_priority === 'High') ? 'Medium' : 'High';
        
        if ($case_bean->save()) {
            echo "✓ Case saved successfully, logic hooks should have executed\n";
            
            // Check if AI fields were updated
            $case_bean->retrieve($case_bean->id);
            if ($case_bean->ai_last_analysis) {
                echo "✓ AI analysis timestamp updated: " . $case_bean->ai_last_analysis . "\n";
            } else {
                echo "? AI analysis timestamp not set (may be rate limited)\n";
            }
        } else {
            echo "✗ Failed to save case\n";
        }
        
    } else {
        echo "✗ Failed to load case bean\n";
    }
} catch (Exception $e) {
    echo "✗ Logic hook test failed: " . $e->getMessage() . "\n";
}

// Test 8: Performance Benchmark
echo "\n--- Test 8: Performance Benchmark ---\n";
$iterations = 5;
$times = [];

for ($i = 0; $i < $iterations; $i++) {
    $start = microtime(true);
    $result = $analyzer->analyzeCase('ai-test-case-001');
    $end = microtime(true);
    $times[] = ($end - $start) * 1000;
}

$avg_time = array_sum($times) / count($times);
$max_time = max($times);
$min_time = min($times);

echo "✓ Performance Results ($iterations iterations):\n";
echo "  - Average: " . number_format($avg_time, 2) . "ms\n";
echo "  - Maximum: " . number_format($max_time, 2) . "ms\n";
echo "  - Minimum: " . number_format($min_time, 2) . "ms\n";
echo "  - Target: <500ms per analysis\n";
echo "  - Result: " . ($avg_time < 500 ? "✓ PASS" : "✗ FAIL - Too slow") . "\n";

// Summary
echo "\n=== TEST SUMMARY ===\n";
echo "If all tests show ✓, your AI Status Intelligence feature is working correctly!\n\n";
echo "Next steps:\n";
echo "1. Access SuiteCRM web interface\n";
echo "2. Navigate to Cases → AI Test Case - Personal Injury Claim\n";
echo "3. Look for the AI Status Intelligence widget\n";
echo "4. Try editing the case to trigger new analysis\n";
echo "5. Test Accept/Dismiss buttons in the AI widget\n\n";

echo "For web testing, check the browser console for any JavaScript errors.\n";
echo "Monitor suitecrm.log for any PHP errors during testing.\n";
?>