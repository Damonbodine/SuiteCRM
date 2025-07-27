<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * Language strings for Billable Hours Quick Entry Dashlet
 * Designed for Criminal Defense Attorneys
 */

$dashletStrings['BillableHoursQuickEntryDashlet'] = array(
    // Dashlet Title and Description
    'LBL_TITLE'                     => 'Billable Hours Quick Entry',
    'LBL_DESCRIPTION'               => 'Quick time logging for criminal defense attorneys',
    
    // Form Labels
    'LBL_CASE'                      => 'Case',
    'LBL_SELECT_CASE'               => '-- Select Case --',
    'LBL_ACTIVITY_TYPE'             => 'Activity Type',
    'LBL_DURATION'                  => 'Duration',
    'LBL_HOURS'                     => 'hours',
    'LBL_RATE'                      => 'Hourly Rate',
    'LBL_DATE'                      => 'Date',
    'LBL_TIME'                      => 'Time',
    'LBL_DESCRIPTION'               => 'Description',
    'LBL_DESCRIPTION_PLACEHOLDER'   => 'Describe the work performed (e.g., "Reviewed police reports and witness statements")',
    
    // Activity Types (Criminal Defense Specific)
    'LBL_COURT_APPEARANCE'          => 'Court Appearance',
    'LBL_CLIENT_MEETING'            => 'Client Meeting/Conference',
    'LBL_CASE_RESEARCH'             => 'Legal Research',
    'LBL_DOCUMENT_REVIEW'           => 'Document Review/Analysis',
    'LBL_LEGAL_WRITING'             => 'Legal Writing/Brief Preparation',
    'LBL_PHONE_CALL'                => 'Phone Call (Client/Court/Counsel)',
    'LBL_INVESTIGATION'             => 'Investigation/Fact Gathering',
    'LBL_TRIAL_PREP'                => 'Trial Preparation',
    'LBL_OTHER'                     => 'Other Legal Work',
    
    // Button Labels
    'LBL_START_TIMER'               => 'Start Timer',
    'LBL_STOP_TIMER'                => 'Stop Timer',
    'LBL_LOG_TIME'                  => 'Log Time',
    'LBL_CLEAR'                     => 'Clear Form',
    'LBL_SAVING'                    => 'Saving...',
    'LBL_EXPORT_PDF'                => 'Export PDF',
    'LBL_AI_ASSIST'                 => 'AI Assist',
    
    // Status Messages
    'LBL_TIMER_STARTED'             => 'Timer started',
    'LBL_TIMER_STOPPED'             => 'Timer stopped - duration updated',
    'LBL_TIME_SAVED_SUCCESS'        => 'Time entry saved successfully',
    
    // Today's Summary
    'LBL_TODAY_SUMMARY'             => 'Today\'s Summary',
    'LBL_ENTRIES'                   => 'Entries',
    'LBL_TOTAL_TIME'                => 'Total Time',
    'LBL_TOTAL_AMOUNT'              => 'Total Amount',
    
    // Configuration Labels
    'LBL_CONFIGURE_TITLE'           => 'Dashlet Title',
    'LBL_CONFIGURE_DEFAULT_ACTIVITY' => 'Default Activity Type',
    'LBL_CONFIGURE_DEFAULT_RATE'    => 'Default Hourly Rate ($)',
    'LBL_CONFIGURE_AUTO_SAVE'       => 'Enable Auto-Save',
    
    // Error Messages
    'LBL_ERROR_REQUIRED_FIELDS'     => 'Please fill in all required fields (Activity Type, Duration, Description, Date)',
    'LBL_ERROR_INVALID_DURATION'    => 'Please enter a valid duration greater than 0',
    'LBL_ERROR_FORM_NOT_FOUND'      => 'Form not found - please refresh the page',
    'LBL_ERROR_SAVING'              => 'Error saving time entry - please try again',
    'LBL_ERROR_GENERAL'             => 'An unexpected error occurred - please try again',
    'LBL_ERROR_PARSING_RESPONSE'    => 'Error processing server response',
    'LBL_ERROR_NETWORK'             => 'Network error - please check your connection',
    
    // Time Details (for task description)
    'LBL_TIME_DETAILS'              => 'Billable Time Details',
    'LBL_BILLABLE_TIME_ENTRY'       => 'Billable Time Entry',
    'LBL_ACTIVITY_TYPE'             => 'Activity Type',
    'LBL_HOURLY_RATE'               => 'Hourly Rate',
    'LBL_TOTAL_AMOUNT'              => 'Total Amount',
);