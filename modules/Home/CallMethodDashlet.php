<?php
/**
 * CallMethodDashlet Action for SuiteCRM
 * Handles AJAX calls to dashlet methods
 * 
 * This action file allows JavaScript to call specific methods on dashlet instances
 * via AJAX requests. Used by dashlets like BillableHoursQuickEntryDashlet for
 * PDF generation and other dynamic functionality.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

// Ensure user is authenticated
global $current_user;
if (empty($current_user->id)) {
    echo json_encode(array('error' => 'Authentication required'));
    sugar_die('Authentication required');
}

// Get required parameters
$dashletId = $_REQUEST['dashlet_id'] ?? '';
$method = $_REQUEST['method'] ?? '';

if (empty($dashletId) || empty($method)) {
    echo json_encode(array('error' => 'Missing required parameters: dashlet_id and method'));
    sugar_die('Missing required parameters');
}

try {
    // Determine dashlet class name
    // SuiteCRM can use different dashlet ID formats:
    // 1. UUID format (e.g., 24746e05-bb10-8f52-bf79-688151126b1a)
    // 2. Traditional format (e.g., DashletType_0_1234567890)
    
    $dashletClass = '';
    
    // Check if it's a UUID format (dashlets in SuiteCRM often use UUIDs)
    if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $dashletId)) {
        // For UUID format, we need to look up the dashlet type from the database or request
        // Check if dashlet class is specified in the request
        if (!empty($_REQUEST['dashlet_class'])) {
            $dashletClass = $_REQUEST['dashlet_class'];
        } else {
            // Try to determine from the referrer or context
            // For our billable hours dashlet, default to the known class
            $dashletClass = 'BillableHoursQuickEntryDashlet';
        }
    } else {
        // Traditional format: DashletType_0_1234567890
        $dashletParts = explode('_', $dashletId);
        
        if (count($dashletParts) < 2) {
            throw new Exception("Invalid dashlet ID format: $dashletId");
        }
        
        // Extract dashlet class name (everything except the last two parts which are position and timestamp)
        for ($i = 0; $i < count($dashletParts) - 2; $i++) {
            if ($i > 0) $dashletClass .= '_';
            $dashletClass .= $dashletParts[$i];
        }
    }
    
    if (empty($dashletClass)) {
        throw new Exception("Could not determine dashlet class from ID: $dashletId");
    }
    
    // Construct path to dashlet file
    $dashletFile = "modules/Home/Dashlets/{$dashletClass}/{$dashletClass}.php";
    
    if (!file_exists($dashletFile)) {
        throw new Exception("Dashlet file not found: $dashletFile");
    }
    
    // Include the dashlet file
    require_once($dashletFile);
    
    // Check if the dashlet class exists
    if (!class_exists($dashletClass)) {
        throw new Exception("Dashlet class not found: $dashletClass");
    }
    
    // Create dashlet instance
    $dashlet = new $dashletClass($dashletId);
    
    // Verify the method exists and is callable
    if (!method_exists($dashlet, $method)) {
        throw new Exception("Method '$method' not found in dashlet class '$dashletClass'");
    }
    
    // Security check - ensure method is not private/protected
    $reflectionMethod = new ReflectionMethod($dashlet, $method);
    if (!$reflectionMethod->isPublic()) {
        throw new Exception("Method '$method' is not publicly accessible");
    }
    
    // Log the method call for debugging
    $GLOBALS['log']->info("CallMethodDashlet: Calling {$dashletClass}::{$method} for user {$current_user->user_name}");
    
    // Call the method
    $result = $dashlet->$method();
    
    // If the method already output content (like PDF generation), don't echo anything else
    if (headers_sent() || ob_get_length() > 0) {
        // Method handled its own output (like PDF download)
        return;
    }
    
    // If no output was generated, the method might have returned a value
    if ($result !== null) {
        // If result is already JSON, output it directly
        if (is_string($result) && (strpos($result, '{') === 0 || strpos($result, '[') === 0)) {
            echo $result;
        } else {
            // Otherwise encode as JSON
            echo json_encode(array('success' => true, 'result' => $result));
        }
    }
    
} catch (Exception $e) {
    // Log the error
    $GLOBALS['log']->error("CallMethodDashlet Error: " . $e->getMessage());
    
    // Return error response
    echo json_encode(array(
        'error' => $e->getMessage(),
        'success' => false
    ));
}

// End execution
sugar_die('');