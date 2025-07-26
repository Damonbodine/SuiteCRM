<?php
/**
 * Simple AI Test - Debug the AI analysis issue
 */

// Prevent direct web access
if (php_sapi_name() !== 'cli') {
    die("This script must be run from command line only.\n");
}

echo "=== Simple AI Analysis Test ===\n\n";

// Check if we're in the right directory
if (!file_exists('config.php')) {
    die("Error: Please run this script from the SuiteCRM root directory.\n");
}

// Include SuiteCRM
define('sugarEntry', true);
require_once 'sugar_version.php';
require_once 'include/entrypoint.php';

echo "✓ SuiteCRM loaded\n";

// Set up current user - load actual admin user from database
global $current_user;
if (!$current_user) {
    // Try to load an actual admin user from the database
    $user_bean = BeanFactory::getBean('Users');
    $admin_user = $user_bean->retrieve('1'); // ID '1' is usually the admin
    
    if ($admin_user && $admin_user->id) {
        $current_user = $admin_user;
        echo "✓ Loaded admin user: " . $admin_user->user_name . "\n";
    } else {
        // Fallback: create a mock admin user
        $current_user = new stdClass();
        $current_user->id = '1';
        $current_user->user_name = 'admin';
        $current_user->is_admin = '1';
        echo "✓ Created mock admin user\n";
    }
}

// Make sure the user is set in GLOBALS too
$GLOBALS['current_user'] = $current_user;

// Load the AI analyzer
require_once 'custom/include/ai/CaseStatusAnalyzer.php';
$analyzer = new CaseStatusAnalyzer();
echo "✓ AI analyzer loaded\n";

// Test case ID
$test_case_id = 'ai-test-case-001';

// Check if case exists in database
global $db;
$query = "SELECT id, name, status FROM cases WHERE id = '$test_case_id'";
$result = $db->query($query);
$case_row = $db->fetchByAssoc($result);

if (!$case_row) {
    die("✗ Test case not found in database\n");
}

echo "✓ Test case found: " . $case_row['name'] . "\n";

// Try loading the case with BeanFactory
$case_bean = BeanFactory::getBean('Cases', $test_case_id);
if (!$case_bean || !$case_bean->id) {
    die("✗ Could not load case bean\n");
}

echo "✓ Case bean loaded: " . $case_bean->name . "\n";

// Test individual components of the analyzer
echo "\n--- Testing AI Analyzer Components ---\n";

// Test input validation manually
$reflection = new ReflectionClass($analyzer);

// Test validateInput method
$validateMethod = $reflection->getMethod('validateInput');
$validateMethod->setAccessible(true);
$isValid = $validateMethod->invoke($analyzer, $test_case_id);
echo ($isValid ? "✓" : "✗") . " Input validation: " . ($isValid ? "PASS" : "FAIL") . "\n";

// Test case loading
$loadMethod = $reflection->getMethod('loadCaseSecurely'); 
$loadMethod->setAccessible(true);
$loaded_case = $loadMethod->invoke($analyzer, $test_case_id);
echo ($loaded_case ? "✓" : "✗") . " Case loading: " . ($loaded_case ? "PASS" : "FAIL") . "\n";

if ($loaded_case) {
    echo "  - Case ID: " . $loaded_case->id . "\n";
    echo "  - Case Name: " . $loaded_case->name . "\n";
    echo "  - Case Status: " . $loaded_case->status . "\n";
}

// Test permission check
if ($loaded_case) {
    echo "Debug permission check:\n";
    echo "  - current_user->id: " . (isset($current_user->id) ? $current_user->id : 'NOT SET') . "\n";
    echo "  - current_user->is_admin: " . (isset($current_user->is_admin) ? $current_user->is_admin : 'NOT SET') . "\n";
    echo "  - case->assigned_user_id: " . (isset($loaded_case->assigned_user_id) ? $loaded_case->assigned_user_id : 'NOT SET') . "\n";
    
    $permMethod = $reflection->getMethod('checkCaseAccess');
    $permMethod->setAccessible(true); 
    $hasAccess = $permMethod->invoke($analyzer, $loaded_case);
    echo ($hasAccess ? "✓" : "✗") . " Permission check: " . ($hasAccess ? "PASS" : "FAIL") . "\n";
}

// Test full analysis
echo "\n--- Testing Full Analysis ---\n";
$analysis_result = $analyzer->analyzeCase($test_case_id);

if ($analysis_result) {
    echo "✓ Analysis successful!\n";
    echo "  - Suggested Status: " . (isset($analysis_result['suggested_status']) ? $analysis_result['suggested_status'] : 'None') . "\n";
    echo "  - Confidence Score: " . (isset($analysis_result['confidence_score']) ? $analysis_result['confidence_score'] : 'N/A') . "\n";
    echo "  - Factors: " . count(isset($analysis_result['analysis_factors']) ? $analysis_result['analysis_factors'] : array()) . "\n";
} else {
    echo "✗ Analysis failed - debugging...\n";
    
    // Check what specific step failed
    echo "\nDebugging analysis failure...\n";
    
    // Try to run analysis steps manually
    try {
        // Set the case bean manually
        $caseProperty = $reflection->getProperty('caseBean');
        $caseProperty->setAccessible(true);
        $caseProperty->setValue($analyzer, $loaded_case);
        
        // Try individual analysis methods
        echo "Testing individual analysis methods:\n";
        
        $taskMethod = $reflection->getMethod('analyzeTaskProgress');
        $taskMethod->setAccessible(true);
        $taskResult = $taskMethod->invoke($analyzer);
        echo "  - Task Analysis: " . (is_array($taskResult) ? "✓ PASS" : "✗ FAIL") . "\n";
        
        $commMethod = $reflection->getMethod('analyzeRecentCommunication');
        $commMethod->setAccessible(true);
        $commResult = $commMethod->invoke($analyzer);
        echo "  - Communication Analysis: " . (is_array($commResult) ? "✓ PASS" : "✗ FAIL") . "\n";
        
    } catch (Exception $e) {
        echo "  - Error during manual analysis: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Test Complete ===\n";
?>