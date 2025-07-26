<?php
/**
 * Debug OAuth Session State
 */

if (!defined('sugarEntry') || !sugarEntry) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

header('Content-Type: text/html; charset=UTF-8');
echo "<html><head><title>OAuth Session Debug</title></head><body>";
echo "<h1>🔍 OAuth Session Debug</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .warning{color:orange} .info{color:blue} code{background:#f8f8f8;padding:2px 4px;border-radius:3px}</style>";

echo "<h2>📊 Session Status</h2>";

// Check session status
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? "✅ Active" : "❌ Inactive") . "</p>";

echo "<h2>🔑 Current Session Data</h2>";
echo "<table style='border-collapse:collapse;width:100%'>";
echo "<tr style='background:#f0f0f0'><th style='border:1px solid #ddd;padding:8px'>Key</th><th style='border:1px solid #ddd;padding:8px'>Value</th></tr>";

foreach ($_SESSION as $key => $value) {
    $displayValue = is_string($value) ? htmlspecialchars($value) : json_encode($value);
    if (strlen($displayValue) > 100) {
        $displayValue = substr($displayValue, 0, 97) . "...";
    }
    echo "<tr>";
    echo "<td style='border:1px solid #ddd;padding:8px'><code>{$key}</code></td>";
    echo "<td style='border:1px solid #ddd;padding:8px'>{$displayValue}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>🧪 State Token Test</h2>";

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'generate':
            // Generate a new state token
            $state = bin2hex(random_bytes(16));
            $_SESSION['google_oauth_state'] = $state;
            $_SESSION['google_oauth_state_time'] = time();
            
            echo "<p class='success'>✅ Generated new state token: <code>{$state}</code></p>";
            echo "<p class='info'>Token stored in session at: " . date('Y-m-d H:i:s') . "</p>";
            
            // Create test OAuth URL
            $testParams = [
                'client_id' => 'test_client_id',
                'redirect_uri' => 'http://localhost:8080/debug_oauth_session.php?action=callback',
                'scope' => 'email',
                'response_type' => 'code',
                'access_type' => 'offline',
                'prompt' => 'consent',
                'state' => $state
            ];
            
            $testUrl = 'http://localhost:8080/debug_oauth_session.php?action=callback&code=test_code&state=' . $state;
            
            echo "<p><a href='{$testUrl}' style='background:#4285f4;color:white;padding:8px 12px;text-decoration:none;border-radius:4px'>🧪 Test Callback</a></p>";
            break;
            
        case 'callback':
            // Simulate callback validation
            $receivedState = $_GET['state'] ?? '';
            $storedState = $_SESSION['google_oauth_state'] ?? '';
            $storedTime = $_SESSION['google_oauth_state_time'] ?? 0;
            
            echo "<p><strong>Received State:</strong> <code>{$receivedState}</code></p>";
            echo "<p><strong>Stored State:</strong> <code>{$storedState}</code></p>";
            echo "<p><strong>Stored Time:</strong> " . ($storedTime ? date('Y-m-d H:i:s', $storedTime) : 'Not set') . "</p>";
            echo "<p><strong>Age:</strong> " . ($storedTime ? (time() - $storedTime) . " seconds" : 'Unknown') . "</p>";
            
            if (empty($storedState)) {
                echo "<p class='error'>❌ No state token found in session</p>";
            } elseif (empty($receivedState)) {
                echo "<p class='error'>❌ No state token received in callback</p>";
            } elseif (!hash_equals($storedState, $receivedState)) {
                echo "<p class='error'>❌ State token mismatch!</p>";
                echo "<p class='info'>This is the CSRF protection error you're experiencing</p>";
            } else {
                echo "<p class='success'>✅ State token validation successful!</p>";
            }
            
            // Check token age
            if ($storedTime && (time() - $storedTime) > 600) {
                echo "<p class='warning'>⚠️ State token is older than 10 minutes (expired)</p>";
            }
            break;
            
        case 'clear':
            unset($_SESSION['google_oauth_state']);
            unset($_SESSION['google_oauth_state_time']);
            echo "<p class='info'>🧹 Cleared OAuth state from session</p>";
            break;
    }
}

echo "<h2>🧪 Test Actions</h2>";
echo "<p><a href='?action=generate' style='background:#28a745;color:white;padding:8px 12px;text-decoration:none;border-radius:4px;margin:5px'>🎲 Generate State</a>";
echo " <a href='?action=clear' style='background:#dc3545;color:white;padding:8px 12px;text-decoration:none;border-radius:4px;margin:5px'>🧹 Clear State</a>";
echo " <a href='?' style='background:#6c757d;color:white;padding:8px 12px;text-decoration:none;border-radius:4px;margin:5px'>🔄 Refresh</a></p>";

echo "<h2>🔧 Session Configuration</h2>";
echo "<p><strong>Session Save Path:</strong> " . session_save_path() . "</p>";
echo "<p><strong>Session Name:</strong> " . session_name() . "</p>";
echo "<p><strong>Session Cookie Lifetime:</strong> " . ini_get('session.cookie_lifetime') . " seconds</p>";
echo "<p><strong>Session GC Max Lifetime:</strong> " . ini_get('session.gc_maxlifetime') . " seconds</p>";

echo "<h2>🚨 Potential Issues</h2>";

$issues = [];

if (session_status() !== PHP_SESSION_ACTIVE) {
    $issues[] = "Session is not active";
}

if (empty(session_id())) {
    $issues[] = "No session ID";
}

if (!isset($_SESSION)) {
    $issues[] = "Session superglobal not available";
}

$cookieLifetime = ini_get('session.cookie_lifetime');
if ($cookieLifetime > 0 && $cookieLifetime < 3600) {
    $issues[] = "Session cookie lifetime is too short ({$cookieLifetime}s)";
}

if (empty($issues)) {
    echo "<p class='success'>✅ No obvious session issues detected</p>";
} else {
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li class='error'>❌ {$issue}</li>";
    }
    echo "</ul>";
}

echo "<h2>💡 Recommendations</h2>";
echo "<div style='background:#e7f3ff;padding:15px;border:1px solid #b3d9ff;border-radius:4px'>";
echo "<h3>If you're experiencing CSRF errors:</h3>";
echo "<ol>";
echo "<li>Check that sessions are working by generating and testing a state token above</li>";
echo "<li>Ensure your browser accepts cookies from localhost:8080</li>";
echo "<li>Check that session storage is writable</li>";
echo "<li>Verify that the OAuth flow completes within 10 minutes</li>";
echo "</ol>";
echo "</div>";

echo "<br><p><a href='test_oauth_setup.php' style='background:#007bff;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🔧 OAuth Setup Test</a>";
echo " <a href='index.php' style='background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🏠 Home</a></p>";

echo "</body></html>";
?>