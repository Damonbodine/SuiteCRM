<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch
 * 
 * SugarBean implementation for Attorney Conflict of Interest Search functionality.
 * This module provides comprehensive search capabilities across Contacts, Accounts, 
 * and Cases modules to identify potential conflicts of interest for legal practices.
 */
class ConflictSearch extends SugarBean
{
    public $table_name = 'conflict_search';
    public $object_name = 'ConflictSearch';
    public $module_name = 'ConflictSearch';
    public $module_dir = 'ConflictSearch';
    
    public $new_schema = true;
    public $importable = false;
    public $duplicates_found = false;
    public $duplicates_checked = false;
    
    // Standard SugarBean fields
    public $id;
    public $name;
    public $date_entered;
    public $date_modified;
    public $modified_user_id;
    public $modified_by_name;
    public $created_by;
    public $created_by_name;
    public $description;
    public $deleted;
    public $assigned_user_id;
    public $assigned_user_name;
    
    // ConflictSearch specific fields
    public $search_term;
    public $search_type;
    public $modules_searched;
    public $confidence_threshold;
    public $total_matches_found;
    public $high_confidence_matches;
    public $medium_confidence_matches;
    public $low_confidence_matches;
    public $search_status;
    public $search_results;
    public $execution_time;
    public $performed_by;
    public $search_criteria;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Set default values
        $this->search_type = 'comprehensive';
        $this->modules_searched = 'Contacts,Accounts,Cases';
        $this->confidence_threshold = 75;
        $this->search_status = 'pending';
        $this->total_matches_found = 0;
        $this->high_confidence_matches = 0;
        $this->medium_confidence_matches = 0;
        $this->low_confidence_matches = 0;
    }
    
    /**
     * Override save to add custom validation and processing
     */
    public function save($check_notify = false)
    {
        // Validate required fields
        if (empty($this->search_term)) {
            throw new InvalidArgumentException('Search term is required for conflict search');
        }
        
        // Sanitize search term
        $this->search_term = $this->sanitizeSearchTerm($this->search_term);
        
        // Set performed_by to current user if not set
        if (empty($this->performed_by)) {
            global $current_user;
            $this->performed_by = $current_user->id ?? '';
        }
        
        // Set name if not provided
        if (empty($this->name)) {
            $this->name = 'Conflict Search: ' . substr($this->search_term, 0, 50);
        }
        
        return parent::save($check_notify);
    }
    
    /**
     * Sanitize search term to prevent injection attacks
     * 
     * @param string $searchTerm The raw search term
     * @return string The sanitized search term
     */
    private function sanitizeSearchTerm($searchTerm)
    {
        // Remove potential script tags and dangerous characters
        $searchTerm = strip_tags($searchTerm);
        $searchTerm = htmlentities($searchTerm, ENT_QUOTES, 'UTF-8');
        
        // Remove potential SQL injection patterns
        $dangerousPatterns = [
            '/(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|UNION)\b)/i',
            '/(\b(OR|AND)\s+\d+\s*=\s*\d+)/i',
            '/(--|\#|\/\*|\*\/)/i',
            '/(\bSCRIPT\b)/i',
            '/(\bjavascript:)/i'
        ];
        
        foreach ($dangerousPatterns as $pattern) {
            $searchTerm = preg_replace($pattern, '', $searchTerm);
        }
        
        return trim($searchTerm);
    }
    
    /**
     * Perform the actual conflict search
     * 
     * @return array Search results with conflict analysis
     */
    public function performConflictSearch()
    {
        if (empty($this->search_term)) {
            throw new InvalidArgumentException('Search term is required');
        }
        
        $startTime = microtime(true);
        $this->search_status = 'running';
        $this->save();
        
        try {
            // Load the ConflictSearchEngine
            require_once 'custom/lib/ConflictSearch/ConflictSearchEngine.php';
            
            $searchEngine = new ConflictSearchEngine();
            
            // Prepare search parameters
            $searchParams = [
                'term' => $this->search_term,
                'modules' => explode(',', $this->modules_searched),
                'confidence_threshold' => $this->confidence_threshold,
                'search_type' => $this->search_type
            ];
            
            // Perform the search
            $results = $searchEngine->searchConflicts($searchParams);
            
            // Process and store results
            $this->processSearchResults($results);
            
            $this->search_status = 'completed';
            $this->execution_time = microtime(true) - $startTime;
            $this->save();
            
            return $results;
            
        } catch (Exception $e) {
            $this->search_status = 'failed';
            $this->description = 'Search failed: ' . $e->getMessage();
            $this->execution_time = microtime(true) - $startTime;
            $this->save();
            
            throw $e;
        }
    }
    
    /**
     * Process and categorize search results
     * 
     * @param array $results Raw search results from the engine
     */
    private function processSearchResults($results)
    {
        $this->total_matches_found = count($results);
        $this->high_confidence_matches = 0;
        $this->medium_confidence_matches = 0;
        $this->low_confidence_matches = 0;
        
        foreach ($results as $result) {
            $confidence = $result['confidence'] ?? 0;
            
            if ($confidence >= 90) {
                $this->high_confidence_matches++;
            } elseif ($confidence >= 70) {
                $this->medium_confidence_matches++;
            } else {
                $this->low_confidence_matches++;
            }
        }
        
        // Store detailed results as JSON
        $this->search_results = json_encode($results);
    }
    
    /**
     * Get formatted search results
     * 
     * @return array Decoded search results
     */
    public function getSearchResults()
    {
        if (empty($this->search_results)) {
            return [];
        }
        
        return json_decode($this->search_results, true) ?? [];
    }
    
    /**
     * Get conflict summary statistics
     * 
     * @return array Summary of conflicts found
     */
    public function getConflictSummary()
    {
        return [
            'total_matches' => $this->total_matches_found,
            'high_confidence' => $this->high_confidence_matches,
            'medium_confidence' => $this->medium_confidence_matches,
            'low_confidence' => $this->low_confidence_matches,
            'search_status' => $this->search_status,
            'execution_time' => $this->execution_time,
            'modules_searched' => explode(',', $this->modules_searched),
            'confidence_threshold' => $this->confidence_threshold
        ];
    }
    
    /**
     * Export search results to various formats
     * 
     * @param string $format Export format (pdf, csv, json)
     * @return mixed Export data or file path
     */
    public function exportResults($format = 'json')
    {
        $results = $this->getSearchResults();
        $summary = $this->getConflictSummary();
        
        switch (strtolower($format)) {
            case 'json':
                return json_encode([
                    'search_info' => $summary,
                    'results' => $results
                ], JSON_PRETTY_PRINT);
                
            case 'csv':
                return $this->exportToCSV($results, $summary);
                
            case 'pdf':
                return $this->exportToPDF($results, $summary);
                
            default:
                throw new InvalidArgumentException('Unsupported export format: ' . $format);
        }
    }
    
    /**
     * Export results to CSV format
     * 
     * @param array $results Search results
     * @param array $summary Search summary
     * @return string CSV content
     */
    private function exportToCSV($results, $summary)
    {
        $csvContent = "Conflict Search Report\n";
        $csvContent .= "Search Term," . $this->search_term . "\n";
        $csvContent .= "Total Matches," . $summary['total_matches'] . "\n";
        $csvContent .= "High Confidence," . $summary['high_confidence'] . "\n";
        $csvContent .= "Medium Confidence," . $summary['medium_confidence'] . "\n";
        $csvContent .= "Low Confidence," . $summary['low_confidence'] . "\n";
        $csvContent .= "\n";
        
        $csvContent .= "Module,Entity Name,Match Type,Confidence,Details\n";
        
        foreach ($results as $result) {
            $csvContent .= sprintf(
                "%s,%s,%s,%d,%s\n",
                $result['module'] ?? '',
                '"' . str_replace('"', '""', $result['name'] ?? '') . '"',
                $result['match_type'] ?? '',
                $result['confidence'] ?? 0,
                '"' . str_replace('"', '""', $result['details'] ?? '') . '"'
            );
        }
        
        return $csvContent;
    }
    
    /**
     * Export results to PDF format
     * 
     * @param array $results Search results  
     * @param array $summary Search summary
     * @return string PDF file path or content
     */
    private function exportToPDF($results, $summary)
    {
        // This would integrate with SuiteCRM's existing PDF generation system
        // For now, return a placeholder
        return "PDF export functionality would be implemented here using SuiteCRM's Sugarpdf system";
    }
    
    /**
     * Check if current user has permission to perform conflict searches
     * 
     * @return bool True if user has permission
     */
    public function checkSearchPermission()
    {
        global $current_user;
        
        if (empty($current_user)) {
            return false;
        }
        
        // Check if user is admin
        if (!empty($current_user->is_admin)) {
            return true;
        }
        
        // Check ACL permissions for this module
        if (!ACLController::checkAccess('ConflictSearch', 'view', true)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get recent conflict searches for current user
     * 
     * @param int $limit Number of recent searches to return
     * @return array Recent searches
     */
    public function getRecentSearches($limit = 10)
    {
        global $current_user;
        
        if (empty($current_user)) {
            return [];
        }
        
        $query = "SELECT id, name, search_term, search_status, date_modified, total_matches_found 
                  FROM {$this->table_name} 
                  WHERE performed_by = ? AND deleted = 0 
                  ORDER BY date_modified DESC 
                  LIMIT ?";
        
        $result = $this->db->pQuery($query, [$current_user->id, $limit]);
        
        $searches = [];
        while ($row = $this->db->fetchByAssoc($result)) {
            $searches[] = $row;
        }
        
        return $searches;
    }
    
    /**
     * Bean implements method to indicate this bean supports ACL
     */
    public function bean_implements($interface)
    {
        switch ($interface) {
            case 'ACL':
                return true;
            default:
                return false;
        }
    }
}