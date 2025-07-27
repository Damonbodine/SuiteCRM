<?php
/**
 * Analyze SuiteCRM Data Structure for Smart Compose
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}
require_once('include/entryPoint.php');

echo "<!DOCTYPE html><html><head><title>SuiteCRM Data Analysis</title></head><body>";
echo "<h1>🔍 SuiteCRM Data Structure Analysis</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .info{color:blue} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ddd;padding:8px;text-align:left}</style>";

try {
    global $db;
    
    echo "<h2>Legal-Relevant Tables</h2>";
    
    // Find tables related to legal work
    $tables = ['cases', 'contacts', 'accounts', 'leads', 'opportunities', 'notes', 'calls', 'meetings', 'tasks'];
    
    foreach ($tables as $table) {
        echo "<h3>📋 Table: $table</h3>";
        
        // Check if table exists and get sample data
        $checkQuery = "SHOW TABLES LIKE '$table'";
        $checkResult = $db->query($checkQuery);
        
        if ($checkResult && $db->fetchByAssoc($checkResult)) {
            echo "<p class='success'>✅ Table exists</p>";
            
            // Get column structure
            $columnsQuery = "SHOW COLUMNS FROM $table";
            $columnsResult = $db->query($columnsQuery);
            
            if ($columnsResult) {
                echo "<h4>Columns:</h4>";
                echo "<table>";
                echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
                
                while ($column = $db->fetchByAssoc($columnsResult)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($column['Key'] ?? '') . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            }
            
            // Get sample records (limit to 3)
            $sampleQuery = "SELECT * FROM $table WHERE deleted = 0 ORDER BY date_entered DESC LIMIT 3";
            $sampleResult = $db->query($sampleQuery);
            
            if ($sampleResult) {
                echo "<h4>Sample Records:</h4>";
                $count = 0;
                while (($row = $db->fetchByAssoc($sampleResult)) && $count < 3) {
                    $count++;
                    echo "<p><strong>Record $count:</strong></p>";
                    echo "<table>";
                    foreach ($row as $field => $value) {
                        if (strlen($value) > 100) $value = substr($value, 0, 100) . '...';
                        echo "<tr><td><strong>$field</strong></td><td>" . htmlspecialchars($value ?? '') . "</td></tr>";
                    }
                    echo "</table><br>";
                }
            }
        } else {
            echo "<p class='error'>❌ Table does not exist</p>";
        }
        echo "<hr>";
    }
    
    echo "<h2>Legal-Specific Analysis</h2>";
    
    // Analyze Cases table specifically
    echo "<h3>🏛️ Cases Analysis</h3>";
    $casesQuery = "SELECT 
                    COUNT(*) as total_cases,
                    SUM(CASE WHEN status = 'Open' THEN 1 ELSE 0 END) as open_cases,
                    SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) as closed_cases,
                    COUNT(DISTINCT assigned_user_id) as attorneys_handling
                   FROM cases 
                   WHERE deleted = 0";
    
    $casesResult = $db->query($casesQuery);
    if ($casesResult && ($caseStats = $db->fetchByAssoc($casesResult))) {
        echo "<p>📊 <strong>Total Cases:</strong> " . $caseStats['total_cases'] . "</p>";
        echo "<p>📂 <strong>Open Cases:</strong> " . $caseStats['open_cases'] . "</p>";
        echo "<p>✅ <strong>Closed Cases:</strong> " . $caseStats['closed_cases'] . "</p>";
        echo "<p>👥 <strong>Attorneys Handling:</strong> " . $caseStats['attorneys_handling'] . "</p>";
    }
    
    // Get recent cases for template context
    echo "<h4>Recent Cases (for templates):</h4>";
    $recentCasesQuery = "SELECT id, name, case_number, status, type, priority 
                         FROM cases 
                         WHERE deleted = 0 
                         ORDER BY date_entered DESC 
                         LIMIT 5";
    
    $recentResult = $db->query($recentCasesQuery);
    if ($recentResult) {
        echo "<table>";
        echo "<tr><th>Case Number</th><th>Name</th><th>Status</th><th>Type</th><th>Priority</th></tr>";
        while ($case = $db->fetchByAssoc($recentResult)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($case['case_number'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($case['name'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($case['status'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($case['type'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($case['priority'] ?? '') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Analyze Contacts for recipient suggestions
    echo "<h3>👥 Contacts Analysis</h3>";
    $contactsQuery = "SELECT 
                        COUNT(*) as total_contacts,
                        COUNT(DISTINCT account_id) as associated_accounts,
                        SUM(CASE WHEN email1 IS NOT NULL AND email1 != '' THEN 1 ELSE 0 END) as contacts_with_email
                      FROM contacts 
                      WHERE deleted = 0";
    
    $contactsResult = $db->query($contactsQuery);
    if ($contactsResult && ($contactStats = $db->fetchByAssoc($contactsResult))) {
        echo "<p>📊 <strong>Total Contacts:</strong> " . $contactStats['total_contacts'] . "</p>";
        echo "<p>🏢 <strong>Associated Accounts:</strong> " . $contactStats['associated_accounts'] . "</p>";
        echo "<p>📧 <strong>Contacts with Email:</strong> " . $contactStats['contacts_with_email'] . "</p>";
    }
    
    echo "<h2>Smart Compose Recommendations</h2>";
    echo "<div class='info'>";
    echo "<h4>💡 Based on this analysis, we can create:</h4>";
    echo "<ul>";
    echo "<li><strong>Case-Based Templates:</strong> Auto-populate case numbers, client names, case types</li>";
    echo "<li><strong>Contact Suggestions:</strong> Smart recipient picker based on case associations</li>";
    echo "<li><strong>Legal Document Templates:</strong> Discovery requests, settlement offers, court filings</li>";
    echo "<li><strong>Client Communication Templates:</strong> Status updates, appointment scheduling</li>";
    echo "<li><strong>Court Communication Templates:</strong> Scheduling, document submissions</li>";
    echo "<li><strong>Opposing Counsel Templates:</strong> Settlement negotiations, discovery responses</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>Next Steps</h2>";
    echo "<p>1. Create legal email templates with variable placeholders</p>";
    echo "<p>2. Build smart recipient suggestions based on case/contact relationships</p>";
    echo "<p>3. Integrate with compose form for contextual assistance</p>";
    echo "<p>4. Add AI-powered template recommendations based on email content</p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>