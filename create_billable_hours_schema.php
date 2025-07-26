<?php
/**
 * Create Billable Hours Database Schema
 * This script safely adds the billable hours fields to the tasks table
 */

// SuiteCRM Bootstrap
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

echo "<h2>Billable Hours Database Schema Creation</h2>\n";

try {
    $db = DBManagerFactory::getInstance();
    
    // Check if we're connected
    if (!$db) {
        throw new Exception("Could not connect to database");
    }
    
    echo "<p>✓ Database connection established</p>\n";
    
    // Check existing table structure
    echo "<h3>Current Tasks Table Structure Check</h3>\n";
    
    $columns = $db->get_columns('tasks');
    $billableFields = [
        'billable_duration' => 'DECIMAL(5,2) DEFAULT 0.00',
        'billable_rate' => 'DECIMAL(8,2) DEFAULT 0.00', 
        'billable_amount' => 'DECIMAL(10,2) DEFAULT 0.00',
        'activity_type' => 'VARCHAR(50) DEFAULT NULL',
        'is_billable' => 'TINYINT(1) DEFAULT 0',
        'billable_entry_date' => 'DATE DEFAULT NULL',
        'billable_entry_time' => 'TIME DEFAULT NULL'
    ];
    
    $fieldsToAdd = [];
    
    foreach ($billableFields as $fieldName => $fieldDef) {
        if (isset($columns[$fieldName])) {
            echo "<li style='color: green;'>✓ Field '$fieldName' already exists</li>\n";
        } else {
            echo "<li style='color: orange;'>+ Field '$fieldName' needs to be added</li>\n";
            $fieldsToAdd[$fieldName] = $fieldDef;
        }
    }
    
    if (empty($fieldsToAdd)) {
        echo "<p style='color: green;'><strong>✓ All billable hours fields already exist in tasks table!</strong></p>\n";
    } else {
        echo "<h3>Adding Missing Fields</h3>\n";
        
        foreach ($fieldsToAdd as $fieldName => $fieldDef) {
            try {
                $sql = "ALTER TABLE tasks ADD COLUMN $fieldName $fieldDef";
                echo "<p>Executing: $sql</p>\n";
                
                $db->query($sql);
                echo "<p style='color: green;'>✓ Successfully added field '$fieldName'</p>\n";
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>✗ Error adding field '$fieldName': " . $e->getMessage() . "</p>\n";
            }
        }
    }
    
    echo "<h3>Creating Indexes for Performance</h3>\n";
    
    $indexes = [
        'idx_tasks_billable' => 'CREATE INDEX idx_tasks_billable ON tasks (is_billable, deleted)',
        'idx_tasks_billable_date' => 'CREATE INDEX idx_tasks_billable_date ON tasks (billable_entry_date, is_billable, deleted)',
        'idx_tasks_billable_user' => 'CREATE INDEX idx_tasks_billable_user ON tasks (assigned_user_id, is_billable, deleted)',
        'idx_tasks_billable_parent' => 'CREATE INDEX idx_tasks_billable_parent ON tasks (parent_id, parent_type, is_billable, deleted)',
        'idx_tasks_activity_type' => 'CREATE INDEX idx_tasks_activity_type ON tasks (activity_type, is_billable, deleted)'
    ];
    
    foreach ($indexes as $indexName => $indexSql) {
        try {
            // Check if index exists
            $checkSql = "SHOW INDEX FROM tasks WHERE Key_name = '$indexName'";
            $result = $db->query($checkSql);
            
            if ($db->getRowCount($result) > 0) {
                echo "<p style='color: green;'>✓ Index '$indexName' already exists</p>\n";
            } else {
                echo "<p>Creating index: $indexSql</p>\n";
                $db->query($indexSql);
                echo "<p style='color: green;'>✓ Successfully created index '$indexName'</p>\n";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: orange;'>Note: Index '$indexName' - " . $e->getMessage() . "</p>\n";
        }
    }
    
    echo "<h3>Creating Sample Data for Testing</h3>\n";
    
    // Check if we have any billable entries
    $query = "SELECT COUNT(*) as billable_count FROM tasks WHERE is_billable = 1 AND deleted = 0";
    $result = $db->query($query);
    $row = $db->fetchByAssoc($result);
    $billableCount = $row['billable_count'] ?? 0;
    
    if ($billableCount == 0) {
        echo "<p>No billable hours entries found. Creating sample data...</p>\n";
        
        // Get current user or admin user
        global $current_user;
        if (empty($current_user->id)) {
            $user = BeanFactory::newBean('Users');
            $users = $user->get_list('', "is_admin = 1 AND deleted = 0", 0, 1);
            if (!empty($users['list'][0])) {
                $current_user = $users['list'][0];
            }
        }
        
        if (!empty($current_user->id)) {
            $sampleEntries = [
                [
                    'name' => 'Client Consultation - PDF Test Entry 1',
                    'description' => 'Initial client consultation regarding criminal defense case. Discussed charges, potential defenses, and legal strategy.',
                    'activity_type' => 'client_meeting',
                    'duration' => 1.5,
                    'rate' => 300.00
                ],
                [
                    'name' => 'Case Research - PDF Test Entry 2', 
                    'description' => 'Legal research on relevant case precedents and statute analysis for defense strategy.',
                    'activity_type' => 'case_research',
                    'duration' => 3.0,
                    'rate' => 250.00
                ],
                [
                    'name' => 'Court Appearance - PDF Test Entry 3',
                    'description' => 'Appeared in court for arraignment hearing. Entered not guilty plea and discussed bail conditions.',
                    'activity_type' => 'court_appearance', 
                    'duration' => 2.0,
                    'rate' => 350.00
                ]
            ];
            
            foreach ($sampleEntries as $i => $entry) {
                try {
                    $task = BeanFactory::newBean('Tasks');
                    $task->name = $entry['name'];
                    $task->description = $entry['description'];
                    $task->status = 'Completed';
                    $task->assigned_user_id = $current_user->id;
                    $task->date_start = date('Y-m-d H:i:s', strtotime("-" . ($i + 1) . " days"));
                    $task->date_due = $task->date_start;
                    
                    // Set billable fields
                    $task->is_billable = 1;
                    $task->billable_duration = $entry['duration'];
                    $task->billable_rate = $entry['rate'];
                    $task->billable_amount = $entry['duration'] * $entry['rate'];
                    $task->activity_type = $entry['activity_type'];
                    $task->billable_entry_date = date('Y-m-d', strtotime("-" . ($i + 1) . " days"));
                    $task->billable_entry_time = date('H:i:s', strtotime("09:00:00"));
                    
                    $task->save();
                    
                    if ($task->id) {
                        echo "<p style='color: green;'>✓ Created sample entry: {$entry['name']} (ID: {$task->id})</p>\n";
                    } else {
                        echo "<p style='color: red;'>✗ Failed to create sample entry: {$entry['name']}</p>\n";
                    }
                    
                } catch (Exception $e) {
                    echo "<p style='color: red;'>✗ Error creating sample entry: " . $e->getMessage() . "</p>\n";
                }
            }
        } else {
            echo "<p style='color: orange;'>No user found to create sample data</p>\n";
        }
    } else {
        echo "<p style='color: green;'>✓ Found $billableCount existing billable hours entries</p>\n";
    }
    
    echo "<h3>✅ Database Schema Creation Complete!</h3>\n";
    echo "<p><strong>Next steps:</strong></p>\n";
    echo "<ul>\n";
    echo "<li>Go to SuiteCRM Admin → Repair → Quick Repair and Rebuild</li>\n";
    echo "<li>Clear browser cache</li>\n";
    echo "<li>Add the Billable Hours Quick Entry dashlet to your home page</li>\n";
    echo "<li>Test the PDF export functionality</li>\n";
    echo "</ul>\n";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Error</h3>\n";
    echo "<p style='color: red;'>Database schema creation failed: " . $e->getMessage() . "</p>\n";
    echo "<p>Please check your database connection and permissions.</p>\n";
}
?>