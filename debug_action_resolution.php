<?php
/**
 * Debug SuiteCRM Action Resolution
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

echo "<h1>Debug Action Resolution</h1>";

// Test action file detection
echo "<h2>1. Action File Existence</h2>";
$actionFile = 'custom/modules/Cases/aiWinnabilityAnalysis.php';
echo "Action file exists: " . (file_exists($actionFile) ? "✅ YES" : "❌ NO") . "<br>";

if (file_exists($actionFile)) {
    echo "File size: " . filesize($actionFile) . " bytes<br>";
    echo "File permissions: " . substr(sprintf('%o', fileperms($actionFile)), -4) . "<br>";
    echo "File modified: " . date('Y-m-d H:i:s', filemtime($actionFile)) . "<br>";
}

echo "<h2>2. Test get_custom_file_if_exists</h2>";
$resolvedFile = get_custom_file_if_exists('modules/Cases/aiWinnabilityAnalysis.php');
echo "SuiteCRM resolves action to: <strong>$resolvedFile</strong><br>";
echo "Resolved file exists: " . (file_exists($resolvedFile) ? "✅ YES" : "❌ NO") . "<br>";

echo "<h2>3. SuiteCRM Controller Test</h2>";
// Simulate what happens when form is submitted
$_REQUEST['module'] = 'Cases';
$_REQUEST['action'] = 'aiWinnabilityAnalysis';
$_REQUEST['record'] = 'ai-test-case-002';

echo "Simulating form submission:<br>";
echo "module = " . $_REQUEST['module'] . "<br>";
echo "action = " . $_REQUEST['action'] . "<br>";
echo "record = " . $_REQUEST['record'] . "<br>";

// Check if SuiteCRM can find the action
require_once('include/MVC/Controller/SugarController.php');
$controller = new SugarController();

echo "<h2>4. Action Method Test</h2>";
$actionMethod = 'action_' . strtolower($_REQUEST['action']);
echo "Looking for method: $actionMethod<br>";

if (method_exists($controller, $actionMethod)) {
    echo "✅ Controller method exists<br>";
} else {
    echo "❌ Controller method NOT found<br>";
}

// Test action file pattern
echo "<h2>5. Action File Pattern Test</h2>";
$actionFileName = SugarController::getActionFilename($_REQUEST['action']);
echo "Action filename: $actionFileName<br>";

$possiblePaths = array(
    "modules/Cases/{$actionFileName}.php",
    "custom/modules/Cases/{$actionFileName}.php",
    "modules/Cases/views/view.{$actionFileName}.php",
    "custom/modules/Cases/views/view.{$actionFileName}.php"
);

echo "<h3>Checking possible paths:</h3>";
foreach ($possiblePaths as $path) {
    echo "$path - " . (file_exists($path) ? "✅ EXISTS" : "❌ NOT FOUND") . "<br>";
}

echo "<h2>Debug Complete</h2>";
?>