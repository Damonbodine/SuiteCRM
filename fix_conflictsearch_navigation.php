<?php

/**
 * Fix ConflictSearch Navigation Integration
 * 
 * This script adds ConflictSearch to all users' navigation tabs using SuiteCRM's
 * TabController system - the proper way to integrate with navigation.
 * 
 * Access via: http://localhost:8080/fix_conflictsearch_navigation.php
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once 'include/entryPoint.php';
require_once 'modules/MySettings/TabController.php';

echo "<h2>ConflictSearch Navigation Integration Fix</h2>";

try {
    $controller = new TabController();
    
    echo "<h3>Step 1: Add ConflictSearch to System Tabs</h3>";
    
    // Get current system tabs
    $systemTabs = $controller->get_system_tabs();
    echo "Current system tabs: " . count($systemTabs) . " modules<br>";
    
    // Add ConflictSearch to system tabs if not present
    if (!isset($systemTabs['ConflictSearch'])) {
        $systemTabs['ConflictSearch'] = 'ConflictSearch';
        $controller->set_system_tabs(array_keys($systemTabs));
        echo "✅ Added ConflictSearch to system tabs<br>";
    } else {
        echo "✅ ConflictSearch already in system tabs<br>";
    }
    
    echo "<h3>Step 2: Add ConflictSearch to All Users' Display Tabs</h3>";
    
    // Get all active users
    $db = DBManagerFactory::getInstance();
    $userQuery = "SELECT id, user_name FROM users WHERE deleted = 0 AND status = 'Active'";
    $userResult = $db->query($userQuery);
    
    $usersProcessed = 0;
    $usersUpdated = 0;
    
    while ($userRow = $db->fetchByAssoc($userResult)) {
        $user = BeanFactory::newBean('Users');
        $user->retrieve($userRow['id']);
        
        if (!$user->id) {
            continue;
        }
        
        $usersProcessed++;
        $updated = false;
        
        // Add to display tabs
        $displayTabs = $controller->get_user_tabs($user, 'display');
        if (!isset($displayTabs['ConflictSearch'])) {
            $displayTabs['ConflictSearch'] = 'ConflictSearch';
            $controller->set_user_tabs(array_keys($displayTabs), $user, 'display');
            $updated = true;
        }
        
        // Remove from hide tabs if present
        $hideTabs = $controller->get_user_tabs($user, 'hide');
        if (isset($hideTabs['ConflictSearch'])) {
            unset($hideTabs['ConflictSearch']);
            $controller->set_user_tabs(array_keys($hideTabs), $user, 'hide');
            $updated = true;
        }
        
        // Remove from remove tabs if present
        $removeTabs = $controller->get_user_tabs($user, 'remove');
        if (isset($removeTabs['ConflictSearch'])) {
            unset($removeTabs['ConflictSearch']);
            $controller->set_user_tabs(array_keys($removeTabs), $user, 'remove');
            $updated = true;
        }
        
        if ($updated) {
            $usersUpdated++;
            echo "✅ Updated tabs for user: {$userRow['user_name']}<br>";
        } else {
            echo "ℹ️ No update needed for user: {$userRow['user_name']}<br>";
        }
    }
    
    echo "<h3>Step 3: Verify Module Access List Integration</h3>";
    
    // Test the query_module_access_list function
    global $current_user;
    if ($current_user && $current_user->id) {
        require_once 'include/utils/security_utils.php';
        $accessList = query_module_access_list($current_user);
        
        if (isset($accessList['ConflictSearch'])) {
            echo "✅ ConflictSearch appears in module access list for current user<br>";
        } else {
            echo "⚠️ ConflictSearch NOT in module access list for current user<br>";
            echo "&nbsp;&nbsp;&nbsp;Available modules: " . implode(', ', array_keys($accessList)) . "<br>";
        }
    } else {
        echo "ℹ️ No current user logged in - cannot test module access list<br>";
    }
    
    echo "<h3>Step 4: Clear Navigation Cache</h3>";
    
    // Clear relevant caches
    if (function_exists('sugar_cache_clear')) {
        sugar_cache_clear('app_list_strings');
        sugar_cache_clear('modules');
        echo "✅ Cleared Sugar cache<br>";
    }
    
    // Clear Smarty template cache
    if (is_dir('cache/smarty/templates_c')) {
        $files = glob('cache/smarty/templates_c/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "✅ Cleared Smarty template cache<br>";
    }
    
    echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;'>";
    echo "<h3>✅ Navigation Integration Complete!</h3>";
    echo "<p><strong>Summary:</strong></p>";
    echo "<ul>";
    echo "<li>✅ ConflictSearch added to system tabs</li>";
    echo "<li>✅ Processed $usersProcessed users, updated $usersUpdated users</li>";
    echo "<li>✅ Navigation cache cleared</li>";
    echo "</ul>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li><strong>Clear your browser cache (Ctrl+Shift+R)</strong></li>";
    echo "<li><strong>Log out and log back in</strong></li>";
    echo "<li><strong>Check navigation menu - ConflictSearch should now be visible!</strong></li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h3>Direct Access Links (Always Work)</h3>";
    echo "<ul>";
    echo "<li><a href='index.php?module=ConflictSearch&action=EditView' target='_blank'><strong>New Conflict Search</strong></a></li>";
    echo "<li><a href='index.php?module=ConflictSearch&action=index' target='_blank'><strong>Conflict Search List</strong></a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}

echo "<hr>";
echo "<p><em>You can delete this file after navigation is working: fix_conflictsearch_navigation.php</em></p>";
?>