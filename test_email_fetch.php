<?php
/**
 * Test email fetching and AI analysis
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<!DOCTYPE html><html><head><title>Email Fetch Test</title></head><body>";
echo "<h1>📧 Email Fetch and AI Analysis Test</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px} .success{color:green} .error{color:red} .info{color:blue}</style>";

try {
    // Test the AIEmailAnalyzer
    require_once('custom/include/AIEmailAnalyzer.php');
    
    echo "<h2>Test 1: AIEmailAnalyzer Initialization</h2>";
    $analyzer = new AIEmailAnalyzer();
    echo "<p class='success'>✅ AIEmailAnalyzer created successfully</p>";
    
    echo "<h2>Test 2: Process Unanalyzed Emails</h2>";
    echo "<p class='info'>🔄 Starting email processing...</p>";
    
    $result = $analyzer->processUnanalyzedEmails();
    
    if ($result) {
        echo "<p class='success'>✅ Email processing completed successfully</p>";
    } else {
        echo "<p class='error'>❌ Email processing failed</p>";
    }
    
    echo "<h2>Test 3: Get Analyzed Emails</h2>";
    $emails = $analyzer->getAnalyzedEmails(5);
    
    if (!empty($emails)) {
        echo "<p class='success'>✅ Found " . count($emails) . " analyzed emails</p>";
        echo "<h3>Sample Emails:</h3>";
        echo "<ul>";
        foreach ($emails as $email) {
            echo "<li><strong>Subject:</strong> " . htmlspecialchars($email['subject'] ?? 'No Subject');
            echo " | <strong>Category:</strong> " . htmlspecialchars($email['category'] ?? 'Uncategorized');
            echo " | <strong>Priority:</strong> " . htmlspecialchars($email['priority_score'] ?? 'N/A') . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='error'>❌ No analyzed emails found</p>";
        echo "<p class='info'>This could mean:</p>";
        echo "<ul>";
        echo "<li>No Gmail connection is configured</li>";
        echo "<li>No emails were fetched from Gmail API</li>";
        echo "<li>AI analysis failed</li>";
        echo "<li>Database connection issues</li>";
        echo "</ul>";
    }
    
    echo "<h2>Test Summary</h2>";
    echo "<p>Check the SuiteCRM error logs for detailed debugging information.</p>";
    echo "<p><a href='index.php?module=Emails&action=index'>📧 Go to Emails Module</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Test Error</h2>";
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

echo "</body></html>";
?>