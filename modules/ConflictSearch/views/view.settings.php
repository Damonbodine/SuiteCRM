<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once 'include/MVC/View/views/view.detail.php';

/**
 * ConflictSearch Settings View
 * 
 * Provides configuration interface for conflict search system settings
 * including search parameters, thresholds, and integration options.
 */
class ConflictSearchViewSettings extends ViewDetail
{
    public function display()
    {
        global $current_user, $app_strings, $mod_strings;
        
        // Check admin permissions
        if (!$current_user->isAdmin()) {
            sugar_die('Access Denied: Administrator privileges required');
        }
        
        // Handle form submission
        if ($_POST['save_settings']) {
            $this->saveSettings();
        }
        
        echo $this->getModuleTitle();
        echo $this->getSettingsForm();
    }
    
    private function getModuleTitle()
    {
        return '
        <div class="moduleTitle">
            <h2>⚙️ Conflict Search Settings</h2>
            <div class="clear"></div>
        </div>';
    }
    
    private function getSettingsForm()
    {
        $settings = $this->loadSettings();
        
        $html = '<div style="padding: 20px;">';
        
        $html .= '<form method="post">';
        $html .= '<input type="hidden" name="save_settings" value="1">';
        
        // Search Configuration
        $html .= '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">';
        $html .= '<h3 style="margin-top: 0; color: #007cba;">🔍 Search Configuration</h3>';
        
        $html .= '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">';
        
        // Default search type
        $html .= '<div>';
        $html .= '<label style="display: block; margin-bottom: 5px; font-weight: bold;">Default Search Type:</label>';
        $html .= '<select name="default_search_type" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">';
        $html .= $this->getSelectOptions(array(
            'comprehensive' => 'Comprehensive Search',
            'exact' => 'Exact Match',
            'fuzzy' => 'Fuzzy Match',
            'phonetic' => 'Phonetic Match'
        ), $settings['default_search_type']);
        $html .= '</select>';
        $html .= '</div>';
        
        // Default confidence threshold
        $html .= '<div>';
        $html .= '<label style="display: block; margin-bottom: 5px; font-weight: bold;">Default Confidence Threshold:</label>';
        $html .= '<select name="default_confidence_threshold" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">';
        $html .= $this->getSelectOptions(array(
            'high' => 'High Risk Only',
            'medium' => 'Medium+ Risk',
            'low' => 'All Matches'
        ), $settings['default_confidence_threshold']);
        $html .= '</select>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        // Search Thresholds
        $html .= '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">';
        $html .= '<h3 style="margin-top: 0; color: #dc3545;">⚠️ Risk Thresholds</h3>';
        
        $html .= '<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">';
        
        // High confidence threshold
        $html .= '<div>';
        $html .= '<label style="display: block; margin-bottom: 5px; font-weight: bold;">High Risk Threshold (%):</label>';
        $html .= '<input type="number" name="high_risk_threshold" min="1" max="100" value="' . $settings['high_risk_threshold'] . '" ';
        $html .= 'style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">';
        $html .= '<small style="color: #666;">Matches above this percentage are considered high risk</small>';
        $html .= '</div>';
        
        // Medium confidence threshold
        $html .= '<div>';
        $html .= '<label style="display: block; margin-bottom: 5px; font-weight: bold;">Medium Risk Threshold (%):</label>';
        $html .= '<input type="number" name="medium_risk_threshold" min="1" max="100" value="' . $settings['medium_risk_threshold'] . '" ';
        $html .= 'style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">';
        $html .= '<small style="color: #666;">Matches above this percentage are considered medium risk</small>';
        $html .= '</div>';
        
        // Fuzzy match similarity
        $html .= '<div>';
        $html .= '<label style="display: block; margin-bottom: 5px; font-weight: bold;">Fuzzy Match Similarity (%):</label>';
        $html .= '<input type="number" name="fuzzy_similarity_threshold" min="1" max="100" value="' . $settings['fuzzy_similarity_threshold'] . '" ';
        $html .= 'style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">';
        $html .= '<small style="color: #666;">Minimum similarity for fuzzy matches</small>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        // Integration Settings
        $html .= '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">';
        $html .= '<h3 style="margin-top: 0; color: #28a745;">🔗 Integration Settings</h3>';
        
        $html .= '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">';
        
        // ElasticSearch settings
        $html .= '<div>';
        $html .= '<label style="display: block; margin-bottom: 10px;">';
        $html .= '<input type="checkbox" name="enable_elasticsearch" value="1" ' . ($settings['enable_elasticsearch'] ? 'checked' : '') . '> ';
        $html .= '<strong>Enable ElasticSearch Integration</strong>';
        $html .= '</label>';
        $html .= '<small style="color: #666;">Use ElasticSearch for faster and more accurate searches</small>';
        $html .= '</div>';
        
        // Automatic searches
        $html .= '<div>';
        $html .= '<label style="display: block; margin-bottom: 10px;">';
        $html .= '<input type="checkbox" name="auto_search_on_save" value="1" ' . ($settings['auto_search_on_save'] ? 'checked' : '') . '> ';
        $html .= '<strong>Auto-search on Contact/Account Save</strong>';
        $html .= '</label>';
        $html .= '<small style="color: #666;">Automatically check for conflicts when saving new contacts or accounts</small>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        // Email Notifications
        $html .= '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">';
        $html .= '<h3 style="margin-top: 0; color: #ffc107;">📧 Email Notifications</h3>';
        
        $html .= '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">';
        
        // High risk notifications
        $html .= '<div>';
        $html .= '<label style="display: block; margin-bottom: 10px;">';
        $html .= '<input type="checkbox" name="notify_high_risk" value="1" ' . ($settings['notify_high_risk'] ? 'checked' : '') . '> ';
        $html .= '<strong>Email Alerts for High Risk Matches</strong>';
        $html .= '</label>';
        $html .= '<input type="email" name="notification_email" placeholder="admin@example.com" value="' . $settings['notification_email'] . '" ';
        $html .= 'style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">';
        $html .= '<small style="color: #666;">Email address to receive high-risk conflict alerts</small>';
        $html .= '</div>';
        
        // Report frequency
        $html .= '<div>';
        $html .= '<label style="display: block; margin-bottom: 5px; font-weight: bold;">Weekly Report Schedule:</label>';
        $html .= '<select name="report_frequency" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">';
        $html .= $this->getSelectOptions(array(
            'disabled' => 'Disabled',
            'weekly' => 'Weekly Summary',
            'monthly' => 'Monthly Report'
        ), $settings['report_frequency']);
        $html .= '</select>';
        $html .= '<small style="color: #666;">Automated conflict search summary reports</small>';
        $html .= '</div>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        // Save button
        $html .= '<div style="text-align: center; padding: 20px;">';
        $html .= '<button type="submit" style="background: #007cba; color: white; border: none; padding: 12px 30px; border-radius: 5px; font-size: 16px; cursor: pointer;">💾 Save Settings</button>';
        $html .= '</div>';
        
        $html .= '</form>';
        $html .= '</div>';
        
        return $html;
    }
    
    private function getSelectOptions($options, $selected = '')
    {
        $html = '';
        foreach ($options as $value => $label) {
            $selectedAttr = ($value == $selected) ? ' selected' : '';
            $html .= "<option value=\"$value\"$selectedAttr>$label</option>";
        }
        return $html;
    }
    
    private function loadSettings()
    {
        global $sugar_config;
        
        $defaults = array(
            'default_search_type' => 'comprehensive',
            'default_confidence_threshold' => 'medium',
            'high_risk_threshold' => 85,
            'medium_risk_threshold' => 60,
            'fuzzy_similarity_threshold' => 75,
            'enable_elasticsearch' => false,
            'auto_search_on_save' => false,
            'notify_high_risk' => false,
            'notification_email' => '',
            'report_frequency' => 'disabled'
        );
        
        $settings = array();
        foreach ($defaults as $key => $default) {
            $settings[$key] = $sugar_config['conflict_search'][$key] ?? $default;
        }
        
        return $settings;
    }
    
    private function saveSettings()
    {
        $settings = array(
            'default_search_type' => $_POST['default_search_type'] ?? 'comprehensive',
            'default_confidence_threshold' => $_POST['default_confidence_threshold'] ?? 'medium',
            'high_risk_threshold' => intval($_POST['high_risk_threshold'] ?? 85),
            'medium_risk_threshold' => intval($_POST['medium_risk_threshold'] ?? 60),
            'fuzzy_similarity_threshold' => intval($_POST['fuzzy_similarity_threshold'] ?? 75),
            'enable_elasticsearch' => !empty($_POST['enable_elasticsearch']),
            'auto_search_on_save' => !empty($_POST['auto_search_on_save']),
            'notify_high_risk' => !empty($_POST['notify_high_risk']),
            'notification_email' => $_POST['notification_email'] ?? '',
            'report_frequency' => $_POST['report_frequency'] ?? 'disabled'
        );
        
        // Update sugar_config
        require_once 'modules/Configurator/Configurator.php';
        $configurator = new Configurator();
        $configurator->config['conflict_search'] = $settings;
        $configurator->saveConfig();
        
        // Show success message
        echo '<div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin: 20px; text-align: center;">';
        echo '✅ Settings saved successfully!';
        echo '</div>';
        
        // Refresh page after short delay
        echo '<script>setTimeout(function(){ window.location.reload(); }, 2000);</script>';
    }
}
?>