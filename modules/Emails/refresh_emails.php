<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * AI Email Refresh Action
 * 
 * This action is called when the user clicks "Refresh Emails" in the AI email interface.
 * It processes new emails from Gmail and analyzes them with AI.
 */

// Set a longer time limit for email processing
set_time_limit(120);

try {
    error_log("AI Email Refresh: Starting email refresh process");
    
    // Include the AI Email Analyzer
    require_once('custom/include/AIEmailAnalyzer.php');
    
    // Initialize and run the analyzer
    $analyzer = new AIEmailAnalyzer();
    $result = $analyzer->processUnanalyzedEmails();
    
    if ($result) {
        error_log("AI Email Refresh: Successfully processed emails");
        $message = "Emails refreshed and analyzed successfully.";
        $messageType = "success";
    } else {
        error_log("AI Email Refresh: Failed to process emails");
        $message = "Failed to refresh emails. Check your Gmail connection.";
        $messageType = "error";
    }
    
} catch (Exception $e) {
    error_log("AI Email Refresh: Exception occurred - " . $e->getMessage());
    $message = "Error refreshing emails: " . $e->getMessage();
    $messageType = "error";
}

// Set a session message to display to the user
if (isset($_SESSION)) {
    $_SESSION['ai_email_message'] = $message;
    $_SESSION['ai_email_message_type'] = $messageType;
}

// Redirect back to the email list with AI view
$redirect_url = 'index.php?module=Emails&action=ai_email_list';

// If this was called via AJAX, return JSON response
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $result,
        'message' => $message,
        'messageType' => $messageType
    ]);
    exit;
}

// Otherwise redirect
header("Location: $redirect_url");
exit;
?>