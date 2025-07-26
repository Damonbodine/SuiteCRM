<?php
/**
 * Debug Winnability Analysis Authentication
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session first
session_start();

// Bootstrap SuiteCRM
define('sugarEntry', true);
require_once('include/entryPoint.php');

echo "<h2>Winnability Analysis Debug</h2>";
echo "<hr>";

// Check authentication status
global $current_user;
echo "<h3>Authentication Status:</h3>";
echo "User ID: " . ($current_user->id ?? 'NULL') . "<br>";
echo "User Name: " . ($current_user->user_name ?? 'NULL') . "<br>";
echo "Is Admin: " . (($current_user->is_admin ?? false) ? 'Yes' : 'No') . "<br>";
echo "Is Authenticated: " . ((!empty($current_user) && !empty($current_user->id)) ? 'Yes' : 'No') . "<br>";

echo "<h3>Session Info:</h3>";
echo "Session Status: " . session_status() . "<br>";
if (session_status() != PHP_SESSION_NONE) {
    echo "Session ID: " . session_id() . "<br>";
}

echo "<h3>Request Info:</h3>";
echo "Request Method: " . $_SERVER['REQUEST_METHOD'] . "<br>";
echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "<br>";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";

if (!empty($_POST)) {
    echo "<h3>POST Data:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
}

// Test case loading if we have a case ID
if (!empty($_GET['case_id']) || !empty($_POST['record'])) {
    $caseId = $_GET['case_id'] ?? $_POST['record'] ?? '';
    echo "<h3>Testing Case Loading:</h3>";
    echo "Case ID: " . htmlspecialchars($caseId) . "<br>";
    
    $case = BeanFactory::getBean('Cases', $caseId);
    if (!empty($case) && !empty($case->id)) {
        echo "✅ Case loaded successfully<br>";
        echo "Case Name: " . htmlspecialchars($case->name ?? 'N/A') . "<br>";
        echo "Case Type: " . htmlspecialchars($case->type ?? 'N/A') . "<br>";
        
        // Check ACL
        if ($case->ACLAccess('edit')) {
            echo "✅ User has edit access<br>";
        } else {
            echo "❌ User does not have edit access<br>";
        }
    } else {
        echo "❌ Case not found or failed to load<br>";
    }
}

echo "<hr>";
echo "<h3>Test Winnability Analysis:</h3>";
echo "<p>To test the analysis, <a href='?case_id=ai-test-case-001'>click here with test case ID</a></p>";

// Form to test POST submission
echo "<form method='post' action='test_winnability_debug.php'>";
echo "<input type='hidden' name='record' value='ai-test-case-001'>";
echo "<input type='submit' value='Test POST Submission' style='padding: 10px; background: #007cba; color: white; border: none; cursor: pointer;'>";
echo "</form>";

echo "<hr>";
echo "<p><strong>Next Step:</strong> If authentication shows 'Yes', try clicking the 'Test POST Submission' button to see what happens.</p>";
?>