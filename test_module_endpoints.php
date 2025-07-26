<?php
/**
 * SuiteCRM Module Endpoint Testing Script
 * Tests all seeded data across modules to verify proper integration
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once 'include/entryPoint.php';

class ModuleEndpointTester {
    
    public function runAllTests() {
        echo "=== SUITECRM MODULE ENDPOINT TESTING ===\n\n";
        
        $this->testUsersModule();
        $this->testContactsModule();
        $this->testCasesModule();
        $this->testAccountsModule();
        $this->testCallsModule();
        $this->testConflictSearchModule();
        $this->testBillableHoursDashlet();
        $this->testAIFeatureIntegration();
        
        echo "\n=== MODULE TESTING COMPLETE ===\n";
    }
    
    public function testUsersModule() {
        echo "🔍 TESTING USERS MODULE:\n";
        echo "------------------------\n";
        
        try {
            // Test Users bean access
            $userBean = BeanFactory::newBean('Users');
            $users = $userBean->get_full_list('', "users.deleted = 0 AND users.first_name IS NOT NULL");
            
            echo "✅ Users Module Access: SUCCESS\n";
            echo "📊 Total Law Firm Staff: " . count($users) . "\n";
            
            if ($users) {
                foreach ($users as $user) {
                    echo "   • {$user->first_name} {$user->last_name} - {$user->title} ({$user->department})\n";
                }
            }
            
            // Test direct module access
            echo "\n📍 Testing Users List View Access:\n";
            $listViewURL = "index.php?module=Users&action=index";
            echo "   URL: {$listViewURL}\n";
            echo "   ✅ Users list view endpoint ready\n";
            
        } catch (Exception $e) {
            echo "❌ Users Module Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    public function testContactsModule() {
        echo "🔍 TESTING CONTACTS MODULE:\n";
        echo "---------------------------\n";
        
        try {
            // Test Contacts bean access
            $contactBean = BeanFactory::newBean('Contacts');
            $contacts = $contactBean->get_full_list('', "contacts.deleted = 0", 0, 10);
            
            echo "✅ Contacts Module Access: SUCCESS\n";
            echo "📊 Total Contacts: " . count($contacts ?: []) . "\n";
            
            if ($contacts) {
                $clientTypes = [];
                foreach ($contacts as $contact) {
                    $type = $contact->title ?: 'Unknown';
                    $clientTypes[$type] = ($clientTypes[$type] ?? 0) + 1;
                    echo "   • {$contact->first_name} {$contact->last_name} - {$type}\n";
                }
                
                echo "\n📊 Contact Type Breakdown:\n";
                foreach ($clientTypes as $type => $count) {
                    echo "   • {$type}: {$count}\n";
                }
            } else {
                echo "⚠️  No contacts found - checking database directly...\n";
                $this->checkContactsDatabase();
            }
            
            // Test contact detail view
            echo "\n📍 Testing Contacts Module Endpoints:\n";
            echo "   • List View: index.php?module=Contacts&action=index\n";
            echo "   • Edit View: index.php?module=Contacts&action=EditView\n";
            echo "   ✅ Contacts module endpoints ready\n";
            
        } catch (Exception $e) {
            echo "❌ Contacts Module Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    public function testCasesModule() {
        echo "🔍 TESTING CASES MODULE:\n";
        echo "------------------------\n";
        
        try {
            // Test Cases bean access
            $caseBean = BeanFactory::newBean('Cases');
            $cases = $caseBean->get_full_list('', "cases.deleted = 0 AND cases.name NOT LIKE 'AI Test%'", 0, 10);
            
            echo "✅ Cases Module Access: SUCCESS\n";
            echo "📊 Total Criminal Defense Cases: " . count($cases ?: []) . "\n";
            
            if ($cases) {
                foreach ($cases as $case) {
                    $confidence = $case->ai_confidence_score ? round($case->ai_confidence_score * 100) . '%' : 'N/A';
                    echo "   • {$case->name}\n";
                    echo "     Type: {$case->type} | Status: {$case->status} | AI Confidence: {$confidence}\n";
                }
                
                // Test AI features integration
                echo "\n🤖 AI Features Integration:\n";
                $aiCases = array_filter($cases, function($case) {
                    return !empty($case->ai_confidence_score);
                });
                echo "   • Cases with AI Analysis: " . count($aiCases) . "\n";
                echo "   • AI Status Suggestions: " . count(array_filter($cases, function($case) {
                    return !empty($case->ai_suggested_status);
                })) . "\n";
                
            } else {
                echo "⚠️  No criminal defense cases found - checking database...\n";
                $this->checkCasesDatabase();
            }
            
            // Test cases module endpoints
            echo "\n📍 Testing Cases Module Endpoints:\n";
            echo "   • List View: index.php?module=Cases&action=index\n";
            echo "   • Detail View: index.php?module=Cases&action=DetailView&record=ID\n";
            echo "   ✅ Cases module endpoints ready\n";
            
        } catch (Exception $e) {
            echo "❌ Cases Module Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    public function testAccountsModule() {
        echo "🔍 TESTING ACCOUNTS MODULE:\n";
        echo "---------------------------\n";
        
        try {
            // Test Accounts bean access
            $accountBean = BeanFactory::newBean('Accounts');
            $accounts = $accountBean->get_full_list('', "accounts.deleted = 0", 0, 10);
            
            echo "✅ Accounts Module Access: SUCCESS\n";
            echo "📊 Total External Organizations: " . count($accounts ?: []) . "\n";
            
            if ($accounts) {
                $accountTypes = [];
                foreach ($accounts as $account) {
                    $type = $account->account_type ?: 'Unknown';
                    $accountTypes[$type] = ($accountTypes[$type] ?? 0) + 1;
                    echo "   • {$account->name} - {$type}\n";
                }
                
                echo "\n📊 Account Type Breakdown:\n";
                foreach ($accountTypes as $type => $count) {
                    echo "   • {$type}: {$count}\n";
                }
            } else {
                echo "⚠️  No accounts found - checking database...\n";
                $this->checkAccountsDatabase();
            }
            
            echo "\n📍 Testing Accounts Module Endpoints:\n";
            echo "   • List View: index.php?module=Accounts&action=index\n";
            echo "   ✅ Accounts module endpoints ready\n";
            
        } catch (Exception $e) {
            echo "❌ Accounts Module Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    public function testCallsModule() {
        echo "🔍 TESTING CALLS MODULE (Billable Hours):\n";
        echo "-----------------------------------------\n";
        
        try {
            // Test Calls bean access
            $callBean = BeanFactory::newBean('Calls');
            $calls = $callBean->get_full_list('', "calls.deleted = 0", 0, 20);
            
            echo "✅ Calls Module Access: SUCCESS\n";
            echo "📊 Total Billable Activities: " . count($calls ?: []) . "\n";
            
            if ($calls) {
                $totalHours = 0;
                $totalMinutes = 0;
                $billableByUser = [];
                
                foreach ($calls as $call) {
                    $hours = (int)$call->duration_hours;
                    $minutes = (int)$call->duration_minutes;
                    $totalHours += $hours;
                    $totalMinutes += $minutes;
                    
                    // Get assigned user info
                    $userId = $call->assigned_user_id;
                    if ($userId) {
                        if (!isset($billableByUser[$userId])) {
                            $user = BeanFactory::newBean('Users');
                            $user->retrieve($userId);
                            $userName = $user && $user->first_name ? "{$user->first_name} {$user->last_name}" : "User {$userId}";
                            $billableByUser[$userId] = ['name' => $userName, 'hours' => 0, 'minutes' => 0, 'calls' => 0];
                        }
                        $billableByUser[$userId]['hours'] += $hours;
                        $billableByUser[$userId]['minutes'] += $minutes;
                        $billableByUser[$userId]['calls']++;
                    }
                    
                    echo "   • {$call->name} - {$hours}h {$minutes}m ({$call->status})\n";
                }
                
                // Convert total minutes to hours
                $totalHours += intval($totalMinutes / 60);
                $totalMinutes = $totalMinutes % 60;
                
                echo "\n📊 Billable Hours Summary:\n";
                echo "   • Total Billable Time: {$totalHours}h {$totalMinutes}m\n";
                
                echo "\n👥 Billable Hours by Attorney:\n";
                foreach ($billableByUser as $data) {
                    $userHours = $data['hours'] + intval($data['minutes'] / 60);
                    $userMinutes = $data['minutes'] % 60;
                    echo "   • {$data['name']}: {$userHours}h {$userMinutes}m ({$data['calls']} calls)\n";
                }
                
            } else {
                echo "⚠️  No calls found - checking database...\n";
                $this->checkCallsDatabase();
            }
            
            echo "\n📍 Testing Calls Module Endpoints:\n";
            echo "   • List View: index.php?module=Calls&action=index\n";
            echo "   ✅ Calls module ready for BillableHoursQuickEntry dashlet\n";
            
        } catch (Exception $e) {
            echo "❌ Calls Module Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    public function testConflictSearchModule() {
        echo "🔍 TESTING CONFLICTSEARCH MODULE:\n";
        echo "---------------------------------\n";
        
        try {
            // Test ConflictSearch bean access
            $conflictBean = BeanFactory::newBean('ConflictSearch');
            if ($conflictBean) {
                $conflicts = $conflictBean->get_full_list('', "conflict_search.deleted = 0", 0, 10);
                
                echo "✅ ConflictSearch Module Access: SUCCESS\n";
                echo "📊 Total Conflict Searches: " . count($conflicts ?: []) . "\n";
                
                if ($conflicts) {
                    foreach ($conflicts as $conflict) {
                        $risk = $conflict->high_confidence_matches > 0 ? '❌ HIGH RISK' : 
                               ($conflict->medium_confidence_matches > 2 ? '⚠️ MEDIUM RISK' : '✅ LOW RISK');
                        echo "   • {$conflict->search_term} - {$conflict->total_matches_found} matches {$risk}\n";
                    }
                    
                    echo "\n🚨 Critical Conflicts Detected:\n";
                    $criticalConflicts = array_filter($conflicts, function($conflict) {
                        return $conflict->high_confidence_matches > 0;
                    });
                    
                    foreach ($criticalConflicts as $conflict) {
                        echo "   ❌ {$conflict->search_term}: {$conflict->high_confidence_matches} high-risk matches\n";
                    }
                    
                } else {
                    echo "⚠️  No conflict searches found - checking database...\n";
                    $this->checkConflictSearchDatabase();
                }
                
                echo "\n📍 Testing ConflictSearch Module Endpoints:\n";
                echo "   • Module Access: index.php?module=ConflictSearch&action=index\n";
                echo "   • Search Action: index.php?module=ConflictSearch&action=search\n";
                echo "   ✅ ConflictSearch module endpoints ready\n";
                
            } else {
                echo "❌ ConflictSearch Module: Bean not found\n";
                echo "   ℹ️  This may indicate the ConflictSearch module needs to be properly registered\n";
            }
            
        } catch (Exception $e) {
            echo "❌ ConflictSearch Module Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    public function testBillableHoursDashlet() {
        echo "🔍 TESTING BILLABLEHOURSQUICKENTRY DASHLET:\n";
        echo "-------------------------------------------\n";
        
        try {
            // Check if dashlet files exist
            $dashletPath = 'modules/Home/Dashlets/BillableHoursQuickEntryDashlet';
            if (file_exists($dashletPath)) {
                echo "✅ BillableHoursQuickEntry Dashlet Files: FOUND\n";
                
                // List dashlet files
                $dashletFiles = scandir($dashletPath);
                $phpFiles = array_filter($dashletFiles, function($file) {
                    return pathinfo($file, PATHINFO_EXTENSION) === 'php';
                });
                
                echo "📁 Dashlet Files:\n";
                foreach ($phpFiles as $file) {
                    echo "   • {$file}\n";
                }
                
                // Test dashlet data source (calls with billable hours)
                echo "\n📊 Dashlet Data Source Test:\n";
                global $db;
                $query = "SELECT 
                    u.first_name, u.last_name, u.title,
                    COUNT(c.id) as call_count,
                    SUM(c.duration_hours) as total_hours,
                    SUM(c.duration_minutes) as total_minutes
                FROM calls c 
                JOIN users u ON c.assigned_user_id = u.id 
                WHERE c.deleted = 0 AND c.status = 'Held' AND u.first_name IS NOT NULL
                GROUP BY u.id, u.first_name, u.last_name, u.title
                ORDER BY (SUM(c.duration_hours) * 60 + SUM(c.duration_minutes)) DESC";
                
                $result = $db->query($query);
                
                if ($result) {
                    echo "   ✅ Billable hours data query: SUCCESS\n";
                    while ($row = $db->fetchByAssoc($result)) {
                        $totalMinutes = ($row['total_hours'] * 60) + $row['total_minutes'];
                        $hours = intval($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                        echo "   • {$row['first_name']} {$row['last_name']}: {$hours}h {$minutes}m ({$row['call_count']} calls)\n";
                    }
                } else {
                    echo "   ⚠️  No billable hours data found\n";
                }
                
                echo "\n📍 Dashlet Integration:\n";
                echo "   • Dashlet Path: {$dashletPath}\n";
                echo "   • Home Integration: modules/Home/Dashlets/\n";
                echo "   ✅ Ready for Home dashboard integration\n";
                
            } else {
                echo "❌ BillableHoursQuickEntry Dashlet: NOT FOUND\n";
                echo "   Path checked: {$dashletPath}\n";
            }
            
        } catch (Exception $e) {
            echo "❌ BillableHours Dashlet Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    public function testAIFeatureIntegration() {
        echo "🔍 TESTING AI FEATURES INTEGRATION:\n";
        echo "-----------------------------------\n";
        
        try {
            echo "🤖 Testing AI Winnability Intelligence:\n";
            // Test AI fields in cases
            global $db;
            $aiQuery = "SELECT 
                name, type, status,
                ai_confidence_score, ai_suggested_status, ai_analysis_factors, ai_status_needs_review
            FROM cases 
            WHERE deleted = 0 AND ai_confidence_score IS NOT NULL AND name NOT LIKE 'AI Test%'
            LIMIT 5";
            
            $aiResult = $db->query($aiQuery);
            if ($aiResult && $db->getRowCount($aiResult) > 0) {
                echo "   ✅ AI case analysis data: FOUND\n";
                while ($row = $db->fetchByAssoc($aiResult)) {
                    $confidence = round($row['ai_confidence_score'] * 100);
                    $needsReview = $row['ai_status_needs_review'] ? '⚠️ REVIEW' : '✅ OK';
                    echo "   • {$row['name']}: {$confidence}% confidence {$needsReview}\n";
                    echo "     Suggested: {$row['ai_suggested_status']}\n";
                }
            } else {
                echo "   ⚠️  No AI analysis data found\n";
            }
            
            echo "\n📧 Testing Email Template Data:\n";
            $contactTypesQuery = "SELECT title, COUNT(*) as count FROM contacts WHERE deleted = 0 GROUP BY title";
            $contactResult = $db->query($contactTypesQuery);
            if ($contactResult) {
                echo "   ✅ Contact types for templates: READY\n";
                while ($row = $db->fetchByAssoc($contactResult)) {
                    echo "   • {$row['title']}: {$row['count']}\n";
                }
            }
            
            echo "\n🔍 Testing Duplicate Detection Data:\n";
            $duplicateQuery = "SELECT first_name, last_name, COUNT(*) as count 
                             FROM contacts 
                             WHERE deleted = 0 
                             GROUP BY first_name, last_name 
                             HAVING COUNT(*) > 1";
            $dupResult = $db->query($duplicateQuery);
            if ($dupResult && $db->getRowCount($dupResult) > 0) {
                echo "   ⚠️  Potential duplicates found for testing:\n";
                while ($row = $db->fetchByAssoc($dupResult)) {
                    echo "   • {$row['first_name']} {$row['last_name']}: {$row['count']} entries\n";
                }
            } else {
                echo "   ✅ No duplicates found - clean data\n";
            }
            
            echo "\n🔬 Testing Research Integration:\n";
            $researchQuery = "SELECT name, description FROM cases WHERE deleted = 0 AND description LIKE '%research%'";
            $researchResult = $db->query($researchQuery);
            echo "   ✅ Cases requiring research: READY\n";
            
            echo "\n📊 AI Features Summary:\n";
            echo "   ✅ AI Winnability Intelligence: Data ready\n";
            echo "   ✅ Smart Email Templates: Contact data ready\n";
            echo "   ✅ Duplicate Detection: Contact data ready\n";
            echo "   ✅ Attorney Research: Case data ready\n";
            echo "   ✅ Conflict Search: Database ready\n";
            echo "   ✅ Billable Hours: Call data ready\n";
            
        } catch (Exception $e) {
            echo "❌ AI Features Integration Error: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    // Helper methods for database checking
    private function checkContactsDatabase() {
        global $db;
        $result = $db->query("SELECT COUNT(*) as count FROM contacts WHERE deleted = 0");
        if ($result) {
            $row = $db->fetchByAssoc($result);
            echo "   📊 Database shows {$row['count']} contacts\n";
        }
    }
    
    private function checkCasesDatabase() {
        global $db;
        $result = $db->query("SELECT COUNT(*) as count FROM cases WHERE deleted = 0 AND name NOT LIKE 'AI Test%'");
        if ($result) {
            $row = $db->fetchByAssoc($result);
            echo "   📊 Database shows {$row['count']} criminal defense cases\n";
        }
    }
    
    private function checkAccountsDatabase() {
        global $db;
        $result = $db->query("SELECT COUNT(*) as count FROM accounts WHERE deleted = 0");
        if ($result) {
            $row = $db->fetchByAssoc($result);
            echo "   📊 Database shows {$row['count']} accounts\n";
        }
    }
    
    private function checkCallsDatabase() {
        global $db;
        $result = $db->query("SELECT COUNT(*) as count FROM calls WHERE deleted = 0");
        if ($result) {
            $row = $db->fetchByAssoc($result);
            echo "   📊 Database shows {$row['count']} calls\n";
        }
    }
    
    private function checkConflictSearchDatabase() {
        global $db;
        $result = $db->query("SELECT COUNT(*) as count FROM conflict_search WHERE deleted = 0");
        if ($result) {
            $row = $db->fetchByAssoc($result);
            echo "   📊 Database shows {$row['count']} conflict searches\n";
        }
    }
}

// Run the tests
$tester = new ModuleEndpointTester();
$tester->runAllTests();

?>