<?php

/**
 * Test ConflictSearch Module Access
 * 
 * Test script to verify ConflictSearch module is working
 * Access via: http://localhost:8080/test_conflict_search.php
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once 'include/entryPoint.php';

echo "<h2>ConflictSearch Module Test</h2>";

try {
    // Test 1: Check if module files exist
    echo "<h3>1. File Structure Check</h3>";
    $moduleFiles = [
        'modules/ConflictSearch/ConflictSearch.php',
        'modules/ConflictSearch/vardefs.php', 
        'modules/ConflictSearch/Menu.php',
        'modules/ConflictSearch/language/en_us.lang.php',
        'custom/lib/ConflictSearch/ConflictSearchEngine.php'
    ];
    
    foreach ($moduleFiles as $file) {
        if (file_exists($file)) {
            echo "✅ $file<br>";
        } else {
            echo "❌ $file<br>";
        }
    }
    
    // Test 2: Check database table
    echo "<h3>2. Database Check</h3>";
    global $db;
    $result = $db->query("SHOW TABLES LIKE 'conflict_search'");
    if ($db->getRowCount($result) > 0) {
        echo "✅ conflict_search table exists<br>";
        
        // Check table structure
        $structure = $db->query("DESCRIBE conflict_search");
        $fieldCount = $db->getRowCount($structure);
        echo "✅ Table has $fieldCount fields<br>";
    } else {
        echo "❌ conflict_search table not found<br>";
    }
    
    // Test 3: Test module instantiation
    echo "<h3>3. Module Instantiation Check</h3>";
    try {
        $conflictSearch = BeanFactory::newBean('ConflictSearch');
        if ($conflictSearch) {
            echo "✅ ConflictSearch bean can be created<br>";
            echo "✅ Module name: " . $conflictSearch->module_name . "<br>";
        } else {
            echo "❌ Failed to create ConflictSearch bean<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error creating bean: " . $e->getMessage() . "<br>";
    }
    
    // Test 4: Direct module access links
    echo "<h3>4. Direct Access Links</h3>";
    echo "<p>Try these direct links:</p>";
    echo "<ul>";
    echo "<li><a href='index.php?module=ConflictSearch&action=index' target='_blank'>ConflictSearch List View</a></li>";
    echo "<li><a href='index.php?module=ConflictSearch&action=EditView' target='_blank'>New Conflict Search</a></li>";
    echo "<li><a href='index.php?module=ConflictSearch&action=ListView' target='_blank'>ConflictSearch ListView</a></li>";
    echo "</ul>";
    
    // Test 5: Check module registration
    echo "<h3>5. Module Registration Check</h3>";
    global $moduleList, $beanList, $beanFiles;
    
    if (in_array('ConflictSearch', $moduleList ?? [])) {
        echo "✅ ConflictSearch is in moduleList<br>";
    } else {
        echo "❌ ConflictSearch not in moduleList<br>";
    }
    
    if (isset($beanList['ConflictSearch'])) {
        echo "✅ ConflictSearch is in beanList<br>";
    } else {
        echo "❌ ConflictSearch not in beanList<br>";
    }
    
    if (isset($beanFiles['ConflictSearch'])) {
        echo "✅ ConflictSearch is in beanFiles<br>";
    } else {
        echo "❌ ConflictSearch not in beanFiles<br>";
    }
    
    echo "<h3>6. Navigation Menu Troubleshooting</h3>";
    echo "<p><strong>If the module is working but not visible in the menu:</strong></p>";
    echo "<ol>";
    echo "<li>Go to <strong>Admin > Display Modules and Subpanels</strong></li>";
    echo "<li>Look for 'ConflictSearch' in the Hidden Modules list</li>";
    echo "<li>If found, move it to Displayed Modules</li>";
    echo "<li>Save and check the navigation menu</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>Delete this file after testing: test_conflict_search.php</em></p>";
?>