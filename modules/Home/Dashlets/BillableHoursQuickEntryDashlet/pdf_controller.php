<?php
/**
 * Billable Hours PDF Generation Controller
 * Handles PDF export requests from the billable hours dashlet
 */

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

use SuiteCRM\PDF\Exceptions\PDFException;
use SuiteCRM\PDF\PDFWrapper;

require_once('modules/AOR_Reports/aor_utils.php');

class BillableHoursPDFController
{
    private $currentUser;
    private $db;
    
    public function __construct()
    {
        global $current_user;
        $this->currentUser = $current_user;
        $this->db = DBManagerFactory::getInstance();
        
        // Log initialization
        $GLOBALS['log']->info('BillableHoursPDFController: Initialized for user ' . $this->currentUser->user_name);
    }
    
    /**
     * Generate PDF report based on request parameters
     */
    public function generatePDF()
    {
        $GLOBALS['log']->info('BillableHoursPDFController: generatePDF() called');
        
        try {
            // Validate user authentication
            if (empty($this->currentUser->id)) {
                throw new Exception('User not authenticated');
            }
            
            // Get and validate parameters
            $startDate = $_REQUEST['start_date'] ?? date('Y-m-01');
            $endDate = $_REQUEST['end_date'] ?? date('Y-m-d');
            $reportType = $_REQUEST['report_type'] ?? 'summary';
            $attorneyFilter = $_REQUEST['attorney_filter'] ?? 'current';
            $caseFilter = $_REQUEST['case_filter'] ?? '';
            
            $GLOBALS['log']->info("BillableHoursPDFController: Parameters - startDate: $startDate, endDate: $endDate, reportType: $reportType");
            
            $options = array(
                'include_descriptions' => !empty($_REQUEST['include_descriptions']),
                'group_by_case' => !empty($_REQUEST['group_by_case']),
                'show_totals' => !empty($_REQUEST['show_totals'])
            );
            
            // Get report data
            $reportData = $this->getReportData($startDate, $endDate, $reportType, $attorneyFilter, $caseFilter);
            $GLOBALS['log']->info('BillableHoursPDFController: Retrieved ' . count($reportData) . ' records');
            
            // Generate HTML
            $html = $this->generateReportHTML($reportData, $reportType, $options);
            
            // Generate and output PDF
            $this->outputPDF($html, $reportType, $startDate, $endDate);
            
        } catch (Exception $e) {
            $GLOBALS['log']->error('BillableHoursPDFController::generatePDF() Error: ' . $e->getMessage());
            
            // Return error response
            if (!headers_sent()) {
                header('Content-Type: application/json');
                echo json_encode(array(
                    'error' => 'Failed to generate PDF: ' . $e->getMessage(),
                    'success' => false
                ));
            }
        }
    }
    
    /**
     * Get billable hours data based on filters
     */
    private function getReportData($startDate, $endDate, $reportType, $attorneyFilter, $caseFilter)
    {
        $whereConditions = array();
        $whereConditions[] = "t.is_billable = 1";
        $whereConditions[] = "t.deleted = 0";
        $whereConditions[] = "t.billable_entry_date >= '" . $this->db->quote($startDate) . "'";
        $whereConditions[] = "t.billable_entry_date <= '" . $this->db->quote($endDate) . "'";
        
        if ($attorneyFilter === 'current') {
            $whereConditions[] = "t.assigned_user_id = '" . $this->currentUser->id . "'";
        }
        
        if (!empty($caseFilter)) {
            $whereConditions[] = "t.parent_type = 'Cases'";
            $whereConditions[] = "t.parent_id = '" . $this->db->quote($caseFilter) . "'";
        }
        
        $orderBy = $this->getOrderByClause($reportType);
        
        $sql = "SELECT 
                    t.id,
                    t.name,
                    t.description,
                    COALESCE(t.billable_duration, 0) as billable_duration,
                    COALESCE(t.billable_rate, 0) as billable_rate,
                    COALESCE(t.billable_amount, 0) as billable_amount,
                    COALESCE(t.activity_type, '') as activity_type,
                    t.billable_entry_date,
                    t.billable_entry_time,
                    t.assigned_user_id,
                    CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) AS attorney_name,
                    t.parent_id,
                    t.parent_type,
                    CASE 
                        WHEN t.parent_type = 'Cases' AND c.name IS NOT NULL THEN c.name
                        ELSE 'No Case'
                    END AS case_name,
                    COALESCE(c.case_number, '') as case_number
                FROM tasks t
                LEFT JOIN users u ON t.assigned_user_id = u.id AND u.deleted = 0
                LEFT JOIN cases c ON t.parent_type = 'Cases' AND t.parent_id = c.id AND c.deleted = 0
                WHERE " . implode(' AND ', $whereConditions) . "
                ORDER BY " . $orderBy;
        
        $GLOBALS['log']->debug('BillableHoursPDFController SQL: ' . $sql);
        
        try {
            $result = $this->db->query($sql);
            $data = array();
            
            while ($row = $this->db->fetchByAssoc($result)) {
                $data[] = $row;
            }
            
            return $data;
            
        } catch (Exception $e) {
            $GLOBALS['log']->error('BillableHoursPDFController: Database query failed: ' . $e->getMessage());
            throw new Exception('Database query failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get appropriate ORDER BY clause for report type
     */
    private function getOrderByClause($reportType)
    {
        switch ($reportType) {
            case 'summary':
                return "case_name, t.billable_entry_date, t.billable_entry_time";
            case 'timesheet':
                return "t.billable_entry_date, t.billable_entry_time";
            case 'case':
                return "case_name, attorney_name, t.billable_entry_date";
            default:
                return "t.billable_entry_date DESC, t.billable_entry_time DESC";
        }
    }
    
    /**
     * Generate HTML content for the report
     */
    private function generateReportHTML($data, $reportType, $options)
    {
        $html = $this->getReportHeader($reportType);
        
        if (empty($data)) {
            $html .= "<p>No billable hours found for the selected period.</p>";
            return $html;
        }
        
        switch ($reportType) {
            case 'summary':
                $html .= $this->generateSummaryReport($data, $options);
                break;
            case 'timesheet':
                $html .= $this->generateTimesheetReport($data, $options);
                break;
            case 'case':
                $html .= $this->generateCaseReport($data, $options);
                break;
            default:
                $html .= $this->generateSummaryReport($data, $options);
        }
        
        if ($options['show_totals']) {
            $html .= $this->generateTotalsSection($data);
        }
        
        return $html;
    }
    
    /**
     * Generate report header
     */
    private function getReportHeader($reportType)
    {
        $titles = array(
            'summary' => 'Billable Hours Summary Report',
            'timesheet' => 'Attorney Timesheet Report',
            'case' => 'Case Billing Report'
        );
        
        $title = $titles[$reportType] ?? 'Billable Hours Report';
        $dateRange = ($_REQUEST['start_date'] ?? date('Y-m-01')) . ' to ' . ($_REQUEST['end_date'] ?? date('Y-m-d'));
        
        return "
        <div class='report-header'>
            <h1>{$title}</h1>
            <p>Period: {$dateRange}</p>
            <p>Generated: " . date('Y-m-d H:i:s') . "</p>
            <p>Attorney: {$this->currentUser->full_name}</p>
        </div>";
    }
    
    /**
     * Generate summary report HTML
     */
    private function generateSummaryReport($data, $options)
    {
        $html = "<table class='report-table'>";
        $html .= "<thead>";
        $html .= "<tr>";
        $html .= "<th>Date</th>";
        $html .= "<th>Case</th>";
        $html .= "<th>Activity</th>";
        $html .= "<th>Duration</th>";
        $html .= "<th>Rate</th>";
        $html .= "<th>Amount</th>";
        if ($options['include_descriptions']) {
            $html .= "<th>Description</th>";
        }
        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";
        
        foreach ($data as $row) {
            $html .= "<tr>";
            $html .= "<td>" . date('M j, Y', strtotime($row['billable_entry_date'])) . "</td>";
            $html .= "<td>" . htmlspecialchars($row['case_name']) . "</td>";
            $html .= "<td>" . htmlspecialchars($row['activity_type']) . "</td>";
            $html .= "<td>" . number_format($row['billable_duration'], 2) . "h</td>";
            $html .= "<td>$" . number_format($row['billable_rate'], 2) . "</td>";
            $html .= "<td>$" . number_format($row['billable_amount'], 2) . "</td>";
            if ($options['include_descriptions']) {
                $html .= "<td>" . htmlspecialchars(substr($row['description'], 0, 100)) . "</td>";
            }
            $html .= "</tr>";
        }
        
        $html .= "</tbody>";
        $html .= "</table>";
        
        return $html;
    }
    
    /**
     * Generate timesheet report HTML
     */
    private function generateTimesheetReport($data, $options)
    {
        // Group data by date for timesheet format
        $groupedData = array();
        foreach ($data as $row) {
            $date = $row['billable_entry_date'];
            if (!isset($groupedData[$date])) {
                $groupedData[$date] = array();
            }
            $groupedData[$date][] = $row;
        }
        
        $html = "";
        foreach ($groupedData as $date => $entries) {
            $html .= "<h3>" . date('l, F j, Y', strtotime($date)) . "</h3>";
            $html .= "<table class='report-table'>";
            $html .= "<thead>";
            $html .= "<tr><th>Time</th><th>Case</th><th>Activity</th><th>Duration</th><th>Rate</th><th>Amount</th></tr>";
            $html .= "</thead>";
            $html .= "<tbody>";
            
            $dayTotal = 0;
            foreach ($entries as $entry) {
                $html .= "<tr>";
                $html .= "<td>" . date('g:i A', strtotime($entry['billable_entry_time'])) . "</td>";
                $html .= "<td>" . htmlspecialchars($entry['case_name']) . "</td>";
                $html .= "<td>" . htmlspecialchars($entry['activity_type']) . "</td>";
                $html .= "<td>" . number_format($entry['billable_duration'], 2) . "h</td>";
                $html .= "<td>$" . number_format($entry['billable_rate'], 2) . "</td>";
                $html .= "<td>$" . number_format($entry['billable_amount'], 2) . "</td>";
                $html .= "</tr>";
                $dayTotal += $entry['billable_amount'];
            }
            
            $html .= "<tr class='total-row'>";
            $html .= "<td colspan='5'><strong>Daily Total:</strong></td>";
            $html .= "<td><strong>$" . number_format($dayTotal, 2) . "</strong></td>";
            $html .= "</tr>";
            $html .= "</tbody>";
            $html .= "</table>";
            $html .= "<br>";
        }
        
        return $html;
    }
    
    /**
     * Generate case report HTML
     */
    private function generateCaseReport($data, $options)
    {
        // Group data by case
        $groupedData = array();
        foreach ($data as $row) {
            $caseKey = $row['case_name'];
            if (!isset($groupedData[$caseKey])) {
                $groupedData[$caseKey] = array();
            }
            $groupedData[$caseKey][] = $row;
        }
        
        $html = "";
        foreach ($groupedData as $caseName => $entries) {
            $html .= "<h3>Case: " . htmlspecialchars($caseName) . "</h3>";
            $html .= "<table class='report-table'>";
            $html .= "<thead>";
            $html .= "<tr><th>Date</th><th>Attorney</th><th>Activity</th><th>Duration</th><th>Rate</th><th>Amount</th></tr>";
            $html .= "</thead>";
            $html .= "<tbody>";
            
            $caseTotal = 0;
            foreach ($entries as $entry) {
                $html .= "<tr>";
                $html .= "<td>" . date('M j, Y', strtotime($entry['billable_entry_date'])) . "</td>";
                $html .= "<td>" . htmlspecialchars($entry['attorney_name']) . "</td>";
                $html .= "<td>" . htmlspecialchars($entry['activity_type']) . "</td>";
                $html .= "<td>" . number_format($entry['billable_duration'], 2) . "h</td>";
                $html .= "<td>$" . number_format($entry['billable_rate'], 2) . "</td>";
                $html .= "<td>$" . number_format($entry['billable_amount'], 2) . "</td>";
                $html .= "</tr>";
                $caseTotal += $entry['billable_amount'];
            }
            
            $html .= "<tr class='total-row'>";
            $html .= "<td colspan='5'><strong>Case Total:</strong></td>";
            $html .= "<td><strong>$" . number_format($caseTotal, 2) . "</strong></td>";
            $html .= "</tr>";
            $html .= "</tbody>";
            $html .= "</table>";
            $html .= "<br>";
        }
        
        return $html;
    }
    
    /**
     * Generate totals section
     */
    private function generateTotalsSection($data)
    {
        $totalHours = 0;
        $totalAmount = 0;
        $totalEntries = count($data);
        
        foreach ($data as $row) {
            $totalHours += $row['billable_duration'];
            $totalAmount += $row['billable_amount'];
        }
        
        $averageRate = $totalHours > 0 ? $totalAmount / $totalHours : 0;
        
        return "
        <div class='totals-section'>
            <h3>Summary Totals</h3>
            <table class='totals-table'>
                <tr><td>Total Entries:</td><td>{$totalEntries}</td></tr>
                <tr><td>Total Hours:</td><td>" . number_format($totalHours, 2) . "</td></tr>
                <tr><td>Total Amount:</td><td>$" . number_format($totalAmount, 2) . "</td></tr>
                <tr><td>Average Rate:</td><td>$" . number_format($averageRate, 2) . "</td></tr>
            </table>
        </div>";
    }
    
    /**
     * Output PDF using SuiteCRM's PDF engine
     */
    private function outputPDF($html, $reportType, $startDate, $endDate)
    {
        $stylesheet = $this->getPDFStylesheet();
        $filename = $this->getFilename($reportType, $startDate, $endDate);
        
        try {
            $pdf = PDFWrapper::getPDFEngine();
            $pdf->configurePDF([
                'mode' => 'en',
                'font' => 'DejaVuSansCondensed',
            ]);
            $pdf->addCSS($stylesheet);
            $pdf->writeHTML($html);
            $pdf->outputPDF($filename, 'D');
            
            $GLOBALS['log']->info("BillableHoursPDFController: Successfully generated PDF: $filename");
            
        } catch (PDFException $e) {
            $GLOBALS['log']->error('BillableHoursPDFController: PDF generation failed: ' . $e->getMessage());
            throw new Exception('PDF generation failed: ' . $e->getMessage());
        } catch (Exception $e) {
            $GLOBALS['log']->error('BillableHoursPDFController: PDF output error: ' . $e->getMessage());
            throw new Exception('PDF output error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get PDF stylesheet
     */
    private function getPDFStylesheet()
    {
        $cssFile = 'modules/Home/Dashlets/BillableHoursQuickEntryDashlet/pdf/billable_hours.css';
        if (file_exists($cssFile)) {
            return file_get_contents($cssFile);
        }
        
        // Default CSS if file doesn't exist
        return "
        body { font-family: DejaVuSansCondensed, sans-serif; font-size: 10pt; }
        .report-header { text-align: center; margin-bottom: 20px; }
        .report-header h1 { font-size: 16pt; margin-bottom: 10px; }
        .report-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .report-table th, .report-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .report-table th { background-color: #f5f5f5; font-weight: bold; }
        .total-row { background-color: #f9f9f9; font-weight: bold; }
        .totals-section { margin-top: 30px; }
        .totals-table { width: 50%; }
        .totals-table td { border: 1px solid #ccc; padding: 5px; }
        ";
    }
    
    /**
     * Generate appropriate filename
     */
    private function getFilename($reportType, $startDate, $endDate)
    {
        $type = ucfirst($reportType);
        $dateRange = date('Y-m-d', strtotime($startDate)) . '_to_' . date('Y-m-d', strtotime($endDate));
        return "BillableHours_{$type}_Report_{$dateRange}.pdf";
    }
}