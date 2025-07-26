<?php
/**
 * Test State Token Generation and Retrieval
 * This helps debug the CSRF state token issue
 */

if (!defined('sugarEntry') || !sugarEntry) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

header('Content-Type: text/html; charset=UTF-8');
echo "<html><head><title>State Token Test</title></head><body>";
echo "<h1>🔍 OAuth State Token Test</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .warning{color:orange} .info{color:blue} code{background:#f8f8f8;padding:2px 4px;border-radius:3px} .test-box{background:#f9f9f9;border:1px solid #ddd;padding:15px;margin:10px 0;border-radius:4px}</style>";

try {
    global $current_user, $db;
    
    echo "<h2>🧪 State Token Generation Test</h2>";
    
    if (isset($_GET['action']) && $_GET['action'] === 'generate') {
        // Simulate what the email view does
        require_once('custom/modules/Emails/views/view.list.php');
        $emailView = new CustomEmailsViewList();
        
        // Use reflection to call the private method
        $reflection = new ReflectionClass($emailView);
        $generateStateMethod = $reflection->getMethod('generateStateToken');
        $generateStateMethod->setAccessible(true);
        
        $state = $generateStateMethod->invoke($emailView);
        
        echo "<div class='test-box'>";
        echo "<h3>✅ State Token Generated</h3>";
        echo "<p><strong>Token:</strong> <code>{$state}</code></p>";
        echo "<p><strong>Length:</strong> " . strlen($state) . " characters</p>";
        echo "<p><strong>Time:</strong> " . date('Y-m-d H:i:s') . "</p>";
        echo "</div>";
        
        // Redirect to callback test
        $callbackUrl = "test_state_token.php?action=callback&state={$state}&code=test_code_12345";
        echo "<p><a href='{$callbackUrl}' style='background:#4285f4;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🧪 Test Callback Validation</a></p>";
        
    } elseif (isset($_GET['action']) && $_GET['action'] === 'callback') {
        // Simulate what the OAuth callback does
        echo "<div class='test-box'>";
        echo "<h3>🔍 Callback Validation Test</h3>";
        
        $receivedState = $_GET['state'] ?? '';
        echo "<p><strong>Received State:</strong> <code>{$receivedState}</code></p>";
        
        // Check all storage methods
        $found = [];
        
        // Method 1: PHP Session
        if (isset($_SESSION['google_oauth_state'])) {
            $found['session'] = $_SESSION['google_oauth_state'];
        }
        
        // Method 2: SuiteCRM Global
        if (isset($GLOBALS['_SESSION']['google_oauth_state'])) {
            $found['global'] = $GLOBALS['_SESSION']['google_oauth_state'];
        }
        
        // Method 3: Temp file
        $tempFile = sys_get_temp_dir() . '/suitecrm_oauth_state_' . md5($receivedState);
        if (file_exists($tempFile)) {
            $stateData = json_decode(file_get_contents($tempFile), true);
            if ($stateData && isset($stateData['state'])) {
                $found['tempfile'] = $stateData['state'];
            }
        }
        
        // Method 4: Database
        if ($current_user && $current_user->id && $db) {
            $userId = $db->quote($current_user->id);
            $query = "SELECT state_token FROM oauth_states WHERE user_id = {$userId} AND state_token = '{$receivedState}' LIMIT 1";
            $result = $db->query($query);
            if ($result && $row = $db->fetchByAssoc($result)) {
                $found['database'] = $row['state_token'];
            }
        }
        
        echo "<h4>🔍 Storage Locations Checked:</h4>";
        echo "<ul>";
        echo "<li>PHP Session: " . (isset($found['session']) ? "✅ Found: " . substr($found['session'], 0, 8) . "..." : "❌ Not found") . "</li>";
        echo "<li>SuiteCRM Global: " . (isset($found['global']) ? "✅ Found: " . substr($found['global'], 0, 8) . "..." : "❌ Not found") . "</li>";
        echo "<li>Temp File: " . (isset($found['tempfile']) ? "✅ Found: " . substr($found['tempfile'], 0, 8) . "..." : "❌ Not found") . "</li>";
        echo "<li>Database: " . (isset($found['database']) ? "✅ Found: " . substr($found['database'], 0, 8) . "..." : "❌ Not found") . "</li>";
        echo "</ul>";
        
        // Check for matches
        $validationResults = [];
        foreach ($found as $method => $storedState) {
            $isValid = hash_equals($storedState, $receivedState);
            $validationResults[$method] = $isValid;
            echo "<p><strong>{$method}:</strong> " . ($isValid ? "✅ Valid" : "❌ Mismatch") . "</p>";
        }
        
        if (empty($found)) {
            echo "<p class='error'>❌ <strong>State token not found in any storage location!</strong></p>";
            echo "<p class='warning'>This is the CSRF error you're experiencing. The state token is being lost between generation and callback.</p>";
        } elseif (in_array(true, $validationResults)) {
            echo "<p class='success'>✅ <strong>At least one storage method worked!</strong></p>";
        } else {
            echo "<p class='error'>❌ <strong>State token found but none matched!</strong></p>";
        }
        
        echo "</div>";
        
    } else {
        // Show main test interface
        echo "<div class='test-box'>";
        echo "<h3>🎯 Test OAuth State Token Flow</h3>";
        echo "<p>This test simulates the OAuth state token generation and validation process to identify where the CSRF error occurs.</p>";
        echo "<p><a href='?action=generate' style='background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🎲 Generate State Token</a></p>";
        echo "</div>";
    }
    
    echo "<h2>📊 Current Session Status</h2>";
    echo "<div class='test-box'>";
    echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
    echo "<p><strong>Session Active:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? "✅ Yes" : "❌ No") . "</p>";
    echo "<p><strong>Current User:</strong> " . ($current_user ? $current_user->user_name . " (ID: {$current_user->id})" : "❌ Not logged in") . "</p>";
    
    // Show current OAuth states in session
    if (isset($_SESSION['google_oauth_state'])) {
        echo "<p><strong>Session OAuth State:</strong> <code>" . substr($_SESSION['google_oauth_state'], 0, 16) . "...</code></p>";
    } else {
        echo "<p><strong>Session OAuth State:</strong> ❌ Not set</p>";
    }
    echo "</div>";
    
    echo "<h2>💡 Debugging Tips</h2>";
    echo "<div class='test-box'>";
    echo "<ol>";
    echo "<li>Run the <strong>Generate State Token</strong> test above</li>";
    echo "<li>Click the callback test link immediately</li>";
    echo "<li>Check which storage methods are working</li>";
    echo "<li>If no storage methods work, there's a session issue</li>";
    echo "<li>If storage works but validation fails, there's a token generation issue</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<br><p><a href='debug_oauth_session.php' style='background:#007bff;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🔍 Session Debug</a>";
echo " <a href='test_oauth_setup.php' style='background:#6c757d;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🔧 OAuth Setup</a>";
echo " <a href='index.php' style='background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🏠 Home</a></p>";

echo "</body></html>";
?>