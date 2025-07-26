<?php
/**
 * OAuth Token Cleanup Script
 * 
 * This script cleans up expired and invalid OAuth tokens to reset the authentication state.
 */

if (!defined('sugarEntry') || !sugarEntry) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

header('Content-Type: text/html; charset=UTF-8');
echo "<html><head><title>OAuth Token Cleanup</title></head><body>";
echo "<h1>🧹 OAuth Token Cleanup</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .warning{color:orange} .info{color:blue}</style>";

try {
    global $db, $current_user;
    
    echo "<h2>📊 Current Status</h2>";
    
    // Show current connections
    $query = "SELECT COUNT(*) as total, 
                     SUM(CASE WHEN deleted = 0 THEN 1 ELSE 0 END) as active,
                     SUM(CASE WHEN deleted = 1 THEN 1 ELSE 0 END) as deleted,
                     SUM(CASE WHEN access_token_expires < NOW() THEN 1 ELSE 0 END) as expired
              FROM external_oauth_connections 
              WHERE name LIKE '%Gmail%'";
    
    $result = $db->query($query);
    $stats = $db->fetchByAssoc($result);
    
    echo "<p><strong>Total Gmail connections:</strong> {$stats['total']}</p>";
    echo "<p><strong>Active connections:</strong> {$stats['active']}</p>";
    echo "<p><strong>Deleted connections:</strong> {$stats['deleted']}</p>";
    echo "<p><strong>Expired connections:</strong> {$stats['expired']}</p>";
    
    if (isset($_GET['action']) && $_GET['action'] === 'cleanup') {
        echo "<h2>🗑️ Performing Cleanup</h2>";
        
        // Step 1: Mark all expired tokens as deleted
        $expiredQuery = "UPDATE external_oauth_connections 
                        SET deleted = 1, date_modified = NOW() 
                        WHERE name LIKE '%Gmail%' 
                        AND access_token_expires < NOW() 
                        AND deleted = 0";
        
        $expiredResult = $db->query($expiredQuery);
        $expiredCount = $db->getAffectedRowCount($expiredResult);
        echo "<p class='info'>✅ Marked {$expiredCount} expired tokens as deleted</p>";
        
        // Step 2: Mark tokens with null/empty access tokens as deleted
        $invalidQuery = "UPDATE external_oauth_connections 
                        SET deleted = 1, date_modified = NOW() 
                        WHERE name LIKE '%Gmail%' 
                        AND (access_token IS NULL OR access_token = '') 
                        AND deleted = 0";
        
        $invalidResult = $db->query($invalidQuery);
        $invalidCount = $db->getAffectedRowCount($invalidResult);
        echo "<p class='info'>✅ Marked {$invalidCount} invalid tokens as deleted</p>";
        
        // Step 3: Clean up old deleted records (older than 30 days)
        $oldQuery = "DELETE FROM external_oauth_connections 
                     WHERE name LIKE '%Gmail%' 
                     AND deleted = 1 
                     AND date_modified < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        
        $oldResult = $db->query($oldQuery);
        $oldCount = $db->getAffectedRowCount($oldResult);
        echo "<p class='info'>✅ Permanently deleted {$oldCount} old records</p>";
        
        echo "<h3>🎯 Cleanup Complete!</h3>";
        echo "<p class='success'>You can now proceed with a fresh OAuth authentication.</p>";
        echo "<p><a href='index.php?module=Emails&action=index' style='background:#28a745;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>Go to Emails Module</a></p>";
        
    } else {
        echo "<h2>🔧 Recommended Actions</h2>";
        
        if ($stats['expired'] > 0 || $stats['deleted'] > 0) {
            echo "<div style='background:#fff3cd;padding:15px;border:1px solid #ffeaa7;border-radius:4px;margin:15px 0'>";
            echo "<h3>⚠️ Cleanup Needed</h3>";
            echo "<p>You have expired or deleted OAuth connections that should be cleaned up.</p>";
            echo "<p><a href='?action=cleanup' style='background:#ffc107;color:black;padding:8px 12px;text-decoration:none;border-radius:4px'>🧹 Run Cleanup</a></p>";
            echo "</div>";
        } else {
            echo "<div style='background:#d4edda;padding:15px;border:1px solid #c3e6cb;border-radius:4px'>";
            echo "<h3>✅ No Cleanup Needed</h3>";
            echo "<p>Your OAuth connections look clean.</p>";
            echo "</div>";
        }
    }
    
    echo "<h2>📋 Current Active Connections</h2>";
    
    // Show active connections
    $activeQuery = "SELECT id, name, assigned_user_id, access_token_expires, 
                           CASE WHEN access_token IS NOT NULL AND access_token != '' THEN 'Yes' ELSE 'No' END as has_token,
                           CASE WHEN refresh_token IS NOT NULL AND refresh_token != '' THEN 'Yes' ELSE 'No' END as has_refresh
                    FROM external_oauth_connections 
                    WHERE name LIKE '%Gmail%' 
                    AND deleted = 0 
                    ORDER BY date_modified DESC";
    
    $activeResult = $db->query($activeQuery);
    
    if ($activeResult) {
        $connections = [];
        while ($row = $db->fetchByAssoc($activeResult)) {
            $connections[] = $row;
        }
        
        if (empty($connections)) {
            echo "<p class='warning'>⚠️ No active Gmail connections found</p>";
        } else {
            echo "<table style='border-collapse:collapse;width:100%'>";
            echo "<tr style='background:#f0f0f0'>";
            echo "<th style='border:1px solid #ddd;padding:8px'>Name</th>";
            echo "<th style='border:1px solid #ddd;padding:8px'>User ID</th>";
            echo "<th style='border:1px solid #ddd;padding:8px'>Has Token</th>";
            echo "<th style='border:1px solid #ddd;padding:8px'>Has Refresh</th>";
            echo "<th style='border:1px solid #ddd;padding:8px'>Expires</th>";
            echo "<th style='border:1px solid #ddd;padding:8px'>Status</th>";
            echo "</tr>";
            
            foreach ($connections as $conn) {
                $isExpired = !empty($conn['access_token_expires']) && strtotime($conn['access_token_expires']) < time();
                $statusClass = $isExpired ? 'error' : 'success';
                $status = $isExpired ? 'Expired' : 'Active';
                
                echo "<tr>";
                echo "<td style='border:1px solid #ddd;padding:8px'>" . htmlspecialchars($conn['name']) . "</td>";
                echo "<td style='border:1px solid #ddd;padding:8px'>" . htmlspecialchars($conn['assigned_user_id']) . "</td>";
                echo "<td style='border:1px solid #ddd;padding:8px'>" . $conn['has_token'] . "</td>";
                echo "<td style='border:1px solid #ddd;padding:8px'>" . $conn['has_refresh'] . "</td>";
                echo "<td style='border:1px solid #ddd;padding:8px'>" . htmlspecialchars($conn['access_token_expires'] ?? 'Never') . "</td>";
                echo "<td style='border:1px solid #ddd;padding:8px' class='{$statusClass}'>" . $status . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<br><p><a href='debug_oauth_status.php' style='background:#007bff;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🔍 Debug OAuth Status</a></p>";
echo "<p><a href='index.php' style='background:#6c757d;color:white;padding:10px 15px;text-decoration:none;border-radius:4px'>🏠 Back to Home</a></p>";

echo "</body></html>";
?>