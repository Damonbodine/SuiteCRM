<?php
/**
 * Trigger AI Analysis - Simple script to run AI analysis on test case
 */

if (php_sapi_name() !== 'cli') {
    die("This script must be run from command line only.\n");
}

define('sugarEntry', true);
require_once 'sugar_version.php';
require_once 'include/entrypoint.php';

echo "=== Triggering AI Analysis ===\n";

// Set up mock user
$GLOBALS['current_user'] = (object) array('id' => '1', 'is_admin' => '1');
global $current_user;
$current_user = $GLOBALS['current_user'];

// Load AI analyzer
require_once 'custom/include/ai/CaseStatusAnalyzer.php';
$analyzer = new CaseStatusAnalyzer();

// Run analysis
echo "Running AI analysis for test case...\n";
$result = $analyzer->analyzeCase('ai-test-case-001');

if ($result) {
    echo "✓ Analysis successful:\n";
    echo "  - Suggested Status: " . $result['suggested_status'] . "\n";
    echo "  - Confidence: " . $result['confidence_score'] . "\n";
    echo "  - Factors: " . count($result['analysis_factors']) . " factors\n";
    
    // Update the case manually
    $case = BeanFactory::getBean('Cases', 'ai-test-case-001');
    if ($case && $case->id) {
        $case->ai_suggested_status = $result['suggested_status'];
        $case->ai_confidence_score = $result['confidence_score'];
        $case->ai_last_analysis = date('Y-m-d H:i:s');
        $case->ai_analysis_factors = json_encode($result['analysis_factors']);
        $case->ai_status_needs_review = ($result['suggested_status'] !== $case->status) ? 1 : 0;
        
        if ($case->save(false)) {
            echo "✓ Case updated successfully!\n";
            echo "  - Status needs review: " . ($case->ai_status_needs_review ? 'Yes' : 'No') . "\n";
        } else {
            echo "✗ Failed to update case\n";
        }
    } else {
        echo "✗ Could not load case\n";
    }
} else {
    echo "✗ Analysis failed or returned no result\n";
    
    // Debug what went wrong
    echo "\nDebugging analysis failure:\n";
    
    $case = BeanFactory::getBean('Cases', 'ai-test-case-001');
    if ($case) {
        echo "  - Case loaded: " . $case->name . "\n";
        echo "  - Current status: " . $case->status . "\n";
        echo "  - Assigned to: " . $case->assigned_user_id . "\n";
    } else {
        echo "  - Could not load case\n";
    }
    
    echo "  - Current user ID: " . ($current_user->id ?? 'NOT SET') . "\n";
    echo "  - Is admin: " . ($current_user->is_admin ?? 'NOT SET') . "\n";
}

echo "\n=== Analysis Complete ===\n";
?>