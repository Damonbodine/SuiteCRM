<?php
/**
 * Simple test action to verify case update creation
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

global $current_user;

// Debug logging
file_put_contents('/tmp/test_action_debug.log', date('Y-m-d H:i:s') . " - Test action called for case: " . ($_REQUEST['record'] ?? 'unknown') . "\n", FILE_APPEND);

// Load case
$case = BeanFactory::newBean('Cases');
if (!$case || !$case->retrieve($_REQUEST['record'] ?? '')) {
    file_put_contents('/tmp/test_action_debug.log', date('H:i:s') . " - ERROR: Case not found\n", FILE_APPEND);
    SugarApplication::appendErrorMessage('Test failed: Case not found');
    SugarApplication::redirect("index.php?module=Cases&action=DetailView&record=" . ($_REQUEST['record'] ?? ''));
    return;
}

file_put_contents('/tmp/test_action_debug.log', date('H:i:s') . " - Case loaded: " . $case->name . "\n", FILE_APPEND);

try {
    // Create a simple test case update
    $caseUpdate = BeanFactory::newBean('AOP_Case_Updates');
    $caseUpdate->name = "🧪 Test Action - Working!";
    $caseUpdate->case_id = $case->id;
    $caseUpdate->contact_id = $case->contact_id;
    $caseUpdate->internal = false;
    $caseUpdate->description = "This is a test case update created at " . date('Y-m-d H:i:s') . " to verify the action system is working.";
    $caseUpdate->assigned_user_id = $current_user->id;
    
    if ($caseUpdate->save()) {
        file_put_contents('/tmp/test_action_debug.log', date('H:i:s') . " - Case update created successfully\n", FILE_APPEND);
        SugarApplication::appendErrorMessage('✅ Test successful! Case update created.');
    } else {
        file_put_contents('/tmp/test_action_debug.log', date('H:i:s') . " - ERROR: Failed to save case update\n", FILE_APPEND);
        SugarApplication::appendErrorMessage('❌ Test failed: Could not create case update');
    }
    
} catch (Exception $e) {
    file_put_contents('/tmp/test_action_debug.log', date('H:i:s') . " - Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    SugarApplication::appendErrorMessage('❌ Test failed: ' . $e->getMessage());
}

// Redirect back
SugarApplication::redirect("index.php?module=Cases&action=DetailView&record=" . $case->id);
?>