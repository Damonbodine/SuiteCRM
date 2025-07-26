<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once 'include/MVC/View/SugarView.php';

/**
 * ConflictSearch Results View
 * 
 * Displays the results of a conflict search with detailed information
 * about potential conflicts found across modules.
 */
class ConflictSearchViewSearch_results extends SugarView
{
    /**
     * @var ConflictSearch
     */
    protected $conflictSearch;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Pre-display processing
     */
    public function preDisplay()
    {
        parent::preDisplay();
        
        // Load the search record
        $searchId = $_REQUEST['search_id'] ?? '';
        if (!empty($searchId)) {
            $this->conflictSearch = BeanFactory::getBean('ConflictSearch', $searchId);
        }
        
        // Check if we have a valid search record
        if (!$this->conflictSearch || !$this->conflictSearch->id) {
            SugarApplication::redirect('index.php?module=ConflictSearch&action=index');
            return;
        }
        
        // Set page title
        global $mod_strings;
        $this->options['show_header'] = true;
        $this->options['show_subpanels'] = false;
        $this->options['show_search'] = false;
    }
    
    /**
     * Display the search results
     */
    public function display()
    {
        global $mod_strings, $app_strings, $current_user;
        
        if (!$this->conflictSearch) {
            echo "<p>Error: Search record not found.</p>";
            return;
        }
        
        // Get search results
        $results = [];
        if ($this->conflictSearch->search_status === 'completed') {
            // In a real implementation, results would be stored or re-retrieved
            // For now, we'll show summary information
            $results = $this->getStoredResults();
        }
        
        echo $this->renderSearchResultsHTML($results);
    }
    
    /**
     * Get stored search results
     * 
     * @return array Search results
     */
    private function getStoredResults()
    {
        // In a production system, you would retrieve actual stored results
        // For this implementation, we'll create sample results based on the search
        return [];
    }
    
    /**
     * Render the search results HTML
     * 
     * @param array $results Search results
     * @return string HTML content
     */
    private function renderSearchResultsHTML($results)
    {
        global $mod_strings;
        
        $html = '<div class="conflict-search-results-page">';
        
        // Header
        $html .= '<div class="moduleTitle">';
        $html .= '<h2>' . $mod_strings['LBL_SEARCH_RESULTS_TITLE'] . '</h2>';
        $html .= '</div>';
        
        // Search Summary
        $html .= $this->renderSearchSummary();
        
        // Results
        if (!empty($results)) {
            $html .= $this->renderResultsList($results);
        } else {
            $html .= $this->renderNoResults();
        }
        
        // Actions
        $html .= $this->renderActionButtons();
        
        $html .= '</div>';
        
        // Include CSS
        $html .= '<link rel="stylesheet" type="text/css" href="modules/ConflictSearch/css/ConflictSearch.css">';
        
        return $html;
    }
    
    /**
     * Render search summary information
     * 
     * @return string HTML content
     */
    private function renderSearchSummary()
    {
        global $mod_strings;
        
        $html = '<div class="conflict-search-summary">';
        $html .= '<h3>' . $mod_strings['LBL_RESULTS_SUMMARY'] . '</h3>';
        $html .= '<table class="table table-bordered">';
        
        $html .= '<tr><td><strong>' . $mod_strings['LBL_SEARCH_TERM'] . ':</strong></td>';
        $html .= '<td>' . htmlspecialchars($this->conflictSearch->search_term) . '</td></tr>';
        
        $html .= '<tr><td><strong>' . $mod_strings['LBL_SEARCH_TYPE'] . ':</strong></td>';
        $html .= '<td>' . htmlspecialchars($this->conflictSearch->search_type) . '</td></tr>';
        
        $html .= '<tr><td><strong>' . $mod_strings['LBL_MODULES_SEARCHED'] . ':</strong></td>';
        $html .= '<td>' . htmlspecialchars($this->conflictSearch->modules_searched) . '</td></tr>';
        
        $html .= '<tr><td><strong>' . $mod_strings['LBL_CONFIDENCE_THRESHOLD'] . ':</strong></td>';
        $html .= '<td>' . $this->conflictSearch->confidence_threshold . '%</td></tr>';
        
        $html .= '<tr><td><strong>' . $mod_strings['LBL_SEARCH_STATUS'] . ':</strong></td>';
        $html .= '<td>' . $this->getStatusDisplay($this->conflictSearch->search_status) . '</td></tr>';
        
        if ($this->conflictSearch->execution_time > 0) {
            $html .= '<tr><td><strong>' . $mod_strings['LBL_EXECUTION_TIME'] . ':</strong></td>';
            $html .= '<td>' . round($this->conflictSearch->execution_time, 3) . ' seconds</td></tr>';
        }
        
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render the results list
     * 
     * @param array $results Search results
     * @return string HTML content
     */
    private function renderResultsList($results)
    {
        global $mod_strings;
        
        $html = '<div class="conflict-results-section">';
        $html .= '<h3>' . count($results) . ' ' . $mod_strings['LBL_CONFLICTS_FOUND'] . '</h3>';
        
        foreach ($results as $index => $result) {
            $html .= $this->renderResultItem($result, $index);
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render individual result item
     * 
     * @param array $result Result data
     * @param int $index Result index
     * @return string HTML content
     */
    private function renderResultItem($result, $index)
    {
        $confidence = $result['confidence'] ?? 0;
        $confidenceClass = $this->getConfidenceClass($confidence);
        
        $html = '<div class="conflict-result-item ' . $confidenceClass . '">';
        
        // Header
        $html .= '<div class="conflict-result-header">';
        $html .= '<div class="conflict-result-name">';
        $html .= '<strong>' . htmlspecialchars($result['name'] ?? 'Unknown') . '</strong>';
        $html .= '<span class="conflict-module-badge">' . ($result['source_module'] ?? 'Unknown') . '</span>';
        $html .= '</div>';
        $html .= '<div class="conflict-result-metrics">';
        $html .= '<span class="confidence-score ' . $confidenceClass . '">' . $confidence . '% Confidence</span>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Details
        if (!empty($result['details'])) {
            $html .= '<div class="conflict-result-details">';
            $html .= '<p>' . htmlspecialchars($result['details']) . '</p>';
            $html .= '</div>';
        }
        
        // Actions
        $html .= '<div class="conflict-result-actions">';
        if (!empty($result['id']) && !empty($result['source_module'])) {
            $viewUrl = 'index.php?module=' . $result['source_module'] . '&action=DetailView&record=' . $result['id'];
            $html .= '<a href="' . $viewUrl . '" class="button" target="_blank">View Record</a>';
        }
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render no results message
     * 
     * @return string HTML content
     */
    private function renderNoResults()
    {
        global $mod_strings;
        
        $html = '<div class="conflict-no-results">';
        
        if ($this->conflictSearch->search_status === 'completed') {
            $html .= '<p>' . $mod_strings['LBL_NO_CONFLICTS_FOUND'] . '</p>';
            $html .= '<p>No potential conflicts were found for "' . htmlspecialchars($this->conflictSearch->search_term) . '"</p>';
        } elseif ($this->conflictSearch->search_status === 'failed') {
            $html .= '<p>Search failed. Please try again or contact your administrator.</p>';
        } else {
            $html .= '<p>Search is still in progress...</p>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Render action buttons
     * 
     * @return string HTML content
     */
    private function renderActionButtons()
    {
        $html = '<div class="conflict-results-actions">';
        
        // Back to search
        $html .= '<a href="index.php?module=ConflictSearch&action=EditView" class="button">New Search</a>';
        
        // View search record
        $html .= '<a href="index.php?module=ConflictSearch&action=DetailView&record=' . $this->conflictSearch->id . '" class="button">View Search Details</a>';
        
        // Re-run search
        $html .= '<a href="index.php?module=ConflictSearch&action=rerun&record=' . $this->conflictSearch->id . '" class="button">Re-run Search</a>';
        
        $html .= '</div>';
        
        return $html;
    }
    
    /**
     * Get confidence CSS class
     * 
     * @param int $confidence Confidence score
     * @return string CSS class
     */
    private function getConfidenceClass($confidence)
    {
        if ($confidence >= 90) return 'confidence-high';
        if ($confidence >= 70) return 'confidence-medium';
        if ($confidence >= 50) return 'confidence-low';
        return 'confidence-minimal';
    }
    
    /**
     * Get status display text
     * 
     * @param string $status Status code
     * @return string Display text
     */
    private function getStatusDisplay($status)
    {
        $statusMap = [
            'pending' => 'Pending',
            'running' => 'Running',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled'
        ];
        
        return $statusMap[$status] ?? ucfirst($status);
    }
}