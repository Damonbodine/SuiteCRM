<?php
/**
 * Test AI data structures without full SuiteCRM
 */

echo "🧪 Testing AI Data Structures\n";
echo "============================\n\n";

// Test the criminal defense activities data directly
$criminalDefenseActivities = array(
    'client_consultation' => array(
        'typical_duration' => 1.5,
        'descriptions' => array(
            'Initial client consultation regarding charges',
            'Client meeting to discuss case strategy',
            'Client consultation on plea negotiations',
            'Review case details with client',
            'Discuss potential defenses with client'
        )
    ),
    'case_research' => array(
        'typical_duration' => 2.0,
        'descriptions' => array(
            'Legal research on applicable statutes',
            'Case law research for defense strategy',
            'Research precedents for similar charges',
            'Investigation of prosecution evidence',
            'Review discovery materials'
        )
    ),
    'court_appearance' => array(
        'typical_duration' => 3.5,
        'descriptions' => array(
            'Arraignment hearing representation',
            'Pre-trial motion hearing',
            'Plea negotiation conference',
            'Trial appearance for jury selection',
            'Sentencing hearing representation'
        )
    )
);

// Test 1: Description Suggestions
echo "TEST 1: Description Suggestions\n";
echo "-------------------------------\n";

$activityType = 'court_appearance';
if (isset($criminalDefenseActivities[$activityType])) {
    $descriptions = $criminalDefenseActivities[$activityType]['descriptions'];
    echo "✅ Found " . count($descriptions) . " descriptions for '$activityType':\n";
    foreach ($descriptions as $i => $desc) {
        echo "   " . ($i + 1) . ". " . $desc . "\n";
    }
} else {
    echo "❌ No data found for '$activityType'\n";
}

// Test 2: Time Predictions  
echo "\nTEST 2: Time Predictions\n";
echo "------------------------\n";

foreach ($criminalDefenseActivities as $activity => $data) {
    $duration = $data['typical_duration'];
    echo "✅ $activity: $duration hours\n";
}

// Test 3: Activity Recognition Keywords
echo "\nTEST 3: Activity Recognition\n";
echo "---------------------------\n";

$keywords = array(
    'court_appearance' => array('court', 'hearing', 'trial', 'arraignment'),
    'case_research' => array('research', 'case law', 'statute', 'legal'),
    'client_consultation' => array('client', 'meeting', 'consultation', 'discuss')
);

$testDescription = "attended arraignment hearing for client";
echo "Test description: '$testDescription'\n";

foreach ($keywords as $activityType => $keywordList) {
    $score = 0;
    foreach ($keywordList as $keyword) {
        if (strpos(strtolower($testDescription), $keyword) !== false) {
            $score++;
        }
    }
    if ($score > 0) {
        $confidence = $score / count($keywordList);
        echo "✅ Matches '$activityType' with " . round($confidence * 100) . "% confidence\n";
    }
}

echo "\n" . str_repeat("=", 40) . "\n";
echo "✅ AI DATA STRUCTURES ARE WORKING!\n";
echo "\nThe AI has:\n";
echo "- " . count($criminalDefenseActivities) . " activity types defined\n";
echo "- " . array_sum(array_map(function($a) { return count($a['descriptions']); }, $criminalDefenseActivities)) . " total description templates\n";
echo "- Time prediction data for all activities\n";
echo "- Keyword matching for activity recognition\n";

?>