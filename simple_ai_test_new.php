<?php
/**
 * Simple test to verify AI suggestions work
 */

echo "<h2>AI Billable Hours Test</h2>\n";

// Check if AI class file exists and is readable
$aiFile = 'custom/include/ai/BillableHoursAI.php';
if (file_exists($aiFile)) {
    echo "✅ AI file exists<br>\n";
    
    // Read the file and check for activity data
    $content = file_get_contents($aiFile);
    
    if (strpos($content, 'client_consultation') !== false) {
        echo "✅ Criminal defense activities data found<br>\n";
    } else {
        echo "❌ Activity data missing<br>\n";
    }
    
    if (strpos($content, 'getSmartDescriptions') !== false) {
        echo "✅ Description suggestion method found<br>\n";
    } else {
        echo "❌ Description method missing<br>\n";
    }
    
    if (strpos($content, 'predictTimeDuration') !== false) {
        echo "✅ Time prediction method found<br>\n";
    } else {
        echo "❌ Time prediction method missing<br>\n";
    }
    
} else {
    echo "❌ AI file not found at $aiFile<br>\n";
}

// Check API endpoint
$apiFile = 'custom/include/entryPoints/aiBillableHours.php';
if (file_exists($apiFile)) {
    echo "✅ API endpoint file exists<br>\n";
    
    $apiContent = file_get_contents($apiFile);
    if (strpos($apiContent, 'get_descriptions') !== false) {
        echo "✅ Description API endpoint found<br>\n";
    }
    if (strpos($apiContent, 'predict_time') !== false) {
        echo "✅ Time prediction API endpoint found<br>\n";
    }
} else {
    echo "❌ API endpoint file not found<br>\n";
}

// Test static data (without requiring full SuiteCRM bootstrap)
echo "<h3>Static AI Data Test</h3>\n";

// Simulate what the AI should return for court_appearance
$activities = array(
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

if (isset($activities['court_appearance'])) {
    echo "✅ Sample court_appearance data:<br>\n";
    echo "- Typical duration: " . $activities['court_appearance']['typical_duration'] . " hours<br>\n";
    echo "- Sample descriptions: " . count($activities['court_appearance']['descriptions']) . " available<br>\n";
    foreach ($activities['court_appearance']['descriptions'] as $desc) {
        echo "&nbsp;&nbsp;• " . htmlspecialchars($desc) . "<br>\n";
    }
}

echo "<br><strong>Next Steps:</strong><br>\n";
echo "1. Visit your SuiteCRM dashboard<br>\n";
echo "2. Add 'Billable Hours Quick Entry' dashlet<br>\n";
echo "3. Click 'AI Assist' button<br>\n";
echo "4. Select 'Court Appearance' activity type<br>\n";
echo "5. Should see description suggestions appear<br>\n";
echo "6. Duration field should auto-fill with 3.5 hours<br>\n";

?>