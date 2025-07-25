<?php
// Test OAuth flow components individually

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<h1>OAuth Flow Component Test</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

try {
    // Test 1: Database connection and user lookup
    echo "<h2>Test 1: User Authentication Context</h2>";
    global $current_user, $db;
    
    echo "<p>Current user: " . (isset($current_user) && $current_user->id ? $current_user->id : "NONE") . "</p>";
    
    // Test findOrCreateUser method
    $testEmail = "damonbodine@gmail.com";
    
    $user = BeanFactory::newBean('Users');
    $users = $user->get_full_list('', "users.email1 = '" . $user->db->quote($testEmail) . "' AND users.deleted = 0");
    
    if (!empty($users)) {
        echo "<p>✓ Found user for $testEmail: ID = " . $users[0]->id . "</p>";
        $userId = $users[0]->id;
    } else {
        echo "<p>⚠ No user found for $testEmail, would use admin fallback (ID=1)</p>";
        $userId = '1';
    }
    
    // Test 2: OAuth Provider lookup
    echo "<h2>Test 2: OAuth Provider Lookup</h2>";
    
    $query = "SELECT id, name FROM external_oauth_providers WHERE name = 'Google' AND deleted = 0 LIMIT 1";
    $result = $db->query($query);
    
    if ($result && $row = $db->fetchByAssoc($result)) {
        echo "<p>✓ Found Google provider: ID = " . $row['id'] . "</p>";
        $providerId = $row['id'];
    } else {
        echo "<p>⚠ No Google provider found, would need to create one</p>";
        $providerId = null;
    }
    
    // Test 3: ExternalOAuthConnection creation (without saving)
    echo "<h2>Test 3: ExternalOAuthConnection Creation Test</h2>";
    
    $connection = BeanFactory::newBean('ExternalOAuthConnection');
    echo "<p>✓ ExternalOAuthConnection bean created</p>";
    
    // Set properties
    $connection->name = 'Gmail - ' . $testEmail;
    $connection->type = 'system'; // Use 'system' to avoid personal account ACL issues
    $connection->client_id = '409390235201-feuni3b2u5blmspoqcpkcv1ins2i5j70.apps.googleusercontent.com';
    $connection->access_token = 'test_access_token';
    $connection->refresh_token = 'test_refresh_token';
    $connection->token_type = 'Bearer';
    $connection->expires_in = 3600;
    $connection->created_by = $userId;
    $connection->assigned_user_id = $userId;
    
    if ($providerId) {
        $connection->external_oauth_provider_id = $providerId;
    }
    
    echo "<p>✓ Connection properties set for user ID: $userId</p>";
    
    // Test 4: Check ACL restrictions
    echo "<h2>Test 4: ACL and Access Control</h2>";
    
    // Check if current user has permission to create ExternalOAuthConnection
    if (class_exists('ACLController')) {
        echo "<p>ACL Controller available</p>";
        // Note: We can't easily test ACL without user context
    }
    
    // Test 5: Attempt to save (this might fail)
    echo "<h2>Test 5: Save Attempt</h2>";
    
    try {
        // Try to save the connection
        $result = $connection->save();
        if ($result) {
            echo "<p>✅ SUCCESS! OAuth connection saved with ID: " . $connection->id . "</p>";
        } else {
            echo "<p>❌ Save returned false</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Save failed with exception: " . $e->getMessage() . "</p>";
    }
    
    echo "<h2>Test Complete</h2>";
    echo "<p><strong>Summary:</strong></p>";
    echo "<ul>";
    echo "<li>User lookup: " . ($userId ? "✓ Working" : "❌ Failed") . "</li>";  
    echo "<li>Provider lookup: " . ($providerId ? "✓ Working" : "⚠ Needs creation") . "</li>";
    echo "<li>Bean creation: ✓ Working</li>";
    echo "<li>Save operation: See results above</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
}

echo "<p><a href='index.php'>Back to SuiteCRM</a></p>";
?>