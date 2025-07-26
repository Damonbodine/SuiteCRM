<?php
/**
 * AI Winnability Analysis Action - Production Ready
 * Robust implementation designed to work across any attorney's case portfolio
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('custom/include/ai/UniversalCaseAnalyzer.php');

global $current_user;

// Debug: Write to a simple log file to confirm action is called
file_put_contents('/tmp/ai_analysis_debug.log', date('Y-m-d H:i:s') . " - AI Analysis Action called for case: " . ($_REQUEST['record'] ?? 'unknown') . "\n", FILE_APPEND);

// Enhanced logging for production debugging
if (isset($GLOBALS['log']) && is_object($GLOBALS['log'])) {
    $GLOBALS['log']->info("AI Analysis Action triggered for case: " . ($_REQUEST['record'] ?? 'unknown'));
}

// Retrieve case with comprehensive error handling
file_put_contents('/tmp/ai_analysis_debug.log', date('H:i:s') . " - Attempting to load case\n", FILE_APPEND);

$case = BeanFactory::newBean('Cases');
if (!$case || !$case->retrieve($_REQUEST['record'] ?? '')) {
    file_put_contents('/tmp/ai_analysis_debug.log', date('H:i:s') . " - ERROR: Case not found\n", FILE_APPEND);
    if (isset($GLOBALS['log']) && is_object($GLOBALS['log'])) {
        $GLOBALS['log']->error("AI Analysis: Case not found - " . ($_REQUEST['record'] ?? 'no record'));
    }
    SugarApplication::appendErrorMessage('Case not found. Please refresh the page and try again.');
    SugarApplication::redirect("index.php?module=Cases&action=index");
    return;
}

file_put_contents('/tmp/ai_analysis_debug.log', date('H:i:s') . " - Case loaded: " . $case->name . "\n", FILE_APPEND);

$GLOBALS['log']->info("AI Analysis: Case loaded - " . $case->name);

// Enhanced permission checking
if (!$case->ACLAccess('edit')) {
    $GLOBALS['log']->security("AI Analysis: Access denied for case " . $case->id . " by user " . ($current_user->id ?? 'unknown'));
    ACLController::displayNoAccess();
    return;
}

// Authentication verification with detailed feedback
if (!$current_user || !$current_user->id) {
    $GLOBALS['log']->error("AI Analysis: No authenticated user");
    SugarApplication::appendErrorMessage('Authentication error: Please refresh the page and log in again.');
    SugarApplication::redirect("index.php?module=Cases&action=DetailView&record=" . $case->id);
    return;
}

$GLOBALS['log']->info("AI Analysis: Permissions validated for user " . $current_user->user_name);

try {
    // Use the new Universal Case Analyzer
    $analyzer = new UniversalCaseAnalyzer();
    $result = $analyzer->analyzeCase($case->id);
    
    if (!$result || !$result['success']) {
        $errorMsg = $result['error'] ?? 'Unknown analysis error';
        $GLOBALS['log']->error("AI Analysis failed: " . $errorMsg);
        SugarApplication::appendErrorMessage('AI analysis failed: ' . $errorMsg . '. Please try again.');
        SugarApplication::redirect("index.php?module=Cases&action=DetailView&record=" . $case->id);
        return;
    }
    
    $GLOBALS['log']->info("AI Analysis completed successfully: " . $result['percentage'] . "% winnability");
    
    // Update case with results using new format
    $case->ai_suggested_status = "Winnability: " . $result['percentage'] . "%";
    $case->ai_confidence_score = $result['confidence'];
    $case->ai_analysis_factors = $result['detailed_analysis'];
    $case->ai_last_analysis = date('Y-m-d H:i:s');
    $case->ai_status_needs_review = 1;
    
    // Save case with error handling
    if (!$case->save()) {
        $GLOBALS['log']->error("AI Analysis: Failed to save case updates");
        SugarApplication::appendErrorMessage('Analysis completed but failed to save results. Please contact support.');
    } else {
        $GLOBALS['log']->info("AI Analysis: Case updated successfully");
    }
    
    // Create detailed case update with enhanced formatting
    try {
        $caseUpdate = BeanFactory::newBean('AOP_Case_Updates');
        $caseUpdate->name = "🤖 AI Winnability Analysis Complete";
        $caseUpdate->case_id = $case->id;
        $caseUpdate->contact_id = $case->contact_id;
        $caseUpdate->internal = false;
        
        // Enhanced description with analysis details
        $updateDesc = "🎯 **AI WINNABILITY ANALYSIS COMPLETE**\n\n";
        $updateDesc .= "**Assessment:** " . $result['percentage'] . "% Winnability\n";
        $updateDesc .= "**Confidence:** " . $result['confidence'] . "%\n";
        $updateDesc .= "**Recommendation:** " . $result['recommendation'] . "\n\n";
        $updateDesc .= "**Detailed Analysis:**\n" . $result['detailed_analysis'] . "\n\n";
        $updateDesc .= "---\n";
        $updateDesc .= "*Analysis performed on " . date('M j, Y \a\t g:i A') . "*\n";
        $updateDesc .= "*Version: " . ($result['version'] ?? 'Unknown') . "*";
        
        $caseUpdate->description = $updateDesc;
        $caseUpdate->assigned_user_id = $current_user->id;
        
        if (!$caseUpdate->save()) {
            $GLOBALS['log']->error("AI Analysis: Failed to create case update");
        } else {
            $GLOBALS['log']->info("AI Analysis: Case update created successfully");
        }
        
    } catch (Exception $e) {
        $GLOBALS['log']->error("AI Analysis: Case update creation failed - " . $e->getMessage());
        // Don't fail the entire process if case update fails
    }
    
    // Success message with detailed information
    $successMsg = sprintf(
        'AI winnability analysis completed! Assessment: %d%% (Confidence: %d%%). %s',
        $result['percentage'],
        $result['confidence'],
        'Check case updates for detailed breakdown.'
    );
    
    SugarApplication::appendErrorMessage($successMsg);
    $GLOBALS['log']->info("AI Analysis: Success message displayed to user");
    
} catch (Exception $e) {
    $GLOBALS['log']->error('AI Analysis Exception: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
    SugarApplication::appendErrorMessage('Analysis failed due to system error: ' . $e->getMessage() . '. Please try again or contact support.');
}

// Always redirect back to case detail with proper error handling
try {
    SugarApplication::redirect("index.php?module=Cases&action=DetailView&record=" . $case->id);
} catch (Exception $e) {
    $GLOBALS['log']->error("AI Analysis: Redirect failed - " . $e->getMessage());
    // Fallback redirect
    header("Location: index.php?module=Cases&action=DetailView&record=" . $case->id);
    exit;
}
?>