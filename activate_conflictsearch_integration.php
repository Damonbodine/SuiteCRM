<?php
/**
 * ConflictSearch Integration Activation Script
 * 
 * This script activates all the ConflictSearch integration features including:
 * - Enhanced dashboard widget
 * - Subpanel integration 
 * - Administration panel integration
 * - Hook-based navigation injection
 * - Cache clearing and system updates
 * 
 * Run this script after implementing the ConflictSearch integration to ensure
 * all features are properly activated and caches are cleared.
 * 
 * Usage: php activate_conflictsearch_integration.php
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once 'include/entryPoint.php';

echo "=== ConflictSearch Integration Activation ===\n\n";

global $current_user;

// Check admin permissions
if (empty($current_user) || !$current_user->isAdmin()) {
    die("Error: Administrator privileges required to run this script.\n");
}

$success_count = 0;
$error_count = 0;

// Function to log success
function log_success($message) {
    global $success_count;
    echo "✅ $message\n";
    $success_count++;
}

// Function to log error
function log_error($message) {
    global $error_count;
    echo "❌ $message\n";
    $error_count++;
}

// 1. Clear SuiteCRM caches
echo "1. CLEARING SUITECRM CACHES\n";
echo "============================\n";

try {
    // Clear file cache
    require_once 'include/utils/file_utils.php';
    if (function_exists('clearAllJsFiles')) {
        clearAllJsFiles();
        log_success("JavaScript cache cleared");
    }
    
    // Clear Smarty templates cache
    require_once 'include/Smarty/Smarty.class.php';
    $smarty = new Smarty();
    if (method_exists($smarty, 'clearAllCache')) {
        $smarty->clearAllCache();
        log_success("Smarty template cache cleared"); 
    }
    
    // Clear sugar cache
    if (function_exists('sugar_cache_clear')) {
        sugar_cache_clear();
        log_success("Sugar cache cleared");
    }
    
    // Clear extension cache files
    $cache_dirs = array(
        'cache/application/Ext/',
        'cache/modules/',
        'cache/themes/'
    );
    
    foreach ($cache_dirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '*.php');
            foreach ($files as $file) {
                if (unlink($file)) {
                    log_success("Removed cache file: " . basename($file));
                }
            }
        }
    }
    
} catch (Exception $e) {
    log_error("Cache clearing failed: " . $e->getMessage());
}

echo "\n";

// 2. Rebuild extensions
echo "2. REBUILDING EXTENSIONS\n";
echo "========================\n";

try {
    require_once 'ModuleInstall/ModuleInstaller.php';
    require_once 'include/SugarObjects/LanguageManager.php';
    
    $moduleInstaller = new ModuleInstaller();
    
    // Rebuild extensions
    $moduleInstaller->rebuild_extensions();
    log_success("Extensions rebuilt successfully");
    
    // Rebuild relationships
    if (method_exists($moduleInstaller, 'rebuild_relationships')) {
        $moduleInstaller->rebuild_relationships();
        log_success("Relationships rebuilt successfully");
    }
    
    // Rebuild dashlets
    if (method_exists($moduleInstaller, 'rebuild_dashlets')) {
        $moduleInstaller->rebuild_dashlets();
        log_success("Dashlets rebuilt successfully");
    }
    
} catch (Exception $e) {
    log_error("Extension rebuild failed: " . $e->getMessage());
}

echo "\n";

// 3. Verify ConflictSearch module registration
echo "3. VERIFYING MODULE REGISTRATION\n";
echo "================================\n";

try {
    global $moduleList, $beanList, $beanFiles;
    
    // Check basic registration
    if (in_array('ConflictSearch', $moduleList)) {
        log_success("ConflictSearch in moduleList");
    } else {
        log_error("ConflictSearch NOT in moduleList");
    }
    
    if (isset($beanList['ConflictSearch'])) {
        log_success("ConflictSearch in beanList");
    } else {
        log_error("ConflictSearch NOT in beanList");
    }
    
    if (isset($beanFiles['ConflictSearch'])) {
        log_success("ConflictSearch bean file registered");
    } else {
        log_error("ConflictSearch bean file NOT registered");
    }
    
    // Check ACL actions
    $db = DBManagerFactory::getInstance();
    $result = $db->query("SELECT COUNT(*) as count FROM acl_actions WHERE category = 'ConflictSearch'");
    $row = $db->fetchByAssoc($result);
    
    if ($row['count'] >= 8) {
        log_success("ConflictSearch ACL actions present ({$row['count']} actions)");
    } else {
        log_error("ConflictSearch ACL actions missing or incomplete ({$row['count']} actions)");
    }
    
} catch (Exception $e) {
    log_error("Module registration check failed: " . $e->getMessage());
}

echo "\n";

// 4. Verify integration files
echo "4. VERIFYING INTEGRATION FILES\n";
echo "===============================\n";

$integration_files = array(
    'Enhanced Dashboard Widget' => 'modules/ConflictSearch/Dashlets/ConflictSearchDashlet/ConflictSearchDashlet.php',
    'Dashlet Registration' => 'custom/Extension/application/Ext/Dashlets/ConflictSearchDashlet.php',
    'Contact Subpanel' => 'custom/Extension/modules/Contacts/Ext/Layoutdefs/conflict_search_subpanel.php',
    'Account Subpanel' => 'custom/Extension/modules/Accounts/Ext/Layoutdefs/conflict_search_subpanel.php',
    'Case Subpanel' => 'custom/Extension/modules/Cases/Ext/Layoutdefs/conflict_search_subpanel.php',
    'Subpanel Utils' => 'custom/modules/ConflictSearch/utils/subpanel_utils.php',
    'Admin Integration' => 'custom/Extension/application/Ext/Administration/ConflictSearchAdmin.php',
    'Navigation Hook' => 'custom/Extension/application/Ext/LogicHooks/ConflictSearchNavigation.php',
    'Hook Implementation' => 'custom/modules/ConflictSearch/hooks/NavigationInjectionHook.php',
    'Statistics View' => 'modules/ConflictSearch/views/view.statistics.php',
    'Settings View' => 'modules/ConflictSearch/views/view.settings.php'
);

foreach ($integration_files as $name => $file) {
    if (file_exists($file)) {
        log_success("$name file exists");
    } else {
        log_error("$name file MISSING: $file");
    }
}

echo "\n";

// 5. Test navigation injection
echo "5. TESTING NAVIGATION INJECTION\n";
echo "===============================\n";

try {
    // Test if hook class can be loaded
    require_once 'custom/modules/ConflictSearch/hooks/NavigationInjectionHook.php';
    
    if (class_exists('ConflictSearchNavigationInjectionHook')) {
        log_success("Navigation injection hook class loaded successfully");
        
        $hook = new ConflictSearchNavigationInjectionHook();
        if (method_exists($hook, 'injectNavigationItem')) {
            log_success("Navigation injection method available");
        } else {
            log_error("Navigation injection method missing");
        }
    } else {
        log_error("Navigation injection hook class not found");
    }
    
} catch (Exception $e) {
    log_error("Navigation injection test failed: " . $e->getMessage());
}

echo "\n";

// 6. Final system state check
echo "6. FINAL SYSTEM STATE CHECK\n";
echo "===========================\n";

try {
    // Check if ConflictSearch module can be instantiated
    $conflictSearch = BeanFactory::newBean('ConflictSearch');
    if ($conflictSearch && $conflictSearch->table_name === 'conflict_search') {
        log_success("ConflictSearch module can be instantiated");
    } else {
        log_error("ConflictSearch module instantiation failed");
    }
    
    // Check database table
    $result = $db->query("SHOW TABLES LIKE 'conflict_search'");
    if ($db->fetchByAssoc($result)) {
        log_success("ConflictSearch database table exists");
    } else {
        log_error("ConflictSearch database table missing");
    }
    
    // Test controller endpoints
    if (file_exists('modules/ConflictSearch/controller.php')) {
        require_once 'modules/ConflictSearch/controller.php';
        if (class_exists('ConflictSearchController')) {
            $controller = new ConflictSearchController();
            if (method_exists($controller, 'action_check_urgent_conflicts')) {
                log_success("Urgent conflicts endpoint available");
            } else {
                log_error("Urgent conflicts endpoint missing");
            }
        }
    }
    
} catch (Exception $e) {
    log_error("System state check failed: " . $e->getMessage());
}

echo "\n";

// 7. Recommendations
echo "7. ACTIVATION SUMMARY & RECOMMENDATIONS\n";
echo "=======================================\n";

echo "✅ Successfully completed: $success_count items\n";
echo "❌ Issues found: $error_count items\n\n";

if ($error_count === 0) {
    echo "🎉 CONGRATULATIONS! ConflictSearch integration is fully activated!\n\n";
    
    echo "✅ AVAILABLE ACCESS METHODS:\n";
    echo "   1. Enhanced Dashboard Widget - Add to your homepage for quick access\n";
    echo "   2. Subpanels - Look for 'Conflict Searches' in Contacts, Accounts, and Cases\n";
    echo "   3. Administration Panel - Check 'Legal & Compliance' section in Admin\n";
    echo "   4. Dynamic Navigation - ConflictSearch item injected into main navigation\n";
    echo "   5. Direct URL Access - index.php?module=ConflictSearch&action=EditView\n\n";
    
    echo "🎯 KEYBOARD SHORTCUTS:\n";
    echo "   - Ctrl+Shift+C (Cmd+Shift+C on Mac) - New conflict search\n";
    echo "   - Ctrl+Shift+S (Cmd+Shift+S on Mac) - Search history\n\n";
    
    echo "📊 ADDITIONAL FEATURES:\n";
    echo "   - Visit: index.php?module=ConflictSearch&action=statistics for analytics\n";
    echo "   - Visit: index.php?module=ConflictSearch&action=settings for configuration\n";
    echo "   - Real-time conflict notifications via navigation badge\n\n";
    
} else {
    echo "⚠️ INTEGRATION PARTIALLY ACTIVATED\n\n";
    echo "Some issues were found during activation. Please review the errors above\n";
    echo "and fix any missing files or configuration problems.\n\n";
    
    echo "🔧 TROUBLESHOOTING:\n";
    echo "   1. Run Quick Repair and Rebuild from Admin panel\n";
    echo "   2. Clear browser cache and hard reload (Ctrl+Shift+R)\n";
    echo "   3. Check file permissions for custom directories\n";
    echo "   4. Verify database connectivity and table structure\n\n";
}

echo "📚 NEXT STEPS:\n";
echo "   1. Log out and log back in to see navigation changes\n";
echo "   2. Add ConflictSearch widget to your dashboard\n";
echo "   3. Test conflict search functionality with sample data\n";
echo "   4. Configure settings via Administration panel\n";
echo "   5. Train users on new access methods\n\n";

echo "=== ConflictSearch Integration Activation Complete ===\n";
?>