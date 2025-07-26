<?php
/**
 * Test script for AI Winnability Analysis
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

// Bootstrap SuiteCRM
chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

echo "<h1>Testing AI Winnability Analysis</h1>";

// Test case ID
$caseId = 'ai-test-case-001';

echo "<h2>1. Testing Case Retrieval</h2>";
$case = BeanFactory::newBean('Cases');
if ($case->retrieve($caseId)) {
    echo "✅ Case found: " . $case->name . "<br>";
    echo "Description: " . $case->description . "<br>";
} else {
    echo "❌ Case not found<br>";
    exit;
}

echo "<h2>2. Testing AI Analyzer Class</h2>";
try {
    require_once('custom/include/ai/CaseStatusAnalyzer.php');
    $analyzer = new CaseWinnabilityAnalyzer();
    echo "✅ CaseWinnabilityAnalyzer class loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Failed to load analyzer: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>3. Testing AI Analysis</h2>";
try {
    $result = $analyzer->analyzeWinnability($caseId);
    if ($result) {  
        echo "✅ Analysis completed successfully<br>";
        echo "Percentage: " . $result['percentage'] . "%<br>";
        echo "Confidence: " . $result['confidence'] . "%<br>";
        echo "Analysis: " . substr($result['detailed_analysis'], 0, 200) . "...<br>";
    } else {
        echo "❌ Analysis returned false<br>";
    }
} catch (Exception $e) {
    echo "❌ Analysis failed: " . $e->getMessage() . "<br>";
}

echo "<h2>4. Testing Action File Access</h2>";
$actionFile = 'custom/modules/Cases/aiWinnabilityAnalysis.php';
if (file_exists($actionFile)) {
    echo "✅ Action file exists: " . $actionFile . "<br>";
    echo "File size: " . filesize($actionFile) . " bytes<br>";
} else {
    echo "❌ Action file not found: " . $actionFile . "<br>";
}

echo "<h2>5. Current AI Fields in Case</h2>";
echo "AI Suggested Status: " . ($case->ai_suggested_status ?: 'None') . "<br>";
echo "AI Confidence Score: " . ($case->ai_confidence_score ?: 'None') . "<br>";
echo "AI Last Analysis: " . ($case->ai_last_analysis ?: 'None') . "<br>";

echo "<h2>Test Complete</h2>";
echo "<p><a href='index.php?module=Cases&action=DetailView&record=" . $caseId . "'>View Case in SuiteCRM</a></p>";
?>