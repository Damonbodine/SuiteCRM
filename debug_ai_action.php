<?php
/**
 * Debug AI Action - Simulate what happens when button is clicked
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

echo "<h1>Debug AI Action Trigger</h1>";

// Test with both case IDs
$testCases = ['ai-test-case-001', 'ai-test-case-002'];

foreach ($testCases as $caseId) {
    echo "<h2>Testing Case: $caseId</h2>";
    
    // Simulate $_REQUEST['record']
    $_REQUEST['record'] = $caseId;
    
    echo "<h3>1. Case Retrieval Test</h3>";
    $case = BeanFactory::newBean('Cases');
    if ($case->retrieve($caseId)) {
        echo "✅ Case found: " . $case->name . "<br>";
        echo "Status: " . $case->status . "<br>";
        echo "Assigned User: " . $case->assigned_user_id . "<br>";
    } else {
        echo "❌ Case not found<br>";
        continue;
    }
    
    echo "<h3>2. Permission Check</h3>";
    if ($case->ACLAccess('edit')) {
        echo "✅ User has edit access<br>";
    } else {
        echo "❌ User lacks edit permission<br>";
        continue;
    }
    
    echo "<h3>3. Current User Check</h3>";
    global $current_user;
    if ($current_user && $current_user->id) {
        echo "✅ User authenticated: " . $current_user->user_name . " (ID: " . $current_user->id . ")<br>";
    } else {
        echo "❌ No authenticated user<br>";
        continue;
    }
    
    echo "<h3>4. AI Analyzer Test</h3>";
    try {
        require_once('custom/include/ai/CaseStatusAnalyzer.php');
        $analyzer = new CaseWinnabilityAnalyzer();
        
        // Use reflection to check validation
        $reflection = new ReflectionClass($analyzer);
        $validateMethod = $reflection->getMethod('validateInput');
        $validateMethod->setAccessible(true);
        
        if ($validateMethod->invoke($analyzer, $caseId)) {
            echo "✅ Case ID validation passed<br>";
        } else {
            echo "❌ Case ID validation failed<br>";
            continue;
        }
        
        echo "<h3>5. AI Analysis Attempt</h3>";
        $result = $analyzer->analyzeWinnability($caseId);
        if ($result) {
            echo "✅ Analysis successful<br>";
            echo "Percentage: " . $result['percentage'] . "%<br>";
            echo "Confidence: " . $result['confidence'] . "%<br>";
        } else {
            echo "❌ Analysis failed or returned false<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "<br>";
    }
    
    echo "<hr>";
}

echo "<h2>Debug Complete</h2>";
echo "<p>If a case fails, check the specific step that failed above.</p>";
?>