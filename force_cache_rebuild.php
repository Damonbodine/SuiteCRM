<?php

/**
 * Force Cache Rebuild for ConflictSearch
 * 
 * Manually creates the missing cache files
 * Access via: http://localhost:8080/force_cache_rebuild.php
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once 'include/entryPoint.php';

echo "<h2>Force Cache Rebuild</h2>";

try {
    echo "<h3>Step 1: Creating Missing Cache Files</h3>";
    
    // Ensure cache directories exist
    $cacheDir = 'cache/application/Ext/Include';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
        echo "✅ Created directory: $cacheDir<br>";
    }
    
    $tabMenuCacheDir = 'cache/application/Ext/TabMenu';
    if (!is_dir($tabMenuCacheDir)) {
        mkdir($tabMenuCacheDir, 0755, true);
        echo "✅ Created directory: $tabMenuCacheDir<br>";
    }
    
    echo "<h3>Step 2: Manual Cache File Creation</h3>";
    
    // Create modules.ext.php manually
    $modulesCache = "<?php\n// WARNING: The contents of this file are based on the module_install.php file\n// WARNING: for the appropriate module. Any modification of the contents of this file\n// WARNING: may lead to unpredictable behavior.\n\n";
    
    // Load our extension file content
    if (file_exists('custom/Extension/application/Ext/Include/ConflictSearch.php')) {
        $extensionContent = file_get_contents('custom/Extension/application/Ext/Include/ConflictSearch.php');
        // Remove PHP opening tag
        $extensionContent = str_replace('<?php', '', $extensionContent);
        $modulesCache .= $extensionContent . "\n";
    }
    
    // Write the cache file
    $modulesCacheFile = 'cache/application/Ext/Include/modules.ext.php';
    if (file_put_contents($modulesCacheFile, $modulesCache)) {
        echo "✅ Created: $modulesCacheFile<br>";
    } else {
        echo "❌ Failed to create: $modulesCacheFile<br>";
    }
    
    // Create TabMenu.ext.php manually
    $tabMenuCache = "<?php\n// WARNING: The contents of this file are based on the module_install.php file\n// WARNING: for the appropriate module. Any modification of the contents of this file\n// WARNING: may lead to unpredictable behavior.\n\n";
    
    // Load TabMenu extension content
    if (file_exists('custom/Extension/application/Ext/TabMenu/ConflictSearch.php')) {
        $tabExtensionContent = file_get_contents('custom/Extension/application/Ext/TabMenu/ConflictSearch.php');
        // Remove PHP opening tag
        $tabExtensionContent = str_replace('<?php', '', $tabExtensionContent);
        $tabMenuCache .= $tabExtensionContent . "\n";
    }
    
    // Write the TabMenu cache file
    $tabMenuCacheFile = 'cache/application/Ext/TabMenu/TabMenu.ext.php';
    if (file_put_contents($tabMenuCacheFile, $tabMenuCache)) {
        echo "✅ Created: $tabMenuCacheFile<br>";
    } else {
        echo "❌ Failed to create: $tabMenuCacheFile<br>";
    }
    
    echo "<h3>Step 3: Verify Cache Files</h3>";
    
    $cacheFiles = [
        'cache/application/Ext/Include/modules.ext.php',
        'cache/application/Ext/TabMenu/TabMenu.ext.php'
    ];
    
    foreach ($cacheFiles as $file) {
        if (file_exists($file)) {
            echo "✅ Exists: $file<br>";
            
            $content = file_get_contents($file);
            if (strpos($content, 'ConflictSearch') !== false) {
                echo "&nbsp;&nbsp;&nbsp;✅ Contains ConflictSearch references<br>";
            } else {
                echo "&nbsp;&nbsp;&nbsp;❌ No ConflictSearch references<br>";
            }
            
            // Show file size
            $size = filesize($file);
            echo "&nbsp;&nbsp;&nbsp;📄 File size: $size bytes<br>";
        } else {
            echo "❌ Still missing: $file<br>";
        }
    }
    
    echo "<h3>Step 4: Alternative - Manual Tab Configuration</h3>";
    
    // Try direct database approach for tab configuration
    global $db;
    
    // Check if there's a user_preferences table we can work with
    $result = $db->query("SHOW TABLES LIKE 'user_preferences'");
    if ($db->getRowCount($result) > 0) {
        echo "✅ Found user_preferences table<br>";
        
        // Check for existing tab preferences
        global $current_user;
        if ($current_user && $current_user->id) {
            $userId = $current_user->id;
            $checkQuery = "SELECT * FROM user_preferences WHERE assigned_user_id = '$userId' AND category = 'global' AND name = 'display_tabs'";
            $existing = $db->query($checkQuery);
            
            if ($db->getRowCount($existing) > 0) {
                echo "✅ Found existing tab preferences for user<br>";
            } else {
                echo "ℹ️ No tab preferences found for user<br>";
            }
        }
    }
    
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>";
    echo "<h3>⚠️ Cache Files Created - Next Steps</h3>";
    echo "<p><strong>Now you MUST do these steps in order:</strong></p>";
    echo "<ol>";
    echo "<li><strong>Go to Admin > Display Modules and Subpanels</strong></li>";
    echo "<li><strong>Make sure ConflictSearch is in 'Displayed Modules' (left side)</strong></li>";
    echo "<li><strong>Click Save</strong></li>";
    echo "<li><strong>Go to Admin > Repair > Quick Repair and Rebuild</strong></li>";
    echo "<li><strong>Click 'Quick Repair and Rebuild'</strong></li>";
    echo "<li><strong>Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)</strong></li>";
    echo "<li><strong>Log out completely and log back in</strong></li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h3>Test Direct Access (Should Always Work)</h3>";
    echo "<ul>";
    echo "<li><a href='index.php?module=ConflictSearch&action=EditView' target='_blank'><strong>Create New Conflict Search</strong></a></li>";
    echo "<li><a href='index.php?module=ConflictSearch&action=index' target='_blank'><strong>View All Conflict Searches</strong></a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}

echo "<hr>";
echo "<p><em>Delete this file after navigation is working: force_cache_rebuild.php</em></p>";
?>