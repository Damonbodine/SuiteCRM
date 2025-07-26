<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once 'include/MVC/Controller/SugarController.php';

/**
 * ConflictSearchController
 * 
 * Custom controller for the ConflictSearch module to handle specialized actions
 * like performing conflict searches, exporting results, and managing search operations.
 */
class ConflictSearchController extends SugarController
{
    /**
     * Pre-process actions before main action execution
     */
    public function preProcess()
    {
        parent::preProcess();
        
        // Check user permissions for conflict search functionality
        if (!$this->checkConflictSearchPermissions()) {
            sugar_die('Access Denied: You do not have permission to use the Conflict Search module.');
        }
    }
    
    /**
     * Check for urgent conflicts (AJAX endpoint for navigation badge)
     */
    public function action_check_urgent_conflicts()
    {
        // Only respond to AJAX requests or bypass calls
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            if (empty($_REQUEST['to_pdf'])) { // Allow bypass for hook calls
                die('Direct access not allowed');
            }
        }
        
        global $current_user;
        
        $urgent_count = 0;
        
        try {
            $db = DBManagerFactory::getInstance();
            
            // Count high-risk matches from recent searches (last 7 days)
            $query = "SELECT COUNT(*) as urgent_count 
                     FROM conflict_search 
                     WHERE deleted = 0 
                     AND high_confidence_matches > 0 
                     AND date_entered >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                     AND search_status = 'completed'";
            
            $result = $db->query($query);
            $row = $db->fetchByAssoc($result);
            $urgent_count = intval($row['urgent_count'] ?? 0);
            
        } catch (Exception $e) {
            // Silently fail for notification badge
            $urgent_count = 0;
        }
        
        header('Content-Type: application/json');
        echo json_encode(array(
            'urgent_count' => $urgent_count,
            'status' => 'success'
        ));
        die();
    }
    
    /**
     * Handle search action - perform a new conflict search
     */
    public function action_search()
    {
        try {
            $searchTerm = $_REQUEST['search_term'] ?? '';
            $searchType = $_REQUEST['search_type'] ?? 'comprehensive';
            $modules = $_REQUEST['modules'] ?? 'Contacts,Accounts,Cases';
            $confidenceThreshold = (int)($_REQUEST['confidence_threshold'] ?? 75);
            
            // Validate input
            if (empty($searchTerm)) {
                throw new InvalidArgumentException('Search term is required');
            }
            
            if (strlen($searchTerm) < 3) {
                throw new InvalidArgumentException('Search term must be at least 3 characters');
            }
            
            // Create new ConflictSearch record
            $conflictSearch = BeanFactory::newBean('ConflictSearch');
            $conflictSearch->search_term = $searchTerm;
            $conflictSearch->search_type = $searchType;
            $conflictSearch->modules_searched = $modules;
            $conflictSearch->confidence_threshold = $confidenceThreshold;
            $conflictSearch->search_criteria = json_encode($_REQUEST);
            
            // Save the search record
            $conflictSearch->save();
            
            // Perform the actual search
            $results = $conflictSearch->performConflictSearch();
            
            // Redirect to results view
            $this->view = 'search_results';
            $_REQUEST['search_id'] = $conflictSearch->id;
            $_REQUEST['results'] = $results;
            
        } catch (Exception $e) {
            $_SESSION['conflict_search_error'] = $e->getMessage();
            SugarApplication::redirect('index.php?module=ConflictSearch&action=EditView');
        }
    }
    
    /**
     * Handle export action - export search results
     */
    public function action_export()
    {
        try {
            $searchId = $_REQUEST['search_id'] ?? '';
            $format = $_REQUEST['format'] ?? 'json';
            
            if (empty($searchId)) {
                throw new InvalidArgumentException('Search ID is required for export');
            }
            
            // Load the search record
            $conflictSearch = BeanFactory::getBean('ConflictSearch', $searchId);
            if (!$conflictSearch || !$conflictSearch->id) {
                throw new NotFoundException('Conflict search not found');
            }
            
            // Check permissions
            if (!$this->checkRecordAccess($conflictSearch)) {
                throw new AccessDeniedException('You do not have permission to export this search');
            }
            
            // Generate export
            $exportData = $conflictSearch->exportResults($format);
            
            // Set appropriate headers and output
            $this->outputExport($exportData, $format, $conflictSearch->name);
            
        } catch (Exception $e) {
            $_SESSION['conflict_search_error'] = $e->getMessage();
            SugarApplication::redirect('index.php?module=ConflictSearch&action=index');
        }
    }
    
    /**
     * Handle quick search action - AJAX endpoint for quick searches
     */
    public function action_quick_search()
    {
        try {
            $searchTerm = $_REQUEST['term'] ?? '';
            
            if (empty($searchTerm)) {
                echo json_encode(['error' => 'Search term is required']);
                return;
            }
            
            // Perform a lightweight search for quick results
            require_once 'custom/lib/ConflictSearch/ConflictSearchEngine.php';
            $searchEngine = new ConflictSearchEngine();
            
            $quickResults = $searchEngine->quickSearch($searchTerm);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'results' => $quickResults,
                'total' => count($quickResults)
            ]);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        
        // Prevent further output
        sugar_cleanup(true);
    }
    
    /**
     * Handle search results view
     */
    public function action_search_results()
    {
        $searchId = $_REQUEST['search_id'] ?? '';
        
        if (empty($searchId)) {
            SugarApplication::redirect('index.php?module=ConflictSearch&action=index');
            return;
        }
        
        // Load the search record
        $conflictSearch = BeanFactory::getBean('ConflictSearch', $searchId);
        if (!$conflictSearch || !$conflictSearch->id) {
            $_SESSION['conflict_search_error'] = 'Search record not found';
            SugarApplication::redirect('index.php?module=ConflictSearch&action=index');
            return;
        }
        
        // Check permissions
        if (!$this->checkRecordAccess($conflictSearch)) {
            sugar_die('Access Denied: You do not have permission to view this search');
        }
        
        // Set up view data
        $this->view_object_map['ConflictSearch'] = $conflictSearch;
        $this->view = 'search_results';
    }
    
    /**
     * Handle rerun search action
     */
    public function action_rerun()
    {
        try {
            $searchId = $_REQUEST['record'] ?? '';
            
            if (empty($searchId)) {
                throw new InvalidArgumentException('Search ID is required');
            }
            
            // Load the original search record
            $originalSearch = BeanFactory::getBean('ConflictSearch', $searchId);
            if (!$originalSearch || !$originalSearch->id) {
                throw new NotFoundException('Original search not found');
            }
            
            // Check permissions
            if (!$this->checkRecordAccess($originalSearch)) {
                throw new AccessDeniedException('You do not have permission to rerun this search');
            }
            
            // Create a new search record with the same parameters
            $newSearch = BeanFactory::newBean('ConflictSearch');
            $newSearch->search_term = $originalSearch->search_term;
            $newSearch->search_type = $originalSearch->search_type;
            $newSearch->modules_searched = $originalSearch->modules_searched;
            $newSearch->confidence_threshold = $originalSearch->confidence_threshold;
            $newSearch->search_criteria = $originalSearch->search_criteria;
            $newSearch->name = 'Rerun: ' . $originalSearch->name;
            $newSearch->save();
            
            // Perform the search
            $results = $newSearch->performConflictSearch();
            
            // Redirect to results
            SugarApplication::redirect('index.php?module=ConflictSearch&action=search_results&search_id=' . $newSearch->id);
            
        } catch (Exception $e) {
            $_SESSION['conflict_search_error'] = $e->getMessage();
            SugarApplication::redirect('index.php?module=ConflictSearch&action=DetailView&record=' . $searchId);
        }
    }
    
    /**
     * Check if current user has permission to use conflict search functionality
     * 
     * @return bool True if user has permission
     */
    private function checkConflictSearchPermissions()
    {
        global $current_user;
        
        if (empty($current_user)) {
            return false;
        }
        
        // Admins always have access
        if (!empty($current_user->is_admin)) {
            return true;
        }
        
        // Check module access permissions
        if (!ACLController::checkAccess('ConflictSearch', 'access', true)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check if current user has access to a specific search record
     * 
     * @param ConflictSearch $conflictSearch The search record to check
     * @return bool True if user has access
     */
    private function checkRecordAccess($conflictSearch)
    {
        global $current_user;
        
        if (empty($current_user) || empty($conflictSearch)) {
            return false;
        }
        
        // Admins can access all records
        if (!empty($current_user->is_admin)) {
            return true;
        }
        
        // Check if user performed the search
        if ($conflictSearch->performed_by === $current_user->id) {
            return true;
        }
        
        // Check if user is assigned to the record
        if ($conflictSearch->assigned_user_id === $current_user->id) {
            return true;
        }
        
        // Check ACL permissions for the specific record
        if (!$conflictSearch->ACLAccess('view')) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Output export data with appropriate headers
     * 
     * @param mixed $data Export data
     * @param string $format Export format
     * @param string $filename Base filename
     */
    private function outputExport($data, $format, $filename)
    {
        $safeFilename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
        $timestamp = date('Y-m-d_H-i-s');
        
        switch (strtolower($format)) {
            case 'csv':
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $safeFilename . '_' . $timestamp . '.csv"');
                echo $data;
                break;
                
            case 'pdf':
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $safeFilename . '_' . $timestamp . '.pdf"');
                echo $data;
                break;
                
            case 'json':
            default:
                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="' . $safeFilename . '_' . $timestamp . '.json"');
                echo $data;
                break;
        }
        
        // Prevent further output
        sugar_cleanup(true);
    }
      /**
       * Settings action - redirect to settings view
       */
      public function action_settings()
      {
          header("Location: index.php?module=ConflictSearch&action=settings&view=settings");
          die();
      }
  
}
