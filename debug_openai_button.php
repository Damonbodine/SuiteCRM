<?php
/**
 * Debug OpenAI Deep Research Button Issue
 * Run this to identify why the button isn't clickable
 */

// Include SuiteCRM bootstrap
require_once('include/entryPoint.php');

echo "<h1>🔍 OpenAI Deep Research Button Debug</h1>";

// Check 1: Entry Point Registration
echo "<h2>1. Entry Point Registration</h2>";
global $entry_point_registry;

// Force reload entry points
$GLOBALS['log']->info("Loading entry point registry...");
require_once('include/entryPointRegistry.php');

if (isset($entry_point_registry['openai_research'])) {
    echo "✅ Entry point 'openai_research' is registered<br>";
    echo "File: " . $entry_point_registry['openai_research']['file'] . "<br>";
    echo "Auth required: " . ($entry_point_registry['openai_research']['auth'] ? 'Yes' : 'No') . "<br>";
} else {
    echo "❌ Entry point 'openai_research' NOT registered<br>";
    echo "<strong>Solution:</strong> Go to Admin → Repair → Quick Repair & Rebuild<br>";
}

// Check 2: JavaScript File
echo "<h2>2. JavaScript File Check</h2>";
$jsFile = 'custom/themes/SuiteP/js/openai-research.js';
if (file_exists($jsFile)) {
    $jsContent = file_get_contents($jsFile);
    echo "✅ JavaScript file exists (" . number_format(strlen($jsContent)) . " bytes)<br>";
    
    // Check for key functions
    if (strpos($jsContent, 'SUGAR.OpenAIResearch') !== false) {
        echo "✅ SUGAR.OpenAIResearch namespace found<br>";
    } else {
        echo "❌ SUGAR.OpenAIResearch namespace NOT found<br>";
    }
    
    if (strpos($jsContent, 'openModal') !== false) {
        echo "✅ openModal function found<br>";
    } else {
        echo "❌ openModal function NOT found<br>";
    }
} else {
    echo "❌ JavaScript file NOT found at: $jsFile<br>";
}

// Check 3: Configuration
echo "<h2>3. Configuration Check</h2>";
global $sugar_config;
$ai_config = isset($sugar_config['ai_status_intelligence']) ? $sugar_config['ai_status_intelligence'] : array();

if (isset($ai_config['enabled']) && $ai_config['enabled'] === true) {
    echo "✅ AI features enabled<br>";
} else {
    echo "❌ AI features NOT enabled<br>";
}

if (isset($ai_config['openai_api_key']) && !empty($ai_config['openai_api_key'])) {
    echo "✅ OpenAI API key configured<br>";
    echo "Key: " . substr($ai_config['openai_api_key'], 0, 10) . "...<br>";
} else {
    echo "❌ OpenAI API key NOT configured<br>";
}

if (isset($ai_config['mock_mode'])) {
    if ($ai_config['mock_mode'] === false) {
        echo "✅ Real API mode enabled (mock_mode = false)<br>";
    } else {
        echo "⚠️ Mock mode enabled (mock_mode = true)<br>";
        echo "Deep Research requires real API mode<br>";
    }
} else {
    echo "❌ mock_mode not set<br>";
}

// Check 4: Cases Module Files
echo "<h2>4. Cases Module Integration</h2>";
$detailViewFile = 'custom/modules/Cases/metadata/detailviewdefs.php';
if (file_exists($detailViewFile)) {
    $content = file_get_contents($detailViewFile);
    if (strpos($content, 'OpenAI Deep Research') !== false) {
        echo "✅ Button found in detailviewdefs.php<br>";
    } else {
        echo "❌ Button NOT found in detailviewdefs.php<br>";
    }
    
    if (strpos($content, 'SUGAR.OpenAIResearch.openModal') !== false) {
        echo "✅ onClick handler found in detailviewdefs.php<br>";
    } else {
        echo "❌ onClick handler NOT found in detailviewdefs.php<br>";
    }
} else {
    echo "❌ detailviewdefs.php NOT found<br>";
}

$viewFile = 'custom/modules/Cases/views/view.detail.php';
if (file_exists($viewFile)) {
    $content = file_get_contents($viewFile);
    if (strpos($content, 'openai-research.js') !== false) {
        echo "✅ JavaScript inclusion found in view.detail.php<br>";
    } else {
        echo "❌ JavaScript inclusion NOT found in view.detail.php<br>";
    }
} else {
    echo "❌ view.detail.php NOT found<br>";
}

// Check 5: Service instantiation
echo "<h2>5. Service Check</h2>";
try {
    require_once('custom/include/OpenAIResearchService.php');
    $service = new OpenAIResearchService();
    echo "✅ OpenAIResearchService loads successfully<br>";
    
    if ($service->isEnabled()) {
        echo "✅ Service is enabled<br>";
    } else {
        echo "❌ Service is NOT enabled<br>";
        echo "Check: enabled=true, mock_mode=false, API key present<br>";
    }
} catch (Exception $e) {
    echo "❌ Service error: " . $e->getMessage() . "<br>";
}

// Solution Steps
echo "<h2>🔧 Troubleshooting Steps</h2>";
echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px;'>";
echo "<h3>Try these steps in order:</h3>";
echo "<ol>";
echo "<li><strong>Quick Repair & Rebuild:</strong><br>";
echo "&nbsp;&nbsp;Go to Admin → Repair → Quick Repair & Rebuild<br>";
echo "&nbsp;&nbsp;This registers the entry point and rebuilds the system cache</li>";

echo "<li><strong>Clear Browser Cache:</strong><br>";
echo "&nbsp;&nbsp;Hard refresh the page (Ctrl+F5 or Cmd+Shift+R)<br>";
echo "&nbsp;&nbsp;Clear browser cache and cookies for your SuiteCRM site</li>";

echo "<li><strong>Check Browser Console:</strong><br>";
echo "&nbsp;&nbsp;Open browser Developer Tools (F12)<br>";
echo "&nbsp;&nbsp;Go to Console tab and look for JavaScript errors<br>";
echo "&nbsp;&nbsp;Look for errors related to SUGAR.OpenAIResearch</li>";

echo "<li><strong>Test JavaScript Loading:</strong><br>";
echo "&nbsp;&nbsp;On a Case detail page, open Developer Tools<br>";
echo "&nbsp;&nbsp;In Console, type: <code>SUGAR.OpenAIResearch</code><br>";
echo "&nbsp;&nbsp;If it returns 'undefined', the JavaScript isn't loading</li>";

echo "<li><strong>Manual JavaScript Test:</strong><br>";
echo "&nbsp;&nbsp;Try this in browser console: <code>alert('Test');</code><br>";
echo "&nbsp;&nbsp;Then try: <code>SUGAR.OpenAIResearch.openModal();</code></li>";
echo "</ol>";
echo "</div>";

// Quick Test HTML
echo "<h2>6. Quick Test</h2>";
echo "<p>Here's a test button you can click right now:</p>";
echo '<button onclick="alert(\'Basic JS works\'); if(typeof SUGAR !== \'undefined\' && SUGAR.OpenAIResearch) { SUGAR.OpenAIResearch.openModal(); } else { alert(\'SUGAR.OpenAIResearch not loaded\'); }" class="btn" style="background: #10a37f; color: white; padding: 10px 20px; border: none; border-radius: 4px;">🔬 Test OpenAI Button</button>';

echo "<br><br><p><strong>What should happen:</strong></p>";
echo "<ul>";
echo "<li>First alert: 'Basic JS works' (confirms JavaScript is working)</li>";
echo "<li>If SUGAR.OpenAIResearch is loaded: Opens the research modal</li>";
echo "<li>If not loaded: Shows 'SUGAR.OpenAIResearch not loaded' alert</li>";
echo "</ul>";
?>