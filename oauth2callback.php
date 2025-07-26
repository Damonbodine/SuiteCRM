<?php
error_log("oauth2callback.php: File accessed - starting processing");

// Force SuiteCRM bootstrap
define('sugarEntry', true);
require_once('include/entryPoint.php');

error_log("oauth2callback.php: SuiteCRM bootstrap complete");

// Simple OAuth callback handler that redirects to SuiteCRM entry point
if (!empty($_GET['code'])) {
    error_log("oauth2callback.php: Authorization code received: " . substr($_GET['code'], 0, 20) . "...");
    
    // Forward all parameters to our SuiteCRM entry point
    $params = $_GET;
    $query = http_build_query($params);
    $redirectUrl = 'oauth_complete.php?' . $query;
    
    // Debug: Let's see what we're doing
    error_log("oauth2callback.php: Redirecting to " . $redirectUrl);
    
    header('Location: ' . $redirectUrl);
    error_log("oauth2callback.php: Redirect header sent, exiting");
    exit;
} else {
    error_log("oauth2callback.php: No authorization code - handling error case");
    // Handle error case
    $error = $_GET['error'] ?? 'Unknown error';
    echo "OAuth Error: " . htmlspecialchars($error);
    echo "<br><br>DEBUG: Available parameters: " . print_r($_GET, true);
}
?>