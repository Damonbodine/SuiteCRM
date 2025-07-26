<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once 'include/Dashlets/DashletGeneric.php';

/**
 * ConflictSearch Dashlet
 * 
 * Provides quick access to ConflictSearch functionality from the dashboard
 */
class ConflictSearchDashlet extends DashletGeneric
{
    public function __construct($id, $def = null)
    {
        global $current_user, $app_strings;
        require_once 'modules/ConflictSearch/ConflictSearch.php';

        parent::__construct($id, $def);

        if (empty($def['title'])) {
            $this->title = 'Attorney Conflict Search';
        }

        $this->searchFields = array();
        $this->isConfigurable = true;
        $this->hasScript = true;
    }

    public function displayOptions()
    {
        return '<input type="hidden" name="searchFormTab" value="basic_search">';
    }

    public function process($lvsParams = array())
    {
        // Override process to provide custom content
        return;
    }

    public function display($text = '')
    {
        global $mod_strings, $current_user;
        
        // Enhanced dashboard widget with inline search form
        $html = '<div class="conflict-search-dashlet" style="padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">';
        
        // Header with icon
        $html .= '<div style="display: flex; align-items: center; margin-bottom: 15px;">';
        $html .= '<span class="suitepicon suitepicon-action-search" style="font-size: 24px; margin-right: 10px; color: #ffd700;"></span>';
        $html .= '<h3 style="margin: 0; color: white; font-size: 18px;">⚖️ Attorney Conflict Search</h3>';
        $html .= '</div>';
        
        // Quick search form
        $html .= '<form id="dashlet-conflict-search" action="index.php" method="post" style="margin-bottom: 15px;">';
        $html .= '<input type="hidden" name="module" value="ConflictSearch">';
        $html .= '<input type="hidden" name="action" value="search_results">';
        $html .= '<div style="margin-bottom: 10px;">';
        $html .= '<input type="text" name="search_term" placeholder="Enter name, company, or case reference..." ';
        $html .= 'style="width: 100%; padding: 8px 12px; border: none; border-radius: 4px; font-size: 14px; box-sizing: border-box;" required>';
        $html .= '</div>';
        
        // Search options
        $html .= '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">';
        $html .= '<select name="search_type" style="padding: 6px 10px; border: none; border-radius: 4px; background: white; margin-right: 10px;">';
        $html .= '<option value="comprehensive">Comprehensive Search</option>';
        $html .= '<option value="exact">Exact Match</option>';
        $html .= '<option value="fuzzy">Fuzzy Match</option>';
        $html .= '<option value="phonetic">Phonetic Match</option>';
        $html .= '</select>';
        $html .= '<select name="confidence_threshold" style="padding: 6px 10px; border: none; border-radius: 4px; background: white;">';
        $html .= '<option value="high">High Risk Only</option>';
        $html .= '<option value="medium" selected>Medium+ Risk</option>';
        $html .= '<option value="low">All Matches</option>';
        $html .= '</select>';
        $html .= '</div>';
        
        // Submit button
        $html .= '<div style="text-align: center; margin-bottom: 15px;">';
        $html .= '<button type="submit" style="background: #28a745; color: white; border: none; padding: 10px 20px; ';
        $html .= 'border-radius: 5px; font-size: 14px; font-weight: bold; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">';
        $html .= '🔍 Search for Conflicts</button>';
        $html .= '</div>';
        $html .= '</form>';
        
        // Action buttons
        $html .= '<div style="display: flex; justify-content: space-between; gap: 10px;">';
        $html .= '<a href="index.php?module=ConflictSearch&action=EditView" ';
        $html .= 'style="flex: 1; background: rgba(255,255,255,0.2); color: white; padding: 8px 12px; text-decoration: none; ';
        $html .= 'border-radius: 4px; text-align: center; transition: background 0.3s; border: 1px solid rgba(255,255,255,0.3);">';
        $html .= '📝 Advanced Search</a>';
        
        $html .= '<a href="index.php?module=ConflictSearch&action=index" ';
        $html .= 'style="flex: 1; background: rgba(255,255,255,0.2); color: white; padding: 8px 12px; text-decoration: none; ';
        $html .= 'border-radius: 4px; text-align: center; transition: background 0.3s; border: 1px solid rgba(255,255,255,0.3);">';
        $html .= '📊 View History</a>';
        $html .= '</div>';
        
        // Recent searches (if any)
        $html .= $this->getRecentSearches();
        
        // Usage stats
        $html .= '<div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.3); font-size: 12px; text-align: center; color: rgba(255,255,255,0.8);">';
        $html .= 'Protect your practice with comprehensive conflict checking';
        $html .= '</div>';
        
        $html .= '</div>';
        
        // Add custom CSS and JavaScript
        $html .= $this->getCustomStyles();
        $html .= $this->getCustomScript();
        
        return $html;
    }
    
    /**
     * Get recent searches for the current user
     */
    private function getRecentSearches()
    {
        global $current_user;
        
        try {
            $db = DBManagerFactory::getInstance();
            $query = "SELECT search_term, search_type, total_matches_found, date_entered 
                     FROM conflict_search 
                     WHERE created_by = '{$current_user->id}' 
                     AND deleted = 0 
                     ORDER BY date_entered DESC 
                     LIMIT 3";
            
            $result = $db->query($query);
            $searches = array();
            
            while ($row = $db->fetchByAssoc($result)) {
                $searches[] = $row;
            }
            
            if (empty($searches)) {
                return '';
            }
            
            $html = '<div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.3);">';
            $html .= '<h4 style="margin: 0 0 10px 0; color: #ffd700; font-size: 14px;">📋 Recent Searches</h4>';
            
            foreach ($searches as $search) {
                $date = date('M j', strtotime($search['date_entered']));
                $matches = $search['total_matches_found'] ?: 0;
                $type = ucfirst($search['search_type']);
                
                $html .= '<div style="background: rgba(255,255,255,0.1); padding: 6px 10px; border-radius: 3px; margin-bottom: 5px; font-size: 12px;">';
                $html .= '<div style="display: flex; justify-content: space-between;">';
                $html .= '<span style="font-weight: bold;">' . htmlspecialchars($search['search_term']) . '</span>';
                $html .= '<span style="color: #ffd700;">' . $matches . ' matches</span>';
                $html .= '</div>';
                $html .= '<div style="color: rgba(255,255,255,0.7); font-size: 11px;">';
                $html .= $type . ' • ' . $date;
                $html .= '</div>';
                $html .= '</div>';
            }
            
            $html .= '</div>';
            return $html;
            
        } catch (Exception $e) {
            return ''; // Silently fail for recent searches
        }
    }
    
    /**
     * Get custom CSS styles
     */
    private function getCustomStyles()
    {
        return '<style>
            .conflict-search-dashlet button:hover {
                background: #218838 !important;
                transform: translateY(-1px);
            }
            .conflict-search-dashlet a:hover {
                background: rgba(255,255,255,0.3) !important;
            }
            .conflict-search-dashlet input:focus,
            .conflict-search-dashlet select:focus {
                outline: none;
                box-shadow: 0 0 0 2px #ffd700;
            }
            @media (max-width: 768px) {
                .conflict-search-dashlet > div[style*="display: flex"] {
                    flex-direction: column !important;
                    gap: 5px !important;
                }
                .conflict-search-dashlet select {
                    margin-right: 0 !important;
                    margin-bottom: 5px;
                }
            }
        </style>';
    }
    
    /**
     * Get custom JavaScript
     */
    private function getCustomScript()
    {
        return '<script>
            document.addEventListener("DOMContentLoaded", function() {
                var form = document.getElementById("dashlet-conflict-search");
                if (form) {
                    form.addEventListener("submit", function(e) {
                        var searchTerm = form.querySelector("input[name=\'search_term\']").value.trim();
                        if (searchTerm.length < 2) {
                            e.preventDefault();
                            alert("Please enter at least 2 characters for the search.");
                            return false;
                        }
                        
                        // Show loading state
                        var button = form.querySelector("button[type=\'submit\']");
                        var originalText = button.innerHTML;
                        button.innerHTML = "🔄 Searching...";
                        button.disabled = true;
                        
                        // Re-enable after a short delay (in case of quick back navigation)
                        setTimeout(function() {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }, 5000);
                    });
                    
                    // Add keyboard shortcut (Ctrl+/ or Cmd+/ to focus search)
                    document.addEventListener("keydown", function(e) {
                        if ((e.ctrlKey || e.metaKey) && e.key === "/") {
                            e.preventDefault();
                            var searchInput = form.querySelector("input[name=\'search_term\']");
                            if (searchInput) {
                                searchInput.focus();
                                searchInput.select();
                            }
                        }
                    });
                }
            });
        </script>';
    }
}