<?php
/**
 * Integration Test for Billable Hours Quick Entry Dashlet
 * Tests the complete workflow from dashlet creation to task storage
 */

require_once 'tests/_bootstrap.php';

class BillableHoursIntegrationTest
{
    private $testCaseId;
    private $testTaskIds = [];
    private $dashletId;

    public function __construct()
    {
        $this->dashletId = 'integration_test_' . uniqid();
        echo "Starting Billable Hours Dashlet Integration Test...\n";
    }

    public function runAllTests()
    {
        try {
            $this->testDashletClassLoading();
            $this->testDashletInstantiation();
            $this->testDashletDisplay();
            $this->testDashletScript();
            $this->testCaseRetrieval();
            $this->testTimeEntrySave();
            $this->testTaskCreationAndLinking();
            $this->testDashletConfiguration();
            $this->testCleanup();
            
            echo "\n✅ All integration tests passed!\n";
            return true;
            
        } catch (Exception $e) {
            echo "\n❌ Integration test failed: " . $e->getMessage() . "\n";
            $this->cleanup();
            return false;
        }
    }

    private function testDashletClassLoading()
    {
        echo "Testing dashlet class loading... ";
        
        $classFile = 'modules/Home/Dashlets/BillableHoursQuickEntryDashlet/BillableHoursQuickEntryDashlet.php';
        if (!file_exists($classFile)) {
            throw new Exception("Dashlet class file not found: $classFile");
        }
        
        require_once $classFile;
        
        if (!class_exists('BillableHoursQuickEntryDashlet')) {
            throw new Exception("Dashlet class not found after include");
        }
        
        echo "✓\n";
    }

    private function testDashletInstantiation()
    {
        echo "Testing dashlet instantiation... ";
        
        $dashlet = new BillableHoursQuickEntryDashlet($this->dashletId);
        
        if (!$dashlet instanceof BillableHoursQuickEntryDashlet) {
            throw new Exception("Failed to instantiate dashlet");
        }
        
        if (!$dashlet instanceof Dashlet) {
            throw new Exception("Dashlet does not extend base Dashlet class");
        }
        
        // Test properties
        if ($dashlet->defaultActivityType !== 'client_meeting') {
            throw new Exception("Default activity type not set correctly");
        }
        
        if ($dashlet->defaultRate !== '250.00') {
            throw new Exception("Default rate not set correctly");
        }
        
        echo "✓\n";
    }

    private function testDashletDisplay()
    {
        echo "Testing dashlet display output... ";
        
        $dashlet = new BillableHoursQuickEntryDashlet($this->dashletId);
        $html = $dashlet->display();
        
        if (empty($html)) {
            throw new Exception("Display method returned empty content");
        }
        
        // Check for required elements
        $required = [
            'billable-hours-dashlet',
            'case_select_',
            'activity_type_',
            'duration_',
            'description_',
            'log_time_btn_'
        ];
        
        foreach ($required as $element) {
            if (strpos($html, $element) === false) {
                throw new Exception("Required element '$element' not found in display output");
            }
        }
        
        echo "✓\n";
    }

    private function testDashletScript()
    {
        echo "Testing dashlet JavaScript output... ";
        
        $dashlet = new BillableHoursQuickEntryDashlet($this->dashletId);
        $script = $dashlet->displayScript();
        
        if (empty($script)) {
            throw new Exception("DisplayScript method returned empty content");
        }
        
        // Check for required JavaScript functions
        $required = [
            'BillableHours',
            'startTimer',
            'stopTimer',
            'logTime',
            'clearForm'
        ];
        
        foreach ($required as $function) {
            if (strpos($script, $function) === false) {
                throw new Exception("Required JavaScript function '$function' not found");
            }
        }
        
        echo "✓\n";
    }

    private function testCaseRetrieval()
    {
        echo "Testing case retrieval... ";
        
        // Create a test case
        $case = BeanFactory::newBean('Cases');
        $case->name = 'Integration Test Case';
        $case->case_number = 'IT-' . date('Y') . '-001';
        $case->status = 'Open';
        $case->assigned_user_id = $GLOBALS['current_user']->id;
        $case->save();
        
        if (empty($case->id)) {
            throw new Exception("Failed to create test case");
        }
        
        $this->testCaseId = $case->id;
        
        // Test getActiveCases method
        $dashlet = new BillableHoursQuickEntryDashlet($this->dashletId);
        $reflection = new ReflectionClass($dashlet);
        $method = $reflection->getMethod('getActiveCases');
        $method->setAccessible(true);
        
        $cases = $method->invoke($dashlet);
        
        if (!is_array($cases)) {
            throw new Exception("getActiveCases did not return an array");
        }
        
        if (!isset($cases[$case->id])) {
            throw new Exception("Created test case not found in active cases");
        }
        
        echo "✓\n";
    }

    private function testTimeEntrySave()
    {
        echo "Testing time entry save functionality... ";
        
        // Mock $_REQUEST for saveTimeEntry
        $_REQUEST = [
            'duration' => '2.5',
            'activity_type' => 'client_meeting',
            'description' => 'Integration test time entry',
            'entry_date' => date('Y-m-d'),
            'entry_time' => '14:30',
            'hourly_rate' => '275.00',
            'case_id' => $this->testCaseId
        ];
        
        $dashlet = new BillableHoursQuickEntryDashlet($this->dashletId);
        
        // Capture output
        ob_start();
        $dashlet->saveTimeEntry();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        
        if (!$response) {
            throw new Exception("saveTimeEntry did not return valid JSON: $output");
        }
        
        if (!$response['success']) {
            throw new Exception("saveTimeEntry failed: " . ($response['message'] ?? 'Unknown error'));
        }
        
        if (empty($response['task_id'])) {
            throw new Exception("saveTimeEntry did not return task_id");
        }
        
        if ($response['duration'] != 2.5) {
            throw new Exception("Incorrect duration in response: " . $response['duration']);
        }
        
        if ($response['amount'] != '687.50') {
            throw new Exception("Incorrect amount calculation: " . $response['amount']);
        }
        
        $this->testTaskIds[] = $response['task_id'];
        
        echo "✓\n";
    }

    private function testTaskCreationAndLinking()
    {
        echo "Testing task creation and case linking... ";
        
        if (empty($this->testTaskIds)) {
            throw new Exception("No test task IDs available");
        }
        
        $taskId = $this->testTaskIds[0];
        $task = BeanFactory::retrieveBean('Tasks', $taskId);
        
        if (!$task) {
            throw new Exception("Could not retrieve created task");
        }
        
        if ($task->status !== 'Completed') {
            throw new Exception("Task status not set to Completed");
        }
        
        if ($task->parent_type !== 'Cases') {
            throw new Exception("Task not linked to Cases module");
        }
        
        if ($task->parent_id !== $this->testCaseId) {
            throw new Exception("Task not linked to correct case");
        }
        
        if (strpos($task->name, 'Billable Time Entry') === false) {
            throw new Exception("Task name does not contain expected text");
        }
        
        if (strpos($task->description, 'Integration test time entry') === false) {
            throw new Exception("Task description does not contain expected text");
        }
        
        echo "✓\n";
    }

    private function testDashletConfiguration()
    {
        echo "Testing dashlet configuration... ";
        
        $config = [
            'title' => 'Test Configuration',
            'defaultActivityType' => 'trial_prep',
            'defaultRate' => '350.00',
            'autoSave' => false
        ];
        
        $dashlet = new BillableHoursQuickEntryDashlet($this->dashletId, $config);
        
        if ($dashlet->title !== 'Test Configuration') {
            throw new Exception("Title configuration not applied");
        }
        
        if ($dashlet->defaultActivityType !== 'trial_prep') {
            throw new Exception("Default activity type configuration not applied");
        }
        
        if ($dashlet->defaultRate !== '350.00') {
            throw new Exception("Default rate configuration not applied");
        }
        
        if ($dashlet->autoSave !== false) {
            throw new Exception("Auto-save configuration not applied");
        }
        
        // Test saveOptions method
        $_REQUEST = [
            'title' => 'Saved Configuration',
            'defaultActivityType' => 'investigation',
            'defaultRate' => '400.00',
            'autoSave' => '1'
        ];
        
        $options = $dashlet->saveOptions($_REQUEST);
        
        if ($options['title'] !== 'Saved Configuration') {
            throw new Exception("saveOptions did not process title correctly");
        }
        
        if ($options['defaultActivityType'] !== 'investigation') {
            throw new Exception("saveOptions did not process defaultActivityType correctly");
        }
        
        if ($options['defaultRate'] !== '400.00') {
            throw new Exception("saveOptions did not process defaultRate correctly");
        }
        
        if ($options['autoSave'] !== true) {
            throw new Exception("saveOptions did not process autoSave correctly");
        }
        
        echo "✓\n";
    }

    private function testCleanup()
    {
        echo "Testing cleanup functionality... ";
        $this->cleanup();
        echo "✓\n";
    }

    private function cleanup()
    {
        // Clean up test tasks
        foreach ($this->testTaskIds as $taskId) {
            $task = BeanFactory::retrieveBean('Tasks', $taskId);
            if ($task) {
                $task->mark_deleted($taskId);
            }
        }
        
        // Clean up test case
        if ($this->testCaseId) {
            $case = BeanFactory::retrieveBean('Cases', $this->testCaseId);
            if ($case) {
                $case->mark_deleted($this->testCaseId);
            }
        }
    }

    public function __destruct()
    {
        $this->cleanup();
    }
}

// Run the integration test if called directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    global $current_user;
    
    // Set up environment
    if (!isset($current_user) || !$current_user) {
        $current_user = BeanFactory::newBean('Users');
        $current_user->id = '1'; // Admin user
        $current_user->user_name = 'admin';
    }
    
    $test = new BillableHoursIntegrationTest();
    $success = $test->runAllTests();
    
    exit($success ? 0 : 1);
}