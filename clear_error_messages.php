<?php
/**
 * Clear SuiteCRM Error Messages
 * Run this script to clear persistent error messages
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('config.php');
require_once('include/utils.php');
require_once('include/SugarApplication.php');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Clear various error message storage mechanisms
if (isset($_SESSION['errors'])) {
    unset($_SESSION['errors']);
    echo "Cleared session errors\n";
}

if (isset($_SESSION['confirmation'])) {
    unset($_SESSION['confirmation']);
    echo "Cleared session confirmations\n";
}

if (isset($_SESSION['message'])) {
    unset($_SESSION['message']);
    echo "Cleared session messages\n";
}

// Clear SugarApplication error messages
if (class_exists('SugarApplication')) {
    // This will clear any stored application messages
    $messages = SugarApplication::getErrorMessages();
    if (!empty($messages)) {
        echo "Found " . count($messages) . " application messages, clearing...\n";
        foreach ($messages as $message) {
            echo "Clearing: " . substr($message, 0, 50) . "...\n";
        }
    }
    
    // Clear them
    $_SESSION['user_error_message'] = array();
    unset($_SESSION['user_error_message']);
}

// Clear any cookies that might store messages
if (isset($_COOKIE['sugar_user_theme'])) {
    // Don't actually clear this one, it's needed
}

echo "Error message clearing completed.\n";
echo "Please refresh your browser and clear browser cache (Ctrl+Shift+R)\n";
?>