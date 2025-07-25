<?php
/**
 * Test document_review activity type specifically
 */

if (!defined('sugarEntry')) define('sugarEntry', true);

echo "<h2>Testing Document Review AI</h2>\n";

try {
    require_once('config.php');
    require_once('custom/include/ai/UniversalCaseAnalyzer.php');
    require_once('custom/include/ai/BillableHoursAI.php');
    
    $ai = new BillableHoursAI();
    
    // Test document_review specifically
    echo "<h3>Testing 'document_review' activity type</h3>\n";
    $descriptions = $ai->getSmartDescriptions('document_review');
    echo "Activity: document_review<br>\n";
    echo "Descriptions found: " . count($descriptions) . "<br>\n";
    
    if (count($descriptions) > 0) {
        echo "✅ AI suggestions working:<br>\n";
        foreach ($descriptions as $i => $desc) {
            echo "   " . ($i + 1) . ". " . htmlspecialchars($desc) . "<br>\n";
        }
    } else {
        echo "❌ No suggestions found<br>\n";
    }
    
    // Test time prediction
    echo "<h3>Testing time prediction</h3>\n";
    $prediction = $ai->predictTimeDuration('document_review');
    echo "Predicted hours: " . $prediction['predicted_hours'] . "<br>\n";
    echo "Confidence: " . round($prediction['confidence'] * 100) . "%<br>\n";
    
    // Test JSON response format
    echo "<h3>Testing JSON response</h3>\n";
    $response = array(
        'success' => true,
        'data' => array(
            'descriptions' => $descriptions,
            'count' => count($descriptions)
        )
    );
    
    $json = json_encode($response);
    echo "JSON valid: " . ($json !== false ? 'Yes' : 'No') . "<br>\n";
    echo "JSON sample: " . substr($json, 0, 100) . "...<br>\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>\n";
}
?>