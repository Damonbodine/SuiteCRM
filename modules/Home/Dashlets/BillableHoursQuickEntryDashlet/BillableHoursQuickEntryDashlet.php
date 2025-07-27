<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once('include/Dashlets/Dashlet.php');

/**
 * Billable Hours Quick Entry Dashlet for Criminal Defense Attorneys
 * Provides a quick and easy way to log billable time directly from the dashboard
 */
#[\AllowDynamicProperties]
class BillableHoursQuickEntryDashlet extends Dashlet
{
    public $defaultActivityType = 'client_meeting';
    public $defaultRate = '250.00';
    public $autoSave = true;
    
    /**
     * Constructor
     *
     * @param string $id id for the current dashlet
     * @param array $def options saved for this dashlet
     */
    public function __construct($id, $def = null)
    {
        $this->loadLanguage('BillableHoursQuickEntryDashlet');
        
        // Load saved options or use defaults
        if (!empty($def['defaultActivityType'])) {
            $this->defaultActivityType = $def['defaultActivityType'];
        }
        if (!empty($def['defaultRate'])) {
            $this->defaultRate = $def['defaultRate'];
        }
        if (isset($def['autoSave'])) {
            $this->autoSave = $def['autoSave'];
        }
        
        parent::__construct($id);
        
        $this->isConfigurable = true;
        $this->hasScript = true;
        
        // Set dashlet title
        if (empty($def['title'])) {
            $this->title = $this->dashletStrings['LBL_TITLE'];
        } else {
            $this->title = $def['title'];
        }
    }
    
    /**
     * Display the dashlet content
     *
     * @return string HTML content
     */
    public function display()
    {
        global $current_user, $app_list_strings;
        
        $ss = new Sugar_Smarty();
        
        // Get active cases for the current user
        $activeCases = $this->getActiveCases();
        
        // Activity types for criminal defense
        $activityTypes = array(
            'court_appearance' => $this->dashletStrings['LBL_COURT_APPEARANCE'],
            'client_meeting' => $this->dashletStrings['LBL_CLIENT_MEETING'],
            'case_research' => $this->dashletStrings['LBL_CASE_RESEARCH'],
            'document_review' => $this->dashletStrings['LBL_DOCUMENT_REVIEW'],
            'legal_writing' => $this->dashletStrings['LBL_LEGAL_WRITING'],
            'phone_call' => $this->dashletStrings['LBL_PHONE_CALL'],
            'investigation' => $this->dashletStrings['LBL_INVESTIGATION'],
            'trial_prep' => $this->dashletStrings['LBL_TRIAL_PREP'],
            'other' => $this->dashletStrings['LBL_OTHER']
        );
        
        // Current date and time
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i');
        
        // Assign template variables
        $ss->assign('id', $this->id);
        $ss->assign('activeCases', $activeCases);
        $ss->assign('activityTypes', $activityTypes);
        $ss->assign('defaultActivityType', $this->defaultActivityType);
        $ss->assign('defaultRate', $this->defaultRate);
        $ss->assign('currentDate', $currentDate);
        $ss->assign('currentTime', $currentTime);
        $ss->assign('strings', $this->dashletStrings);
        
        $content = $ss->fetch('modules/Home/Dashlets/BillableHoursQuickEntryDashlet/BillableHoursQuickEntryDashlet.tpl');
        
        return parent::display() . $content . '<br />';
    }
    
    /**
     * Display JavaScript for the dashlet
     *
     * @return string JavaScript code
     */
    public function displayScript()
    {
        $ss = new Sugar_Smarty();
        $ss->assign('id', $this->id);
        $ss->assign('strings', $this->dashletStrings);
        
        return $ss->fetch('modules/Home/Dashlets/BillableHoursQuickEntryDashlet/BillableHoursQuickEntryDashletScript.tpl');
    }
    
    /**
     * Save time entry via AJAX call
     * This method is called via the CallMethodDashlet action
     */
    public function saveTimeEntry()
    {
        global $current_user;
        
        $response = array('success' => false, 'message' => '');
        
        try {
            // Debug logging
            $GLOBALS['log']->info("BillableHours DEBUG: saveTimeEntry called");
            $GLOBALS['log']->info("BillableHours DEBUG: Request data: " . print_r($_REQUEST, true));
            
            // Quick test response to see if we're reaching this method
            if (isset($_REQUEST['test_only'])) {
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode(array('success' => true, 'message' => 'Method reached successfully'));
                die();
            }
            
            // Validate required fields
            if (empty($_REQUEST['duration']) || empty($_REQUEST['activity_type']) || empty($_REQUEST['description'])) {
                $response['message'] = $this->dashletStrings['LBL_ERROR_REQUIRED_FIELDS'];
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode($response);
                die();
            }
            
            // Create new Task for time entry
            $task = BeanFactory::newBean('Tasks');
            
            // Parse duration (expected format: "1.5" for 1.5 hours)
            $durationHours = floatval($_REQUEST['duration']);
            $durationMinutes = intval($durationHours * 60);
            
            // Set task fields
            $task->name = $this->dashletStrings['LBL_BILLABLE_TIME_ENTRY'] . ' - ' . $_REQUEST['activity_type'];
            $task->description = SugarCleaner::cleanHtml($_REQUEST['description']);
            $task->status = 'Completed';
            $task->assigned_user_id = $current_user->id;
            
            // Properly format the date_start field
            $entryDate = $_REQUEST['entry_date'] ?: date('Y-m-d');
            $entryTime = $_REQUEST['entry_time'] ?: '00:00';
            $task->date_start = $entryDate . ' ' . $entryTime . ':00';
            $task->date_due = $task->date_start;
            
            // Link to case if selected
            if (!empty($_REQUEST['case_id']) && $_REQUEST['case_id'] !== '') {
                $task->parent_type = 'Cases';
                $task->parent_id = $_REQUEST['case_id'];
            }
            
            // Save the task
            $task->save();
            
            if ($task->id) {
                // Add custom fields for billable hours tracking
                // Note: In a real implementation, you'd add these fields via Studio or vardefs
                $this->addCustomTimeFields($task, array(
                    'duration_hours' => $durationHours,
                    'duration_minutes' => $durationMinutes,
                    'activity_type' => $_REQUEST['activity_type'],
                    'hourly_rate' => floatval($_REQUEST['hourly_rate'] ?: $this->defaultRate),
                    'is_billable' => true,
                    'total_amount' => $durationHours * floatval($_REQUEST['hourly_rate'] ?: $this->defaultRate)
                ));
                
                $response['success'] = true;
                $response['message'] = $this->dashletStrings['LBL_TIME_SAVED_SUCCESS'];
                $response['task_id'] = $task->id;
                $response['duration'] = $durationHours;
                $response['amount'] = number_format($durationHours * floatval($_REQUEST['hourly_rate'] ?: $this->defaultRate), 2);
            } else {
                $response['message'] = $this->dashletStrings['LBL_ERROR_SAVING'];
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->error('BillableHoursQuickEntryDashlet::saveTimeEntry() Error: ' . $e->getMessage());
            $response['message'] = $this->dashletStrings['LBL_ERROR_GENERAL'];
        }
        
        // Ensure clean JSON output
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        die();
    }
    
    /**
     * Add custom fields for time tracking
     * In a real implementation, these would be proper custom fields
     *
     * @param Task $task The task object
     * @param array $timeData Time tracking data
     */
    private function addCustomTimeFields($task, $timeData)
    {
        // This is a simplified implementation
        // In production, you'd have actual custom fields in the database
        $description = $task->description . "\n\n" . $this->dashletStrings['LBL_TIME_DETAILS'] . ":\n";
        $description .= $this->dashletStrings['LBL_DURATION'] . ": " . $timeData['duration_hours'] . " " . $this->dashletStrings['LBL_HOURS'] . "\n";
        $description .= $this->dashletStrings['LBL_ACTIVITY_TYPE'] . ": " . $timeData['activity_type'] . "\n";
        $description .= $this->dashletStrings['LBL_HOURLY_RATE'] . ": $" . number_format($timeData['hourly_rate'], 2) . "\n";
        $description .= $this->dashletStrings['LBL_TOTAL_AMOUNT'] . ": $" . number_format($timeData['total_amount'], 2);
        
        $task->description = $description;
        $task->save();
    }
    
    /**
     * Get active cases for the current user
     *
     * @return array Array of cases
     */
    private function getActiveCases()
    {
        global $current_user;
        
        $cases = array();
        
        try {
            // Get cases assigned to current user that are not closed
            $case = BeanFactory::newBean('Cases');
            $query = "SELECT id, name, case_number FROM cases 
                     WHERE assigned_user_id = '" . $current_user->id . "' 
                     AND status NOT IN ('Closed', 'Rejected', 'Duplicate') 
                     AND deleted = 0 
                     ORDER BY name";
            
            $result = $case->db->query($query);
            
            while ($row = $case->db->fetchByAssoc($result)) {
                $cases[$row['id']] = $row['name'] . ' (' . $row['case_number'] . ')';
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->error('BillableHoursQuickEntryDashlet::getActiveCases() Error: ' . $e->getMessage());
        }
        
        return $cases;
    }
    
    /**
     * Display configuration options
     *
     * @return string HTML for configuration form
     */
    public function displayOptions()
    {
        global $app_strings;
        
        $ss = new Sugar_Smarty();
        
        // Activity types for dropdown
        $activityTypes = array(
            'court_appearance' => $this->dashletStrings['LBL_COURT_APPEARANCE'],
            'client_meeting' => $this->dashletStrings['LBL_CLIENT_MEETING'],
            'case_research' => $this->dashletStrings['LBL_CASE_RESEARCH'],
            'document_review' => $this->dashletStrings['LBL_DOCUMENT_REVIEW'],
            'legal_writing' => $this->dashletStrings['LBL_LEGAL_WRITING'],
            'phone_call' => $this->dashletStrings['LBL_PHONE_CALL'],
            'investigation' => $this->dashletStrings['LBL_INVESTIGATION'],
            'trial_prep' => $this->dashletStrings['LBL_TRIAL_PREP'],
            'other' => $this->dashletStrings['LBL_OTHER']
        );
        
        $ss->assign('titleLbl', $this->dashletStrings['LBL_CONFIGURE_TITLE']);
        $ss->assign('defaultActivityTypeLbl', $this->dashletStrings['LBL_CONFIGURE_DEFAULT_ACTIVITY']);
        $ss->assign('defaultRateLbl', $this->dashletStrings['LBL_CONFIGURE_DEFAULT_RATE']);
        $ss->assign('autoSaveLbl', $this->dashletStrings['LBL_CONFIGURE_AUTO_SAVE']);
        $ss->assign('saveLbl', $app_strings['LBL_SAVE_BUTTON_LABEL']);
        $ss->assign('clearLbl', $app_strings['LBL_CLEAR_BUTTON_LABEL']);
        
        $ss->assign('title', $this->title);
        $ss->assign('defaultActivityType', $this->defaultActivityType);
        $ss->assign('defaultRate', $this->defaultRate);
        $ss->assign('autoSave', $this->autoSave);
        $ss->assign('activityTypes', $activityTypes);
        $ss->assign('id', $this->id);
        
        return parent::displayOptions() . $ss->fetch('modules/Home/Dashlets/BillableHoursQuickEntryDashlet/BillableHoursQuickEntryDashletOptions.tpl');
    }
    
    /**
     * Save configuration options
     *
     * @param array $req Request data
     * @return array Options to save
     */
    public function saveOptions($req)
    {
        $options = array();
        $options['title'] = $_REQUEST['title'];
        $options['defaultActivityType'] = $_REQUEST['defaultActivityType'];
        
        // Validate and clean rate
        $rate = floatval($_REQUEST['defaultRate']);
        if ($rate > 0) {
            $options['defaultRate'] = number_format($rate, 2, '.', '');
        } else {
            $options['defaultRate'] = '250.00';
        }
        
        $options['autoSave'] = !empty($_REQUEST['autoSave']);
        
        return $options;
    }
    
    /**
     * Get PDF export modal HTML
     */
    public function getPDFExportModal()
    {
        $ss = new Sugar_Smarty();
        $ss->assign('id', $this->id);
        $ss->assign('strings', $this->dashletStrings);
        $ss->assign('default_start_date', date('Y-m-01')); // First day of current month
        $ss->assign('default_end_date', date('Y-m-d'));   // Today
        $ss->assign('activeCases', $this->getActiveCases());
        
        ob_clean();
        header('Content-Type: text/html');
        echo $ss->fetch('modules/Home/Dashlets/BillableHoursQuickEntryDashlet/pdf_export_modal.tpl');
        die();
    }
    
    /**
     * Generate PDF report for billable hours
     */
    public function generateBillableHoursPDF()
    {
        global $current_user;
        
        try {
            // CRITICAL: Clean all output buffers before PDF generation
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            // Get parameters from request
            $startDate = $_REQUEST['start_date'] ?? date('Y-m-01');
            $endDate = $_REQUEST['end_date'] ?? date('Y-m-t');
            $userId = $_REQUEST['user_id'] ?? $current_user->id;
            
            // Get billable hours data
            $billableData = $this->getBillableHoursData($startDate, $endDate, $userId);
            
            // Create HTML content with real data
            $html = "<h1>Billable Hours Report</h1>";
            $html .= "<p>Date Range: " . htmlspecialchars($startDate) . " to " . htmlspecialchars($endDate) . "</p>";
            $html .= "<p>User: " . htmlspecialchars($current_user->full_name) . "</p>";
            $html .= "<p>Generated: " . date('Y-m-d H:i:s') . "</p>";
            
            if (empty($billableData)) {
                $html .= "<h2>No Billable Hours Found</h2>";
                $html .= "<p>No billable time entries were found for the selected date range.</p>";
            } else {
                $html .= "<h2>Billable Hours Summary (" . count($billableData) . " entries)</h2>";
                $html .= "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
                $html .= "<tr style='background-color: #f5f5f5;'>";
                $html .= "<th>Date</th><th>Activity</th><th>Case</th><th>Duration</th><th>Rate</th><th>Amount</th><th>Description</th>";
                $html .= "</tr>";
                
                $totalHours = 0;
                $totalAmount = 0;
                
                foreach ($billableData as $entry) {
                    $html .= "<tr>";
                    $html .= "<td>" . htmlspecialchars($entry['date']) . "</td>";
                    $html .= "<td>" . htmlspecialchars($entry['activity_type']) . "</td>";
                    $html .= "<td>" . htmlspecialchars($entry['case_name']) . "</td>";
                    $html .= "<td>" . number_format($entry['duration'], 2) . "h</td>";
                    $html .= "<td>$" . number_format($entry['rate'], 2) . "</td>";
                    $html .= "<td>$" . number_format($entry['amount'], 2) . "</td>";
                    $html .= "<td>" . htmlspecialchars(substr($entry['description'], 0, 100)) . "</td>";
                    $html .= "</tr>";
                    
                    $totalHours += $entry['duration'];
                    $totalAmount += $entry['amount'];
                }
                
                $html .= "<tr style='background-color: #e9e9e9; font-weight: bold;'>";
                $html .= "<td colspan='3'>TOTALS</td>";
                $html .= "<td>" . number_format($totalHours, 2) . "h</td>";
                $html .= "<td>-</td>";
                $html .= "<td>$" . number_format($totalAmount, 2) . "</td>";
                $html .= "<td>-</td>";
                $html .= "</tr>";
                $html .= "</table>";
            }
            
            // Send as downloadable HTML file for now (easier to debug)
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="billable_hours_' . date('Y-m-d') . '.html"');
            header('Content-Length: ' . strlen($html));
            echo $html;
            exit();
            
        } catch (Exception $e) {
            $GLOBALS['log']->error('PDF Generation Error: ' . $e->getMessage());
            
            // Clean output and send error
            while (ob_get_level()) {
                ob_end_clean();
            }
            
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'error' => 'PDF generation failed: ' . $e->getMessage()));
            exit();
        }
    }
    
    /**
     * Get billable hours data for PDF generation
     */
    private function getBillableHoursData($startDate, $endDate, $userId)
    {
        global $db;
        $data = array();
        
        try {
            // Query tasks that were created by the billable hours dashlet
            // We look for tasks with "Billable Time Entry" in the name and completed status
            // Handle both NULL date_start (use date_entered) and valid date_start
            $query = "SELECT 
                        t.id,
                        t.name, 
                        t.description, 
                        t.date_start,
                        t.date_entered,
                        t.assigned_user_id,
                        c.name as case_name, 
                        c.case_number
                      FROM tasks t
                      LEFT JOIN cases c ON t.parent_id = c.id AND t.parent_type = 'Cases'
                      WHERE t.assigned_user_id = '" . $db->quote($userId) . "'
                      AND t.name LIKE '%Billable Time Entry%'
                      AND t.status = 'Completed'
                      AND (
                          (t.date_start IS NOT NULL AND DATE(t.date_start) BETWEEN '" . $db->quote($startDate) . "' AND '" . $db->quote($endDate) . "')
                          OR
                          (t.date_start IS NULL AND DATE(t.date_entered) BETWEEN '" . $db->quote($startDate) . "' AND '" . $db->quote($endDate) . "')
                      )
                      AND t.deleted = 0
                      ORDER BY COALESCE(t.date_start, t.date_entered) DESC";
            
            $GLOBALS['log']->info("Billable Hours Query: " . $query);
            
            $result = $db->query($query);
            
            while ($row = $db->fetchByAssoc($result)) {
                // Parse the billable hours data from the task description
                $timeData = $this->parseTimeDataFromDescription($row['description']);
                
                // Use date_start if available, otherwise fall back to date_entered
                $dateToUse = $row['date_start'] ?: $row['date_entered'];
                
                $data[] = array(
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'description' => $this->extractOriginalDescription($row['description']),
                    'date' => date('Y-m-d', strtotime($dateToUse)),
                    'time' => date('H:i', strtotime($dateToUse)),
                    'duration' => $timeData['duration_hours'],
                    'rate' => $timeData['hourly_rate'],
                    'amount' => $timeData['total_amount'],
                    'activity_type' => $timeData['activity_type'],
                    'case_name' => $row['case_name'] ?: 'No Case',
                    'case_number' => $row['case_number'] ?: ''
                );
            }
            
            $GLOBALS['log']->info("Found " . count($data) . " billable hours entries");
            
        } catch (Exception $e) {
            $GLOBALS['log']->error('getBillableHoursData Error: ' . $e->getMessage());
        }
        
        return $data;
    }
    
    /**
     * Parse time tracking data from task description
     */
    private function parseTimeDataFromDescription($description)
    {
        $timeData = array(
            'duration_hours' => 0,
            'hourly_rate' => 0,
            'total_amount' => 0,
            'activity_type' => 'Unknown'
        );
        
        // Parse the structured data that we add to task descriptions
        if (preg_match('/Duration: ([\d.]+) hours/', $description, $matches)) {
            $timeData['duration_hours'] = floatval($matches[1]);
        }
        
        if (preg_match('/Activity Type: (.+)/', $description, $matches)) {
            $timeData['activity_type'] = trim($matches[1]);
        }
        
        if (preg_match('/Hourly Rate: \$([\d.]+)/', $description, $matches)) {
            $timeData['hourly_rate'] = floatval($matches[1]);
        }
        
        if (preg_match('/Total Amount: \$([\d.]+)/', $description, $matches)) {
            $timeData['total_amount'] = floatval($matches[1]);
        }
        
        return $timeData;
    }
    
    /**
     * Extract the original description (before our time details)
     */
    private function extractOriginalDescription($description)
    {
        $parts = explode("Billable Time Details:", $description);
        return trim($parts[0]);
    }
    
    /**
     * Generate professional legal billing narrative using AI
     */
    public function generateBillingNarrative()
    {
        global $current_user;
        
        $response = array('success' => false, 'narrative' => '', 'error' => '');
        
        try {
            // Get parameters
            $rawDescription = $_REQUEST['raw_description'] ?? '';
            $activityType = $_REQUEST['activity_type'] ?? '';
            $caseId = $_REQUEST['case_id'] ?? '';
            
            if (empty($rawDescription)) {
                $response['error'] = 'Description is required for AI enhancement';
                ob_clean();
                header('Content-Type: application/json');
                echo json_encode($response);
                die();
            }
            
            // Get case information for context
            $caseInfo = '';
            if (!empty($caseId)) {
                $case = BeanFactory::retrieveBean('Cases', $caseId);
                if ($case) {
                    $caseInfo = "Case: " . $case->name . " (Type: " . ($case->type ?? 'Criminal Defense') . ")";
                }
            }
            
            // Create AI prompt for legal billing narrative
            $prompt = $this->buildLegalBillingPrompt($rawDescription, $activityType, $caseInfo);
            
            // Call AI service (using a mock implementation for now)
            $aiNarrative = $this->callAIService($prompt);
            
            if ($aiNarrative) {
                $response['success'] = true;
                $response['narrative'] = $aiNarrative;
                $response['original'] = $rawDescription;
            } else {
                $response['error'] = 'AI service temporarily unavailable';
            }
            
        } catch (Exception $e) {
            $GLOBALS['log']->error('BillableHours AI Narrative Error: ' . $e->getMessage());
            $response['error'] = 'AI enhancement failed: ' . $e->getMessage();
        }
        
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode($response);
        die();
    }
    
    /**
     * Build AI prompt for legal billing narrative
     */
    private function buildLegalBillingPrompt($rawDescription, $activityType, $caseInfo)
    {
        $activityContext = array(
            'court_appearance' => 'court proceedings and litigation activities',
            'client_meeting' => 'client consultation and strategic planning',
            'case_research' => 'legal research and case law analysis',
            'document_review' => 'document examination and analysis',
            'legal_writing' => 'legal document preparation and drafting',
            'phone_call' => 'telephonic communication and consultation',
            'investigation' => 'case investigation and fact-gathering',
            'trial_prep' => 'trial preparation and case strategy development',
            'other' => 'legal services and case management'
        );
        
        $context = $activityContext[$activityType] ?? 'legal services';
        
        $prompt = "Convert this casual time entry description into professional legal billing language suitable for criminal defense attorney billing records:

Original Description: \"{$rawDescription}\"
Activity Type: {$activityType} ({$context})
{$caseInfo}

Requirements:
1. Use professional legal terminology
2. Be specific about the legal work performed
3. Maintain appropriate attorney-client privilege language
4. Follow standard legal billing practices
5. Keep it concise but descriptive (1-2 sentences)
6. Include action-oriented language (e.g., 'analyzed', 'reviewed', 'consulted', 'researched')

Examples of good legal billing descriptions:
- 'Client conference regarding case strategy development and legal options analysis pursuant to pending criminal charges'
- 'Legal research and analysis of Fourth Amendment precedents applicable to search and seizure issues in client matter'
- 'Review and analysis of prosecutorial discovery materials and witness statements'
- 'Preparation of motion to suppress evidence based on constitutional violations'

Enhanced Description:";
        
        return $prompt;
    }
    
    /**
     * Call AI service for narrative generation
     * This is a mock implementation - in production you'd integrate with OpenAI, Claude, etc.
     */
    private function callAIService($prompt)
    {
        // Mock AI responses based on common patterns
        // In production, this would call an actual AI API
        
        $rawDescription = strtolower($_REQUEST['raw_description'] ?? '');
        $activityType = $_REQUEST['activity_type'] ?? '';
        
        // Pattern-based enhancement rules
        $enhancements = array(
            // Client meetings
            'met with client' => 'Client conference regarding case strategy development and legal options analysis',
            'talked to client' => 'Telephonic consultation with client regarding case status and procedural matters',
            'client meeting' => 'Client conference regarding case strategy development and ongoing legal representation',
            
            // Court appearances
            'went to court' => 'Court appearance for scheduled hearing and advocacy on behalf of client',
            'court hearing' => 'Court appearance and oral argument before the tribunal regarding client matter',
            'arraignment' => 'Client representation at arraignment proceeding and entry of plea',
            
            // Research activities
            'researched' => 'Legal research and analysis of applicable statutes and case law precedents',
            'looked up law' => 'Legal research and analysis of relevant jurisprudence and statutory authority',
            'case law research' => 'Comprehensive legal research and analysis of controlling case law and precedents',
            
            // Document work
            'reviewed documents' => 'Review and analysis of case-related documents and evidentiary materials',
            'wrote motion' => 'Preparation and drafting of motion practice documentation for court filing',
            'drafted letter' => 'Preparation of legal correspondence regarding client representation matters',
            
            // Communication
            'called prosecutor' => 'Prosecutorial communication regarding plea negotiations and case resolution discussions',
            'spoke with DA' => 'Communication with prosecuting attorney regarding case disposition and settlement discussions',
            'phone call' => 'Telephonic consultation and communication regarding ongoing legal representation'
        );
        
        // Find best match
        $enhancedDescription = null;
        foreach ($enhancements as $pattern => $enhancement) {
            if (strpos($rawDescription, $pattern) !== false) {
                $enhancedDescription = $enhancement;
                break;
            }
        }
        
        // Fallback enhancement based on activity type
        if (!$enhancedDescription) {
            $activityEnhancements = array(
                'client_meeting' => 'Client consultation regarding legal strategy and case development matters',
                'court_appearance' => 'Court appearance and legal advocacy on behalf of client',
                'case_research' => 'Legal research and analysis of applicable law and precedents',
                'document_review' => 'Review and analysis of case-related documentation and materials',
                'legal_writing' => 'Preparation of legal documentation and written advocacy materials',
                'phone_call' => 'Telephonic communication regarding ongoing legal representation',
                'investigation' => 'Case investigation and fact-gathering activities for client defense',
                'trial_prep' => 'Trial preparation and case strategy development activities',
                'other' => 'Legal services and professional consultation regarding client matter'
            );
            
            $enhancedDescription = $activityEnhancements[$activityType] ?? 
                'Professional legal services rendered in connection with ongoing client representation';
        }
        
        // Add case-specific context if available
        if (!empty($_REQUEST['case_id'])) {
            $enhancedDescription .= ' pursuant to pending criminal matter';
        }
        
        return $enhancedDescription;
    }
}