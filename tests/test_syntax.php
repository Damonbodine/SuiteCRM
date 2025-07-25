<?php
/**
 * Test syntax of detailviewdefs.php
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}
echo "<h1>Testing DetailView Syntax</h1>";

// Test if the file can be included without syntax errors
try {
    ob_start();
    include('custom/modules/Cases/metadata/detailviewdefs.php');
    ob_end_clean();
    echo "✅ Syntax OK - No parse errors found<br>";
    
    // Check if viewdefs array is properly defined
    if (isset($viewdefs) && isset($viewdefs['Cases'])) {
        echo "✅ ViewDefs array properly defined<br>";
    } else {
        echo "❌ ViewDefs array not found<br>";
    }
    
} catch (ParseError $e) {
    echo "❌ Parse Error: " . $e->getMessage() . "<br>";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h2>Test Complete</h2>";
?>