<?php
/**
 * Test Universal Case Analyzer
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

echo "<h1>Testing Universal Case Analyzer</h1>";

// Test both cases
$testCases = ['ai-test-case-001', 'ai-test-case-002'];

foreach ($testCases as $caseId) {
    echo "<h2>Testing Case: $caseId</h2>";
    
    try {
        require_once('custom/include/ai/UniversalCaseAnalyzer.php');
        $analyzer = new UniversalCaseAnalyzer();
        
        echo "<h3>Running Analysis...</h3>";
        $result = $analyzer->analyzeCase($caseId);
        
        if ($result && $result['success']) {
            echo "✅ <strong>Analysis Successful!</strong><br>";
            echo "Winnability: " . $result['percentage'] . "%<br>";
            echo "Confidence: " . $result['confidence'] . "%<br>";
            echo "Recommendation: " . $result['recommendation'] . "<br>";
            echo "<h4>Detailed Analysis:</h4>";
            echo "<pre style='background: #f5f5f5; padding: 10px; font-size: 11px;'>" . htmlspecialchars($result['detailed_analysis']) . "</pre>";
        } else {
            echo "❌ <strong>Analysis Failed:</strong> " . ($result['error'] ?? 'Unknown error') . "<br>";
        }
        
        // Show logs for debugging
        $logs = $analyzer->getAnalysisLog();
        if (!empty($logs)) {
            echo "<h4>Analysis Log:</h4>";
            echo "<ul style='font-size: 11px;'>";
            foreach ($logs as $log) {
                echo "<li>" . htmlspecialchars($log) . "</li>";
            }
            echo "</ul>";
        }
        
        $errors = $analyzer->getErrorLog();
        if (!empty($errors)) {
            echo "<h4>Error Log:</h4>";
            echo "<ul style='font-size: 11px; color: red;'>";
            foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
            }
            echo "</ul>";
        }
        
    } catch (Exception $e) {
        echo "❌ <strong>Exception:</strong> " . $e->getMessage() . "<br>";
    }
    
    echo "<hr>";
}

echo "<h2>Test Complete</h2>";
?>