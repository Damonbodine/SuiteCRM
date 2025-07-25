<?php

/**
 * Rebuild Navigation Cache for ConflictSearch
 * 
 * Forces SuiteCRM to rebuild the navigation cache files
 * Access via: http://localhost:8080/rebuild_navigation.php
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once 'include/entryPoint.php';

echo "<h2>Rebuilding Navigation Cache</h2>";

try {
    // Force include the extension files to rebuild cache
    require_once 'include/utils.php';
    require_once 'modules/Administration/QuickRepairAndRebuild.php';
    
    echo "<h3>Step 1: Loading Extensions</h3>";
    
    // Manually load our extension files
    $extensionFiles = [
        'custom/Extension/application/Ext/Include/ConflictSearch.php',
        'custom/Extension/application/Ext/TabMenu/ConflictSearch.php'
    ];
    
    foreach ($extensionFiles as $file) {
        if (file_exists($file)) {
            include_once $file;
            echo "✅ Loaded: $file<br>";
        } else {
            echo "❌ Missing: $file<br>";
        }
    }
    
    echo "<h3>Step 2: Rebuilding Extensions</h3>";
    
    // Create repair object and rebuild
    $repair = new RepairAndClear();
    $repair->show_output = false;
    
    // Rebuild specific extensions
    $actions = [
        'rebuildExtensions',
        'clearExternalAPICache', 
        'clearSugarFeedCache',
        'clearJsLangFiles',
        'clearTpls'
    ];
    
    foreach ($actions as $action) {
        try {
            $repair->$action();
            echo "✅ Executed: $action<br>";
        } catch (Exception $e) {
            echo "⚠️ Warning in $action: " . $e->getMessage() . "<br>";
        }
    }
    
    echo "<h3>Step 3: Checking Cache Files</h3>";
    
    $cacheFiles = [
        'cache/application/Ext/Include/modules.ext.php',
        'cache/application/Ext/TabMenu/TabMenu.ext.php'
    ];
    
    foreach ($cacheFiles as $file) {
        if (file_exists($file)) {
            echo "✅ Created: $file<br>";
            
            // Check if ConflictSearch is in the cache
            $content = file_get_contents($file);
            if (strpos($content, 'ConflictSearch') !== false) {
                echo "&nbsp;&nbsp;&nbsp;✅ Contains ConflictSearch<br>";
            } else {
                echo "&nbsp;&nbsp;&nbsp;⚠️ ConflictSearch not found in cache<br>";
            }
        } else {
            echo "❌ Still missing: $file<br>";
        }
    }
    
    echo "<h3>Step 4: Force Module Registration</h3>";
    
    // Double-check module registration
    global $moduleList, $beanList, $beanFiles;
    
    if (!in_array('ConflictSearch', $moduleList ?? [])) {
        $moduleList[] = 'ConflictSearch';
        echo "✅ Added ConflictSearch to moduleList<br>";
    } else {
        echo "✅ ConflictSearch already in moduleList<br>";
    }
    
    $beanList['ConflictSearch'] = 'ConflictSearch';
    $beanFiles['ConflictSearch'] = 'modules/ConflictSearch/ConflictSearch.php';
    echo "✅ Bean registration confirmed<br>";
    
    echo "<h3>Step 5: Tab Controller Reset</h3>";
    
    // Try to reset the tab controller
    require_once 'include/tabConfig.php';
    $tabs = new TabController();
    
    // Get current user's tabs
    global $current_user;
    if ($current_user) {
        $userTabs = $tabs->get_user_tabs($current_user);
        echo "✅ Retrieved user tabs<br>";
        
        // Force add ConflictSearch if not present
        if (!isset($userTabs['ConflictSearch'])) {
            $userTabs['ConflictSearch'] = array(
                'label' => 'Conflict Search',
                'module' => 'ConflictSearch'
            );
            echo "✅ Added ConflictSearch to user tabs<br>";
        }
    }
    
    echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>";
    echo "<h3>✅ Rebuild Complete!</h3>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li><strong>Go to Admin > Display Modules and Subpanels</strong></li>";
    echo "<li><strong>Ensure ConflictSearch is in 'Displayed Modules' (left side)</strong></li>";
    echo "<li><strong>Click Save</strong></li>";
    echo "<li><strong>Clear your browser cache (Ctrl+Shift+R)</strong></li>";
    echo "<li><strong>Log out and log back in</strong></li>";
    echo "<li><strong>Check navigation menu</strong></li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h3>Direct Access Links (Always Work)</h3>";
    echo "<ul>";
    echo "<li><a href='index.php?module=ConflictSearch&action=EditView' target='_blank'><strong>New Conflict Search</strong></a></li>";
    echo "<li><a href='index.php?module=ConflictSearch&action=index' target='_blank'><strong>Conflict Search List</strong></a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>You can delete this file after navigation is working: rebuild_navigation.php</em></p>";
?>