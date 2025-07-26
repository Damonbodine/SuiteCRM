<?php
/**
 * Direct OAuth Completion Handler
 * Bypasses SuiteCRM's entry point system to directly handle OAuth completion
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

// Basic environment setup
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';

require_once('include/entryPoint.php');

// Force output to prevent hanging
header('Content-Type: text/html; charset=UTF-8');
echo "<!DOCTYPE html><html><head><title>OAuth Completion</title></head><body>";
echo "<h1>🔄 Processing OAuth...</h1>";
flush();

try {
    global $current_user, $db, $log;
    
    echo "<p>✓ Bootstrap complete</p>";
    flush();
    
    // Validate parameters
    if (empty($_GET['code'])) {
        throw new Exception('Missing authorization code');
    }
    
    if (empty($_GET['state'])) {
        throw new Exception('Missing state parameter');
    }
    
    echo "<p>✓ Parameters validated</p>";
    flush();
    
    // Load secure config
    require_once('custom/Extension/application/Ext/Include/ai_secure_config.php');
    AISecurity::loadSecureConfig();
    
    $clientId = AISecurity::getApiCredential('google_client_id');
    $clientSecret = AISecurity::getApiCredential('google_client_secret');
    
    if (empty($clientId) || empty($clientSecret)) {
        throw new Exception('OAuth credentials not configured');
    }
    
    echo "<p>✓ Credentials loaded</p>";
    flush();
    
    // Exchange code for tokens
    echo "<p>🔄 Exchanging code for tokens...</p>";
    flush();
    
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $redirectUri = 'http://localhost:8080/oauth2callback.php';
    
    $postData = [
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'code' => $_GET['code'],
        'grant_type' => 'authorization_code',
        'redirect_uri' => $redirectUri
    ];
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $tokenUrl,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($postData),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/x-www-form-urlencoded',
            'Accept: application/json'
        ]
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        throw new Exception("Token exchange failed - HTTP {$httpCode}: {$response}");
    }
    
    $tokens = json_decode($response, true);
    if (!$tokens || !isset($tokens['access_token'])) {
        throw new Exception('Invalid token response from Google');
    }
    
    echo "<p>✓ Tokens received successfully</p>";
    flush();
    
    // Determine user ID
    $userId = null;
    if ($current_user && $current_user->id) {
        $userId = $current_user->id;
    } else {
        // Fallback to admin user
        $result = $db->query("SELECT id FROM users WHERE status = 'Active' AND deleted = 0 AND is_admin = 1 LIMIT 1");
        if ($result && $row = $db->fetchByAssoc($result)) {
            $userId = $row['id'];
        }
    }
    
    if (!$userId) {
        throw new Exception('Unable to determine user ID');
    }
    
    echo "<p>✓ User ID determined: {$userId}</p>";
    flush();
    
    // Clean up old connections
    echo "<p>🧹 Cleaning up old connections...</p>";
    flush();
    
    $cleanupQuery = "UPDATE external_oauth_connections 
                    SET deleted = 1, date_modified = NOW() 
                    WHERE assigned_user_id = '{$userId}' 
                    AND name LIKE '%Gmail%' 
                    AND deleted = 0";
    $db->query($cleanupQuery);
    
    // Create new connection
    echo "<p>💾 Creating OAuth connection...</p>";
    flush();
    
    $connectionId = create_guid();
    $expiresAt = date('Y-m-d H:i:s', time() + ($tokens['expires_in'] ?? 3600));
    
    // Use base64 encoding to safely store tokens with special characters
    $accessTokenSafe = base64_encode($tokens['access_token']);
    $refreshTokenSafe = base64_encode($tokens['refresh_token'] ?? '');
    $expiresIn = intval($tokens['expires_in'] ?? 3600);
    
    // Use proper string escaping for SQL - addslashes should work for these base64 values
    $connectionIdEsc = addslashes($connectionId);
    $userIdEsc = addslashes($userId);  
    $accessTokenEsc = addslashes($accessTokenSafe);
    $refreshTokenEsc = addslashes($refreshTokenSafe);
    $expiresAtEsc = addslashes($expiresAt);
    
    $insertQuery = "INSERT INTO external_oauth_connections 
                   (id, name, date_entered, date_modified, assigned_user_id, created_by, 
                    type, access_token, refresh_token, token_type, expires_in, 
                    access_token_expires, deleted) 
                   VALUES 
                   ('$connectionIdEsc', 'Gmail OAuth Connection', NOW(), NOW(), '$userIdEsc', '$userIdEsc', 
                    'system', '$accessTokenEsc', '$refreshTokenEsc', 'Bearer', 
                    $expiresIn, '$expiresAtEsc', 0)";
    
    $result = $db->query($insertQuery);
    if (!$result) {
        throw new Exception('Failed to save OAuth connection: ' . $db->lastError());
    }
    
    echo "<p>✅ OAuth connection saved successfully!</p>";
    echo "<p><strong>Connection ID:</strong> {$connectionId}</p>";
    echo "<p><strong>Expires:</strong> {$expiresAt}</p>";
    flush();
    
    // Verify the connection
    $verifyQuery = "SELECT * FROM external_oauth_connections WHERE id = '$connectionIdEsc'";
    $verifyResult = $db->query($verifyQuery);
    if ($verifyResult && $row = $db->fetchByAssoc($verifyResult)) {
        echo "<p>✅ Connection verified in database</p>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($row['name']) . "</p>";
        echo "<p><strong>User ID:</strong> " . htmlspecialchars($row['assigned_user_id']) . "</p>";
        echo "<p><strong>Deleted:</strong> " . ($row['deleted'] ? 'Yes' : 'No') . "</p>";
    }
    
    echo "<h2>🎉 OAuth Setup Complete!</h2>";
    echo "<p>Your Gmail account has been successfully connected to SuiteCRM.</p>";
    echo "<p>You can now go to the Emails module to view your emails with AI analysis.</p>";
    
    echo "<p><a href='index.php?module=Emails&action=index' style='background:#28a745;color:white;padding:15px 20px;text-decoration:none;border-radius:4px;font-size:16px'>📧 Go to Emails Module</a></p>";
    
    // Log success
    if ($log) {
        $log->info("OAuth Complete: Successfully created connection {$connectionId} for user {$userId}");
    }
    
} catch (Exception $e) {
    echo "<h2>❌ OAuth Error</h2>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please try the OAuth process again.</p>";
    echo "<p><a href='index.php?module=Emails&action=index' style='background:#dc3545;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🔄 Try Again</a></p>";
    
    if (isset($log)) {
        $log->error("OAuth Complete: Error - " . $e->getMessage());
    }
}

echo "</body></html>";
?>