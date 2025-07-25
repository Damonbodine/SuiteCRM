<?php
/**
 * Quick CLI Repair using SuiteCRM's built-in functions
 * Simpler version that uses existing SuiteCRM repair mechanisms
 */

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from command line');
}

echo "⚡ SuiteCRM Quick Repair (CLI)\n";
echo "=============================\n";

// Bootstrap SuiteCRM
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');
require_once('modules/Administration/QuickRepairAndRebuild.php');

echo "✅ SuiteCRM loaded\n";

try {
    // Use SuiteCRM's built-in repair class
    echo "\n🔧 Running Quick Repair & Rebuild...\n";
    
    $repair = new RepairAndClear();
    $repair->repairAndClearAll(
        array("clearAll"),  // Clear all caches
        array(
            'rebuildExtensions',    // Rebuild extensions
            'clearJsLangFiles',     // Clear JavaScript language files
            'clearJsFiles',         // Clear JavaScript files
            'clearDashlets',        // Clear dashlets
            'clearSugarFeedCache',  // Clear feed cache
            'clearThemeCache',      // Clear theme cache
            'clearVardefs',         // Clear vardefs
            'clearSearchCache'      // Clear search cache
        ),
        false,  // Show output
        false   // Not from admin interface
    );
    
    echo "✅ Quick Repair & Rebuild completed successfully\n";
    
} catch (Exception $e) {
    echo "❌ Error during repair: " . $e->getMessage() . "\n";
    
    // Fallback to manual cache clearing
    echo "\n🔄 Attempting manual cache clear...\n";
    
    $cache_dirs = ['cache/modules', 'cache/themes', 'cache/smarty', 'cache/include'];
    foreach ($cache_dirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            $count = 0;
            foreach ($files as $file) {
                if (is_file($file) && unlink($file)) {
                    $count++;
                }
            }
            echo "   Cleared $count files from $dir\n";
        }
    }
}

// Quick check for OpenAI Research
echo "\n🔍 OpenAI Research Status:\n";
global $entry_point_registry;

// Force reload entry points
if (file_exists('include/entryPointRegistry.php')) {
    require_once('include/entryPointRegistry.php');
}

if (isset($entry_point_registry['openai_research'])) {
    echo "✅ OpenAI Research entry point: REGISTERED\n";
} else {
    echo "❌ OpenAI Research entry point: NOT REGISTERED\n";
    echo "   Try running the full repair script: php cli_repair_rebuild.php\n";
}

echo "\n🎯 Next Steps:\n";
echo "1. Clear browser cache (Ctrl+F5)\n";
echo "2. Test the OpenAI Research button on a Case page\n";
echo "3. If still not working, run: php debug_openai_button.php\n";
echo "\n";
?>