<?php

use Faker\Generator;

/**
 * Acceptance Tests for Billable Hours Quick Entry Dashlet
 * Tests user interaction with the dashlet from browser perspective
 */
#[\AllowDynamicProperties]
class BillableHoursQuickEntryDashletCest
{
    /**
     * @var Generator $fakeData
     */
    protected $fakeData;

    /**
     * @var integer $fakeDataSeed
     */
    protected $fakeDataSeed;

    /**
     * @var string Test case ID for cleanup
     */
    protected $testCaseId;

    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
        if (!$this->fakeData) {
            $this->fakeData = Faker\Factory::create();
        }

        $this->fakeDataSeed = mt_rand(0, 2048);
        $this->fakeData->seed($this->fakeDataSeed);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _after(AcceptanceTester $I)
    {
        // Clean up any created test data
        if ($this->testCaseId) {
            $I->cleanupTestCase($this->testCaseId);
        }
    }

    /**
     * Test adding the Billable Hours dashlet to the dashboard
     *
     * @param AcceptanceTester $I
     */
    public function testAddBillableHoursDashletToDashboard(AcceptanceTester $I)
    {
        $I->wantTo('Add Billable Hours Quick Entry dashlet to dashboard');

        // Login as admin
        $I->loginAsAdmin();

        // Navigate to Home dashboard
        $I->visitPage('Home', 'index');
        $I->waitForPageLoad();

        // Click Add Dashlets
        $I->click('Add Dashlets');
        $I->waitForElementVisible('.dashlet-container');

        // Look for our dashlet in the Tools category
        $I->see('Billable Hours Quick Entry');
        $I->see('Quick time logging for criminal defense attorneys');

        // Add the dashlet
        $I->click('//div[contains(@class, "dashlet-item")][contains(., "Billable Hours Quick Entry")]//a[contains(@class, "add-dashlet")]');
        
        // Wait for dashlet to be added
        $I->waitForElementVisible('.billable-hours-dashlet');
        $I->see('Billable Hours Quick Entry');
    }

    /**
     * Test basic form elements are present
     *
     * @param AcceptanceTester $I
     */
    public function testBillableHoursFormElements(AcceptanceTester $I)
    {
        $I->wantTo('Verify all form elements are present in the dashlet');

        $this->addDashletToHomePage($I);

        // Check all form elements are present
        $I->seeElement('#case_select_');
        $I->seeElement('#activity_type_');
        $I->seeElement('#duration_');
        $I->seeElement('#hourly_rate_');
        $I->seeElement('#entry_date_');
        $I->seeElement('#entry_time_');
        $I->seeElement('#description_');

        // Check buttons are present
        $I->seeElement('#start_timer_');
        $I->seeElement('#log_time_btn_');
        $I->seeElement('#clear_form_btn_');

        // Check activity type options
        $I->selectOption('#activity_type_', 'court_appearance');
        $I->selectOption('#activity_type_', 'client_meeting');
        $I->selectOption('#activity_type_', 'case_research');
        $I->selectOption('#activity_type_', 'document_review');
        $I->selectOption('#activity_type_', 'legal_writing');
    }

    /**
     * Test logging billable time with valid data
     *
     * @param AcceptanceTester $I
     */
    public function testLogBillableTimeSuccess(AcceptanceTester $I)
    {
        $I->wantTo('Successfully log billable time using the dashlet');

        $this->addDashletToHomePage($I);
        $this->createTestCase($I);

        // Fill out the form
        $I->selectOption('[id*="case_select_"]', $this->testCaseId);
        $I->selectOption('[id*="activity_type_"]', 'client_meeting');
        $I->fillField('[id*="duration_"]', '2.5');
        $I->fillField('[id*="hourly_rate_"]', '275.00');
        $I->fillField('[id*="entry_date_"]', date('Y-m-d'));
        $I->fillField('[id*="entry_time_"]', '14:30');
        $I->fillField('[id*="description_"]', 'Discussed case strategy and reviewed evidence with client');

        // Submit the form
        $I->click('[id*="log_time_btn_"]');

        // Wait for success message
        $I->waitForElementVisible('[id*="status_message_"]');
        $I->see('Time entry saved successfully');

        // Verify daily summary updated
        $I->see('1', '[id*="daily_entries_"]');
        $I->see('2.5h', '[id*="daily_hours_"]');
        $I->see('$687.50', '[id*="daily_amount_"]');

        // Verify form was cleared
        $I->seeInField('[id*="duration_"]', '');
        $I->seeInField('[id*="description_"]', '');
    }

    /**
     * Test timer functionality
     *
     * @param AcceptanceTester $I
     */
    public function testTimerFunctionality(AcceptanceTester $I)
    {
        $I->wantTo('Test the timer start/stop functionality');

        $this->addDashletToHomePage($I);

        // Start timer
        $I->click('[id*="start_timer_"]');
        
        // Verify timer started
        $I->see('Timer started');
        $I->dontSeeElement('[id*="start_timer_"]'); // Start button should be hidden
        $I->seeElement('[id*="stop_timer_"]'); // Stop button should be visible

        // Wait a moment for timer to run
        $I->wait(2);

        // Verify timer display is updating (should show at least 00:00:01)
        $timerText = $I->grabTextFrom('[id*="timer_display_"]');
        $I->assertNotEquals('00:00:00', $timerText);

        // Stop timer
        $I->click('[id*="stop_timer_"]');

        // Verify timer stopped
        $I->see('Timer stopped');
        $I->seeElement('[id*="start_timer_"]'); // Start button should be visible again
        $I->dontSeeElement('[id*="stop_timer_"]'); // Stop button should be hidden

        // Verify duration field was populated
        $duration = $I->grabValueFrom('[id*="duration_"]');
        $I->assertGreaterThan(0, floatval($duration));
    }

    /**
     * Test form validation
     *
     * @param AcceptanceTester $I
     */
    public function testFormValidation(AcceptanceTester $I)
    {
        $I->wantTo('Test form validation with missing required fields');

        $this->addDashletToHomePage($I);

        // Try to submit empty form
        $I->click('[id*="log_time_btn_"]');

        // Should see validation error
        $I->waitForElementVisible('[id*="status_message_"]');
        $I->see('Please fill in all required fields');

        // Fill some fields but leave others empty
        $I->selectOption('[id*="activity_type_"]', 'phone_call');
        $I->fillField('[id*="duration_"]', '');
        $I->fillField('[id*="description_"]', 'Test call');
        $I->fillField('[id*="entry_date_"]', date('Y-m-d'));

        // Try to submit
        $I->click('[id*="log_time_btn_"]');

        // Should still see validation error
        $I->waitForElementVisible('[id*="status_message_"]');
        $I->see('Please fill in all required fields');
    }

    /**
     * Test invalid duration handling
     *
     * @param AcceptanceTester $I
     */
    public function testInvalidDurationValidation(AcceptanceTester $I)
    {
        $I->wantTo('Test validation for invalid duration values');

        $this->addDashletToHomePage($I);

        // Fill form with negative duration
        $I->selectOption('[id*="activity_type_"]', 'case_research');
        $I->fillField('[id*="duration_"]', '-1.5');
        $I->fillField('[id*="description_"]', 'Research case law');
        $I->fillField('[id*="entry_date_"]', date('Y-m-d'));

        // Try to submit
        $I->click('[id*="log_time_btn_"]');

        // Should see validation error
        $I->waitForElementVisible('[id*="status_message_"]');
        $I->see('Please enter a valid duration greater than 0');
    }

    /**
     * Test clear form functionality
     *
     * @param AcceptanceTester $I
     */
    public function testClearFormFunctionality(AcceptanceTester $I)
    {
        $I->wantTo('Test the clear form functionality');

        $this->addDashletToHomePage($I);

        // Fill out form
        $I->selectOption('[id*="activity_type_"]', 'legal_writing');
        $I->fillField('[id*="duration_"]', '3.0');
        $I->fillField('[id*="description_"]', 'Drafted motion to suppress');

        // Clear the form
        $I->click('[id*="clear_form_btn_"]');

        // Verify form is cleared
        $I->seeOptionIsSelected('[id*="case_select_"]', '-- Select Case --');
        $I->seeInField('[id*="duration_"]', '');
        $I->seeInField('[id*="description_"]', '');

        // Activity type should retain default selection
        $I->seeOptionIsSelected('[id*="activity_type_"]', 'Client Meeting/Conference');
    }

    /**
     * Test dashlet configuration
     *
     * @param AcceptanceTester $I
     */
    public function testDashletConfiguration(AcceptanceTester $I)
    {
        $I->wantTo('Test configuring the dashlet options');

        $this->addDashletToHomePage($I);

        // Click on dashlet options/configuration
        $I->click('.dashlet-options');
        $I->waitForElementVisible('.dashlet-config-form');

        // Change configuration options
        $I->fillField('title', 'Custom Time Tracker');
        $I->selectOption('defaultActivityType', 'trial_prep');
        $I->fillField('defaultRate', '350.00');
        $I->checkOption('autoSave');

        // Save configuration
        $I->click('Save');
        $I->waitForPageLoad();

        // Verify changes took effect
        $I->see('Custom Time Tracker');
        $I->seeInField('[id*="hourly_rate_"]', '350.00');
        $I->seeOptionIsSelected('[id*="activity_type_"]', 'Trial Preparation');
    }

    /**
     * Test integration with Cases module
     *
     * @param AcceptanceTester $I
     */
    public function testCaseIntegration(AcceptanceTester $I)
    {
        $I->wantTo('Test that logged time is properly linked to cases');

        $this->addDashletToHomePage($I);
        $this->createTestCase($I);

        // Log time against the test case
        $I->selectOption('[id*="case_select_"]', $this->testCaseId);
        $I->selectOption('[id*="activity_type_"]', 'investigation');
        $I->fillField('[id*="duration_"]', '4.0');
        $I->fillField('[id*="description_"]', 'Interviewed witnesses and collected statements');
        $I->fillField('[id*="entry_date_"]', date('Y-m-d'));
        $I->fillField('[id*="hourly_rate_"]', '225.00');

        $I->click('[id*="log_time_btn_"]');
        $I->waitForElementVisible('[id*="status_message_"]');
        $I->see('Time entry saved successfully');

        // Navigate to the case and verify the task is linked
        $I->visitPage('Cases', 'DetailView', $this->testCaseId);
        $I->waitForPageLoad();

        // Look for the created task in the Activities subpanel
        $I->see('Billable Time Entry - investigation');
        $I->see('Interviewed witnesses and collected statements');
    }

    /**
     * Helper method to add the dashlet to home page
     *
     * @param AcceptanceTester $I
     */
    protected function addDashletToHomePage(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
        $I->visitPage('Home', 'index');
        $I->waitForPageLoad();

        // Check if dashlet already exists, if not add it
        try {
            $I->seeElement('.billable-hours-dashlet');
        } catch (Exception $e) {
            $I->click('Add Dashlets');
            $I->waitForElementVisible('.dashlet-container');
            $I->click('//div[contains(@class, "dashlet-item")][contains(., "Billable Hours Quick Entry")]//a[contains(@class, "add-dashlet")]');
            $I->waitForElementVisible('.billable-hours-dashlet');
        }
    }

    /**
     * Helper method to create a test case
     *
     * @param AcceptanceTester $I
     */
    protected function createTestCase(AcceptanceTester $I)
    {
        $caseName = 'Test Criminal Case - ' . $this->fakeData->lastName;
        $caseNumber = 'CR-' . date('Y') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        // Create case via API or direct database insertion
        // For simplicity, using the UI approach
        $I->visitPage('Cases', 'EditView');
        $I->waitForPageLoad();

        $I->fillField('name', $caseName);
        $I->fillField('case_number', $caseNumber);
        $I->selectOption('status', 'Open');
        $I->selectOption('type', 'Criminal');
        $I->selectOption('priority', 'High');

        $I->click('Save');
        $I->waitForPageLoad();

        // Store the case ID for cleanup and usage
        $currentUrl = $I->grabFromCurrentUrl();
        if (preg_match('/record=([a-f0-9-]{36})/', $currentUrl, $matches)) {
            $this->testCaseId = $matches[1];
        }
    }
}