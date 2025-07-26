<?php
/**
 * CLI Test for Billable Hours AI Integration
 * Properly bootstraps SuiteCRM and tests AI functionality
 */

// Bootstrap SuiteCRM
if (!defined('sugarEntry')) define('sugarEntry', true);

// Check if we can load the basic config
if (!file_exists('config.php')) {
    echo "❌ ERROR: config.php not found. Run this from SuiteCRM root directory.\n";
    exit(1);
}

echo "🧪 Testing Billable Hours AI Integration\n";
echo "=====================================\n\n";

try {
    // Load SuiteCRM config
    require_once('config.php');
    global $sugar_config;
    echo "✅ SuiteCRM config loaded\n";
    
    // Load database
    require_once('include/database/DBManagerFactory.php');
    $db = DBManagerFactory::getInstance();
    echo "✅ Database connection established\n";
    
    // Load user session (simulate logged in user)
    require_once('modules/Users/User.php');
    global $current_user;
    $current_user = new User();
    $current_user->getSystemUser();
    echo "✅ System user loaded (ID: " . $current_user->id . ")\n";
    
    // Load required classes
    require_once('include/utils.php');
    require_once('include/SugarObjects/SugarBean.php');
    require_once('modules/Cases/Case.php');
    require_once('modules/Tasks/Task.php');
    echo "✅ SuiteCRM classes loaded\n\n";
    
    // Test 1: Load AI Class
    echo "TEST 1: Loading AI Class\n";
    echo "------------------------\n";
    
    if (!file_exists('custom/include/ai/BillableHoursAI.php')) {
        echo "❌ BillableHoursAI.php not found\n";
        exit(1);
    }
    
    require_once('custom/include/ai/BillableHoursAI.php');
    $ai = new BillableHoursAI();
    echo "✅ BillableHoursAI class instantiated\n";
    
    // Test 2: Smart Description Suggestions
    echo "\nTEST 2: Smart Description Suggestions\n";
    echo "------------------------------------\n";
    
    $descriptions = $ai->getSmartDescriptions('court_appearance');
    echo "Activity Type: court_appearance\n";
    echo "Suggestions returned: " . count($descriptions) . "\n";
    
    if (count($descriptions) > 0) {
        echo "✅ Description suggestions working:\n";
        foreach ($descriptions as $i => $desc) {
            echo "   " . ($i + 1) . ". " . $desc . "\n";
        }
    } else {
        echo "❌ No description suggestions returned\n";
    }
    
    // Test 3: Time Prediction
    echo "\nTEST 3: Time Prediction\n";
    echo "-----------------------\n";
    
    $prediction = $ai->predictTimeDuration('client_consultation');
    echo "Activity Type: client_consultation\n";
    echo "Predicted Hours: " . $prediction['predicted_hours'] . "\n";
    echo "Confidence: " . round($prediction['confidence'] * 100) . "%\n";
    echo "Reasoning: " . $prediction['reasoning'] . "\n";
    
    if ($prediction['predicted_hours'] > 0) {
        echo "✅ Time prediction working\n";
    } else {
        echo "❌ Time prediction failed\n";
    }
    
    // Test 4: Activity Type Suggestion
    echo "\nTEST 4: Activity Type Suggestion\n";
    echo "--------------------------------\n";
    
    $testDescription = "reviewed police reports and prepared motion to suppress evidence";
    $suggestions = $ai->suggestActivityType($testDescription);
    echo "Description: '$testDescription'\n";
    echo "Activity suggestions: " . count($suggestions) . "\n";
    
    if (count($suggestions) > 0) {
        echo "✅ Activity suggestions working:\n";
        foreach ($suggestions as $suggestion) {
            echo "   - " . $suggestion['label'] . " (" . round($suggestion['confidence'] * 100) . "% confident)\n";
        }
    } else {
        echo "❌ No activity suggestions returned\n";
    }
    
    // Test 5: Database Integration
    echo "\nTEST 5: Database Integration\n";
    echo "---------------------------\n";
    
    // Check for cases
    $query = "SELECT COUNT(*) as case_count FROM cases WHERE deleted = 0";
    $result = $db->query($query);
    $row = $db->fetchByAssoc($result);
    echo "Total cases in database: " . $row['case_count'] . "\n";
    
    // Check for existing time entries
    $query = "SELECT COUNT(*) as task_count FROM tasks WHERE name LIKE 'Billable Time Entry%' AND deleted = 0";
    $result = $db->query($query);
    $row = $db->fetchByAssoc($result);
    echo "Existing billable time entries: " . $row['task_count'] . "\n";
    
    if ($row['case_count'] > 0) {
        echo "✅ Database integration working\n";
        
        // Test case-specific suggestions
        $query = "SELECT id, name FROM cases WHERE deleted = 0 LIMIT 1";
        $result = $db->query($query);
        if ($testCase = $db->fetchByAssoc($result)) {
            echo "\nTesting case-specific suggestions with case: " . $testCase['name'] . "\n";
            $caseSpecific = $ai->getSmartDescriptions('case_research', $testCase['id']);
            echo "Case-specific suggestions: " . count($caseSpecific) . "\n";
        }
    } else {
        echo "⚠️  No cases found in database - case-specific features won't work\n";
    }
    
    // Test 6: Entry Point File
    echo "\nTEST 6: Entry Point Configuration\n";
    echo "---------------------------------\n";
    
    if (file_exists('custom/include/entryPoints/aiBillableHours.php')) {
        echo "✅ Entry point file exists\n";
    } else {
        echo "❌ Entry point file missing\n";
    }
    
    if (file_exists('custom/include/MVC/Controller/entry_point_registry.php')) {
        echo "✅ Entry point registry exists\n";
        $registryContent = file_get_contents('custom/include/MVC/Controller/entry_point_registry.php');
        if (strpos($registryContent, 'aiBillableHours') !== false) {
            echo "✅ aiBillableHours endpoint registered\n";
        } else {
            echo "❌ aiBillableHours endpoint not registered\n";
        }
    } else {
        echo "❌ Entry point registry missing\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "🎉 AI INTEGRATION TEST COMPLETE\n";
    echo "The AI features should now work in the billable hours dashlet!\n";
    echo "\nNext steps:\n";
    echo "1. Clear your browser cache\n";
    echo "2. Add the Billable Hours dashlet to your dashboard\n";
    echo "3. Click 'AI Assist' button\n";
    echo "4. Test the AI suggestions\n";
    
} catch (Exception $e) {
    echo "❌ FATAL ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
?>