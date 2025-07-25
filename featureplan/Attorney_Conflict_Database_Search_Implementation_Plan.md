# Attorney Conflict Database Search - Comprehensive Implementation Plan

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** Ultra Low-Risk Implementation  
**Risk Level:** ⭐ (Very Low)  
**Business Value:** 🏆 (Critical for ethics compliance)  

---

## 🎯 **Executive Summary**

This document provides a comprehensive, step-by-step implementation plan for creating an Attorney Conflict Database Search feature in SuiteCRM. The approach leverages existing SuiteCRM infrastructure to minimize risk while delivering critical compliance functionality for legal professionals.

**Key Benefits:**
- **Ethics Compliance**: Prevents conflicts of interest violations
- **Low Implementation Risk**: Uses existing search infrastructure
- **High Daily Value**: Essential tool for legal practice management
- **Safe Integration**: No core file modifications required

---

## 🔍 **Architecture Research Foundation**

Based on comprehensive codebase analysis, the implementation will leverage:

### **Core Infrastructure Components:**
- **Search Framework**: `/lib/Search/` - Modern, extensible search engine system
- **Security System**: ACL permissions and SecurityGroups for record-level access
- **Data Layer**: SugarBean ORM with existing security integration
- **UI Framework**: Smarty templates with SuiteP theme integration

### **Key Architecture Files:**
```
/lib/Search/                        # Search engine framework
/include/SearchForm/SearchForm2.php  # Search form components
/data/SugarBean.php                 # Core ORM with ACL integration
/modules/ACL*/ACLController.php     # Permission checking system
/modules/SecurityGroups/            # Record-level security
/themes/SuiteP/include/ListView/    # List view templates
```

---

## 🛡️ **Security-First Design Principles**

### **1. Permission Integration**
- **ACL Validation**: Check module access permissions before search
- **SecurityGroup Filtering**: Ensure users only see records they're authorized to view
- **Record-Level Security**: Leverage existing bean-level access controls

### **2. Input Security**
- **SQL Injection Prevention**: Use prepared statements exclusively
- **Input Validation**: Sanitize all search parameters
- **Rate Limiting**: Prevent abuse with request throttling

### **3. Output Security**
- **XSS Prevention**: Sanitize all search results before display
- **Data Filtering**: Apply ACL filtering to all returned results
- **Audit Logging**: Track conflict searches for compliance

---

## 📋 **Step-by-Step Implementation Plan**

### **Phase 1: Foundation & Research (Days 1-2)**

#### **Day 1 Morning: Architecture Deep Dive**

**Task 1.1: Analyze Existing Search Infrastructure**
```bash
# Key files to examine:
/lib/Search/BasicSearch/BasicSearchEngine.php  # Base search patterns
/modules/Contacts/metadata/searchdefs.php      # Contact search definitions
/modules/Accounts/metadata/searchdefs.php      # Account search definitions
/include/SearchForm/SearchForm2.php           # Search form generation
```

**Research Checklist:**
- [ ] Understand SearchEngine abstract class and extension patterns
- [ ] Analyze searchdefs.php structure and field definitions
- [ ] Review search form generation and validation
- [ ] Document security filtering mechanisms in search results

**Task 1.2: Security Integration Analysis**
```bash
# Security-critical files to understand:
/modules/ACL*/ACLController.php               # Permission checking
/modules/SecurityGroups/SecurityGroup.php    # Record filtering
/data/SugarBean.php (lines 6107-6182)       # Bean-level ACL integration
```

**Security Research Checklist:**
- [ ] Document ACL permission checking patterns
- [ ] Understand SecurityGroup record filtering
- [ ] Analyze input validation and sanitization methods
- [ ] Review audit logging mechanisms

#### **Day 1 Afternoon: UI Integration Strategy**

**Task 1.3: Theme and Template Analysis**
```bash
# UI integration points:
/themes/SuiteP/include/SearchForm/    # Search form templates
/include/ListView/ListViewSearch.php  # Search integration
/themes/SuiteP/tpls/                 # Template patterns
```

**UI Research Checklist:**
- [ ] Understand Smarty template patterns for search forms
- [ ] Analyze responsive design patterns in SuiteP theme
- [ ] Document safe customization locations
- [ ] Review existing search result display patterns

### **Phase 2: Safe Implementation Structure (Day 2)**

#### **Task 2.1: Create Custom Directory Structure**

**Directory Organization:**
```bash
# All files in custom/ directory (no core modifications)
custom/modules/Contacts/
├── ConflictSearch/
│   ├── ConflictSearchEngine.php      # Core search logic
│   ├── ConflictSearchForm.php        # Search form handling
│   ├── ConflictSearchResults.php     # Results processing
│   └── ConflictSearchSecurity.php    # Security validation
├── views/
│   └── view.conflictsearch.php       # MVC view controller
├── controllers/
│   └── controller.php                # Enhanced controller
├── metadata/
│   └── conflictsearchdefs.php        # Search field definitions
├── tpls/
│   └── conflictsearch.tpl           # Search form template
└── language/
    └── en_us.lang.php               # Language strings
```

#### **Task 2.2: Database Optimization Planning**

**Required Database Indexes:**
```sql
-- Performance optimization indexes
CREATE INDEX idx_contacts_conflict_search 
ON contacts (first_name, last_name, email1, deleted);

CREATE INDEX idx_accounts_conflict_search  
ON accounts (name, deleted);

CREATE INDEX idx_contacts_fullname_search
ON contacts (CONCAT(first_name, ' ', last_name), deleted);

-- Full-text search index for advanced searching (MySQL 5.6+)
ALTER TABLE contacts ADD FULLTEXT(first_name, last_name, description);
ALTER TABLE accounts ADD FULLTEXT(name, description);
```

### **Phase 3: Core Implementation (Days 3-4)**

#### **Task 3.1: Secure Search Engine Implementation**

**File: `custom/modules/Contacts/ConflictSearch/ConflictSearchEngine.php`**
```php
<?php
require_once('lib/Search/SearchEngine.php');

class ConflictSearchEngine extends SearchEngine {
    
    private $allowedModules = ['Contacts', 'Accounts'];
    private $maxResults = 100;
    private $cacheTimeout = 300; // 5 minutes
    
    public function performConflictSearch($searchTerm, $options = []) {
        // Step 1: Security validation
        $this->validateSearchAccess();
        $cleanTerm = $this->sanitizeSearchTerm($searchTerm);
        
        // Step 2: Check cache first
        $cacheKey = $this->generateCacheKey($cleanTerm, $options);
        $cachedResults = $this->getCachedResults($cacheKey);
        if ($cachedResults !== false) {
            return $cachedResults;
        }
        
        // Step 3: Perform secure search
        $results = [];
        foreach ($this->allowedModules as $module) {
            if ($this->canSearchModule($module)) {
                $results[$module] = $this->searchModule($module, $cleanTerm);
            }
        }
        
        // Step 4: Cache and return results
        $formattedResults = $this->formatConflictResults($results);
        $this->cacheResults($cacheKey, $formattedResults);
        
        return $formattedResults;
    }
    
    private function validateSearchAccess() {
        global $current_user;
        
        // Check basic authentication
        if (empty($current_user->id)) {
            throw new SugarApiExceptionNotAuthorized('Authentication required');
        }
        
        // Check module access permissions
        foreach ($this->allowedModules as $module) {
            if (!ACLController::checkAccess($module, 'list', true)) {
                throw new SugarApiExceptionNotAuthorized("Access denied to $module module");
            }
        }
        
        // Rate limiting check
        $this->enforceRateLimit();
    }
    
    private function sanitizeSearchTerm($term) {
        // Input validation
        if (strlen($term) < 2) {
            throw new InvalidArgumentException('Search term must be at least 2 characters');
        }
        
        if (strlen($term) > 100) {
            throw new InvalidArgumentException('Search term too long (max 100 characters)');
        }
        
        // Sanitize input
        $cleanTerm = SugarCleaner::cleanHtml($term);
        
        // Pattern validation (letters, numbers, spaces, common punctuation)
        if (!preg_match('/^[a-zA-Z0-9\s\-\.,&\'@]+$/', $cleanTerm)) {
            throw new InvalidArgumentException('Invalid characters in search term');
        }
        
        return $cleanTerm;
    }
    
    private function searchModule($module, $term) {
        global $current_user;
        
        // Create bean for security context
        $bean = BeanFactory::newBean($module);
        
        // Double-check ACL access
        if (!$bean->ACLAccess('list')) {
            return [];
        }
        
        // Build secure query
        $db = DBManagerFactory::getInstance();
        
        if ($module === 'Contacts') {
            $query = "
                SELECT c.id, c.first_name, c.last_name, c.email1, 
                       c.account_name, c.title, c.phone_work,
                       CONCAT(c.first_name, ' ', c.last_name) as full_name
                FROM contacts c
                WHERE c.deleted = 0
                AND (
                    c.first_name LIKE ? OR 
                    c.last_name LIKE ? OR 
                    CONCAT(c.first_name, ' ', c.last_name) LIKE ? OR
                    c.email1 LIKE ? OR
                    c.account_name LIKE ?
                )
                ORDER BY c.last_name, c.first_name
                LIMIT ?
            ";
            
            $searchPattern = '%' . $term . '%';
            $params = [
                $searchPattern, $searchPattern, $searchPattern, 
                $searchPattern, $searchPattern, $this->maxResults
            ];
            
        } else if ($module === 'Accounts') {
            $query = "
                SELECT a.id, a.name, a.email1, a.phone_office,
                       a.billing_address_city, a.billing_address_state,
                       a.website, a.industry
                FROM accounts a
                WHERE a.deleted = 0
                AND (
                    a.name LIKE ? OR
                    a.email1 LIKE ?
                )
                ORDER BY a.name
                LIMIT ?
            ";
            
            $searchPattern = '%' . $term . '%';
            $params = [$searchPattern, $searchPattern, $this->maxResults];
        }
        
        // Apply SecurityGroup filtering
        $securityWhere = SecurityGroup::getGroupWhere($module, $current_user->id);
        if (!empty($securityWhere)) {
            $query = str_replace('WHERE', "WHERE $securityWhere AND", $query);
        }
        
        // Execute prepared statement
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $this->sanitizeResultRow($row, $module);
        }
        
        return $results;
    }
    
    private function sanitizeResultRow($row, $module) {
        // Sanitize all output to prevent XSS
        $sanitized = [];
        foreach ($row as $key => $value) {
            $sanitized[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        
        // Add module context
        $sanitized['module_name'] = $module;
        $sanitized['detail_url'] = $this->generateDetailUrl($sanitized['id'], $module);
        
        return $sanitized;
    }
    
    private function generateDetailUrl($id, $module) {
        return "index.php?module=$module&action=DetailView&record=$id";
    }
    
    private function enforceRateLimit() {
        // Simple rate limiting (can be enhanced with Redis/Memcache)
        $rateLimitKey = 'conflict_search_' . $GLOBALS['current_user']->id;
        $currentHour = date('Y-m-d-H');
        $cacheKey = $rateLimitKey . '_' . $currentHour;
        
        $requestCount = (int)sugar_cache_retrieve($cacheKey);
        if ($requestCount >= 100) { // 100 searches per hour
            throw new SugarApiExceptionRequestTooLarge('Rate limit exceeded. Please try again later.');
        }
        
        sugar_cache_put($cacheKey, $requestCount + 1, 3600);
    }
}
```

#### **Task 3.2: Search Form and UI Implementation**

**File: `custom/modules/Contacts/views/view.conflictsearch.php`**
```php
<?php
require_once('include/MVC/View/views/view.detail.php');

class ContactsViewConflictsearch extends ViewDetail {
    
    public function __construct() {
        parent::__construct();
        $this->useForSubpanel = false;
        $this->useModuleQuickMenu = false;
    }
    
    public function preDisplay() {
        // Set security headers
        $this->setSecurityHeaders();
        
        // Verify permissions
        if (!ACLController::checkAccess('Contacts', 'list', true)) {
            sugar_die('Access Denied: Contact list permissions required for conflict checking');
        }
        
        if (!ACLController::checkAccess('Accounts', 'list', true)) {
            sugar_die('Access Denied: Account list permissions required for conflict checking');
        }
        
        parent::preDisplay();
    }
    
    public function display() {
        global $current_user, $app_strings, $mod_strings;
        
        // Set page title
        $this->ss->assign('MODULE_TITLE', 'Attorney Conflict Database Search');
        
        // Generate CSRF token
        $csrfToken = $this->generateCSRFToken();
        $this->ss->assign('csrf_token', $csrfToken);
        
        // Search form configuration
        $searchConfig = [
            'search_url' => 'index.php?module=Contacts&action=ConflictSearchAjax',
            'csrf_token' => $csrfToken,
            'max_results' => 100,
            'min_search_length' => 2
        ];
        $this->ss->assign('search_config', json_encode($searchConfig));
        
        // User preferences
        $this->ss->assign('user_date_format', $current_user->getPreference('datef'));
        $this->ss->assign('user_time_format', $current_user->getPreference('timef'));
        
        // Search help text
        $helpText = [
            'search_help' => 'Search across contacts and accounts for potential conflicts of interest.',
            'search_examples' => 'Examples: "John Smith", "Acme Corp", "john@example.com"',
            'legal_notice' => 'This search is for conflict checking purposes only. Ensure compliance with your firm\'s conflict checking procedures.'
        ];
        $this->ss->assign('help_text', $helpText);
        
        // Load the template
        echo $this->ss->fetch('custom/modules/Contacts/tpls/conflictsearch.tpl');
    }
    
    private function setSecurityHeaders() {
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'");
        header("X-Frame-Options: SAMEORIGIN");
        header("X-XSS-Protection: 1; mode=block");
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: strict-origin-when-cross-origin");
    }
    
    private function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
```

#### **Task 3.3: AJAX Controller Implementation**

**File: `custom/modules/Contacts/controllers/controller.php`**
```php
<?php
require_once('modules/Contacts/controller.php');

class CustomContactsController extends ContactsController {
    
    public function action_ConflictSearchAjax() {
        // Verify CSRF token
        $this->validateCSRFToken();
        
        // Set JSON response headers
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        try {
            // Get and validate search parameters
            $searchTerm = $this->getSearchTerm();
            $options = $this->getSearchOptions();
            
            // Perform search
            require_once('custom/modules/Contacts/ConflictSearch/ConflictSearchEngine.php');
            $searchEngine = new ConflictSearchEngine();
            $results = $searchEngine->performConflictSearch($searchTerm, $options);
            
            // Log the search for compliance
            $this->logConflictSearch($searchTerm, count($results));
            
            // Return results
            echo json_encode([
                'status' => 'success',
                'data' => $results,
                'meta' => [
                    'search_term' => $searchTerm,
                    'result_count' => count($results),
                    'timestamp' => date('c')
                ]
            ]);
            
        } catch (Exception $e) {
            $this->handleSearchError($e);
        }
    }
    
    private function validateCSRFToken() {
        $submittedToken = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (empty($submittedToken) || empty($sessionToken) || 
            !hash_equals($sessionToken, $submittedToken)) {
            throw new SugarApiExceptionNotAuthorized('Invalid CSRF token');
        }
    }
    
    private function getSearchTerm() {
        $term = $_POST['search_term'] ?? $_GET['search_term'] ?? '';
        
        if (empty($term)) {
            throw new InvalidArgumentException('Search term is required');
        }
        
        return trim($term);
    }
    
    private function getSearchOptions() {
        return [
            'modules' => $_POST['modules'] ?? ['Contacts', 'Accounts'],
            'limit' => min((int)($_POST['limit'] ?? 100), 100) // Max 100 results
        ];
    }
    
    private function logConflictSearch($searchTerm, $resultCount) {
        global $current_user;
        
        $GLOBALS['log']->info(
            "CONFLICT_SEARCH: User: {$current_user->user_name} | " .
            "Term: " . substr($searchTerm, 0, 50) . " | " .
            "Results: $resultCount | " .
            "IP: " . $this->getClientIP()
        );
    }
    
    private function handleSearchError($exception) {
        global $current_user;
        
        // Log error
        $GLOBALS['log']->error(
            "CONFLICT_SEARCH_ERROR: " . $exception->getMessage() . 
            " | User: {$current_user->user_name} | " .
            "IP: " . $this->getClientIP()
        );
        
        // Return user-friendly error
        $errorCode = 'SEARCH_ERROR_' . date('Ymd_His');
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Search temporarily unavailable. Please try again.',
            'error_code' => $errorCode,
            'timestamp' => date('c')
        ]);
    }
    
    private function getClientIP() {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
               $_SERVER['HTTP_X_REAL_IP'] ?? 
               $_SERVER['REMOTE_ADDR'] ?? 
               'unknown';
    }
}
```

### **Phase 4: User Interface Templates (Day 4)**

#### **Task 4.1: Search Form Template**

**File: `custom/modules/Contacts/tpls/conflictsearch.tpl`**
```smarty
{* Attorney Conflict Database Search Template *}
<div class="moduleTitle">
    <h2>{$MODULE_TITLE}</h2>
</div>

<div class="conflict-search-container">
    
    {* Legal Notice *}
    <div class="alert alert-info mb-3">
        <i class="fa fa-info-circle"></i>
        <strong>Legal Notice:</strong> {$help_text.legal_notice}
    </div>
    
    {* Search Form *}
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-search"></i> Conflict Database Search
            </h3>
        </div>
        <div class="panel-body">
            
            <form id="conflict-search-form" method="post">
                <input type="hidden" name="csrf_token" value="{$csrf_token}">
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="search_term">Search Term:</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search_term" 
                                   name="search_term"
                                   placeholder="Enter name, company, or email address..."
                                   minlength="2"
                                   maxlength="100"
                                   required>
                            <small class="form-text text-muted">
                                {$help_text.search_examples}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary form-control">
                                <i class="fa fa-search"></i> Search for Conflicts
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Search In:</label>
                            <div class="checkbox-inline">
                                <label>
                                    <input type="checkbox" name="modules[]" value="Contacts" checked> 
                                    Contacts
                                </label>
                            </div>
                            <div class="checkbox-inline">
                                <label>
                                    <input type="checkbox" name="modules[]" value="Accounts" checked> 
                                    Companies
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
            </form>
            
        </div>
    </div>
    
    {* Loading Indicator *}
    <div id="search-loading" class="text-center" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Searching...</span>
        </div>
        <p class="mt-2">Searching for potential conflicts...</p>
    </div>
    
    {* Search Results *}
    <div id="search-results" style="display: none;">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="fa fa-list"></i> Search Results
                    <span id="result-count" class="badge pull-right"></span>
                </h3>
            </div>
            <div class="panel-body">
                <div id="results-container"></div>
            </div>
        </div>
    </div>
    
    {* No Results Message *}
    <div id="no-results" class="alert alert-success" style="display: none;">
        <i class="fa fa-check-circle"></i>
        <strong>No Conflicts Found:</strong> No potential conflicts were detected for this search term.
    </div>
    
    {* Error Message *}
    <div id="error-message" class="alert alert-danger" style="display: none;">
        <i class="fa fa-exclamation-triangle"></i>
        <strong>Search Error:</strong> <span id="error-text"></span>
    </div>
    
</div>

{* Search Result Templates *}
<script type="text/template" id="contact-result-template">
    <div class="search-result-item border-left-primary">
        <div class="row">
            <div class="col-md-8">
                <h4 class="result-name">
                    <a href="{{detail_url}}" target="_blank">
                        <i class="fa fa-user"></i> {{full_name}}
                    </a>
                </h4>
                <p class="result-details">
                    {{#if title}}<strong>{{title}}</strong><br>{{/if}}
                    {{#if account_name}}<em>{{account_name}}</em><br>{{/if}}
                    {{#if email1}}<i class="fa fa-envelope"></i> {{email1}}<br>{{/if}}
                    {{#if phone_work}}<i class="fa fa-phone"></i> {{phone_work}}{{/if}}
                </p>
            </div>
            <div class="col-md-4 text-right">
                <span class="badge badge-primary">Contact</span>
                <br><br>
                <a href="{{detail_url}}" class="btn btn-sm btn-outline-primary" target="_blank">
                    View Details
                </a>
            </div>
        </div>
    </div>
</script>

<script type="text/template" id="account-result-template">
    <div class="search-result-item border-left-info">
        <div class="row">
            <div class="col-md-8">
                <h4 class="result-name">
                    <a href="{{detail_url}}" target="_blank">
                        <i class="fa fa-building"></i> {{name}}
                    </a>
                </h4>
                <p class="result-details">
                    {{#if industry}}<strong>{{industry}}</strong><br>{{/if}}
                    {{#if billing_address_city}}{{billing_address_city}}, {{billing_address_state}}<br>{{/if}}
                    {{#if email1}}<i class="fa fa-envelope"></i> {{email1}}<br>{{/if}}
                    {{#if phone_office}}<i class="fa fa-phone"></i> {{phone_office}}<br>{{/if}}
                    {{#if website}}<i class="fa fa-globe"></i> {{website}}{{/if}}
                </p>
            </div>
            <div class="col-md-4 text-right">
                <span class="badge badge-info">Company</span>
                <br><br>
                <a href="{{detail_url}}" class="btn btn-sm btn-outline-info" target="_blank">
                    View Details
                </a>
            </div>
        </div>
    </div>
</script>

{* JavaScript Implementation *}
<script>
$(document).ready(function() {
    
    var ConflictSearch = {
        config: {$search_config},
        templates: {},
        
        init: function() {
            this.bindEvents();
            this.compileTemplates();
            this.setupFormValidation();
        },
        
        bindEvents: function() {
            $('#conflict-search-form').on('submit', this.handleSearch.bind(this));
            $('#search_term').on('input', this.handleSearchInput.bind(this));
        },
        
        compileTemplates: function() {
            // Simple template compilation (can be enhanced with Handlebars if available)
            this.templates.contact = $('#contact-result-template').html();
            this.templates.account = $('#account-result-template').html();
        },
        
        setupFormValidation: function() {
            $('#search_term').on('blur', function() {
                var term = $(this).val().trim();
                if (term.length > 0 && term.length < 2) {
                    $(this).addClass('is-invalid');
                    $(this).next('.form-text').text('Search term must be at least 2 characters');
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.form-text').text('{$help_text.search_examples}');
                }
            });
        },
        
        handleSearch: function(e) {
            e.preventDefault();
            
            var searchTerm = $('#search_term').val().trim();
            var selectedModules = $('input[name="modules[]"]:checked').map(function() {
                return $(this).val();
            }).get();
            
            if (searchTerm.length < 2) {
                this.showError('Search term must be at least 2 characters');
                return;
            }
            
            if (selectedModules.length === 0) {
                this.showError('Please select at least one search category');
                return;
            }
            
            this.performSearch(searchTerm, selectedModules);
        },
        
        handleSearchInput: function(e) {
            // Auto-search with debouncing (optional enhancement)
            clearTimeout(this.searchTimeout);
            var self = this;
            var term = $(e.target).val().trim();
            
            if (term.length >= 3) {
                this.searchTimeout = setTimeout(function() {
                    // Auto-search can be enabled here
                }, 500);
            }
        },
        
        performSearch: function(searchTerm, modules) {
            var self = this;
            
            // Show loading
            this.showLoading();
            
            // Prepare request data
            var requestData = {
                search_term: searchTerm,
                modules: modules,
                csrf_token: this.config.csrf_token
            };
            
            // Perform AJAX request
            $.ajax({
                url: this.config.search_url,
                method: 'POST',
                data: requestData,
                dataType: 'json',
                timeout: 30000, // 30 second timeout
                success: function(response) {
                    self.handleSearchSuccess(response);
                },
                error: function(xhr, status, error) {
                    self.handleSearchError(xhr, status, error);
                }
            });
        },
        
        handleSearchSuccess: function(response) {
            this.hideLoading();
            
            if (response.status === 'success') {
                if (response.data && Object.keys(response.data).length > 0) {
                    this.displayResults(response.data, response.meta);
                } else {
                    this.showNoResults();
                }
            } else {
                this.showError(response.message || 'Search failed');
            }
        },
        
        handleSearchError: function(xhr, status, error) {
            this.hideLoading();
            
            var message = 'Search request failed. Please try again.';
            
            if (status === 'timeout') {
                message = 'Search request timed out. Please try a more specific search term.';
            } else if (xhr.status === 429) {
                message = 'Too many search requests. Please wait a moment before searching again.';
            } else if (xhr.status === 403) {
                message = 'Access denied. You may not have permission to perform conflict searches.';
            }
            
            this.showError(message);
        },
        
        displayResults: function(results, meta) {
            var resultsHtml = '';
            var totalCount = 0;
            
            // Process each module's results
            for (var module in results) {
                if (results[module] && results[module].length > 0) {
                    totalCount += results[module].length;
                    
                    // Add module header
                    resultsHtml += '<h4 class="mt-3 mb-2">' + 
                                  this.getModuleDisplayName(module) + 
                                  ' <span class="badge badge-secondary">' + results[module].length + '</span></h4>';
                    
                    // Add results for this module
                    for (var i = 0; i < results[module].length; i++) {
                        resultsHtml += this.renderResult(results[module][i], module);
                    }
                }
            }
            
            // Update UI
            $('#result-count').text(totalCount);
            $('#results-container').html(resultsHtml);
            this.showResults();
        },
        
        renderResult: function(result, module) {
            var template = this.templates[module.toLowerCase()];
            if (!template) return '';
            
            // Simple template replacement (can be enhanced with proper templating)
            var html = template;
            for (var key in result) {
                var regex = new RegExp('\\{\\{' + key + '\\}\\}', 'g');
                html = html.replace(regex, result[key] || '');
            }
            
            // Handle conditional sections (basic implementation)
            html = html.replace(/\{\{#if\s+(\w+)\}\}(.*?)\{\{\/if\}\}/g, function(match, field, content) {
                return result[field] ? content : '';
            });
            
            return html;
        },
        
        getModuleDisplayName: function(module) {
            var displayNames = {
                'Contacts': 'People',
                'Accounts': 'Companies'
            };
            return displayNames[module] || module;
        },
        
        showLoading: function() {
            $('#search-results, #no-results, #error-message').hide();
            $('#search-loading').show();
        },
        
        hideLoading: function() {
            $('#search-loading').hide();
        },
        
        showResults: function() {
            $('#no-results, #error-message').hide();
            $('#search-results').show();
        },
        
        showNoResults: function() {
            $('#search-results, #error-message').hide();
            $('#no-results').show();
        },
        
        showError: function(message) {
            $('#search-results, #no-results').hide();
            $('#error-text').text(message);
            $('#error-message').show();
        }
    };
    
    // Initialize the conflict search
    ConflictSearch.init();
    
    // Focus on search input
    $('#search_term').focus();
});
</script>

{* Custom CSS *}
<style>
.conflict-search-container {
    max-width: 1200px;
    margin: 0 auto;
}

.search-result-item {
    padding: 15px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #fafafa;
}

.search-result-item:hover {
    background: #f0f0f0;
    border-color: #ccc;
}

.border-left-primary {
    border-left: 4px solid #007bff !important;
}

.border-left-info {
    border-left: 4px solid #17a2b8 !important;
}

.result-name {
    margin-bottom: 8px;
}

.result-name a {
    text-decoration: none;
    color: #333;
}

.result-name a:hover {
    color: #007bff;
}

.result-details {
    color: #666;
    font-size: 0.9em;
    line-height: 1.4;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

#search_term.is-invalid {
    border-color: #dc3545;
}

.alert {
    border-radius: 4px;
}

.badge {
    font-size: 0.8em;
}

@media (max-width: 768px) {
    .conflict-search-container {
        padding: 0 10px;
    }
    
    .search-result-item .col-md-4 {
        margin-top: 10px;
        text-align: left !important;
    }
}
</style>
```

### **Phase 5: Testing & Integration (Day 5)**

#### **Task 5.1: Comprehensive Test Suite**

**File: `custom/modules/Contacts/ConflictSearch/ConflictSearchTestSuite.php`**
```php
<?php
require_once 'tests/SuiteCRMTestCase.php';

class ConflictSearchTestSuite extends SuiteCRMTestCase {
    
    private $conflictSearch;
    private $testUser;
    
    public function setUp() {
        parent::setUp();
        
        require_once('custom/modules/Contacts/ConflictSearch/ConflictSearchEngine.php');
        $this->conflictSearch = new ConflictSearchEngine();
        
        // Create test user with limited permissions
        $this->testUser = $this->createTestUser();
    }
    
    public function testSQLInjectionPrevention() {
        $maliciousInputs = [
            "'; DROP TABLE contacts; --",
            "' UNION SELECT password FROM users --",
            "<script>alert('xss')</script>",
            "'; UPDATE contacts SET deleted=1; --"
        ];
        
        foreach ($maliciousInputs as $maliciousInput) {
            try {
                $result = $this->conflictSearch->performConflictSearch($maliciousInput);
                
                // Should either throw exception or return safe results
                $this->assertTrue(is_array($result));
                
                // Verify tables still exist
                $this->assertTrue($this->tableExists('contacts'));
                $this->assertTrue($this->tableExists('accounts'));
                
            } catch (Exception $e) {
                // Exception is acceptable for malicious input
                $this->assertStringContains('Invalid characters', $e->getMessage());
            }
        }
    }
    
    public function testInputValidation() {
        // Test minimum length
        try {
            $this->conflictSearch->performConflictSearch('a');
            $this->fail('Should reject single character search');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContains('at least 2 characters', $e->getMessage());
        }
        
        // Test maximum length
        $longString = str_repeat('a', 101);
        try {
            $this->conflictSearch->performConflictSearch($longString);
            $this->fail('Should reject overly long search terms');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContains('too long', $e->getMessage());
        }
        
        // Test invalid characters
        try {
            $this->conflictSearch->performConflictSearch('test<script>');
            $this->fail('Should reject script tags');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContains('Invalid characters', $e->getMessage());
        }
    }
    
    public function testACLPermissions() {
        // Test with user who has no contact access
        $limitedUser = $this->createUserWithLimitedAccess();
        $this->setCurrentUser($limitedUser);
        
        try {
            $this->conflictSearch->performConflictSearch('TestContact');
            $this->fail('Should deny access for users without permissions');
        } catch (SugarApiExceptionNotAuthorized $e) {
            $this->assertStringContains('Access denied', $e->getMessage());
        }
        
        // Test with user who has contact access
        $this->setCurrentUser($this->testUser);
        $result = $this->conflictSearch->performConflictSearch('TestContact');
        $this->assertIsArray($result);
    }
    
    public function testSecurityGroupFiltering() {
        // Create test data in different security groups
        $contact1 = $this->createTestContact('Group1 Contact', 'group1');
        $contact2 = $this->createTestContact('Group2 Contact', 'group2');
        
        // User should only see contacts from their security group
        $this->setCurrentUser($this->testUser);
        $result = $this->conflictSearch->performConflictSearch('Contact');
        
        // Verify security group filtering worked
        $foundContacts = $result['Contacts'] ?? [];
        $contactIds = array_column($foundContacts, 'id');
        
        $this->assertContains($contact1->id, $contactIds);
        $this->assertNotContains($contact2->id, $contactIds);
    }
    
    public function testPerformanceWithLargeDataset() {
        // Create large dataset
        $this->createTestContacts(1000);
        $this->createTestAccounts(500);
        
        $startTime = microtime(true);
        
        $result = $this->conflictSearch->performConflictSearch('Test');
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Should complete within 2 seconds even with large dataset
        $this->assertLessThan(2.0, $executionTime, 'Search should complete within 2 seconds');
        
        // Should respect result limits
        $totalResults = 0;
        foreach ($result as $moduleResults) {
            $totalResults += count($moduleResults);
        }
        $this->assertLessThanOrEqual(200, $totalResults, 'Should respect result limits');
    }
    
    public function testRateLimiting() {
        $this->setCurrentUser($this->testUser);
        
        // Make rapid requests to trigger rate limiting
        $successCount = 0;
        $rateLimitHit = false;
        
        for ($i = 0; $i < 102; $i++) {
            try {
                $this->conflictSearch->performConflictSearch('Test' . $i);
                $successCount++;
            } catch (SugarApiExceptionRequestTooLarge $e) {
                $rateLimitHit = true;
                break;
            }
        }
        
        $this->assertTrue($rateLimitHit, 'Rate limiting should trigger');
        $this->assertGreaterThanOrEqual(99, $successCount, 'Should allow at least 99 requests');
        $this->assertLessThanOrEqual(101, $successCount, 'Should block after 100 requests');
    }
    
    public function testXSSPrevention() {
        // Create contact with potentially malicious data
        $contact = $this->createTestContact('<script>alert("xss")</script>', '');
        $contact->email1 = 'test@<script>alert("xss")</script>.com';
        $contact->save();
        
        $result = $this->conflictSearch->performConflictSearch('script');
        
        // Verify XSS prevention in results
        if (!empty($result['Contacts'])) {
            foreach ($result['Contacts'] as $contactResult) {
                $this->assertStringNotContains('<script>', $contactResult['first_name']);
                $this->assertStringNotContains('<script>', $contactResult['email1']);
                $this->assertStringContains('&lt;script&gt;', $contactResult['first_name']);
            }
        }
    }
    
    public function testCachingFunctionality() {
        $searchTerm = 'CacheTest' . uniqid();
        
        // First search (should hit database)
        $startTime1 = microtime(true);
        $result1 = $this->conflictSearch->performConflictSearch($searchTerm);
        $time1 = microtime(true) - $startTime1;
        
        // Second search (should hit cache)
        $startTime2 = microtime(true);
        $result2 = $this->conflictSearch->performConflictSearch($searchTerm);
        $time2 = microtime(true) - $startTime2;
        
        // Cached search should be faster
        $this->assertLessThan($time1, $time2);
        
        // Results should be identical
        $this->assertEquals($result1, $result2);
    }
    
    public function testAuditLogging() {
        // Clear existing logs
        $this->clearTestLogs();
        
        // Perform search
        $this->conflictSearch->performConflictSearch('AuditTest');
        
        // Verify audit log entry was created
        $logEntries = $this->getTestLogEntries('CONFLICT_SEARCH');
        $this->assertGreaterThan(0, count($logEntries));
        
        $logEntry = $logEntries[0];
        $this->assertStringContains('CONFLICT_SEARCH', $logEntry);
        $this->assertStringContains('AuditTest', $logEntry);
        $this->assertStringContains($this->testUser->user_name, $logEntry);
    }
    
    // Helper methods
    private function createTestUser() {
        $user = new User();
        $user->user_name = 'testuser_' . uniqid();
        $user->first_name = 'Test';
        $user->last_name = 'User';
        $user->save();
        
        // Grant necessary permissions
        $this->grantModuleAccess($user, 'Contacts', 'list');
        $this->grantModuleAccess($user, 'Accounts', 'list');
        
        return $user;
    }
    
    private function createUserWithLimitedAccess() {
        $user = new User();
        $user->user_name = 'limiteduser_' . uniqid();
        $user->first_name = 'Limited';
        $user->last_name = 'User';
        $user->save();
        
        // No module access permissions granted
        
        return $user;
    }
    
    private function createTestContact($name, $securityGroup = '') {
        $contact = new Contact();
        $contact->first_name = $name;
        $contact->last_name = 'TestContact';
        $contact->email1 = strtolower($name) . '@test.com';
        $contact->save();
        
        if (!empty($securityGroup)) {
            $this->assignToSecurityGroup($contact, $securityGroup);
        }
        
        return $contact;
    }
    
    private function createTestContacts($count) {
        for ($i = 0; $i < $count; $i++) {
            $this->createTestContact("TestContact$i");
        }
    }
    
    private function createTestAccounts($count) {
        for ($i = 0; $i < $count; $i++) {
            $account = new Account();
            $account->name = "TestAccount$i";
            $account->email1 = "testaccount$i@test.com";
            $account->save();
        }
    }
}
```

#### **Task 5.2: Menu Integration**

**File: `custom/Extension/modules/Contacts/Ext/Menus/conflict_search.php`**
```php
<?php
// Add Conflict Search to Contacts module menu
if (ACLController::checkAccess('Contacts', 'list', true)) {
    $module_menu[] = array(
        'index.php?module=Contacts&action=ConflictSearch',
        'Attorney Conflict Search',
        'ConflictSearch',
        'Contacts'
    );
}
```

### **Phase 6: Documentation & Deployment (Day 6)**

#### **Task 6.1: Installation Instructions**

**File: `custom/modules/Contacts/ConflictSearch/INSTALLATION.md`**
```markdown
# Attorney Conflict Database Search - Installation Guide

## Prerequisites
- SuiteCRM 7.14+ or compatible version
- PHP 7.4+ with PDO extension
- MySQL 5.7+ or MariaDB 10.2+
- User must have admin permissions to install

## Installation Steps

### 1. Deploy Files
Copy all files from the package to their respective locations:
```bash
cp -r custom/ /path/to/suitecrm/
```

### 2. Create Database Indexes
Execute the following SQL commands to optimize search performance:
```sql
CREATE INDEX idx_contacts_conflict_search 
ON contacts (first_name, last_name, email1, deleted);

CREATE INDEX idx_accounts_conflict_search  
ON accounts (name, deleted);

CREATE INDEX idx_contacts_fullname_search
ON contacts (CONCAT(first_name, ' ', last_name), deleted);
```

### 3. Clear SuiteCRM Cache
- Go to Admin -> Repair -> Quick Repair and Rebuild
- Click "Execute" to rebuild the system

### 4. Set Permissions
Ensure the following permissions are granted to attorneys:
- Contacts: List access
- Accounts: List access

### 5. Verify Installation
- Navigate to Contacts module
- Look for "Attorney Conflict Search" in the module menu
- Test with a sample search

## Security Configuration

### Rate Limiting
Default: 100 searches per hour per user
To modify: Edit `ConflictSearchEngine.php` line 150

### Audit Logging
Searches are logged in the SuiteCRM log file with prefix "CONFLICT_SEARCH"
Location: logs/suitecrm.log

### Access Control
Uses standard SuiteCRM ACL and SecurityGroup permissions
No additional configuration required

## Troubleshooting

### Common Issues

1. **"Access Denied" Error**
   - Verify user has Contacts and Accounts list permissions
   - Check SecurityGroup memberships

2. **Slow Search Performance**
   - Verify database indexes were created
   - Check database query performance

3. **No Results Found**
   - Verify data exists in Contacts/Accounts
   - Check SecurityGroup filtering

### Performance Tuning

For large datasets (>10,000 records):
1. Monitor search performance
2. Consider additional database indexes
3. Adjust cache timeout in ConflictSearchEngine.php

## Uninstallation

To remove the feature:
1. Delete custom/modules/Contacts/ConflictSearch/ directory
2. Delete custom/modules/Contacts/views/view.conflictsearch.php
3. Delete custom/modules/Contacts/tpls/conflictsearch.tpl
4. Drop database indexes:
   ```sql
   DROP INDEX idx_contacts_conflict_search ON contacts;
   DROP INDEX idx_accounts_conflict_search ON accounts;
   DROP INDEX idx_contacts_fullname_search ON contacts;
   ```
5. Clear cache via Admin -> Repair -> Quick Repair and Rebuild
```

#### **Task 6.2: User Guide**

**File: `custom/modules/Contacts/ConflictSearch/USER_GUIDE.md`**
```markdown
# Attorney Conflict Database Search - User Guide

## Overview
The Attorney Conflict Database Search helps legal professionals identify potential conflicts of interest by searching across contacts and companies in the CRM system.

## Accessing the Feature
1. Navigate to the **Contacts** module
2. Click on **Attorney Conflict Search** in the module menu
3. The conflict search interface will open

## Using the Search

### Basic Search
1. Enter a search term in the **Search Term** field
   - Examples: "John Smith", "Acme Corporation", "john@example.com"
2. Select search categories:
   - **Contacts**: Search through people/individuals
   - **Companies**: Search through organizations/businesses
3. Click **Search for Conflicts**

### Search Tips
- **Minimum Length**: Search terms must be at least 2 characters
- **Wildcards**: The system automatically searches for partial matches
- **Multiple Terms**: Use full names or company names for best results
- **Email Addresses**: Can search by email address

### Understanding Results

#### Contact Results
Display information including:
- Full name and title
- Company affiliation
- Email address and phone number
- Link to full contact details

#### Company Results
Display information including:
- Company name and industry
- Location (city, state)
- Contact information
- Website

### Search Categories
- **People**: Individual contacts, potential clients, opposing parties
- **Companies**: Organizations, law firms, corporations

## Best Practices

### Conflict Checking Process
1. **Search Multiple Variations**
   - Try different spellings of names
   - Search both individual names and company names
   - Include common abbreviations or nicknames

2. **Document Your Search**
   - All searches are automatically logged for compliance
   - Consider printing or saving important results
   - Document your conflict checking process

3. **Review Related Records**
   - Click through to full contact/company details
   - Review relationships and associated records
   - Check historical information

### Legal Compliance
- This tool assists with conflict checking but doesn't replace professional judgment
- Always follow your firm's specific conflict checking procedures
- Consult with ethics counsel when in doubt
- Maintain proper documentation of conflict searches

## Security Features

### Access Control
- Only users with appropriate permissions can access the search
- Results are filtered based on your security group memberships
- Searches are limited to records you have permission to view

### Privacy Protection
- All searches are logged for audit purposes
- Search data is protected by SuiteCRM's security framework
- Results are never cached permanently

### Rate Limiting
- Limited to 100 searches per hour per user
- Prevents system abuse and ensures performance

## Troubleshooting

### No Results Found
- **Good News**: No conflicts detected for the search term
- **Double-check**: Try alternative spellings or search terms
- **Verify Access**: Ensure you have permission to view contacts/companies

### Search Too Slow
- Use more specific search terms
- Search smaller date ranges when possible
- Contact your system administrator if performance issues persist

### Access Denied
- Contact your system administrator to verify permissions
- Ensure you have Contacts and Accounts list access
- Check your security group memberships

## Getting Help
For technical issues or questions about the conflict search feature:
1. Contact your system administrator
2. Consult your firm's IT support
3. Review the installation documentation for technical details

## Compliance Notes
- All conflict searches are logged with timestamp and user information
- Search logs are available to administrators for compliance reporting
- This tool is designed to assist with ABA Model Rule 1.7 compliance
- Always consult with ethics counsel for complex conflict situations
```

---

## 🛡️ **Security Checklist**

### ✅ **Critical Security Implementations**
- [ ] SQL injection prevention via prepared statements
- [ ] XSS prevention with output sanitization  
- [ ] CSRF token validation for all requests
- [ ] ACL permission checking before search
- [ ] SecurityGroup record filtering
- [ ] Input validation and sanitization
- [ ] Rate limiting (100 searches/hour per user)
- [ ] Comprehensive audit logging
- [ ] Security headers (CSP, XSS Protection, etc.)
- [ ] Safe error handling without information disclosure

### ✅ **Performance Optimizations**
- [ ] Database indexes for search performance
- [ ] Query result limiting (max 100 per module)
- [ ] Search result caching (5-minute TTL)
- [ ] Efficient SQL queries with proper JOINs
- [ ] Client-side search debouncing
- [ ] Response compression

### ✅ **Integration Safety**
- [ ] All files in custom/ directories
- [ ] No core file modifications
- [ ] Uses existing SuiteCRM frameworks
- [ ] Leverages built-in security systems
- [ ] Compatible with existing themes
- [ ] Follows SuiteCRM coding standards

---

## 📊 **Risk Assessment Summary**

| Risk Category | Risk Level | Mitigation Strategy | Success Criteria |
|---------------|------------|-------------------|------------------|
| **Security Vulnerabilities** | **VERY LOW** ✅ | Comprehensive security implementation | Zero security issues in testing |
| **Performance Impact** | **VERY LOW** ✅ | Database optimization and caching | <2 second search response time |
| **Framework Integration** | **VERY LOW** ✅ | Uses existing SuiteCRM patterns | No conflicts with existing functionality |
| **Maintenance Complexity** | **LOW** ✅ | Well-documented, standard architecture | Easy troubleshooting and updates |
| **User Adoption** | **VERY LOW** ✅ | Intuitive interface, familiar patterns | Immediate usability for attorneys |

---

## 🎯 **Success Metrics**

### **Functional Requirements**
- [ ] Search across Contacts and Accounts successfully
- [ ] Results display within 2 seconds for typical queries
- [ ] Proper permission filtering applied to all results
- [ ] Mobile-responsive interface works on tablets/phones
- [ ] Comprehensive audit logging for compliance

### **Security Requirements**  
- [ ] All security tests pass (SQL injection, XSS, CSRF, etc.)
- [ ] Rate limiting prevents abuse (max 100/hour per user)
- [ ] Access control properly integrated with existing ACL
- [ ] Input validation prevents malicious input
- [ ] Output sanitization prevents XSS attacks

### **Business Value Requirements**
- [ ] Attorneys can identify potential conflicts quickly
- [ ] Search covers essential conflict checking scenarios
- [ ] Interface is intuitive for legal professionals
- [ ] Compliance logging meets legal industry standards
- [ ] Feature integrates seamlessly with existing workflow

---

## 🚀 **Implementation Timeline**

| Phase | Duration | Key Deliverables | Risk Level |
|-------|----------|------------------|------------|
| **Phase 1**: Research & Architecture | 2 days | Architecture analysis, security design | ⭐ Very Low |
| **Phase 2**: Core Implementation | 2 days | Search engine, security, database optimization | ⭐ Very Low |
| **Phase 3**: UI & Templates | 1 day | Search form, results display, responsive design | ⭐ Very Low | 
| **Phase 4**: Testing & Integration | 1 day | Security testing, performance testing, menu integration | ⭐ Very Low |
| **Phase 5**: Documentation & Deployment | 1 day | User guide, installation guide, final testing | ⭐ Very Low |

**Total Timeline**: 7 days  
**Overall Risk Level**: ⭐ (Very Low)  
**Business Value**: 🏆 (Critical for legal compliance)

---

This comprehensive implementation plan provides a complete roadmap for creating a secure, performant, and compliant Attorney Conflict Database Search feature that leverages SuiteCRM's existing infrastructure while minimizing implementation risk and maximizing business value for legal professionals.