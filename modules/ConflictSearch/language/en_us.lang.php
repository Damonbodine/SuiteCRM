<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch Language File - English (US)
 * 
 * Language definitions for the Attorney Conflict of Interest Search module
 */

$mod_strings = array(
    // Module Labels
    'LBL_MODULE_NAME' => 'Conflict Search',
    'LBL_MODULE_TITLE' => 'Attorney Conflict of Interest Search',
    'LBL_SEARCH_FORM_TITLE' => 'Conflict Search',
    'LBL_LIST_FORM_TITLE' => 'Conflict Search List',
    'LBL_NEW_FORM_TITLE' => 'New Conflict Search',
    'LBL_SEARCH_RESULTS_TITLE' => 'Conflict Search Results',
    
    // Standard Fields
    'LBL_ID' => 'ID',
    'LBL_NAME' => 'Search Name',
    'LBL_DATE_ENTERED' => 'Date Created',
    'LBL_DATE_MODIFIED' => 'Date Modified',
    'LBL_MODIFIED' => 'Modified By',
    'LBL_MODIFIED_NAME' => 'Modified By Name',
    'LBL_CREATED' => 'Created By',
    'LBL_CREATED_BY' => 'Created By',
    
    // Subpanel Fields
    'LBL_CONFLICT_SEARCH_SUBPANEL_TITLE' => 'Conflict Searches',
    'LBL_NEW_CONFLICT_SEARCH' => 'New Conflict Search',
    'LBL_QUICK_CONFLICT_CHECK' => 'Quick Check',
    'LBL_SEARCH_TERM' => 'Search Term',
    'LBL_SEARCH_TYPE' => 'Type',
    'LBL_TOTAL_MATCHES' => 'Matches',
    'LBL_CONFIDENCE_SUMMARY' => 'Risk Level',
    'LBL_STATUS' => 'Status',
    'LBL_DESCRIPTION' => 'Description',
    'LBL_DELETED' => 'Deleted',
    'LBL_ASSIGNED_TO' => 'Assigned To',
    'LBL_ASSIGNED_TO_ID' => 'Assigned User ID',
    'LBL_ASSIGNED_TO_NAME' => 'Assigned To',
    'LBL_ASSIGNED_USER' => 'Assigned User',
    'LBL_ASSIGNED_USER_ID' => 'Assigned User ID',
    'LBL_ASSIGNED_USER_NAME' => 'Assigned User Name',
    
    // ConflictSearch Specific Fields
    'LBL_SEARCH_TERM' => 'Search Term',
    'LBL_SEARCH_TYPE' => 'Search Type',
    'LBL_MODULES_SEARCHED' => 'Modules Searched',
    'LBL_CONFIDENCE_THRESHOLD' => 'Confidence Threshold (%)',
    'LBL_TOTAL_MATCHES_FOUND' => 'Total Matches',
    'LBL_HIGH_CONFIDENCE_MATCHES' => 'High Confidence',
    'LBL_MEDIUM_CONFIDENCE_MATCHES' => 'Medium Confidence',
    'LBL_LOW_CONFIDENCE_MATCHES' => 'Low Confidence',
    'LBL_SEARCH_STATUS' => 'Status',
    'LBL_SEARCH_RESULTS' => 'Search Results',
    'LBL_EXECUTION_TIME' => 'Execution Time (seconds)',
    'LBL_PERFORMED_BY' => 'Performed By',
    'LBL_PERFORMED_BY_NAME' => 'Performed By',
    'LBL_PERFORMED_BY_USER' => 'Performed By User',
    'LBL_SEARCH_CRITERIA' => 'Additional Criteria',
    
    // Field Help Text
    'LBL_SEARCH_TERM_HELP' => 'Enter the name, company, or entity to search for potential conflicts',
    'LBL_SEARCH_TYPE_HELP' => 'Select the type of conflict search to perform',
    'LBL_MODULES_SEARCHED_HELP' => 'Modules that will be searched for conflicts',
    'LBL_CONFIDENCE_THRESHOLD_HELP' => 'Minimum confidence percentage for matches to be included in results',
    
    // List View Labels
    'LBL_LIST_NAME' => 'Search Name',
    'LBL_LIST_SEARCH_TERM' => 'Search Term',
    'LBL_LIST_SEARCH_TYPE' => 'Type',
    'LBL_LIST_TOTAL_MATCHES' => 'Total Matches',
    'LBL_LIST_HIGH_CONFIDENCE' => 'High Confidence',
    'LBL_LIST_STATUS' => 'Status',
    'LBL_LIST_DATE_ENTERED' => 'Date Created',
    'LBL_LIST_PERFORMED_BY' => 'Performed By',
    'LBL_LIST_ASSIGNED_USER' => 'Assigned To',
    
    // Search Form Labels
    'LBL_SEARCH_BUTTON' => 'Search for Conflicts',
    'LBL_CLEAR_BUTTON' => 'Clear',
    'LBL_ADVANCED_SEARCH' => 'Advanced Search Options',
    'LBL_BASIC_SEARCH' => 'Basic Search',
    'LBL_CURRENT_USER_FILTER' => 'My Searches',
    'LBL_SEARCH_HELP' => 'Enter a name, company, or entity to search for potential conflicts of interest',
    
    // Results Labels
    'LBL_RESULTS_SUMMARY' => 'Search Results Summary',
    'LBL_NO_CONFLICTS_FOUND' => 'No potential conflicts found',
    'LBL_CONFLICTS_FOUND' => 'Potential conflicts found',
    'LBL_CONFLICT_DETAILS' => 'Conflict Details',
    'LBL_MATCH_TYPE' => 'Match Type',
    'LBL_CONFIDENCE_SCORE' => 'Confidence Score',
    'LBL_ENTITY_TYPE' => 'Entity Type',
    'LBL_ENTITY_NAME' => 'Entity Name',
    'LBL_MATCH_REASON' => 'Match Reason',
    'LBL_VIEW_ENTITY' => 'View Entity',
    'LBL_RELATED_ENTITIES' => 'Related Entities',
    
    // Export Labels
    'LBL_EXPORT_RESULTS' => 'Export Results',
    'LBL_EXPORT_PDF' => 'Export to PDF',
    'LBL_EXPORT_CSV' => 'Export to CSV',
    'LBL_EXPORT_JSON' => 'Export to JSON',
    'LBL_PRINT_REPORT' => 'Print Report',
    
    // Action Labels
    'LBL_NEW_SEARCH' => 'New Conflict Search',
    'LBL_EDIT_SEARCH' => 'Edit Search',
    'LBL_VIEW_SEARCH' => 'View Search',
    'LBL_DELETE_SEARCH' => 'Delete Search',
    'LBL_DUPLICATE_SEARCH' => 'Duplicate Search',
    'LBL_RERUN_SEARCH' => 'Re-run Search',
    'LBL_SAVE_SEARCH' => 'Save Search',
    'LBL_CANCEL' => 'Cancel',
    
    // Status Messages
    'LBL_SEARCH_PENDING' => 'Search Pending',
    'LBL_SEARCH_RUNNING' => 'Search Running...',
    'LBL_SEARCH_COMPLETED' => 'Search Completed',
    'LBL_SEARCH_FAILED' => 'Search Failed',
    'LBL_SEARCH_CANCELLED' => 'Search Cancelled',
    
    // Error Messages
    'ERR_SEARCH_TERM_REQUIRED' => 'Search term is required',
    'ERR_SEARCH_TERM_TOO_SHORT' => 'Search term must be at least 3 characters',
    'ERR_INVALID_CONFIDENCE_THRESHOLD' => 'Confidence threshold must be between 1 and 100',
    'ERR_NO_MODULES_SELECTED' => 'At least one module must be selected for searching',
    'ERR_SEARCH_FAILED' => 'Search failed: {error_message}',
    'ERR_PERMISSION_DENIED' => 'You do not have permission to perform conflict searches',
    'ERR_INVALID_SEARCH_TYPE' => 'Invalid search type selected',
    'ERR_EXPORT_FAILED' => 'Export failed: {error_message}',
    
    // Informational Messages
    'MSG_SEARCH_HELP' => 'Enter a name, company, phone number, email, or address to search for potential conflicts across Contacts, Accounts, and Cases.',
    'MSG_CONFIDENCE_HELP' => 'Higher confidence scores indicate stronger potential conflicts. Set a lower threshold to find more potential matches.',
    'MSG_SEARCH_COMPLETE' => 'Search completed successfully. Found {total_matches} potential conflicts.',
    'MSG_NO_RESULTS' => 'No potential conflicts found for the specified search criteria.',
    'MSG_EXPORT_SUCCESS' => 'Results exported successfully.',
    
    // Dropdown Options - Search Types
    'conflict_search_type_dom' => array(
        'comprehensive' => 'Comprehensive Search',
        'exact' => 'Exact Match Only',
        'fuzzy' => 'Fuzzy Match',
        'phonetic' => 'Phonetic Match',
        'address' => 'Address Proximity',
        'email_domain' => 'Email Domain Match',
        'phone' => 'Phone Number Match'
    ),
    
    // Dropdown Options - Search Status
    'conflict_search_status_dom' => array(
        'pending' => 'Pending',
        'running' => 'Running',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled'
    ),
    
    // Dropdown Options - Match Types
    'conflict_match_type_dom' => array(
        'exact_name' => 'Exact Name Match',
        'fuzzy_name' => 'Similar Name',
        'phonetic_name' => 'Sounds Like',
        'email_exact' => 'Exact Email Match',
        'email_domain' => 'Same Email Domain',
        'phone_exact' => 'Exact Phone Match',
        'phone_normalized' => 'Same Phone Number',
        'address_exact' => 'Exact Address Match',
        'address_proximity' => 'Address Proximity',
        'relationship' => 'Related Entity'
    ),
    
    // Help Text
    'LBL_HELP_SEARCH_TYPES' => array(
        'comprehensive' => 'Searches using all available matching algorithms for the most thorough results',
        'exact' => 'Only finds exact matches, fastest but may miss related conflicts',
        'fuzzy' => 'Finds similar names and entities that may be spelled differently',
        'phonetic' => 'Finds names that sound similar using phonetic matching',
        'address' => 'Searches for entities at the same or nearby addresses',
        'email_domain' => 'Finds entities with the same email domain (company relationships)',
        'phone' => 'Searches for the same phone numbers across different formats'
    ),
    
    // Recent Searches
    'LBL_RECENT_SEARCHES' => 'Recent Searches',
    'LBL_NO_RECENT_SEARCHES' => 'No recent searches found',
    'LBL_VIEW_ALL_SEARCHES' => 'View All Searches',
    
    // Dashboard/Dashlet Labels
    'LBL_CONFLICT_SEARCH_DASHLET' => 'Conflict Search',
    'LBL_RECENT_CONFLICTS' => 'Recent Conflict Searches',
    'LBL_HIGH_RISK_CONFLICTS' => 'High Risk Conflicts',
    'LBL_SEARCH_STATISTICS' => 'Search Statistics',
    
    // Relationship Labels
    'LBL_CREATED_BY_USER' => 'Created By User',
    'LBL_MODIFIED_BY_USER' => 'Modified By User',
    
    // Subpanel Labels
    'LBL_CONFLICT_SEARCHES_SUBPANEL_TITLE' => 'Conflict Searches',
    'LBL_NEW_CONFLICT_SEARCH' => 'New Conflict Search',
    
    // Quick Create Labels
    'LBL_QUICK_SEARCH' => 'Quick Conflict Search',
    'LBL_QUICK_SEARCH_HELP' => 'Enter a search term to quickly check for conflicts',
    
    // Security Labels
    'LBL_ACCESS_DENIED_TITLE' => 'Access Denied',
    'LBL_ACCESS_DENIED_MESSAGE' => 'You do not have sufficient privileges to access conflict search functionality. Please contact your administrator.',
    
    // Module Tab
    'LNK_NEW_CONFLICT_SEARCH' => 'Create Conflict Search',
    'LNK_CONFLICT_SEARCH_LIST' => 'View Conflict Searches',
    'LNK_IMPORT_CONFLICT_SEARCHES' => 'Import Conflict Searches',
    
    // Additional Labels for Complex UI Elements
    'LBL_SEARCH_PROGRESS' => 'Search Progress',
    'LBL_SEARCHING_MODULE' => 'Searching {module_name}...',
    'LBL_MATCHES_FOUND_IN_MODULE' => 'Found {count} matches in {module_name}',
    'LBL_SEARCH_SUMMARY_STATS' => '{total} total matches: {high} high confidence, {medium} medium confidence, {low} low confidence',
    
    // Advanced Search Labels
    'LBL_INCLUDE_CONTACTS' => 'Include Contacts',
    'LBL_INCLUDE_ACCOUNTS' => 'Include Accounts', 
    'LBL_INCLUDE_CASES' => 'Include Cases',
    'LBL_SEARCH_OPTIONS' => 'Search Options',
    'LBL_MATCH_SETTINGS' => 'Match Settings',
    'LBL_RESULT_SETTINGS' => 'Result Settings',
    
    // Accessibility Labels
    'LBL_ARIA_SEARCH_FORM' => 'Conflict search form',
    'LBL_ARIA_RESULTS_TABLE' => 'Conflict search results table',
    'LBL_ARIA_EXPORT_MENU' => 'Export options menu',
    'LBL_ARIA_SEARCH_PROGRESS' => 'Search progress indicator'
);