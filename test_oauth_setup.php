<?php
/**
 * Test OAuth Setup and Credentials
 */

if (!defined('sugarEntry') || !sugarEntry) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');
require_once('custom/Extension/application/Ext/Include/ai_secure_config.php');

header('Content-Type: text/html; charset=UTF-8');
echo "<html><head><title>OAuth Setup Test</title></head><body>";
echo "<h1>🔧 OAuth Setup Test</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .warning{color:orange} .info{color:blue}</style>";

try {
    global $current_user;
    
    echo "<h2>👤 User Status</h2>";
    if ($current_user && $current_user->id) {
        echo "<p class='success'>✅ Current User: {$current_user->user_name} (ID: {$current_user->id})</p>";
    } else {
        echo "<p class='error'>❌ No current user found</p>";
    }
    
    echo "<h2>🔑 Credential Status</h2>";
    
    // Test AISecurity credential loading
    AISecurity::loadSecureConfig();
    
    $googleClientId = AISecurity::getApiCredential('google_client_id');
    $googleClientSecret = AISecurity::getApiCredential('google_client_secret');
    $openaiKey = AISecurity::getApiCredential('openai_api_key');
    
    echo "<p class='info'>Google Client ID: " . (empty($googleClientId) ? "❌ Missing" : "✅ Configured (" . substr($googleClientId, 0, 10) . "...)") . "</p>";
    echo "<p class='info'>Google Client Secret: " . (empty($googleClientSecret) ? "❌ Missing" : "✅ Configured (" . substr($googleClientSecret, 0, 10) . "...)") . "</p>";
    echo "<p class='info'>OpenAI API Key: " . (empty($openaiKey) ? "❌ Missing" : "✅ Configured (" . substr($openaiKey, 0, 10) . "...)") . "</p>";
    
    echo "<h2>🌐 OAuth URL Generation Test</h2>";
    
    if (empty($googleClientId)) {
        echo "<p class='error'>❌ Cannot generate OAuth URL - Google Client ID missing</p>";
    } else {
        // Test OAuth URL generation
        $redirectUri = 'http://localhost:8080/oauth2callback.php';
        $state = bin2hex(random_bytes(16));
        
        $params = [
            'client_id' => $googleClientId,
            'redirect_uri' => $redirectUri,
            'scope' => 'https://www.googleapis.com/auth/gmail.readonly https://www.googleapis.com/auth/gmail.send https://www.googleapis.com/auth/userinfo.email',
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state
        ];
        
        $oauthUrl = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
        
        echo "<p class='success'>✅ OAuth URL generated successfully</p>";
        echo "<p><strong>Redirect URI:</strong> {$redirectUri}</p>";
        echo "<p><strong>State Token:</strong> {$state}</p>";
        echo "<p><a href='{$oauthUrl}' target='_blank' style='background:#4285f4;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🔗 Test Gmail OAuth</a></p>";
    }
    
    echo "<h2>📧 Email View Test</h2>";
    
    // Test the custom email view
    require_once('custom/modules/Emails/views/view.list.php');
    $emailView = new CustomEmailsViewList();
    
    // Test hasGmailConnection method using reflection
    $reflection = new ReflectionClass($emailView);
    $hasConnectionMethod = $reflection->getMethod('hasGmailConnection');
    $hasConnectionMethod->setAccessible(true);
    
    $hasConnection = $hasConnectionMethod->invoke($emailView);
    
    echo "<p class='info'>Gmail Connection Status: " . ($hasConnection ? "✅ Connected" : "❌ No valid connection") . "</p>";
    
    // Test OAuth URL generation
    $getOAuthUrlMethod = $reflection->getMethod('getGmailOAuthUrl');
    $getOAuthUrlMethod->setAccessible(true);
    
    $emailOAuthUrl = $getOAuthUrlMethod->invoke($emailView);
    
    echo "<p class='info'>Email View OAuth URL: " . (empty($emailOAuthUrl) ? "❌ Failed to generate" : "✅ Generated successfully") . "</p>";
    
    echo "<h2>🎯 Next Steps</h2>";
    
    if (empty($googleClientId)) {
        echo "<div style='background:#f8d7da;padding:15px;border:1px solid #f5c6cb;border-radius:4px'>";
        echo "<h3>❌ Missing Google Credentials</h3>";
        echo "<p>You need to configure Google OAuth credentials:</p>";
        echo "<ol>";
        echo "<li>Create a <strong>.env</strong> file in your SuiteCRM root directory</li>";
        echo "<li>Add these lines:<br><code>GOOGLE_CLIENT_ID=your_client_id_here<br>GOOGLE_CLIENT_SECRET=your_client_secret_here</code></li>";
        echo "<li>Restart your web server</li>";
        echo "</ol>";
        echo "</div>";
    } else if (!$hasConnection) {
        echo "<div style='background:#fff3cd;padding:15px;border:1px solid #ffeaa7;border-radius:4px'>";
        echo "<h3>⚠️ Ready for OAuth Authorization</h3>";
        echo "<p>Your credentials are configured. You can now:</p>";
        echo "<ol>";
        echo "<li>Click the OAuth test link above to authorize Gmail access</li>";
        echo "<li>Or go to the <a href='index.php?module=Emails&action=index'>Emails module</a> to start the authorization flow</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div style='background:#d4edda;padding:15px;border:1px solid #c3e6cb;border-radius:4px'>";
        echo "<h3>✅ OAuth Setup Complete</h3>";
        echo "<p>You have a valid Gmail connection. You can:</p>";
        echo "<ol>";
        echo "<li>Go to the <a href='index.php?module=Emails&action=index'>Emails module</a> to view your emails</li>";
        echo "<li>Test the AI email analysis features</li>";
        echo "</ol>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "<br><p><a href='cleanup_oauth_tokens.php' style='background:#6c757d;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🧹 OAuth Cleanup</a>";
echo " <a href='debug_oauth_status.php' style='background:#007bff;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🔍 Debug Status</a>";
echo " <a href='index.php' style='background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🏠 Home</a></p>";

echo "</body></html>";
?>