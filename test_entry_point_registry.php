<?php
// Test entry point registry loading

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<h1>Entry Point Registry Test</h1>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

// Try to create a controller and load the entry point registry
require_once('include/MVC/Controller/SugarController.php');

$controller = new SugarController();

// Use reflection to access the private loadMapping method
$reflection = new ReflectionClass('SugarController');
$loadMappingMethod = $reflection->getMethod('loadMapping');
$loadMappingMethod->setAccessible(true);

echo "<h2>Loading Entry Point Registry</h2>";

try {
    // Load the entry point registry
    $loadMappingMethod->invoke($controller, 'entry_point_registry');
    
    // Access the private entry_point_registry property
    $registryProperty = $reflection->getProperty('entry_point_registry');
    $registryProperty->setAccessible(true);
    $registry = $registryProperty->getValue($controller);
    
    echo "<p>✓ Entry point registry loaded successfully</p>";
    echo "<p>Total entry points: " . count($registry) . "</p>";
    
    echo "<h3>Available Entry Points:</h3>";
    echo "<ul>";
    foreach ($registry as $name => $config) {
        echo "<li><strong>$name</strong> - " . ($config['file'] ?? 'no file') . "</li>";
    }
    echo "</ul>";
    
    // Check our specific entry points
    echo "<h3>Our Entry Points Status:</h3>";
    
    $ourEntryPoints = ['GoogleOAuthCallback', 'GoogleOAuthCallbackTest'];
    foreach ($ourEntryPoints as $entryPoint) {
        if (isset($registry[$entryPoint])) {
            echo "<p>✓ <strong>$entryPoint</strong> is registered</p>";
            echo "<pre>" . print_r($registry[$entryPoint], true) . "</pre>";
        } else {
            echo "<p>✗ <strong>$entryPoint</strong> is NOT registered</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>✗ Error loading entry point registry: " . $e->getMessage() . "</p>";
    echo "<p>Trace: " . $e->getTraceAsString() . "</p>";
}

echo "<h2>Manual Entry Point Test</h2>";

// Try to manually call the handleEntryPoint method
try {
    $_REQUEST['entryPoint'] = 'GoogleOAuthCallbackTest';
    
    $handleMethod = $reflection->getMethod('handleEntryPoint');
    $handleMethod->setAccessible(true);
    
    echo "<p>Attempting to handle GoogleOAuthCallbackTest entry point...</p>";
    $handleMethod->invoke($controller);
    echo "<p>Entry point handling completed</p>";
    
} catch (Exception $e) {
    echo "<p>✗ Error handling entry point: " . $e->getMessage() . "</p>";
}

echo "<h2>Test Complete</h2>";
echo "<p><a href='index.php'>Back to SuiteCRM</a></p>";
?>