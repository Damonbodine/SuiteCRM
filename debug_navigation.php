<?php

/**
 * Debug Navigation Menu Issue
 * 
 * Comprehensive debugging for ConflictSearch module navigation visibility
 * Access via: http://localhost:8080/debug_navigation.php
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once 'include/entryPoint.php';

echo "<h2>ConflictSearch Navigation Debug</h2>";

try {
    global $moduleList, $beanList, $beanFiles, $modInvisList, $current_user;
    
    // Force rebuild of extensions
    require_once 'include/utils.php';
    
    echo "<h3>1. Module Registration Check</h3>";
    
    // Check moduleList
    if (in_array('ConflictSearch', $moduleList ?? [])) {
        echo "✅ ConflictSearch in \$moduleList<br>";
    } else {
        echo "❌ ConflictSearch NOT in \$moduleList<br>";
        echo "Current moduleList: " . implode(', ', array_slice($moduleList ?? [], 0, 10)) . "...<br>";
    }
    
    // Check beanList
    if (isset($beanList['ConflictSearch'])) {
        echo "✅ ConflictSearch in \$beanList<br>";
    } else {
        echo "❌ ConflictSearch NOT in \$beanList<br>";
    }
    
    // Check modInvisList
    if (in_array('ConflictSearch', $modInvisList ?? [])) {
        echo "✅ ConflictSearch in \$modInvisList<br>";
    } else {
        echo "❌ ConflictSearch NOT in \$modInvisList<br>";
    }
    
    echo "<h3>2. Extension Files Check</h3>";
    
    $extensionFiles = [
        'custom/Extension/application/Ext/Include/ConflictSearch.php',
        'custom/Extension/application/Ext/TabMenu/ConflictSearch.php',
        'custom/Extension/modules/Administration/Ext/Administration/ConflictSearch.php'
    ];
    
    foreach ($extensionFiles as $file) {
        if (file_exists($file)) {
            echo "✅ $file<br>";
        } else {
            echo "❌ $file<br>";
        }
    }
    
    echo "<h3>3. Cache Files Check</h3>";
    
    $cacheFiles = [
        'cache/application/Ext/Include/modules.ext.php',
        'cache/application/Ext/TabMenu/TabMenu.ext.php',
        'cache/modules/Administration/Administration.ext.php'
    ];
    
    foreach ($cacheFiles as $file) {
        if (file_exists($file)) {
            echo "✅ $file exists<br>";
            
            // Check if ConflictSearch is in the cache file
            $content = file_get_contents($file);
            if (strpos($content, 'ConflictSearch') !== false) {
                echo "&nbsp;&nbsp;&nbsp;✅ Contains ConflictSearch references<br>";
            } else {
                echo "&nbsp;&nbsp;&nbsp;❌ No ConflictSearch references found<br>";
            }
        } else {
            echo "❌ $file missing<br>";
        }
    }
    
    echo "<h3>4. User Permissions Check</h3>";
    
    // Check ACL permissions
    require_once 'include/SugarACL/SugarACL.php';
    
    if (ACLController::checkAccess('ConflictSearch', 'access', true)) {
        echo "✅ User has access to ConflictSearch module<br>";
    } else {
        echo "❌ User does NOT have access to ConflictSearch module<br>";
    }
    
    echo "<h3>5. Admin Display Modules Check</h3>";
    
    // Check display tabs configuration
    $tabs = new TabController();
    $allTabs = $tabs->get_tabs($current_user);
    
    echo "Current visible tabs: ";
    if (isset($allTabs[0]) && is_array($allTabs[0])) {
        $visibleTabs = array_keys($allTabs[0]);
        echo implode(', ', $visibleTabs) . "<br>";
        
        if (in_array('ConflictSearch', $visibleTabs)) {
            echo "✅ ConflictSearch in visible tabs<br>";
        } else {
            echo "❌ ConflictSearch NOT in visible tabs<br>";
        }
    } else {
        echo "Error retrieving tab information<br>";
    }
    
    echo "<h3>6. Manual Cache Clear</h3>";
    
    // Try to clear specific cache files
    $cacheFilesToDelete = [
        'cache/application/Ext/Include/modules.ext.php',
        'cache/application/Ext/TabMenu/TabMenu.ext.php',
        'cache/modules/Administration/Administration.ext.php',
        'cache/smarty/templates_c/*'
    ];
    
    echo "<p>Attempting to clear cache files:</p>";
    foreach ($cacheFilesToDelete as $pattern) {
        if (strpos($pattern, '*') !== false) {
            $files = glob($pattern);
            foreach ($files as $file) {
                if (unlink($file)) {
                    echo "✅ Deleted: $file<br>";
                }
            }
        } else {
            if (file_exists($pattern) && unlink($pattern)) {
                echo "✅ Deleted: $pattern<br>";
            } else {
                echo "ℹ️ Not found or couldn't delete: $pattern<br>";
            }
        }
    }
    
    echo "<h3>7. Force Rebuild Extensions</h3>";
    
    // Try to rebuild extensions programmatically
    require_once 'modules/Administration/QuickRepairAndRebuild.php';
    $repair = new RepairAndClear();
    $repair->show_output = false;
    
    // Clear and rebuild extensions
    $repair->clearExternalAPICache();
    $repair->rebuildExtensions();
    
    echo "✅ Extensions rebuilt<br>";
    
    echo "<h3>8. Next Steps</h3>";
    echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #007cba;'>";
    echo "<p><strong>After running this debug script:</strong></p>";
    echo "<ol>";
    echo "<li>Go to <strong>Admin > Repair > Quick Repair and Rebuild</strong></li>";
    echo "<li>Click <strong>'Quick Repair and Rebuild'</strong> again</li>";
    echo "<li>Clear your browser cache (Ctrl+F5 or Cmd+Shift+R)</li>";
    echo "<li>Log out and log back in</li>";
    echo "<li>Check the navigation menu again</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h3>9. Alternative Access Methods</h3>";
    echo "<p>If navigation still doesn't work, use these direct links:</p>";
    echo "<ul>";
    echo "<li><strong>New Search:</strong> <a href='index.php?module=ConflictSearch&action=EditView' target='_blank'>index.php?module=ConflictSearch&action=EditView</a></li>";
    echo "<li><strong>Search List:</strong> <a href='index.php?module=ConflictSearch&action=index' target='_blank'>index.php?module=ConflictSearch&action=index</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>Delete this file after debugging: debug_navigation.php</em></p>";
?>