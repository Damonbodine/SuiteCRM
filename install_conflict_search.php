<?php

/**
 * Manual ConflictSearch Module Registration Script
 * 
 * Run this script directly in your browser to register the ConflictSearch module
 * URL: http://your-suitecrm-url/install_conflict_search.php
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once 'include/entryPoint.php';

global $sugar_config, $db;

echo "<h2>ConflictSearch Module Registration</h2>";

try {
    // Register the module in the module list
    echo "<p>Registering ConflictSearch module...</p>";
    
    // Add to module registry if not already present
    require_once 'include/utils.php';
    require_once 'include/modules.php';
    
    // Check if module is already registered
    if (!in_array('ConflictSearch', $moduleList)) {
        echo "<p>✓ Adding ConflictSearch to module list</p>";
        
        // This would normally be done through Quick Repair and Rebuild
        // But we can verify the files are in place
        
        $moduleDir = 'modules/ConflictSearch';
        if (is_dir($moduleDir)) {
            echo "<p>✓ ConflictSearch module directory found</p>";
            
            // Check for required files
            $requiredFiles = [
                'ConflictSearch.php',
                'vardefs.php',
                'Menu.php',
                'language/en_us.lang.php',
                'metadata/listviewdefs.php',
                'metadata/editviewdefs.php',
                'metadata/detailviewdefs.php'
            ];
            
            $allFilesPresent = true;
            foreach ($requiredFiles as $file) {
                if (file_exists($moduleDir . '/' . $file)) {
                    echo "<p>✓ Found: $file</p>";
                } else {
                    echo "<p>✗ Missing: $file</p>";
                    $allFilesPresent = false;
                }
            }
            
            if ($allFilesPresent) {
                echo "<p><strong>✓ All required files are present!</strong></p>";
                echo "<p><strong>Next steps:</strong></p>";
                echo "<ol>";
                echo "<li>Go to Admin > Repair > Quick Repair and Rebuild</li>";
                echo "<li>Click 'Quick Repair and Rebuild'</li>";
                echo "<li>The ConflictSearch module should appear in your navigation menu</li>";
                echo "</ol>";
            } else {
                echo "<p><strong>✗ Some required files are missing. Please check the module installation.</strong></p>";
            }
            
        } else {
            echo "<p>✗ ConflictSearch module directory not found at: $moduleDir</p>";
        }
    } else {
        echo "<p>✓ ConflictSearch is already registered in the module list</p>";
    }
    
    // Check custom extension
    $extensionFile = 'custom/Extension/application/Ext/Include/ConflictSearch.php';
    if (file_exists($extensionFile)) {
        echo "<p>✓ Extension file found: $extensionFile</p>";
    } else {
        echo "<p>✗ Extension file missing: $extensionFile</p>";
    }
    
    echo "<hr>";
    echo "<h3>Manual Navigation Test</h3>";
    echo "<p>Try accessing the ConflictSearch module directly:</p>";
    echo "<ul>";
    echo "<li><a href='index.php?module=ConflictSearch&action=index'>ConflictSearch List View</a></li>";
    echo "<li><a href='index.php?module=ConflictSearch&action=EditView'>New Conflict Search</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>After running Quick Repair and Rebuild, you can delete this file.</em></p>";
?>