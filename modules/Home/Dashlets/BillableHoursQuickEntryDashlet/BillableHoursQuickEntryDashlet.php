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
            $task->date_start = $_REQUEST['entry_date'] . ' ' . ($_REQUEST['entry_time'] ?: '00:00');
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
            // Get parameters from request
            $startDate = $_REQUEST['start_date'] ?? date('Y-m-01');
            $endDate = $_REQUEST['end_date'] ?? date('Y-m-t');
            $userId = $_REQUEST['user_id'] ?? $current_user->id;
            
            // Get billable hours data
            $billableData = $this->getBillableHoursData($startDate, $endDate, $userId);
            
            // Load PDF generation library or create simple PDF
            require_once('modules/Home/Dashlets/BillableHoursQuickEntryDashlet/pdf_controller.php');
            
            $pdfController = new BillableHoursPDFController();
            $pdfController->generatePDF($billableData, $startDate, $endDate);
            
        } catch (Exception $e) {
            $GLOBALS['log']->error('PDF Generation Error: ' . $e->getMessage());
            ob_clean();
            header('Content-Type: application/json');
            echo json_encode(array('success' => false, 'error' => 'PDF generation failed'));
            die();
        }
    }
    
    /**
     * Get billable hours data for PDF generation
     */
    private function getBillableHoursData($startDate, $endDate, $userId)
    {
        $data = array();
        
        try {
            // Query tasks that represent billable time entries
            $query = "SELECT t.name, t.description, t.date_start, t.assigned_user_id,
                             COALESCE(bd.billable_duration, 0) as duration,
                             COALESCE(br.billable_rate, 0) as rate,
                             COALESCE(ba.billable_amount, 0) as amount,
                             c.name as case_name, c.case_number
                      FROM tasks t
                      LEFT JOIN cases c ON t.parent_id = c.id AND t.parent_type = 'Cases'
                      LEFT JOIN (SELECT related_id, billable_duration FROM tasks_cstm WHERE related_id = t.id) bd ON 1=1
                      LEFT JOIN (SELECT related_id, billable_rate FROM tasks_cstm WHERE related_id = t.id) br ON 1=1  
                      LEFT JOIN (SELECT related_id, billable_amount FROM tasks_cstm WHERE related_id = t.id) ba ON 1=1
                      WHERE t.assigned_user_id = ? 
                      AND t.date_start BETWEEN ? AND ?
                      AND t.status = 'Completed'
                      AND t.deleted = 0
                      ORDER BY t.date_start DESC";
            
            // For now, return mock data since the custom fields might not exist yet
            $data = array(
                array(
                    'name' => 'Legal Research',
                    'description' => 'Case law research for defense strategy',
                    'date_start' => date('Y-m-d H:i:s'),
                    'duration' => 1.5,
                    'rate' => 250.00,
                    'amount' => 375.00,
                    'case_name' => 'AI Test Case',
                    'case_number' => 'CR-2025-001'
                )
            );
            
        } catch (Exception $e) {
            $GLOBALS['log']->error('getBillableHoursData Error: ' . $e->getMessage());
        }
        
        return $data;
    }
}