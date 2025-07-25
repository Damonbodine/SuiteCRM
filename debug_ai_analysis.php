<?php
/**
 * Debug AI Analysis Step by Step
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

echo "<h1>Debugging AI Analysis</h1>";

$caseId = 'ai-test-case-001';

require_once('custom/include/ai/CaseStatusAnalyzer.php');
$analyzer = new CaseWinnabilityAnalyzer();

// Use reflection to access private methods for debugging
$reflection = new ReflectionClass($analyzer);

echo "<h2>1. Testing validateInput</h2>";
$validateMethod = $reflection->getMethod('validateInput');
$validateMethod->setAccessible(true);
$validResult = $validateMethod->invoke($analyzer, $caseId);
echo "Validation result: " . ($validResult ? "✅ PASS" : "❌ FAIL") . "<br>";

echo "<h2>2. Testing loadCaseSecurely</h2>";
$loadMethod = $reflection->getMethod('loadCaseSecurely');
$loadMethod->setAccessible(true);
$caseBean = $loadMethod->invoke($analyzer, $caseId);
echo "Case loading result: " . ($caseBean ? "✅ PASS" : "❌ FAIL") . "<br>";

if ($caseBean) {
    echo "<h2>3. Testing checkCaseAccess</h2>";
    $accessMethod = $reflection->getMethod('checkCaseAccess');
    $accessMethod->setAccessible(true);
    $accessResult = $accessMethod->invoke($analyzer, $caseBean);
    echo "Access check result: " . ($accessResult ? "✅ PASS" : "❌ FAIL") . "<br>";
}

echo "<h2>4. Current User</h2>";
global $current_user;
if ($current_user && $current_user->id) {
    echo "User ID: " . $current_user->id . "<br>";
    echo "User Name: " . $current_user->user_name . "<br>";
} else {
    echo "❌ No authenticated user found<br>";
}

echo "<h2>Debug Complete</h2>";
?>