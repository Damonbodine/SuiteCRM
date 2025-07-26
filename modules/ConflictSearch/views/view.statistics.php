<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

require_once 'include/MVC/View/views/view.detail.php';

/**
 * ConflictSearch Statistics View
 * 
 * Displays comprehensive statistics and analytics for conflict searches
 * including usage patterns, risk assessments, and compliance metrics.
 */
class ConflictSearchViewStatistics extends ViewDetail
{
    public function display()
    {
        global $current_user, $app_strings, $mod_strings;
        
        // Check admin permissions
        if (!$current_user->isAdmin()) {
            sugar_die('Access Denied: Administrator privileges required');
        }
        
        echo $this->getModuleTitle();
        echo $this->getStatisticsContent();
    }
    
    private function getModuleTitle()
    {
        return '
        <div class="moduleTitle">
            <h2>⚖️ Conflict Search Statistics & Analytics</h2>
            <div class="clear"></div>
        </div>';
    }
    
    private function getStatisticsContent()
    {
        $stats = $this->gatherStatistics();
        
        $html = '<div style="padding: 20px;">';
        
        // Summary Cards
        $html .= '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">';
        
        $html .= $this->createStatCard('Total Searches', $stats['total_searches'], '🔍', '#007cba');
        $html .= $this->createStatCard('High Risk Matches', $stats['high_risk_total'], '🔴', '#dc3545');
        $html .= $this->createStatCard('Medium Risk Matches', $stats['medium_risk_total'], '🟡', '#ffc107');
        $html .= $this->createStatCard('Active Users', $stats['active_users'], '👥', '#28a745');
        
        $html .= '</div>';
        
        // Charts and Analytics
        $html .= '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">';
        
        // Recent Activity Chart
        $html .= '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
        $html .= '<h3 style="margin-top: 0;">📈 Search Activity (Last 30 Days)</h3>';
        $html .= $this->createActivityChart($stats['daily_activity']);
        $html .= '</div>';
        
        // Risk Distribution Pie Chart
        $html .= '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
        $html .= '<h3 style="margin-top: 0;">⚠️ Risk Level Distribution</h3>';
        $html .= $this->createRiskChart($stats);
        $html .= '</div>';
        
        $html .= '</div>';
        
        // Detailed Tables
        $html .= '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">';
        
        // Top Search Terms
        $html .= '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
        $html .= '<h3 style="margin-top: 0;">🏆 Most Searched Terms</h3>';
        $html .= $this->createTopSearchTermsTable($stats['top_search_terms']);
        $html .= '</div>';
        
        // Most Active Users
        $html .= '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">';
        $html .= '<h3 style="margin-top: 0;">👤 Most Active Users</h3>';
        $html .= $this->createActiveUsersTable($stats['active_user_list']);
        $html .= '</div>';
        
        $html .= '</div>';
        
        // Action Buttons
        $html .= '<div style="margin-top: 30px; text-align: center;">';
        $html .= '<a href="index.php?module=ConflictSearch&action=index" class="button" style="margin-right: 10px;">View All Searches</a>';
        $html .= '<a href="index.php?module=ConflictSearch&action=EditView" class="button">New Search</a>';
        $html .= '</div>';
        
        $html .= '</div>';
        
        return $html;
    }
    
    private function createStatCard($title, $value, $icon, $color)
    {
        return "
        <div style='background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center;'>
            <div style='font-size: 48px; margin-bottom: 10px;'>$icon</div>
            <div style='font-size: 36px; font-weight: bold; color: $color; margin-bottom: 5px;'>$value</div>
            <div style='color: #666; font-size: 14px;'>$title</div>
        </div>";
    }
    
    private function createActivityChart($dailyActivity)
    {
        $html = '<div style="height: 200px; display: flex; align-items: end; gap: 5px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">';
        
        $maxValue = max(array_column($dailyActivity, 'count')) ?: 1;
        
        foreach ($dailyActivity as $day) {
            $height = ($day['count'] / $maxValue) * 180;
            $html .= "<div style='background: #007cba; width: 20px; height: {$height}px; border-radius: 2px 2px 0 0; position: relative;' title='{$day['date']}: {$day['count']} searches'></div>";
        }
        
        $html .= '</div>';
        $html .= '<div style="margin-top: 10px; font-size: 12px; color: #666;">Hover over bars for details</div>';
        
        return $html;
    }
    
    private function createRiskChart($stats)
    {
        $total = $stats['high_risk_total'] + $stats['medium_risk_total'] + $stats['low_risk_total'];
        
        if ($total == 0) {
            return '<div style="text-align: center; color: #666; padding: 40px;">No risk data available</div>';
        }
        
        $highPercent = round(($stats['high_risk_total'] / $total) * 100, 1);
        $mediumPercent = round(($stats['medium_risk_total'] / $total) * 100, 1);
        $lowPercent = round(($stats['low_risk_total'] / $total) * 100, 1);
        
        return "
        <div style='text-align: center;'>
            <div style='display: inline-block; margin: 20px;'>
                <div style='width: 150px; height: 150px; border-radius: 50%; background: conic-gradient(
                    #dc3545 0deg {$highPercent}%,
                    #ffc107 {$highPercent}% " . ($highPercent + $mediumPercent) . "%,
                    #28a745 " . ($highPercent + $mediumPercent) . "% 100%
                ); position: relative;'>
                    <div style='position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); font-weight: bold;'>
                        $total<br><small>Total</small>
                    </div>
                </div>
            </div>
            <div style='margin-top: 20px;'>
                <div style='display: inline-block; margin: 0 10px;'>
                    <span style='color: #dc3545;'>● High Risk: {$stats['high_risk_total']} ({$highPercent}%)</span>
                </div>
                <div style='display: inline-block; margin: 0 10px;'>
                    <span style='color: #ffc107;'>● Medium Risk: {$stats['medium_risk_total']} ({$mediumPercent}%)</span>
                </div>
                <div style='display: inline-block; margin: 0 10px;'>
                    <span style='color: #28a745;'>● Low Risk: {$stats['low_risk_total']} ({$lowPercent}%)</span>
                </div>
            </div>
        </div>";
    }
    
    private function createTopSearchTermsTable($topTerms)
    {
        if (empty($topTerms)) {
            return '<div style="text-align: center; color: #666; padding: 20px;">No search data available</div>';
        }
        
        $html = '<table style="width: 100%; border-collapse: collapse;">';
        $html .= '<thead><tr style="background: #f8f9fa;"><th style="padding: 10px; text-align: left;">Search Term</th><th style="padding: 10px; text-align: center;">Count</th></tr></thead>';
        $html .= '<tbody>';
        
        foreach ($topTerms as $term) {
            $html .= "<tr style='border-bottom: 1px solid #eee;'>";
            $html .= "<td style='padding: 10px;'>" . htmlspecialchars($term['search_term']) . "</td>";
            $html .= "<td style='padding: 10px; text-align: center;'>" . $term['search_count'] . "</td>";
            $html .= "</tr>";
        }
        
        $html .= '</tbody></table>';
        
        return $html;
    }
    
    private function createActiveUsersTable($activeUsers)
    {
        if (empty($activeUsers)) {
            return '<div style="text-align: center; color: #666; padding: 20px;">No user data available</div>';
        }
        
        $html = '<table style="width: 100%; border-collapse: collapse;">';
        $html .= '<thead><tr style="background: #f8f9fa;"><th style="padding: 10px; text-align: left;">User</th><th style="padding: 10px; text-align: center;">Searches</th></tr></thead>';
        $html .= '<tbody>';
        
        foreach ($activeUsers as $user) {
            $html .= "<tr style='border-bottom: 1px solid #eee;'>";
            $html .= "<td style='padding: 10px;'>" . htmlspecialchars($user['user_name']) . "</td>";
            $html .= "<td style='padding: 10px; text-align: center;'>" . $user['search_count'] . "</td>";
            $html .= "</tr>";
        }
        
        $html .= '</tbody></table>';
        
        return $html;
    }
    
    private function gatherStatistics()
    {
        $db = DBManagerFactory::getInstance();
        
        $stats = array();
        
        // Total searches
        $result = $db->query("SELECT COUNT(*) as total FROM conflict_search WHERE deleted = 0");
        $row = $db->fetchByAssoc($result);
        $stats['total_searches'] = $row['total'] ?: 0;
        
        // Risk totals
        $result = $db->query("SELECT 
            SUM(high_confidence_matches) as high_total,
            SUM(medium_confidence_matches) as medium_total,
            SUM(low_confidence_matches) as low_total
            FROM conflict_search WHERE deleted = 0");
        $row = $db->fetchByAssoc($result);
        $stats['high_risk_total'] = $row['high_total'] ?: 0;
        $stats['medium_risk_total'] = $row['medium_total'] ?: 0;
        $stats['low_risk_total'] = $row['low_total'] ?: 0;
        
        // Active users count
        $result = $db->query("SELECT COUNT(DISTINCT created_by) as active_users FROM conflict_search WHERE deleted = 0");
        $row = $db->fetchByAssoc($result);
        $stats['active_users'] = $row['active_users'] ?: 0;
        
        // Daily activity (last 30 days)
        $stats['daily_activity'] = array();
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $result = $db->query("SELECT COUNT(*) as count FROM conflict_search 
                WHERE DATE(date_entered) = '$date' AND deleted = 0");
            $row = $db->fetchByAssoc($result);
            $stats['daily_activity'][] = array(
                'date' => $date,
                'count' => $row['count'] ?: 0
            );
        }
        
        // Top search terms
        $result = $db->query("SELECT search_term, COUNT(*) as search_count 
            FROM conflict_search 
            WHERE deleted = 0 AND search_term IS NOT NULL AND search_term != ''
            GROUP BY search_term 
            ORDER BY search_count DESC 
            LIMIT 10");
        $stats['top_search_terms'] = array();
        while ($row = $db->fetchByAssoc($result)) {
            $stats['top_search_terms'][] = $row;
        }
        
        // Active users list
        $result = $db->query("SELECT u.user_name, COUNT(*) as search_count 
            FROM conflict_search cs 
            JOIN users u ON cs.created_by = u.id 
            WHERE cs.deleted = 0 
            GROUP BY cs.created_by, u.user_name 
            ORDER BY search_count DESC 
            LIMIT 10");
        $stats['active_user_list'] = array();
        while ($row = $db->fetchByAssoc($result)) {
            $stats['active_user_list'][] = $row;
        }
        
        return $stats;
    }
}
?>