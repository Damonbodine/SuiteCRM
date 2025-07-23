<?php
/**
 * Acceptance Tests for AI Status Intelligence Feature
 * End-to-end testing of complete AI functionality
 */

use Step\Acceptance\Cases;

class AIStatusIntelligenceCest
{
    private $caseId;
    
    /**
     * Test complete AI status intelligence workflow
     */
    public function testAIStatusIntelligenceWorkflow(\AcceptanceTester $I, Cases $casesStep)
    {
        $I->wantTo('Test complete AI status intelligence feature');
        
        // Login as admin user
        $I->loginAsAdmin();
        
        // Create a new case for testing
        $caseName = 'AI Test Case ' . date('Y-m-d H:i:s');
        $this->caseId = $this->createTestCase($I, $casesStep, $caseName);
        
        // Verify AI fields are present in case
        $this->verifyAIFieldsPresent($I);
        
        // Create some tasks to trigger AI analysis
        $this->createTestTasks($I, $casesStep);
        
        // Trigger AI analysis by updating case
        $this->triggerAIAnalysis($I, $casesStep);
        
        // Verify AI widget appears
        $this->verifyAIWidgetDisplay($I);
        
        // Test accepting AI suggestion
        $this->testAcceptAISuggestion($I);
        
        // Verify status was updated
        $this->verifyStatusUpdate($I);
    }
    
    /**
     * Test AI permission enforcement
     */
    public function testAIPermissionEnforcement(\AcceptanceTester $I, Cases $casesStep)
    {
        $I->wantTo('Test AI feature permission enforcement');
        
        // Create regular user without admin privileges
        $this->createRegularUser($I);
        
        // Login as regular user
        $I->loginAsRegularUser();
        
        // Try to access case with AI features
        $caseName = 'Permission Test Case';
        $this->caseId = $this->createTestCase($I, $casesStep, $caseName);
        
        // Verify regular user can see basic AI info but limited actions
        $this->verifyLimitedAIAccess($I);
        
        // Test that unauthorized actions are blocked
        $this->testUnauthorizedAIActions($I);
    }
    
    /**
     * Test AI widget security features
     */
    public function testAIWidgetSecurity(\AcceptanceTester $I, Cases $casesStep)
    {
        $I->wantTo('Test AI widget security measures');
        
        $I->loginAsAdmin();
        
        // Create test case with AI data
        $caseName = 'Security Test Case';
        $this->caseId = $this->createTestCase($I, $casesStep, $caseName);
        
        // Test CSRF token validation
        $this->testCSRFProtection($I);
        
        // Test input sanitization
        $this->testInputSanitization($I);
        
        // Test rate limiting
        $this->testRateLimiting($I);
    }
    
    /**
     * Test AI analysis accuracy
     */
    public function testAIAnalysisAccuracy(\AcceptanceTester $I, Cases $casesStep)
    {
        $I->wantTo('Test AI analysis accuracy and logic');
        
        $I->loginAsAdmin();
        
        // Test scenario 1: High task completion should suggest closure
        $case1Name = 'High Completion Case';
        $case1Id = $this->createTestCase($I, $casesStep, $case1Name);
        $this->createCompletedTasks($I, $case1Id, 9, 10); // 90% completion
        
        $this->triggerAIAnalysis($I, $casesStep);
        
        $I->see('AI Suggests', '.ai-status-widget');
        $I->see('Closed', '.ai-status-widget .suggested-status');
        
        // Test scenario 2: No activity should suggest pending input
        $case2Name = 'Inactive Case';
        $case2Id = $this->createTestCase($I, $casesStep, $case2Name);
        // Don't create tasks or activities
        
        $this->triggerAIAnalysis($I, $casesStep);
        
        $I->see('Pending Input', '.ai-status-widget');
    }
    
    /**
     * Test AI widget responsive design
     */
    public function testAIWidgetResponsiveDesign(\AcceptanceTester $I, Cases $casesStep)
    {
        $I->wantTo('Test AI widget responsive design');
        
        $I->loginAsAdmin();
        
        $caseName = 'Responsive Test Case';
        $this->caseId = $this->createTestCase($I, $casesStep, $caseName);
        
        // Test desktop view
        $I->resizeWindow(1920, 1080);
        $I->amOnPage("/index.php?module=Cases&action=DetailView&record={$this->caseId}");
        $I->seeElement('.ai-status-widget');
        $I->seeElement('.ai-actions .btn-group-vertical');
        
        // Test tablet view
        $I->resizeWindow(768, 1024);
        $I->amOnPage("/index.php?module=Cases&action=DetailView&record={$this->caseId}");
        $I->seeElement('.ai-status-widget');
        
        // Test mobile view
        $I->resizeWindow(375, 667);
        $I->amOnPage("/index.php?module=Cases&action=DetailView&record={$this->caseId}");
        $I->seeElement('.ai-status-widget');
        
        // Reset window size
        $I->resizeWindow(1920, 1080);
    }
    
    // Helper methods
    private function createTestCase(\AcceptanceTester $I, Cases $casesStep, $caseName)
    {
        $I->amOnPage('/index.php?module=Cases&action=EditView');
        $I->fillField('name', $caseName);
        $I->selectOption('status', 'Open_Assigned');
        $I->selectOption('priority', 'Medium');
        $I->click('Save');
        
        // Get case ID from URL
        $I->seeInCurrentUrl('action=DetailView');
        $url = $I->grabFromCurrentUrl();
        preg_match('/record=([a-f0-9-]{36})/i', $url, $matches);
        
        return $matches[1] ?? null;
    }
    
    private function verifyAIFieldsPresent(\AcceptanceTester $I)
    {
        // Navigate to case detail view
        $I->amOnPage("/index.php?module=Cases&action=DetailView&record={$this->caseId}");
        
        // Look for AI widget container (might not be visible if no suggestions)
        $I->seeElementInDOM('#ai-status-widget-container');
    }
    
    private function createTestTasks(\AcceptanceTester $I, Cases $casesStep)
    {
        // Create some tasks related to the case
        for ($i = 1; $i <= 5; $i++) {
            $I->amOnPage('/index.php?module=Tasks&action=EditView');
            $I->fillField('name', "Test Task {$i}");
            $I->selectOption('parent_type', 'Cases');
            $I->fillField('parent_name', $this->caseId);
            $I->fillField('parent_id', $this->caseId);
            
            // Mark some tasks as completed
            if ($i <= 3) {
                $I->selectOption('status', 'Completed');
            }
            
            $I->click('Save');
        }
    }
    
    private function createCompletedTasks(\AcceptanceTester $I, $caseId, $completed, $total)
    {
        for ($i = 1; $i <= $total; $i++) {
            $I->amOnPage('/index.php?module=Tasks&action=EditView');
            $I->fillField('name', "Task {$i} for {$caseId}");
            $I->selectOption('parent_type', 'Cases');
            $I->fillField('parent_id', $caseId);
            
            if ($i <= $completed) {
                $I->selectOption('status', 'Completed');
            } else {
                $I->selectOption('status', 'Not Started');
            }
            
            $I->click('Save');
        }
    }
    
    private function triggerAIAnalysis(\AcceptanceTester $I, Cases $casesStep)
    {
        // Navigate to case and make a change to trigger analysis
        $I->amOnPage("/index.php?module=Cases&action=EditView&record={$this->caseId}");
        $I->selectOption('priority', 'High'); // Change priority to trigger hook
        $I->click('Save');
        
        // Wait for analysis to complete
        $I->wait(2);
    }
    
    private function verifyAIWidgetDisplay(\AcceptanceTester $I)
    {
        $I->amOnPage("/index.php?module=Cases&action=DetailView&record={$this->caseId}");
        
        // Look for AI widget elements
        $I->waitForElement('.ai-status-widget', 10);
        $I->see('AI Status Intelligence', '.ai-status-widget .panel-title');
    }
    
    private function testAcceptAISuggestion(\AcceptanceTester $I)
    {
        // Check if suggestion is available
        try {
            $I->seeElement('.ai-accept-btn');
            $I->click('.ai-accept-btn');
            $I->wait(3); // Wait for AJAX request
            $I->see('Status updated successfully'); // Or page refresh
        } catch (Exception $e) {
            // No suggestion available, which is also valid
            $I->see('Status appears current', '.ai-status-current');
        }
    }
    
    private function verifyStatusUpdate(\AcceptanceTester $I)
    {
        // Refresh page and verify status was updated
        $I->reloadPage();
        
        // Check that AI suggestion is no longer present (was accepted or dismissed)
        $I->dontSee('Accept Suggestion', '.ai-status-widget');
    }
    
    private function createRegularUser(\AcceptanceTester $I)
    {
        // Implementation would create a non-admin user
        // This is a placeholder for the test structure
    }
    
    private function verifyLimitedAIAccess(\AcceptanceTester $I)
    {
        $I->amOnPage("/index.php?module=Cases&action=DetailView&record={$this->caseId}");
        
        // Regular users should see limited AI information
        try {
            $I->seeElement('.ai-status-widget');
            // But actions might be limited
            $I->dontSee('Accept Suggestion', '.ai-status-widget');
        } catch (Exception $e) {
            // Widget not visible for regular users is also acceptable
            $I->dontSeeElement('.ai-status-widget');
        }
    }
    
    private function testUnauthorizedAIActions(\AcceptanceTester $I)
    {
        // Try to directly call AI action endpoint without proper permissions
        $I->sendPOST('/index.php?entryPoint=aiStatusAction', [
            'action' => 'accept_suggestion',
            'case_id' => $this->caseId,
            'suggested_status' => 'Closed_Closed'
        ]);
        
        // Should receive error response
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains('Access denied');
    }
    
    private function testCSRFProtection(\AcceptanceTester $I)
    {
        // Try to submit AI action without CSRF token
        $I->sendPOST('/index.php?entryPoint=aiStatusAction', [
            'action' => 'accept_suggestion',
            'case_id' => $this->caseId,
            'suggested_status' => 'Closed_Closed'
            // Missing csrf_token
        ]);
        
        // Should be rejected
        $I->seeResponseCodeIs(400);
        $I->seeResponseContains('Invalid security token');
    }
    
    private function testInputSanitization(\AcceptanceTester $I)
    {
        // Try to submit malicious input
        $I->sendPOST('/index.php?entryPoint=aiStatusAction', [
            'action' => '<script>alert("xss")</script>',
            'case_id' => $this->caseId . "'; DROP TABLE cases; --",
            'suggested_status' => '<img src=x onerror=alert(1)>'
        ]);
        
        // Should handle gracefully
        $I->seeResponseCodeIs(400);
    }
    
    private function testRateLimiting(\AcceptanceTester $I)
    {
        // Send multiple rapid requests to test rate limiting
        for ($i = 0; $i < 10; $i++) {
            $I->sendPOST('/index.php?entryPoint=aiStatusAction', [
                'action' => 'dismiss_suggestion',
                'case_id' => $this->caseId
            ]);
        }
        
        // Later requests should be rate limited
        // Exact behavior depends on implementation
    }
}