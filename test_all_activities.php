<?php
/**
 * Test all activity types in the AI system
 */

if (!defined('sugarEntry')) define('sugarEntry', true);

echo "<h2>Testing All Activity Types</h2>\n";

try {
    require_once('config.php');
    require_once('custom/include/ai/UniversalCaseAnalyzer.php');
    require_once('custom/include/ai/BillableHoursAI.php');
    
    $ai = new BillableHoursAI();
    
    // Test all activity types from the dashlet
    $activityTypes = array(
        'court_appearance',
        'client_meeting', 
        'case_research',
        'document_review',
        'legal_writing',
        'phone_call',
        'investigation',
        'trial_prep'
    );
    
    foreach ($activityTypes as $activityType) {
        echo "<h3>Testing: $activityType</h3>\n";
        
        // Test descriptions
        $descriptions = $ai->getSmartDescriptions($activityType);
        echo "Descriptions: " . count($descriptions) . "<br>\n";
        
        if (count($descriptions) > 0) {
            echo "✅ Working - Sample: " . htmlspecialchars($descriptions[0]) . "<br>\n";
        } else {
            echo "❌ No descriptions found<br>\n";
        }
        
        // Test time prediction
        $prediction = $ai->predictTimeDuration($activityType);
        echo "Time: " . $prediction['predicted_hours'] . "h (" . round($prediction['confidence'] * 100) . "% confident)<br>\n";
        
        echo "<br>\n";
    }
    
    echo "<h3>Testing Activity Recognition</h3>\n";
    $testDescriptions = array(
        "called client about court date" => "phone_call",
        "drafted motion for summary judgment" => "legal_writing", 
        "reviewed contract documents" => "document_review",
        "met with client to discuss strategy" => "client_meeting",
        "appeared in court for hearing" => "court_appearance"
    );
    
    foreach ($testDescriptions as $desc => $expectedType) {
        $suggestions = $ai->suggestActivityType($desc);
        $topSuggestion = count($suggestions) > 0 ? $suggestions[0]['activity_type'] : 'none';
        
        echo "\"$desc\" → $topSuggestion ";
        if ($topSuggestion === $expectedType) {
            echo "✅<br>\n";
        } else {
            echo "❌ (expected $expectedType)<br>\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>\n";
}
?>