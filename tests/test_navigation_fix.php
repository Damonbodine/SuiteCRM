<?php
if(!defined('sugarEntry') || !sugarEntry) define('sugarEntry', true);

chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

echo "=== TESTING CONFLICTSEARCH NAVIGATION FIX ===\n\n";

// Get current user (should be admin user ID 1)
global $current_user;
if (empty($current_user)) {
    // Load admin user for testing
    $current_user = BeanFactory::newBean('Users');
    $current_user->retrieve('1');
}

echo "Testing with user: " . $current_user->user_name . " (ID: " . $current_user->id . ")\n";
echo "Is Admin: " . (is_admin($current_user) ? 'Yes' : 'No') . "\n\n";

// Test TabController
echo "1. TESTING TABCONTROLLER:\n";
echo "==========================\n";
$tabController = new TabController();
$system_tabs = $tabController->get_system_tabs();

if (isset($system_tabs['ConflictSearch'])) {
    echo "✓ ConflictSearch found in system tabs\n";
} else {
    echo "✗ ConflictSearch NOT found in system tabs\n";
}

$user_tabs = $tabController->get_user_tabs($current_user);
if (isset($user_tabs['ConflictSearch'])) {
    echo "✓ ConflictSearch found in user tabs\n";
} else {
    echo "✗ ConflictSearch NOT found in user tabs\n";
}

// Test ACL filtering
echo "\n2. TESTING ACL FILTERING:\n";
echo "==========================\n";
global $moduleList;
$testModuleList = $moduleList;
echo "Modules before ACL filtering: " . count($testModuleList) . "\n";
echo "ConflictSearch in moduleList: " . (in_array('ConflictSearch', $testModuleList) ? 'Yes' : 'No') . "\n";

ACLController::filterModuleList($testModuleList);
echo "Modules after ACL filtering: " . count($testModuleList) . "\n";
echo "ConflictSearch after ACL filtering: " . (in_array('ConflictSearch', $testModuleList) ? 'Yes' : 'No') . "\n";

// Test ACL actions directly  
echo "\n3. TESTING ACL ACTIONS:\n";
echo "=======================\n";
$actions = ACLAction::getUserActions($current_user->id, false);
if (isset($actions['ConflictSearch'])) {
    echo "✓ ConflictSearch ACL actions found\n";
    $csActions = $actions['ConflictSearch'];
    if (isset($csActions['module']['access'])) {
        echo "  Access level: " . $csActions['module']['access']['aclaccess'] . "\n";
    }
} else {
    echo "✗ ConflictSearch ACL actions NOT found\n";
}

echo "\n4. FINAL TAB TEST:\n";
echo "==================\n";
list($display_tabs, $hide_tabs, $remove_tabs) = $tabController->get_tabs($current_user);

echo "Display tabs count: " . count($display_tabs) . "\n";
echo "ConflictSearch in display tabs: " . (isset($display_tabs['ConflictSearch']) ? 'Yes' : 'No') . "\n";

if (isset($display_tabs['ConflictSearch'])) {
    echo "\n🎉 SUCCESS: ConflictSearch should now appear in navigation!\n";
} else {
    echo "\n❌ ISSUE: ConflictSearch still not appearing in navigation\n";
    
    // Debug information
    echo "\nDEBUG INFO:\n";
    echo "- Hidden tabs: " . (isset($hide_tabs['ConflictSearch']) ? 'ConflictSearch is hidden' : 'ConflictSearch not in hidden') . "\n";
    echo "- Removed tabs: " . (isset($remove_tabs['ConflictSearch']) ? 'ConflictSearch is removed' : 'ConflictSearch not in removed') . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>