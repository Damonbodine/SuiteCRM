<?php
/**
 * Debug which detailviewdefs.php file SuiteCRM is actually loading
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

echo "<h1>Debugging DetailView Metadata Loading</h1>";

// Test which detailviewdefs.php file is being used
echo "<h2>1. File System Check</h2>";

$baseFile = 'modules/Cases/metadata/detailviewdefs.php';
$customFile = 'custom/modules/Cases/metadata/detailviewdefs.php';

echo "Base file exists: " . (file_exists($baseFile) ? "✅ YES" : "❌ NO") . "<br>";
echo "Custom file exists: " . (file_exists($customFile) ? "✅ YES" : "❌ NO") . "<br>";

if (file_exists($baseFile)) {
    echo "Base file size: " . filesize($baseFile) . " bytes<br>";
    echo "Base file modified: " . date('Y-m-d H:i:s', filemtime($baseFile)) . "<br>";
}

if (file_exists($customFile)) {
    echo "Custom file size: " . filesize($customFile) . " bytes<br>";
    echo "Custom file modified: " . date('Y-m-d H:i:s', filemtime($customFile)) . "<br>";
}

echo "<h2>2. SuiteCRM Metadata Loading Test</h2>";

// Load metadata as SuiteCRM would
$viewdefs = array();

// Check what get_custom_file_if_exists returns
$metadataFile = get_custom_file_if_exists('modules/Cases/metadata/detailviewdefs.php');
echo "SuiteCRM will load: <strong>$metadataFile</strong><br>";

// Load the actual file
include($metadataFile);

echo "<h2>3. Loaded Metadata Analysis</h2>";

if (isset($viewdefs['Cases']['DetailView']['templateMeta']['form']['buttons'])) {
    $buttons = $viewdefs['Cases']['DetailView']['templateMeta']['form']['buttons'];
    echo "Number of buttons found: " . count($buttons) . "<br>";
    
    echo "<h3>Button Definitions:</h3>";
    foreach ($buttons as $index => $button) {
        if (is_array($button)) {
            echo "Button $index: CUSTOM - ";
            if (isset($button['customCode'])) {
                $customCode = substr($button['customCode'], 0, 100);
                echo "CustomCode: " . htmlspecialchars($customCode) . "...<br>";
            } else {
                echo "No customCode found<br>";
            }
        } else {
            echo "Button $index: STANDARD - $button<br>";
        }
    }
} else {
    echo "❌ No buttons array found in metadata<br>";
}

echo "<h2>4. Cache Check</h2>";

$cacheFile = 'cache/modules/Cases/Casevardefs.php';
echo "Cases cache exists: " . (file_exists($cacheFile) ? "✅ YES" : "❌ NO") . "<br>";

if (file_exists($cacheFile)) {
    echo "Cache file modified: " . date('Y-m-d H:i:s', filemtime($cacheFile)) . "<br>";
}

// Check smarty cache
$smartyCacheDir = 'cache/smarty/templates_c/';
$smartyFiles = glob($smartyCacheDir . '*DetailView*');
echo "Smarty DetailView cache files: " . count($smartyFiles) . "<br>";

echo "<h2>Debug Complete</h2>";
?>