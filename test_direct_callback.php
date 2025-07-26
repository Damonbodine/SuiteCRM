<?php
// Direct test of callback functionality without entry point system

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<h1>Direct Callback Test</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>SuiteCRM Bootstrap: " . (defined('sugarEntry') ? "LOADED" : "NOT LOADED") . "</p>";

// Test basic PHP functionality
echo "<h2>Basic PHP Test</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Current Directory: " . getcwd() . "</p>";

try {
    // Test global variables
    echo "<h2>SuiteCRM Globals Test</h2>";
    global $sugar_config, $current_user, $db;
    
    echo "<p>sugar_config: " . (isset($sugar_config) ? "EXISTS" : "NOT SET") . "</p>";
    echo "<p>current_user: " . (isset($current_user) ? "EXISTS (ID: " . ($current_user->id ?? 'EMPTY') . ")" : "NOT SET") . "</p>";
    echo "<p>database: " . (isset($db) ? "EXISTS" : "NOT SET") . "</p>";
    
    // Test database connection
    if (isset($db)) {
        echo "<h2>Database Test</h2>";
        $result = $db->query("SELECT 1 as test");
        if ($result) {
            echo "<p>✓ Database connection working</p>";
        } else {
            echo "<p>✗ Database query failed</p>";
        }
    }
    
    // Test BeanFactory
    echo "<h2>BeanFactory Test</h2>";
    if (class_exists('BeanFactory')) {
        echo "<p>✓ BeanFactory available</p>";
        try {
            $user = BeanFactory::newBean('Users');
            echo "<p>✓ Can create User bean</p>";
        } catch (Exception $e) {
            echo "<p>✗ BeanFactory error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>✗ BeanFactory not available</p>";
    }
    
    // Test OAuth Provider lookup (the failing query)
    echo "<h2>OAuth Provider Test</h2>";
    if (class_exists('BeanFactory')) {
        try {
            $provider = BeanFactory::newBean('ExternalOAuthProvider');
            echo "<p>✓ Can create ExternalOAuthProvider bean</p>";
            
            // Try the failing query with fix
            $sql = "SELECT * FROM external_oauth_providers WHERE external_oauth_providers.name = 'Google' AND external_oauth_providers.deleted = 0";
            $result = $db->query($sql);
            if ($result) {
                echo "<p>✓ Fixed OAuth provider query works</p>";
            } else {
                echo "<p>✗ OAuth provider query still fails</p>";
            }
        } catch (Exception $e) {
            echo "<p>✗ OAuth provider test error: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
}

echo "<h2>Test Complete</h2>";
echo "<p><a href='index.php'>Back to SuiteCRM</a></p>";
?>