<?php
/**
 * Simple test to see if action resolution works
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

// Set working directory 
chdir(dirname(__FILE__));

// Load SuiteCRM
require_once('include/entryPoint.php');

echo "<h1>Action Resolution Test</h1>";

// Check if action file exists
$actionFile = 'custom/modules/Cases/aiWinnabilityAnalysis.php';
echo "Action file exists: " . (file_exists($actionFile) ? "✅ YES" : "❌ NO") . "<br>";

// Try to load it manually 
if (file_exists($actionFile)) {
    echo "Manually loading action file...<br>";
    
    // Set up request simulation
    $_REQUEST['module'] = 'Cases';
    $_REQUEST['action'] = 'aiWinnabilityAnalysis';
    $_REQUEST['record'] = 'ai-test-case-002';
    
    echo "Request set up - module: " . $_REQUEST['module'] . ", action: " . $_REQUEST['action'] . "<br>";
    
    // Try to include and run
    try {
        include_once($actionFile);
        echo "✅ Action file loaded successfully<br>";
    } catch (Exception $e) {
        echo "❌ Error loading action: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Action file not found<br>";
}

echo "<br>Test complete.";
?>