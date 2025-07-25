<?php
/**
 * Debug the AI endpoint directly
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Bootstrap minimal SuiteCRM
if (!defined('sugarEntry')) define('sugarEntry', true);

echo "<h2>AI Endpoint Debug</h2>\n";

try {
    // Check if we can load config
    if (file_exists('config.php')) {
        require_once('config.php');
        echo "✅ Config loaded<br>\n";
    } else {
        echo "❌ Config not found<br>\n";
    }
    
    // Check if AI class file exists
    if (file_exists('custom/include/ai/BillableHoursAI.php')) {
        echo "✅ BillableHoursAI.php exists<br>\n";
    } else {
        echo "❌ BillableHoursAI.php missing<br>\n";
        exit;
    }
    
    // Check parent class
    if (file_exists('custom/include/ai/UniversalCaseAnalyzer.php')) {
        echo "✅ UniversalCaseAnalyzer.php exists<br>\n";
        require_once('custom/include/ai/UniversalCaseAnalyzer.php');
        echo "✅ Parent class loaded<br>\n";
    } else {
        echo "❌ UniversalCaseAnalyzer.php missing<br>\n";
        exit;
    }
    
    // Try to load AI class
    require_once('custom/include/ai/BillableHoursAI.php');
    echo "✅ BillableHoursAI class loaded<br>\n";
    
    // Try to instantiate
    $ai = new BillableHoursAI();
    echo "✅ BillableHoursAI instantiated<br>\n";
    
    // Test the method that's failing
    echo "<h3>Testing getSmartDescriptions</h3>\n";
    $descriptions = $ai->getSmartDescriptions('court_appearance');
    echo "Descriptions returned: " . count($descriptions) . "<br>\n";
    
    if (count($descriptions) > 0) {
        echo "✅ AI method working! Descriptions:<br>\n";
        foreach ($descriptions as $i => $desc) {
            echo ($i + 1) . ". " . htmlspecialchars($desc) . "<br>\n";
        }
    } else {
        echo "❌ No descriptions returned<br>\n";
    }
    
    // Test JSON encoding
    $testResponse = array(
        'success' => true,
        'data' => array(
            'descriptions' => $descriptions,
            'count' => count($descriptions)
        )
    );
    
    $json = json_encode($testResponse);
    if ($json === false) {
        echo "❌ JSON encoding failed: " . json_last_error_msg() . "<br>\n";
    } else {
        echo "✅ JSON encoding works<br>\n";
        echo "Sample JSON: " . substr($json, 0, 100) . "...<br>\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "<br>\n";
    echo "Stack trace:<br>\n<pre>" . $e->getTraceAsString() . "</pre>\n";
}

echo "<br><strong>Test complete</strong><br>\n";
?>