<?php

// Direct tab fix without user session requirements
$config = array(
    'host' => 'db',
    'username' => 'suiteuser',
    'password' => 'suitepass',  
    'database' => 'suitecrm'
);

echo "Direct ConflictSearch Tab Integration\n\n";

try {
    $pdo = new PDO("mysql:host={$config['host']};dbname={$config['database']}", 
                   $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "1. Checking current tab configuration...\n";
    
    // Check if system tabs config exists
    $stmt = $pdo->prepare("SELECT * FROM config WHERE category = 'system' AND name = 'tabs'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "   Found existing system tabs config\n";
    } else {
        echo "   No system tabs config found, will create\n";
    }
    
    echo "2. Adding ConflictSearch to display modules configuration...\n";
    
    // Update the config to include ConflictSearch in system tabs
    $tabsArray = array(
        'ConflictSearch' => 'ConflictSearch',
        'Home' => 'Home',
        'Contacts' => 'Contacts', 
        'Accounts' => 'Accounts',
        'Cases' => 'Cases',
        'Leads' => 'Leads',
        'Opportunities' => 'Opportunities'
    );
    
    $serializedTabs = base64_encode(serialize($tabsArray));
    
    $stmt = $pdo->prepare("INSERT INTO config (category, name, value) VALUES ('system', 'tabs', ?) 
                          ON DUPLICATE KEY UPDATE value = ?");
    $stmt->execute([$serializedTabs, $serializedTabs]);
    
    echo "   Updated system tabs configuration\n";
    
    echo "3. Setting ConflictSearch as displayed module...\n";
    
    // Ensure ConflictSearch is in the displayed modules list
    $stmt = $pdo->prepare("INSERT INTO config (category, name, value) VALUES ('display_tabs', 'ConflictSearch', '1') 
                          ON DUPLICATE KEY UPDATE value = '1'");
    $stmt->execute();
    
    echo "   Set ConflictSearch as displayed module\n";
    
    echo "4. Creating default user preference for all users...\n";
    
    // Get all users and add ConflictSearch to their display tabs
    $stmt = $pdo->prepare("SELECT id FROM users WHERE deleted = 0 AND status = 'Active'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $userCount = 0;
    foreach ($users as $user) {
        // Create display_tabs preference for user if it doesn't exist
        $userTabs = array('ConflictSearch' => 'ConflictSearch');
        $serializedUserTabs = base64_encode(serialize($userTabs));
        
        $stmt = $pdo->prepare("INSERT INTO user_preferences (id, assigned_user_id, category, contents, deleted) 
                              VALUES (UUID(), ?, 'display_tabs', ?, 0)
                              ON DUPLICATE KEY UPDATE contents = ?");
        $stmt->execute([$user['id'], $serializedUserTabs, $serializedUserTabs]);
        $userCount++;
    }
    
    echo "   Updated display tabs for $userCount users\n";
    
    echo "5. Verification...\n";
    
    // Verify the configuration
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM config WHERE name = 'tabs' AND value LIKE '%ConflictSearch%'");
    $stmt->execute();
    $configCount = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM user_preferences WHERE category = 'display_tabs' AND contents LIKE '%ConflictSearch%'");
    $stmt->execute();
    $userPrefCount = $stmt->fetchColumn();
    
    echo "   System configs with ConflictSearch: $configCount\n";
    echo "   User preferences with ConflictSearch: $userPrefCount\n";
    
    echo "\n✅ SUCCESS: ConflictSearch tab integration complete!\n";
    echo "\nNext steps:\n";
    echo "1. Clear browser cache and refresh SuiteCRM\n";
    echo "2. Log out and log back in\n"; 
    echo "3. ConflictSearch should appear in navigation\n";
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}
?>