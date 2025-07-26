<?php
/**
 * Check and create ai_email_analysis table if needed
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<!DOCTYPE html><html><head><title>Database Table Check</title></head><body>";
echo "<h1>🗄️ Database Table Check</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .info{color:blue}</style>";

try {
    global $db;
    
    echo "<h2>Check 1: ai_email_analysis Table</h2>";
    
    // Check if table exists
    $tableCheck = $db->query("SHOW TABLES LIKE 'ai_email_analysis'");
    if ($tableCheck && $db->fetchByAssoc($tableCheck)) {
        echo "<p class='success'>✅ ai_email_analysis table exists</p>";
        
        // Check table structure
        $structureCheck = $db->query("DESCRIBE ai_email_analysis");
        if ($structureCheck) {
            echo "<h3>Table Structure:</h3>";
            echo "<ul>";
            while ($row = $db->fetchByAssoc($structureCheck)) {
                echo "<li><strong>" . $row['Field'] . "</strong> - " . $row['Type'] . "</li>";
            }
            echo "</ul>";
        }
        
        // Count records
        $countResult = $db->query("SELECT COUNT(*) as count FROM ai_email_analysis");
        if ($countResult && $countRow = $db->fetchByAssoc($countResult)) {
            echo "<p class='info'>📊 Table has " . $countRow['count'] . " records</p>";
        }
        
    } else {
        echo "<p class='error'>❌ ai_email_analysis table does not exist</p>";
        echo "<p class='info'>🔄 Creating table...</p>";
        
        // Read and execute the schema
        $schemaFile = 'create_ai_email_analysis_table.sql';
        if (file_exists($schemaFile)) {
            $schema = file_get_contents($schemaFile);
            
            // Split by semicolon and execute each statement
            $statements = explode(';', $schema);
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement) && !strpos($statement, '--') === 0) {
                    $result = $db->query($statement);
                    if (!$result) {
                        throw new Exception("Failed to execute: " . $statement . " - " . $db->lastError());
                    }
                }
            }
            
            echo "<p class='success'>✅ Table created successfully</p>";
        } else {
            echo "<p class='error'>❌ Schema file not found: $schemaFile</p>";
        }
    }
    
    echo "<h2>Check 2: Test Data</h2>";
    
    // Check if test data exists
    $testDataCheck = $db->query("SELECT COUNT(*) as count FROM ai_email_analysis WHERE email_message_id LIKE 'test_%'");
    if ($testDataCheck && $testRow = $db->fetchByAssoc($testDataCheck)) {
        echo "<p class='info'>📊 Found " . $testRow['count'] . " test records</p>";
        
        if ($testRow['count'] == 0) {
            echo "<p class='info'>🔄 Adding test data...</p>";
            
            $testInsert = "INSERT INTO ai_email_analysis 
                          (id, email_message_id, user_id, category, priority_score, ai_summary, analysis_date, expires_at, deleted) 
                          VALUES 
                          ('" . create_guid() . "', 'test_email_1', '1', 'sales', 8, 'Important sales inquiry from potential client about enterprise services', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 0),
                          ('" . create_guid() . "', 'test_email_2', '1', 'support', 6, 'Customer support request requiring follow-up within 24 hours', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 0),
                          ('" . create_guid() . "', 'test_email_3', '1', 'general', 4, 'General business communication, no immediate action required', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 0)";
            
            $result = $db->query($testInsert);
            if ($result) {
                echo "<p class='success'>✅ Test data added successfully</p>";
            } else {
                echo "<p class='error'>❌ Failed to add test data: " . $db->lastError() . "</p>";
            }
        }
    }
    
    echo "<h2>Summary</h2>";
    echo "<p>Database setup is now ready for email analysis.</p>";
    echo "<p><a href='test_email_fetch.php'>🧪 Test Email Fetch</a> | <a href='index.php?module=Emails&action=index'>📧 Go to Emails</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Database Error</h2>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>