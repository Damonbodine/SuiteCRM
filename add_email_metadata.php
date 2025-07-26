<?php
/**
 * Add email metadata columns to ai_email_analysis table
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<!DOCTYPE html><html><head><title>Add Email Metadata</title></head><body>";
echo "<h1>📧 Adding Email Metadata Columns</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .info{color:blue}</style>";

try {
    global $db;
    
    echo "<h2>Step 1: Check Current Table Structure</h2>";
    
    $columnsQuery = "SHOW COLUMNS FROM ai_email_analysis";
    $columnsResult = $db->query($columnsQuery);
    
    $existingColumns = [];
    if ($columnsResult) {
        echo "<p class='info'>Current columns:</p><ul>";
        while ($row = $db->fetchByAssoc($columnsResult)) {
            $existingColumns[] = $row['Field'];
            echo "<li>" . $row['Field'] . " - " . $row['Type'] . "</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>Step 2: Add Missing Email Metadata Columns</h2>";
    
    $columnsToAdd = [
        'sender_name' => "VARCHAR(255) DEFAULT NULL COMMENT 'Email sender name'",
        'sender_email' => "VARCHAR(255) DEFAULT NULL COMMENT 'Email sender email address'", 
        'subject' => "VARCHAR(500) DEFAULT NULL COMMENT 'Email subject line'",
        'email_date' => "DATETIME DEFAULT NULL COMMENT 'Original email date'",
        'email_body' => "LONGTEXT DEFAULT NULL COMMENT 'Full email body content'"
    ];
    
    foreach ($columnsToAdd as $columnName => $columnDefinition) {
        if (!in_array($columnName, $existingColumns)) {
            echo "<p class='info'>🔄 Adding column: $columnName</p>";
            
            $alterQuery = "ALTER TABLE ai_email_analysis ADD COLUMN $columnName $columnDefinition";
            $result = $db->query($alterQuery);
            
            if ($result) {
                echo "<p class='success'>✅ Added $columnName successfully</p>";
            } else {
                echo "<p class='error'>❌ Failed to add $columnName: " . $db->lastError() . "</p>";
            }
        } else {
            echo "<p class='info'>✓ Column $columnName already exists</p>";
        }
    }
    
    echo "<h2>Step 3: Verify Updated Structure</h2>";
    
    $verifyQuery = "SHOW COLUMNS FROM ai_email_analysis";
    $verifyResult = $db->query($verifyQuery);
    
    if ($verifyResult) {
        echo "<p class='success'>✅ Updated table structure:</p><ul>";
        while ($row = $db->fetchByAssoc($verifyResult)) {
            echo "<li><strong>" . $row['Field'] . "</strong> - " . $row['Type'] . "</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>Success!</h2>";
    echo "<p class='success'>Email metadata columns have been added to the ai_email_analysis table.</p>";
    echo "<p><a href='debug_existing_data.php'>🔍 Debug Data</a> | <a href='test_email_fetch.php'>🧪 Test Email Fetch</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error</h2>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</body></html>";
?>