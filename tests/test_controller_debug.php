<?php
/**
 * Test if custom controller loads
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

echo "<h1>Controller Debug Test</h1>";

// Test controller loading
echo "<h2>1. Controller File Check</h2>";
$controllerFile = 'custom/modules/Cases/controller.php';
echo "Custom controller exists: " . (file_exists($controllerFile) ? "✅ YES" : "❌ NO") . "<br>";

if (file_exists($controllerFile)) {
    echo "Loading custom controller...<br>";
    try {
        require_once($controllerFile);
        echo "✅ Controller loaded successfully<br>";
        
        if (class_exists('CustomCasesController')) {
            echo "✅ CustomCasesController class found<br>";
            
            $controller = new CustomCasesController();
            if (method_exists($controller, 'action_aiWinnabilityAnalysis')) {
                echo "✅ action_aiWinnabilityAnalysis method exists<br>";
            } else {
                echo "❌ action_aiWinnabilityAnalysis method NOT found<br>";
            }
        } else {
            echo "❌ CustomCasesController class NOT found<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error loading controller: " . $e->getMessage() . "<br>";
    }
}

echo "<h2>2. Action File Check</h2>";
$actionFile = 'custom/modules/Cases/aiWinnabilityAnalysis.php';
echo "Action file exists: " . (file_exists($actionFile) ? "✅ YES" : "❌ NO") . "<br>";

echo "<h2>3. SuiteCRM Controller Resolution</h2>";
// Simulate what SuiteCRM does
$_REQUEST['module'] = 'Cases';
$_REQUEST['action'] = 'aiWinnabilityAnalysis';

echo "Simulating request: module=Cases, action=aiWinnabilityAnalysis<br>";

// Test if SuiteCRM can resolve the controller
require_once('include/MVC/Controller/ControllerFactory.php');
try {
    $controller = ControllerFactory::getController($_REQUEST['module']);
    echo "✅ Controller factory worked<br>";
    echo "Controller class: " . get_class($controller) . "<br>";
    
    if (method_exists($controller, 'action_' . strtolower($_REQUEST['action']))) {
        echo "✅ Action method found in controller<br>";
    } else {
        echo "❌ Action method NOT found in controller<br>";
    }
} catch (Exception $e) {
    echo "❌ Controller factory error: " . $e->getMessage() . "<br>";
}

echo "<br>Debug complete.";
?>