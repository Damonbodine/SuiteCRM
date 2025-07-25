<?php

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once 'include/entryPoint.php';
require_once 'modules/MySettings/TabController.php';

echo "ConflictSearch Tab Fix - Simple Version\n\n";

try {
    $controller = new TabController();
    
    // Get current admin user
    global $current_user;
    if (!$current_user || !$current_user->is_admin) {
        echo "Error: Script must be run by admin user\n";
        exit;
    }
    
    echo "1. Adding to system tabs...\n";
    $systemTabs = $controller->get_system_tabs();
    if (!isset($systemTabs['ConflictSearch'])) {
        $systemTabs['ConflictSearch'] = 'ConflictSearch';
        $result = $controller->set_system_tabs(array_keys($systemTabs));
        echo "   Added ConflictSearch to system tabs\n";
    } else {
        echo "   ConflictSearch already in system tabs\n";
    }
    
    echo "2. Adding to current user's display tabs...\n";
    $displayTabs = $controller->get_user_tabs($current_user, 'display');
    if (!isset($displayTabs['ConflictSearch'])) {
        $displayTabs['ConflictSearch'] = 'ConflictSearch';
        $controller->set_user_tabs(array_keys($displayTabs), $current_user, 'display');
        echo "   Added ConflictSearch to user display tabs\n";
    } else {
        echo "   ConflictSearch already in user display tabs\n";
    }
    
    echo "3. Checking final status...\n";
    $finalTabs = $controller->get_tabs($current_user);
    if (isset($finalTabs[0]['ConflictSearch'])) {
        echo "   SUCCESS: ConflictSearch found in user's navigation tabs!\n";
    } else {
        echo "   WARNING: ConflictSearch still not found in navigation tabs\n";
        echo "   Available tabs: " . implode(', ', array_keys($finalTabs[0])) . "\n";
    }
    
    echo "\nDone. Please refresh browser and check navigation.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>