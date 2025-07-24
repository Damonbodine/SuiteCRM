<?php

use SuiteCRM\Test\SuitePHPUnitFrameworkTestCase;

/**
 * Unit Tests for BillableHoursQuickEntryDashlet
 * Tests criminal defense attorney time tracking functionality
 */
class BillableHoursQuickEntryDashletTest extends SuitePHPUnitFrameworkTestCase
{
    /**
     * @var BillableHoursQuickEntryDashlet
     */
    protected $dashlet;
    
    /**
     * @var string
     */
    protected $dashletId;

    protected function setUp(): void
    {
        parent::setUp();
        
        global $current_user;
        get_sugar_config_defaults();
        $current_user = BeanFactory::newBean('Users');
        $current_user->user_name = 'test_attorney';
        $current_user->id = create_guid();
        
        // Set up global strings
        $GLOBALS['mod_strings'] = return_module_language($GLOBALS['current_language'], 'Home');
        
        // Include the dashlet class
        require_once 'modules/Home/Dashlets/BillableHoursQuickEntryDashlet/BillableHoursQuickEntryDashlet.php';
        
        $this->dashletId = 'test_dashlet_' . uniqid();
        $this->dashlet = new BillableHoursQuickEntryDashlet($this->dashletId);
    }

    protected function tearDown(): void
    {
        // Clean up any created tasks
        if (isset($this->createdTaskIds)) {
            foreach ($this->createdTaskIds as $taskId) {
                $task = BeanFactory::retrieveBean('Tasks', $taskId);
                if ($task) {
                    $task->mark_deleted($taskId);
                }
            }
        }
        
        parent::tearDown();
    }

    /**
     * Test dashlet constructor and basic properties
     */
    public function testConstructor(): void
    {
        $this->assertInstanceOf('BillableHoursQuickEntryDashlet', $this->dashlet);
        $this->assertInstanceOf('Dashlet', $this->dashlet);
        
        // Test default properties
        $this->assertEquals('client_meeting', $this->dashlet->defaultActivityType);
        $this->assertEquals('250.00', $this->dashlet->defaultRate);
        $this->assertTrue($this->dashlet->autoSave);
        $this->assertTrue($this->dashlet->isConfigurable);
        $this->assertTrue($this->dashlet->hasScript);
    }

    /**
     * Test constructor with custom configuration
     */
    public function testConstructorWithCustomConfig(): void
    {
        $customConfig = [
            'title' => 'Custom Time Tracker',
            'defaultActivityType' => 'court_appearance',
            'defaultRate' => '300.00',
            'autoSave' => false
        ];
        
        $customDashlet = new BillableHoursQuickEntryDashlet('custom_id', $customConfig);
        
        $this->assertEquals('court_appearance', $customDashlet->defaultActivityType);
        $this->assertEquals('300.00', $customDashlet->defaultRate);
        $this->assertFalse($customDashlet->autoSave);
        $this->assertEquals('Custom Time Tracker', $customDashlet->title);
    }

    /**
     * Test display method returns HTML content
     */
    public function testDisplay(): void
    {
        $html = $this->dashlet->display();
        
        $this->assertIsString($html);
        $this->assertStringContainsString('billable-hours-dashlet', $html);
        $this->assertStringContainsString('billable_hours_form_', $html);
        $this->assertStringContainsString('case_select_', $html);
        $this->assertStringContainsString('activity_type_', $html);
        $this->assertStringContainsString('duration_', $html);
        $this->assertStringContainsString('description_', $html);
    }

    /**
     * Test display method includes all required activity types
     */
    public function testDisplayIncludesActivityTypes(): void
    {
        $html = $this->dashlet->display();
        
        // Check for criminal defense specific activity types
        $expectedActivityTypes = [
            'court_appearance',
            'client_meeting',
            'case_research',
            'document_review',
            'legal_writing',
            'phone_call',
            'investigation',
            'trial_prep',
            'other'
        ];
        
        foreach ($expectedActivityTypes as $activityType) {
            $this->assertStringContainsString($activityType, $html);
        }
    }

    /**
     * Test displayScript method returns JavaScript
     */
    public function testDisplayScript(): void
    {
        $script = $this->dashlet->displayScript();
        
        $this->assertIsString($script);
        $this->assertStringContainsString('BillableHours', $script);
        $this->assertStringContainsString('startTimer', $script);
        $this->assertStringContainsString('stopTimer', $script);
        $this->assertStringContainsString('logTime', $script);
    }

    /**
     * Test getActiveCases method
     */
    public function testGetActiveCases(): void
    {
        // Create a test case
        $case = BeanFactory::newBean('Cases');
        $case->name = 'Test Criminal Case';
        $case->case_number = 'CR-2025-001';
        $case->status = 'Open';
        $case->assigned_user_id = $GLOBALS['current_user']->id;
        $case->save();
        
        // Use reflection to access private method
        $reflection = new ReflectionClass($this->dashlet);
        $method = $reflection->getMethod('getActiveCases');
        $method->setAccessible(true);
        
        $cases = $method->invoke($this->dashlet);
        
        $this->assertIsArray($cases);
        $this->assertArrayHasKey($case->id, $cases);
        $this->assertStringContainsString('Test Criminal Case', $cases[$case->id]);
        $this->assertStringContainsString('CR-2025-001', $cases[$case->id]);
        
        // Clean up
        $case->mark_deleted($case->id);
    }

    /**
     * Test saveTimeEntry method with valid data
     */
    public function testSaveTimeEntrySuccess(): void
    {
        // Mock $_REQUEST data
        $_REQUEST = [
            'duration' => '2.5',
            'activity_type' => 'client_meeting',
            'description' => 'Discussed case strategy and plea options',
            'entry_date' => '2025-01-15',
            'entry_time' => '14:30',
            'hourly_rate' => '275.00',
            'case_id' => ''
        ];
        
        // Capture output
        ob_start();
        $this->dashlet->saveTimeEntry();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        
        $this->assertIsArray($response);
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('message', $response);
        $this->assertArrayHasKey('task_id', $response);
        $this->assertArrayHasKey('duration', $response);
        $this->assertArrayHasKey('amount', $response);
        
        $this->assertEquals(2.5, $response['duration']);
        $this->assertEquals('687.50', $response['amount']); // 2.5 * 275.00
        
        // Verify task was created
        $task = BeanFactory::retrieveBean('Tasks', $response['task_id']);
        $this->assertInstanceOf('Task', $task);
        $this->assertEquals('Completed', $task->status);
        $this->assertStringContainsString('client_meeting', $task->name);
        $this->assertStringContainsString('Discussed case strategy', $task->description);
        
        // Store for cleanup
        $this->createdTaskIds[] = $response['task_id'];
    }

    /**
     * Test saveTimeEntry method with missing required fields
     */
    public function testSaveTimeEntryMissingFields(): void
    {
        // Mock $_REQUEST data with missing fields
        $_REQUEST = [
            'duration' => '',
            'activity_type' => 'client_meeting',
            'description' => '',
            'entry_date' => '2025-01-15'
        ];
        
        // Capture output
        ob_start();
        $this->dashlet->saveTimeEntry();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        
        $this->assertIsArray($response);
        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('message', $response);
    }

    /**
     * Test saveTimeEntry method with invalid duration
     */
    public function testSaveTimeEntryInvalidDuration(): void
    {
        // Mock $_REQUEST data with invalid duration
        $_REQUEST = [
            'duration' => '-1.5',
            'activity_type' => 'client_meeting',
            'description' => 'Test description',
            'entry_date' => '2025-01-15'
        ];
        
        // Capture output
        ob_start();
        $this->dashlet->saveTimeEntry();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        
        $this->assertIsArray($response);
        $this->assertFalse($response['success']);
    }

    /**
     * Test saveTimeEntry with case linkage
     */
    public function testSaveTimeEntryWithCase(): void
    {
        // Create a test case
        $case = BeanFactory::newBean('Cases');
        $case->name = 'Test Case for Time Entry';
        $case->case_number = 'CR-2025-002';
        $case->status = 'Open';
        $case->assigned_user_id = $GLOBALS['current_user']->id;
        $case->save();
        
        // Mock $_REQUEST data with case_id
        $_REQUEST = [
            'duration' => '1.0',
            'activity_type' => 'court_appearance',
            'description' => 'Attended arraignment hearing',
            'entry_date' => '2025-01-15',
            'entry_time' => '09:00',
            'hourly_rate' => '300.00',
            'case_id' => $case->id
        ];
        
        // Capture output
        ob_start();
        $this->dashlet->saveTimeEntry();
        $output = ob_get_clean();
        
        $response = json_decode($output, true);
        
        $this->assertTrue($response['success']);
        
        // Verify task is linked to case
        $task = BeanFactory::retrieveBean('Tasks', $response['task_id']);
        $this->assertEquals('Cases', $task->parent_type);
        $this->assertEquals($case->id, $task->parent_id);
        
        // Clean up
        $this->createdTaskIds[] = $response['task_id'];
        $case->mark_deleted($case->id);
    }

    /**
     * Test displayOptions method
     */
    public function testDisplayOptions(): void
    {
        $options = $this->dashlet->displayOptions();
        
        $this->assertIsString($options);
        $this->assertStringContainsString('defaultActivityType', $options);
        $this->assertStringContainsString('defaultRate', $options);
        $this->assertStringContainsString('autoSave', $options);
        $this->assertStringContainsString('court_appearance', $options);
        $this->assertStringContainsString('client_meeting', $options);
    }

    /**
     * Test saveOptions method
     */
    public function testSaveOptions(): void
    {
        $_REQUEST = [
            'title' => 'Custom Billable Hours',
            'defaultActivityType' => 'trial_prep',
            'defaultRate' => '350.00',
            'autoSave' => '1'
        ];
        
        $options = $this->dashlet->saveOptions($_REQUEST);
        
        $this->assertIsArray($options);
        $this->assertEquals('Custom Billable Hours', $options['title']);
        $this->assertEquals('trial_prep', $options['defaultActivityType']);
        $this->assertEquals('350.00', $options['defaultRate']);
        $this->assertTrue($options['autoSave']);
    }

    /**
     * Test saveOptions method with invalid rate
     */
    public function testSaveOptionsInvalidRate(): void
    {
        $_REQUEST = [
            'title' => 'Test',
            'defaultActivityType' => 'client_meeting',
            'defaultRate' => '-100.00',
            'autoSave' => ''
        ];
        
        $options = $this->dashlet->saveOptions($_REQUEST);
        
        $this->assertEquals('250.00', $options['defaultRate']); // Should default
        $this->assertFalse($options['autoSave']);
    }

    /**
     * Test addCustomTimeFields method (private method)
     */
    public function testAddCustomTimeFields(): void
    {
        $task = BeanFactory::newBean('Tasks');
        $task->name = 'Test Task';
        $task->description = 'Original description';
        $task->save();
        
        $timeData = [
            'duration_hours' => 2.5,
            'activity_type' => 'legal_writing',
            'hourly_rate' => 275.00,
            'total_amount' => 687.50
        ];
        
        // Use reflection to access private method
        $reflection = new ReflectionClass($this->dashlet);
        $method = $reflection->getMethod('addCustomTimeFields');
        $method->setAccessible(true);
        
        $method->invoke($this->dashlet, $task, $timeData);
        
        // Retrieve updated task
        $updatedTask = BeanFactory::retrieveBean('Tasks', $task->id);
        
        $this->assertStringContainsString('Original description', $updatedTask->description);
        $this->assertStringContainsString('2.5 hours', $updatedTask->description);
        $this->assertStringContainsString('legal_writing', $updatedTask->description);
        $this->assertStringContainsString('$275.00', $updatedTask->description);
        $this->assertStringContainsString('$687.50', $updatedTask->description);
        
        // Clean up
        $task->mark_deleted($task->id);
    }

    /**
     * Test error handling in getActiveCases
     */
    public function testGetActiveCasesErrorHandling(): void
    {
        // Mock database error by temporarily corrupting the global database
        $originalDb = $GLOBALS['db'];
        $GLOBALS['db'] = null;
        
        // Use reflection to access private method
        $reflection = new ReflectionClass($this->dashlet);
        $method = $reflection->getMethod('getActiveCases');
        $method->setAccessible(true);
        
        $cases = $method->invoke($this->dashlet);
        
        $this->assertIsArray($cases);
        $this->assertEmpty($cases); // Should return empty array on error
        
        // Restore database
        $GLOBALS['db'] = $originalDb;
    }

    /**
     * Test time calculation accuracy
     */
    public function testTimeCalculationAccuracy(): void
    {
        $testCases = [
            ['duration' => '0.25', 'rate' => '200.00', 'expected' => '50.00'],
            ['duration' => '1.5', 'rate' => '350.00', 'expected' => '525.00'],
            ['duration' => '8.0', 'rate' => '175.50', 'expected' => '1404.00'],
            ['duration' => '0.1', 'rate' => '500.00', 'expected' => '50.00']
        ];
        
        foreach ($testCases as $testCase) {
            $_REQUEST = [
                'duration' => $testCase['duration'],
                'activity_type' => 'case_research',
                'description' => 'Time calculation test',
                'entry_date' => '2025-01-15',
                'hourly_rate' => $testCase['rate']
            ];
            
            ob_start();
            $this->dashlet->saveTimeEntry();
            $output = ob_get_clean();
            
            $response = json_decode($output, true);
            
            $this->assertTrue($response['success'], 'Failed for duration: ' . $testCase['duration']);
            $this->assertEquals($testCase['expected'], $response['amount'], 
                'Amount mismatch for duration: ' . $testCase['duration'] . ' at rate: ' . $testCase['rate']);
            
            // Store for cleanup
            if (isset($response['task_id'])) {
                $this->createdTaskIds[] = $response['task_id'];
            }
        }
    }

    /**
     * Array to store created task IDs for cleanup
     * @var array
     */
    private $createdTaskIds = [];
}