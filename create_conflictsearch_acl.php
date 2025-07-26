<?php

// Direct MySQL connection using config values
$config = array(
    'host' => 'db',
    'username' => 'suiteuser',
    'password' => 'suitepass',  
    'database' => 'suitecrm'
);

function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['database']}", 
                   $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== CREATING ACL ACTIONS FOR CONFLICTSEARCH MODULE ===\n\n";
    
    // Standard ACL actions that every module should have
    $aclActions = array(
        'access',
        'view', 
        'list',
        'edit',
        'delete',
        'export',
        'import',
        'massupdate'
    );
    
    // Check if ConflictSearch ACL actions already exist
    $stmt = $pdo->prepare("SELECT name FROM acl_actions WHERE category = 'ConflictSearch'");
    $stmt->execute();
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (!empty($existing)) {
        echo "Found existing ACL actions for ConflictSearch: " . implode(', ', $existing) . "\n";
        echo "Deleting existing actions first...\n";
        $stmt = $pdo->prepare("DELETE FROM acl_actions WHERE category = 'ConflictSearch'");
        $stmt->execute();
        echo "Deleted " . $stmt->rowCount() . " existing ACL actions.\n\n";
    }
    
    // Insert ACL actions for ConflictSearch
    $insertStmt = $pdo->prepare("INSERT INTO acl_actions (id, name, category, acltype, aclaccess, deleted) VALUES (?, ?, 'ConflictSearch', 'module', 90, 0)");
    
    $created = 0;
    foreach ($aclActions as $action) {
        $id = generateUUID();
        echo "Creating ACL action: ConflictSearch -> $action (ID: $id)\n";
        
        $insertStmt->execute([$id, $action]);
        $created++;
    }
    
    echo "\nSuccessfully created $created ACL actions for ConflictSearch module.\n";
    
    // Verify the creation
    echo "\n=== VERIFICATION ===\n";
    $stmt = $pdo->prepare("SELECT * FROM acl_actions WHERE category = 'ConflictSearch' ORDER BY name");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($results as $row) {
        echo "✓ ConflictSearch -> {$row['name']} (ACL Access: {$row['aclaccess']})\n";
    }
    
    echo "\n=== NEXT STEPS ===\n";
    echo "1. The ACL actions have been created with aclaccess = 90 (Allow All)\n";
    echo "2. You may need to clear SuiteCRM cache\n";
    echo "3. You may need to run a Quick Repair and Rebuild\n";
    echo "4. The module should now appear in navigation\n";
    
} catch (PDOException $e) {
    echo "Database operation failed: " . $e->getMessage() . "\n";
}
?>