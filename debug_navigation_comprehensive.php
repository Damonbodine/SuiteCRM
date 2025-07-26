<?php
/**
 * Comprehensive Navigation Debugging Script for ConflictSearch Module
 * 
 * This script traces the complete navigation flow in SuiteCRM to identify
 * why ConflictSearch module is not appearing in navigation despite proper setup.
 * 
 * Usage: Run via CLI or web browser after logging into SuiteCRM
 * php debug_navigation_comprehensive.php
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once 'include/entryPoint.php';

echo "=== ConflictSearch Navigation Comprehensive Debug Analysis ===\n\n";

// Initialize global variables
global $current_user, $moduleList, $beanList, $app_list_strings, $modInvisList;

// Section 1: Basic Module Registration Check
echo "1. BASIC MODULE REGISTRATION\n";
echo "===========================\n";
echo "ConflictSearch in \$moduleList: " . (in_array('ConflictSearch', $moduleList) ? "✅ YES" : "❌ NO") . "\n";
echo "ConflictSearch in \$beanList: " . (isset($beanList['ConflictSearch']) ? "✅ YES" : "❌ NO") . "\n";
echo "ConflitSearch in \$modInvisList: " . (in_array('ConflictSearch', $modInvisList) ? "❌ YES (HIDDEN)" : "✅ NO (VISIBLE)") . "\n";
echo "ConflictSearch in \$app_list_strings: " . (isset($app_list_strings['moduleList']['ConflictSearch']) ? "✅ YES" : "❌ NO") . "\n\n";

// Section 2: User and Permission Analysis
echo "2. USER AND PERMISSIONS\n";
echo "=======================\n";
echo "Current User ID: " . $current_user->id . "\n";
echo "Current User Name: " . $current_user->user_name . "\n";
echo "Is Admin: " . ($current_user->isAdmin() ? "✅ YES" : "❌ NO") . "\n";

// Check ACL actions
require_once 'modules/ACL/ACLAction.php';
$acl_actions = ACLAction::getUserActions($current_user->id, false);
$conflictsearch_acl = isset($acl_actions['ConflictSearch']) ? $acl_actions['ConflictSearch'] : null;

echo "ACL Actions for ConflictSearch: " . ($conflictsearch_acl ? "✅ EXISTS" : "❌ MISSING") . "\n";
if ($conflictsearch_acl) {
    foreach ($conflictsearch_acl as $action => $level) {
        echo "  - $action: $level\n";
    }
}
echo "\n";

// Section 3: Navigation Data Flow Analysis
echo "3. NAVIGATION DATA FLOW ANALYSIS\n";
echo "================================\n";

// Simulate query_module_access_list function
require_once 'include/utils.php';
$accessible_modules = query_module_access_list($current_user);
echo "query_module_access_list() result:\n";
echo "  ConflictSearch accessible: " . (in_array('ConflictSearch', $accessible_modules) ? "✅ YES" : "❌ NO") . "\n";
echo "  Total accessible modules: " . count($accessible_modules) . "\n";

// Check ACL filtering
require_once 'modules/ACL/ACLController.php';
$filtered_modules = $accessible_modules;
ACLController::filterModuleList($filtered_modules);
echo "After ACLController::filterModuleList():\n";
echo "  ConflictSearch after ACL filter: " . (in_array('ConflictSearch', $filtered_modules) ? "✅ YES" : "❌ NO") . "\n";
echo "  Total after ACL filter: " . count($filtered_modules) . "\n\n";

// Section 4: TabController Analysis
echo "4. TABCONTROLLER ANALYSIS\n";
echo "=========================\n";
require_once 'modules/MySettings/TabController.php';
$tabController = new TabController();

// Get system tabs
$system_tabs = $tabController->get_system_tabs();
echo "TabController get_system_tabs():\n";
echo "  ConflictSearch in system tabs: " . (in_array('ConflictSearch', $system_tabs) ? "✅ YES" : "❌ NO") . "\n";
echo "  Total system tabs: " . count($system_tabs) . "\n";

// Get user tabs
$user_tabs = $tabController->get_tabs($current_user);
echo "TabController get_tabs() for current user:\n";
echo "  ConflitSearch in user tabs: " . (in_array('ConflictSearch', $user_tabs) ? "✅ YES" : "❌ NO") . "\n";
echo "  Total user tabs: " . count($user_tabs) . "\n\n";

// Section 5: Theme and Template Analysis
echo "5. THEME AND TEMPLATE ANALYSIS\n";
echo "==============================\n";

// Simulate SugarView navigation preparation
require_once 'include/MVC/View/SugarView.php';

// Check if SugarView can see the module
$test_view = new SugarView();
$fullModuleList = array();

foreach (query_module_access_list($current_user) as $module) {
    if (isset($app_list_strings['moduleList'][$module])) {
        $fullModuleList[$module] = $app_list_strings['moduleList'][$module];
    }
}

echo "SugarView fullModuleList preparation:\n";
echo "  ConflictSearch in fullModuleList: " . (isset($fullModuleList['ConflictSearch']) ? "✅ YES" : "❌ NO") . "\n";
echo "  Total in fullModuleList: " . count($fullModuleList) . "\n\n";

// Section 6: GroupedTabs Analysis
echo "6. GROUPED TABS ANALYSIS\n";
echo "========================\n";

$user_navigation_paradigm = $current_user->getPreference('navigation_paradigm');
if (!isset($user_navigation_paradigm)) {
    $user_navigation_paradigm = $GLOBALS['sugar_config']['default_navigation_paradigm'];
}
echo "User navigation paradigm: " . $user_navigation_paradigm . "\n";

if ($user_navigation_paradigm == 'gm') {
    require_once 'include/GroupedTabs/GroupedTabStructure.php';
    $groupedTabsClass = new GroupedTabStructure();
    $modules = query_module_access_list($current_user);
    $groupTabs = $groupedTabsClass->get_tab_structure(get_val_array($modules));
    
    echo "Using grouped tabs navigation\n";
    echo "Checking ConflictSearch in group tabs:\n";
    
    $found_in_groups = false;
    foreach ($groupTabs as $groupName => $groupData) {
        if (isset($groupData['modules']['ConflictSearch'])) {
            echo "  Found in group '$groupName': ✅ YES\n";
            $found_in_groups = true;
        }
    }
    
    if (!$found_in_groups) {
        echo "  ConflictSearch not found in any tab groups: ❌ NO\n";
    }
} else {
    echo "Using flat navigation (not grouped tabs)\n";
}
echo "\n";

// Section 7: Database Verification
echo "7. DATABASE VERIFICATION\n";
echo "========================\n";

// Check ACL actions in database
$db = DBManagerFactory::getInstance();
$acl_query = "SELECT category, name, aclaccess FROM acl_actions WHERE category = 'ConflictSearch'";
$acl_result = $db->query($acl_query);

echo "ACL Actions in database:\n";
$acl_count = 0;
while ($row = $db->fetchByAssoc($acl_result)) {
    echo "  - {$row['name']}: {$row['aclaccess']}\n";
    $acl_count++;
}
echo "Total ACL actions for ConflictSearch: $acl_count\n";

// Check user preferences
$prefs_query = "SELECT contents FROM user_preferences WHERE assigned_user_id = '{$current_user->id}' AND category = 'global'";
$prefs_result = $db->query($prefs_query);
$prefs_row = $db->fetchByAssoc($prefs_result);

if ($prefs_row) {
    $prefs = unserialize(base64_decode($prefs_row['contents']));
    echo "User preferences contain display_tabs: " . (isset($prefs['display_tabs']) ? "✅ YES" : "❌ NO") . "\n";
    
    if (isset($prefs['display_tabs'])) {
        $display_tabs = is_string($prefs['display_tabs']) ? unserialize(base64_decode($prefs['display_tabs'])) : $prefs['display_tabs'];
        echo "ConflictSearch in display_tabs: " . (is_array($display_tabs) && in_array('ConflictSearch', $display_tabs) ? "✅ YES" : "❌ NO") . "\n";
    }
} else {
    echo "No user preferences found in database\n";
}
echo "\n";

// Section 8: Cache Analysis
echo "8. CACHE ANALYSIS\n";
echo "=================\n";

// Check extension cache files
$include_cache = 'cache/application/Ext/Include/modules.ext.php';
$tabmenu_cache = 'cache/application/Ext/TabMenu/TabMenu.ext.php';

echo "Extension cache files:\n";
echo "  modules.ext.php exists: " . (file_exists($include_cache) ? "✅ YES" : "❌ NO") . "\n";
echo "  TabMenu.ext.php exists: " . (file_exists($tabmenu_cache) ? "✅ YES" : "❌ NO") . "\n";

if (file_exists($include_cache)) {
    $cache_content = file_get_contents($include_cache);
    echo "  ConflictSearch in modules cache: " . (strpos($cache_content, 'ConflictSearch') !== false ? "✅ YES" : "❌ NO") . "\n";
}

if (file_exists($tabmenu_cache)) {
    $tabmenu_content = file_get_contents($tabmenu_cache);
    echo "  ConflictSearch in TabMenu cache: " . (strpos($tabmenu_content, 'ConflictSearch') !== false ? "✅ YES" : "❌ NO") . "\n";
}
echo "\n";

// Section 9: Final Diagnosis
echo "9. FINAL DIAGNOSIS AND RECOMMENDATIONS\n";
echo "======================================\n";

$issues = array();
$recommendations = array();

// Analyze all the checks
if (!in_array('ConflictSearch', $moduleList)) {
    $issues[] = "ConflictSearch not in \$moduleList";
    $recommendations[] = "Fix module registration in extension files";
}

if (!isset($app_list_strings['moduleList']['ConflictSearch'])) {
    $issues[] = "ConflictSearch not in app_list_strings";
    $recommendations[] = "Add ConflictSearch to language files";
}

if (!in_array('ConflictSearch', $accessible_modules)) {
    $issues[] = "ConflictSearch filtered out by query_module_access_list()";
    $recommendations[] = "Check module permissions and ACL setup";
}

if (!in_array('ConflictSearch', $filtered_modules)) {
    $issues[] = "ConflictSearch filtered out by ACLController";
    $recommendations[] = "Verify ACL actions exist and have proper permissions (aclaccess >= 90)";
}

if (!in_array('ConflictSearch', $user_tabs)) {
    $issues[] = "ConflictSearch not in user's tab preferences";
    $recommendations[] = "Update user tab preferences via TabController";
}

if (empty($issues)) {
    echo "🎉 NO TECHNICAL ISSUES FOUND!\n";
    echo "ConflictSearch appears to pass all navigation requirements.\n";
    echo "This suggests a deeper SuiteCRM navigation rendering issue.\n\n";
    
    echo "RECOMMENDED NEXT STEPS:\n";
    echo "1. Implement dashboard widget for immediate access\n";
    echo "2. Add subpanel integration for contextual access\n";
    echo "3. Create administration panel integration\n";
    echo "4. Implement JavaScript navigation injection\n";
} else {
    echo "ISSUES FOUND:\n";
    foreach ($issues as $issue) {
        echo "❌ $issue\n";
    }
    echo "\nRECOMMENDATIONS:\n";
    foreach ($recommendations as $rec) {
        echo "🔧 $rec\n";
    }
}

echo "\n=== Debug Analysis Complete ===\n";
?>