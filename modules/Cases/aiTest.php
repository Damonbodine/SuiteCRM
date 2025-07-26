<?php
/**
 * Simple test action to debug routing
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

// Immediate debug output
file_put_contents('/tmp/ai_test_debug.log', date('Y-m-d H:i:s') . " - aiTest action called\n", FILE_APPEND);

global $current_user;

// Load case
$case = BeanFactory::newBean('Cases');
if ($case && $case->retrieve($_REQUEST['record'] ?? '')) {
    // Create simple case update
    $caseUpdate = BeanFactory::newBean('AOP_Case_Updates');
    $caseUpdate->name = "🧪 AI Test - Working!";
    $caseUpdate->case_id = $case->id;
    $caseUpdate->contact_id = $case->contact_id;
    $caseUpdate->internal = false;
    $caseUpdate->description = "AI Test action called successfully at " . date('Y-m-d H:i:s');
    $caseUpdate->assigned_user_id = $current_user->id;
    $caseUpdate->save();
    
    file_put_contents('/tmp/ai_test_debug.log', date('H:i:s') . " - Case update created\n", FILE_APPEND);
}

// Redirect back
SugarApplication::redirect("index.php?module=Cases&action=DetailView&record=" . $case->id);
?>