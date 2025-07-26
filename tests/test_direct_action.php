<?php
/**
 * Test direct action call
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

echo "<h1>Direct Action Test</h1>";

// Set up request parameters
$_REQUEST['module'] = 'Cases';
$_REQUEST['action'] = 'aiWinnabilityAnalysis';
$_REQUEST['record'] = 'ai-test-case-002';

echo "Testing direct action call with case: ai-test-case-002<br>";

// Try to call the controller directly
require_once('custom/modules/Cases/controller.php');

try {
    $controller = new CustomCasesController();
    
    if (method_exists($controller, 'action_aiWinnabilityAnalysis')) {
        echo "✅ Method exists, calling action...<br>";
        
        // Call the action directly
        $controller->action_aiWinnabilityAnalysis();
        
        echo "✅ Action completed!<br>";
        
        // Check if debug logs were created
        if (file_exists('/tmp/controller_debug.log')) {
            echo "✅ Controller debug log created<br>";
            echo "Log content: " . file_get_contents('/tmp/controller_debug.log') . "<br>";
        }
        
        if (file_exists('/tmp/ai_analysis_debug.log')) {
            echo "✅ AI analysis debug log created<br>";
            echo "Log content: " . file_get_contents('/tmp/ai_analysis_debug.log') . "<br>";
        }
        
    } else {
        echo "❌ Method doesn't exist<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error calling action: " . $e->getMessage() . "<br>";
}

echo "<br>Test complete.";
?>