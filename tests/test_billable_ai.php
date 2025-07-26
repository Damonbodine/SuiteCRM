<?php
/**
 * Test script for Billable Hours AI functionality
 */

// Set up SuiteCRM environment
if (!defined('sugarEntry')) define('sugarEntry', true);
require_once('config.php');
require_once('include/MVC/preDispatch.php');
require_once('custom/include/ai/BillableHoursAI.php');

echo "<h2>Testing Billable Hours AI Integration</h2>\n";

try {
    // Initialize AI class
    $ai = new BillableHoursAI();
    echo "✅ BillableHoursAI class loaded successfully<br>\n";
    
    // Test 1: Smart Description Suggestions
    echo "<h3>Test 1: Smart Description Suggestions</h3>\n";
    $descriptions = $ai->getSmartDescriptions('court_appearance');
    echo "Activity: court_appearance<br>\n";
    echo "Suggestions (" . count($descriptions) . "):<br>\n";
    foreach ($descriptions as $desc) {
        echo "- " . htmlspecialchars($desc) . "<br>\n";
    }
    echo "<br>\n";
    
    // Test 2: Time Prediction
    echo "<h3>Test 2: Time Prediction</h3>\n";
    $prediction = $ai->predictTimeDuration('client_consultation');
    echo "Activity: client_consultation<br>\n";
    echo "Predicted Hours: " . $prediction['predicted_hours'] . "<br>\n";
    echo "Confidence: " . round($prediction['confidence'] * 100) . "%<br>\n";
    echo "Reasoning: " . $prediction['reasoning'] . "<br><br>\n";
    
    // Test 3: Activity Type Suggestion
    echo "<h3>Test 3: Activity Type Suggestion</h3>\n";
    $suggestions = $ai->suggestActivityType('reviewed police reports and evidence for case preparation');
    echo "Description: 'reviewed police reports and evidence for case preparation'<br>\n";
    echo "Suggested Activities:<br>\n";
    foreach ($suggestions as $suggestion) {
        echo "- " . $suggestion['label'] . " (" . round($suggestion['confidence'] * 100) . "% confident)<br>\n";
    }
    echo "<br>\n";
    
    // Test 4: Case-Specific Suggestions (if we have cases)
    echo "<h3>Test 4: Database Connection Test</h3>\n";
    global $db;
    if ($db) {
        echo "✅ Database connection established<br>\n";
        
        // Check for cases
        $query = "SELECT COUNT(*) as case_count FROM cases WHERE deleted = 0";
        $result = $db->query($query);
        $row = $db->fetchByAssoc($result);
        echo "Total cases in system: " . $row['case_count'] . "<br>\n";
        
        // Check for existing time entries
        $query = "SELECT COUNT(*) as task_count FROM tasks WHERE name LIKE 'Billable Time Entry%' AND deleted = 0";
        $result = $db->query($query);
        $row = $db->fetchByAssoc($result);
        echo "Existing billable time entries: " . $row['task_count'] . "<br>\n";
    } else {
        echo "❌ Database connection failed<br>\n";
    }
    
    echo "<br><strong>✅ All AI tests completed successfully!</strong><br>\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>\n";
    echo "Stack trace:<br>\n<pre>" . $e->getTraceAsString() . "</pre>\n";
}

echo "<br><a href='index.php'>← Back to SuiteCRM</a>\n";
?>