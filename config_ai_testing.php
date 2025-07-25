<?php
/**
 * AI Status Intelligence - Testing Configuration
 * Add this content to your config_override.php file
 */

// AI Status Intelligence Configuration for Testing
$sugar_config['ai_status_intelligence'] = array(
    // Enable the AI feature
    'enabled' => true,
    
    // Use mock AI analysis for testing (no OpenAI API required)
    'mock_mode' => true,
    
    // OpenAI API configuration (not needed in mock mode)
    'openai_api_key' => '', // Leave empty for mock mode
    'openai_model' => 'gpt-3.5-turbo', // Model to use when mock_mode is false
    'openai_max_tokens' => 150,
    'openai_temperature' => 0.3,
    
    // Analysis configuration
    'analysis_threshold' => 0.6, // Minimum confidence to show suggestions
    'rate_limit_seconds' => 60, // Minimum seconds between analyses per case
    'max_analysis_factors' => 10, // Maximum analysis factors to consider
    
    // UI configuration
    'show_confidence_bar' => true,
    'show_analysis_factors' => true,
    'show_last_analysis_time' => true,
    'widget_position' => 'top', // top, bottom, or sidebar
    
    // Security settings
    'require_case_edit_permission' => true,
    'admin_only_mode' => false, // Set to true to restrict to admin users only
    'audit_all_actions' => true,
    'enable_rate_limiting' => true,
    
    // Development/testing settings
    'debug_mode' => true, // Extra logging for troubleshooting
    'bypass_permission_check' => false, // Only for testing - NEVER use in production
    'mock_analysis_delay' => 0.1, // Simulate API delay in seconds
    
    // Mock analysis behavior configuration
    'mock_responses' => array(
        // High task completion scenario
        'high_completion' => array(
            'suggested_status' => 'Open_Pending Input',
            'confidence_score' => 0.85,
            'reasoning' => 'High task completion rate suggests case is ready for client input or closure'
        ),
        // Low activity scenario  
        'low_activity' => array(
            'suggested_status' => 'Open_Pending Input',
            'confidence_score' => 0.75,
            'reasoning' => 'Limited recent activity indicates case may be waiting for external input'
        ),
        // New case scenario
        'new_case' => array(
            'suggested_status' => 'Open_Assigned',
            'confidence_score' => 0.70,
            'reasoning' => 'New case should be assigned and have initial tasks created'
        ),
        // Ready for closure scenario
        'ready_for_closure' => array(
            'suggested_status' => 'Closed_Closed',
            'confidence_score' => 0.90,
            'reasoning' => 'All tasks completed and no recent activity suggests case can be closed'
        )
    )
);

// Development mode settings for better testing experience
$sugar_config['developer_mode'] = true;
$sugar_config['log_level'] = 'debug';
$sugar_config['display_errors'] = true;

// Cache settings to avoid stale data during testing
$sugar_config['disable_cache_for_tests'] = true;

// Database query logging (helpful for debugging)
$sugar_config['sql_debug_query'] = false; // Set to true if you need to see SQL queries

// JavaScript debugging
$sugar_config['js_debug'] = true;

// Email settings for testing (prevent accidental emails)
$sugar_config['disable_emails_for_testing'] = true;

?>

<!-- 
INSTRUCTIONS:
1. Copy the PHP code above (without this HTML comment block) 
2. Add it to your config_override.php file in the SuiteCRM root directory
3. If config_override.php doesn't exist, create it and start with:
   <?php
   if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
   
4. Save the file and clear SuiteCRM cache:
   - Admin → System Settings → Repair → Clear Cache
   
5. The AI feature will now work in mock mode without requiring OpenAI API

TESTING WORKFLOW:
1. Run: mysql -u [user] -p [db] < create_test_data.sql
2. Run: php test_ai_feature.php  
3. Access SuiteCRM web interface
4. Navigate to Cases module
5. Open "AI Test Case - Personal Injury Claim" 
6. Look for AI Status Intelligence widget
7. Edit case to trigger analysis
8. Test Accept/Dismiss functionality

SWITCHING TO REAL AI:
When ready to use actual OpenAI API:
1. Set 'mock_mode' => false
2. Add your OpenAI API key to 'openai_api_key'
3. Test with a small case first
4. Monitor usage and costs

SECURITY NOTES:
- Never commit API keys to version control
- Use environment variables for production API keys
- Set 'debug_mode' => false in production
- Set 'bypass_permission_check' => false (should always be false)
-->