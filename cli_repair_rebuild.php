<?php
/**
 * CLI Quick Repair & Rebuild for SuiteCRM
 * Equivalent to Admin → Repair → Quick Repair & Rebuild
 */

if (php_sapi_name() !== 'cli') {
    die('This script can only be run from command line');
}

echo "🔧 SuiteCRM CLI Repair & Rebuild\n";
echo "================================\n";

// Include SuiteCRM bootstrap
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "✅ SuiteCRM loaded\n";

// Step 1: Clear all caches
echo "\n1. Clearing Caches...\n";
try {
    // Clear various caches
    if (function_exists('sugar_cache_clear')) {
        sugar_cache_clear();
        echo "   ✅ Sugar cache cleared\n";
    }
    
    // Clear file caches
    $cache_dirs = [
        'cache/modules',
        'cache/themes',
        'cache/smarty',
        'cache/include/javascript',
        'cache/include/language',
        'cache/workflow'
    ];
    
    foreach ($cache_dirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            echo "   ✅ Cleared $dir\n";
        }
    }
} catch (Exception $e) {
    echo "   ⚠️ Cache clear warning: " . $e->getMessage() . "\n";
}

// Step 2: Rebuild Extensions
echo "\n2. Rebuilding Extensions...\n";
try {
    require_once('ModuleInstall/ModuleInstaller.php');
    $moduleInstaller = new ModuleInstaller();
    $moduleInstaller->silent = true;
    
    // Rebuild extensions
    $extensions = [
        'application/Ext/Include',
        'application/Ext/Utils', 
        'application/Ext/Language',
        'application/Ext/EntryPointRegistry'
    ];
    
    foreach ($extensions as $ext) {
        try {
            $moduleInstaller->rebuild_all_related_extensions();
            echo "   ✅ Extensions rebuilt\n";
            break; // Only need to call once
        } catch (Exception $e) {
            echo "   ⚠️ Extension rebuild warning: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ⚠️ Extension rebuild error: " . $e->getMessage() . "\n";
}

// Step 3: Rebuild Entry Points
echo "\n3. Rebuilding Entry Points...\n";
try {
    // Force regeneration of entry point registry
    $entryPointDir = 'custom/Extension/application/Ext/EntryPointRegistry';
    if (is_dir($entryPointDir)) {
        $entryPointFiles = glob($entryPointDir . '/*.php');
        echo "   Found " . count($entryPointFiles) . " entry point files\n";
        
        foreach ($entryPointFiles as $file) {
            echo "   - " . basename($file) . "\n";
        }
        
        // Rebuild entry point registry
        if (file_exists('include/utils.php')) {
            require_once('include/utils.php');
            if (function_exists('rebuild_entry_point_registry')) {
                rebuild_entry_point_registry();
                echo "   ✅ Entry point registry rebuilt\n";
            }
        }
    }
} catch (Exception $e) {
    echo "   ⚠️ Entry point rebuild error: " . $e->getMessage() . "\n";
}

// Step 4: Rebuild JavaScript and CSS
echo "\n4. Rebuilding JavaScript and CSS...\n";
try {
    // Clear JavaScript cache
    $js_cache_dir = 'cache/include/javascript';
    if (is_dir($js_cache_dir)) {
        $files = glob($js_cache_dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "   ✅ JavaScript cache cleared\n";
    }
    
    // Clear theme cache
    $theme_dirs = glob('cache/themes/*', GLOB_ONLYDIR);
    foreach ($theme_dirs as $dir) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
    echo "   ✅ Theme cache cleared\n";
    
} catch (Exception $e) {
    echo "   ⚠️ JS/CSS rebuild warning: " . $e->getMessage() . "\n";
}

// Step 5: Rebuild Language Files
echo "\n5. Rebuilding Language Files...\n";
try {
    $lang_cache_dir = 'cache/include/language';
    if (is_dir($lang_cache_dir)) {
        $files = glob($lang_cache_dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "   ✅ Language cache cleared\n";
    }
} catch (Exception $e) {
    echo "   ⚠️ Language rebuild warning: " . $e->getMessage() . "\n";
}

// Step 6: Rebuild Vardefs
echo "\n6. Rebuilding Vardefs...\n";
try {
    // Clear vardef cache
    $modules_cache_dir = 'cache/modules';
    if (is_dir($modules_cache_dir)) {
        $files = glob($modules_cache_dir . '/**/vardefs.php');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "   ✅ Vardefs cache cleared\n";
    }
} catch (Exception $e) {
    echo "   ⚠️ Vardefs rebuild warning: " . $e->getMessage() . "\n";
}

// Step 7: Specific OpenAI Research checks
echo "\n7. OpenAI Research Integration Check...\n";
try {
    // Check entry point registration
    global $entry_point_registry;
    require_once('include/entryPointRegistry.php');
    
    if (isset($entry_point_registry['openai_research'])) {
        echo "   ✅ OpenAI Research entry point registered\n";
        echo "   File: " . $entry_point_registry['openai_research']['file'] . "\n";
    } else {
        echo "   ❌ OpenAI Research entry point NOT registered\n";
        echo "   Check: custom/Extension/application/Ext/EntryPointRegistry/openaiResearch.php\n";
    }
    
    // Check JavaScript file
    if (file_exists('custom/themes/SuiteP/js/openai-research.js')) {
        echo "   ✅ OpenAI Research JavaScript found\n";
    } else {
        echo "   ❌ OpenAI Research JavaScript NOT found\n";
    }
    
    // Check service file
    if (file_exists('custom/include/OpenAIResearchService.php')) {
        echo "   ✅ OpenAI Research Service found\n";
    } else {
        echo "   ❌ OpenAI Research Service NOT found\n";
    }
    
} catch (Exception $e) {
    echo "   ⚠️ OpenAI check error: " . $e->getMessage() . "\n";
}

// Step 8: Final cleanup
echo "\n8. Final Cleanup...\n";
try {
    // Remove any .php~ backup files
    $backup_files = glob('**/*.php~', GLOB_BRACE);
    foreach ($backup_files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    
    // Set proper permissions if possible
    if (function_exists('chmod')) {
        chmod('cache', 0755);
        echo "   ✅ Cache permissions set\n";
    }
    
} catch (Exception $e) {
    echo "   ⚠️ Cleanup warning: " . $e->getMessage() . "\n";
}

echo "\n🎉 Repair & Rebuild Complete!\n";
echo "================================\n";
echo "Next steps:\n";
echo "1. Clear your browser cache\n";
echo "2. Go to a Case detail page\n";
echo "3. Try clicking the '🔬 OpenAI Deep Research' button\n";
echo "4. If issues persist, run: php debug_openai_button.php\n";
echo "\n";
?>