<?php
/**
 * Clear all SuiteCRM sessions and cached data
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('config.php');

echo "Starting session and cache cleanup...\n";

// 1. Clear PHP sessions directory
$sessionPath = session_save_path();
if (empty($sessionPath)) {
    $sessionPath = sys_get_temp_dir();
}

echo "Session path: $sessionPath\n";

// 2. Start and destroy current session
session_start();
echo "Current session ID: " . session_id() . "\n";

// Clear all session variables
$_SESSION = array();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy session
session_destroy();
echo "✓ Session destroyed\n";

// 3. Clear SuiteCRM-specific session files
$suiteSessionPattern = $sessionPath . '/sess_*';
$sessionFiles = glob($suiteSessionPattern);
$clearedCount = 0;

foreach ($sessionFiles as $file) {
    if (is_file($file) && is_writable($file)) {
        $content = file_get_contents($file);
        // Only clear sessions that might contain SuiteCRM data
        if (strpos($content, 'sugar') !== false || strpos($content, 'SuiteCRM') !== false) {
            unlink($file);
            $clearedCount++;
        }
    }
}

echo "✓ Cleared $clearedCount SuiteCRM session files\n";

// 4. Clear cache again
echo "Clearing cache directory...\n";
$cacheDir = __DIR__ . '/cache';
if (is_dir($cacheDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    $clearedFiles = 0;
    foreach ($iterator as $file) {
        if ($file->isDir()) {
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
            $clearedFiles++;
        }
    }
    echo "✓ Cleared $clearedFiles cache files\n";
}

// 5. Clear upload temp directory
$uploadTempDir = __DIR__ . '/upload/upgrades/temp';
if (is_dir($uploadTempDir)) {
    $files = glob($uploadTempDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✓ Cleared upload temp directory\n";
}

echo "\n=== SESSION CLEANUP COMPLETE ===\n";
echo "All sessions and cache cleared.\n";
echo "Please close your browser completely and reopen before accessing SuiteCRM.\n";
?>