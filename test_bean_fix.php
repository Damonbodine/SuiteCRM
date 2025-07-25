<?php
/**
 * Test the BeanFactory fix
 */

if (!defined('sugarEntry')) define('sugarEntry', true);

echo "<h2>Testing Bean Factory Fix</h2>\n";

try {
    require_once('config.php');
    require_once('include/utils.php');
    require_once('modules/Cases/Case.php');
    require_once('custom/include/ai/UniversalCaseAnalyzer.php');
    require_once('custom/include/ai/BillableHoursAI.php');
    
    $ai = new BillableHoursAI();
    
    // Test with case_research and a case ID
    echo "<h3>Testing with case ID</h3>\n";
    $caseId = 'ai-test-case-002'; // From your screenshot
    $descriptions = $ai->getSmartDescriptions('case_research', $caseId);
    
    echo "Activity: case_research<br>\n";
    echo "Case ID: " . htmlspecialchars($caseId) . "<br>\n";
    echo "Descriptions found: " . count($descriptions) . "<br>\n";
    
    if (count($descriptions) > 0) {
        echo "✅ AI working with case context:<br>\n";
        foreach ($descriptions as $i => $desc) {
            echo "   " . ($i + 1) . ". " . htmlspecialchars($desc) . "<br>\n";
        }
    } else {
        echo "❌ No suggestions found<br>\n";
    }
    
    // Test time prediction
    echo "<h3>Testing time prediction with case</h3>\n";
    $prediction = $ai->predictTimeDuration('case_research', $caseId);
    echo "Predicted hours: " . $prediction['predicted_hours'] . "<br>\n";
    echo "Confidence: " . round($prediction['confidence'] * 100) . "%<br>\n";
    echo "Reasoning: " . htmlspecialchars($prediction['reasoning']) . "<br>\n";
    
    echo "<br>✅ BeanFactory fix successful!<br>\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>\n";
    echo "Stack trace:<br>\n<pre>" . $e->getTraceAsString() . "</pre>\n";
}
?>