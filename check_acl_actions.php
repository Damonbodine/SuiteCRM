<?php

// Direct MySQL connection using config values
$config = array(
    'host' => 'db',
    'username' => 'suiteuser',
    'password' => 'suitepass',  
    'database' => 'suitecrm'
);

try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['database']}", 
                   $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ACL ACTIONS ANALYSIS ===\n\n";
    
    // Show sample ACL actions for comparison
    echo "1. SAMPLE ACL ACTIONS (first 10):\n";
    echo "==================================\n";
    $stmt = $pdo->query("SELECT * FROM acl_actions LIMIT 10");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $row) {
        echo "Category: {$row['category']}, Name: {$row['name']}, ACL Type: {$row['acltype']}\n";
    }
    
    echo "\n2. ACL ACTIONS FOR CASES MODULE (for comparison):\n";
    echo "==================================================\n";
    $stmt = $pdo->query("SELECT * FROM acl_actions WHERE category = 'Cases'");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $row) {
        echo "ID: {$row['id']}, Name: {$row['name']}, ACL Type: {$row['acltype']}\n";
    }
    
    echo "\n3. UNIQUE CATEGORIES IN ACL_ACTIONS:\n";
    echo "====================================\n";
    $stmt = $pdo->query("SELECT DISTINCT category FROM acl_actions ORDER BY category");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $row) {
        echo "- {$row['category']}\n";
    }
    
    echo "\n4. STANDARD ACL ACTION NAMES:\n";
    echo "=============================\n";
    $stmt = $pdo->query("SELECT DISTINCT name FROM acl_actions ORDER BY name");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($results as $row) {
        echo "- {$row['name']}\n";
    }
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
?>