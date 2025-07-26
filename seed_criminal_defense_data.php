<?php
/**
 * Criminal Defense Attorney CRM - Mock Data Seeder
 * 
 * This script creates comprehensive mock data for a criminal defense law firm
 * including users, clients, cases, accounts, and activities that support
 * the 6 AI features outlined in featureplan/final6features
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once 'include/entryPoint.php';
require_once 'include/utils.php';
require_once 'modules/Users/User.php';
require_once 'modules/Contacts/Contact.php';
require_once 'modules/Cases/Case.php';
require_once 'modules/Accounts/Account.php';
require_once 'modules/Calls/Call.php';
require_once 'modules/Meetings/Meeting.php';
require_once 'modules/Tasks/Task.php';

class CriminalDefenseDataSeeder {
    
    private $created_users = [];
    private $created_contacts = [];
    private $created_accounts = [];
    private $created_cases = [];
    
    // Criminal Defense Case Types for realistic data
    private $case_types = [
        'DUI/DWI',
        'Drug Possession',
        'Drug Trafficking',
        'Assault',
        'Battery',
        'Domestic Violence',
        'Theft',
        'Burglary',
        'Robbery',
        'White Collar Crime',
        'Fraud',
        'Embezzlement',
        'Traffic Violation',
        'Weapons Charge',
        'Homicide',
        'Sexual Assault',
        'Cybercrime',
        'Money Laundering',
        'Tax Evasion',
        'Conspiracy'
    ];
    
    private $case_statuses = [
        'New',
        'Open_Assigned',
        'Investigation',
        'Pre_Trial',
        'Trial_Pending',
        'In_Trial',
        'Plea_Negotiation',
        'Sentencing',
        'Appeal',
        'Closed_Won',
        'Closed_Lost',
        'Dismissed'
    ];
    
    private $priorities = ['High', 'Medium', 'Low'];
    
    public function run() {
        echo "Starting Criminal Defense CRM Data Seeding...\n\n";
        
        $this->createUsers();
        $this->createAccounts();
        $this->createContacts();
        $this->createCases();
        $this->createActivities();
        $this->createConflictSearchData();
        
        echo "\n=== DATA SEEDING COMPLETE ===\n";
        echo "Created:\n";
        echo "- " . count($this->created_users) . " Users\n";
        echo "- " . count($this->created_accounts) . " Accounts\n";
        echo "- " . count($this->created_contacts) . " Contacts\n";
        echo "- " . count($this->created_cases) . " Cases\n";
        echo "\nYour criminal defense CRM is now populated with realistic mock data!\n";
    }
    
    private function createUsers() {
        echo "Creating Users (Attorneys, Paralegals, Staff)...\n";
        
        $users_data = [
            // Senior Partners
            ['Sarah', 'Mitchell', 'sarah.mitchell', 'Senior Partner', 'Criminal Defense', true, false],
            ['Robert', 'Chen', 'robert.chen', 'Senior Partner', 'White Collar Defense', true, false],
            ['Maria', 'Rodriguez', 'maria.rodriguez', 'Managing Partner', 'Criminal Defense', true, false],
            
            // Associates
            ['David', 'Thompson', 'david.thompson', 'Associate Attorney', 'DUI/Traffic Defense', false, false],
            ['Jennifer', 'Williams', 'jennifer.williams', 'Associate Attorney', 'Drug Crimes Defense', false, false],
            ['Michael', 'Johnson', 'michael.johnson', 'Associate Attorney', 'Assault/Battery Defense', false, false],
            ['Lisa', 'Anderson', 'lisa.anderson', 'Associate Attorney', 'Domestic Violence Defense', false, false],
            ['James', 'Brown', 'james.brown', 'Associate Attorney', 'Theft/Burglary Defense', false, false],
            ['Ashley', 'Davis', 'ashley.davis', 'Associate Attorney', 'Fraud Defense', false, false],
            
            // Junior Associates
            ['Christopher', 'Miller', 'christopher.miller', 'Junior Associate', 'General Criminal Defense', false, false],
            ['Amanda', 'Wilson', 'amanda.wilson', 'Junior Associate', 'Appeals', false, false],
            ['Daniel', 'Moore', 'daniel.moore', 'Junior Associate', 'Pre-Trial Motions', false, false],
            
            // Paralegals
            ['Michelle', 'Taylor', 'michelle.taylor', 'Senior Paralegal', 'Case Management', false, false],
            ['Steven', 'Jackson', 'steven.jackson', 'Paralegal', 'Discovery Support', false, false],
            ['Rebecca', 'White', 'rebecca.white', 'Paralegal', 'Client Intake', false, false],
            ['Kevin', 'Harris', 'kevin.harris', 'Paralegal', 'Research Assistant', false, false],
            ['Nicole', 'Martin', 'nicole.martin', 'Paralegal', 'Document Preparation', false, false],
            
            // Administrative Staff
            ['Patricia', 'Garcia', 'patricia.garcia', 'Office Manager', 'Administration', false, false],
            ['Thomas', 'Martinez', 'thomas.martinez', 'Legal Secretary', 'Document Management', false, false],
            ['Karen', 'Robinson', 'karen.robinson', 'Billing Coordinator', 'Financial Management', false, false],
            ['Mark', 'Clark', 'mark.clark', 'IT Coordinator', 'Technology Support', false, false],
            
            // Support Staff
            ['Linda', 'Lewis', 'linda.lewis', 'Receptionist', 'Client Services', false, false],
            ['Paul', 'Walker', 'paul.walker', 'Investigator', 'Case Investigation', false, false],
            ['Donna', 'Hall', 'donna.hall', 'Records Clerk', 'File Management', false, false],
            ['Gary', 'Allen', 'gary.allen', 'Process Server', 'Legal Service', false, false],
            
            // System Admin
            ['Alex', 'Cruz', 'alex.cruz', 'System Administrator', 'CRM Management', true, false]
        ];
        
        foreach ($users_data as $user_data) {
            $user = $this->createUser($user_data[0], $user_data[1], $user_data[2], $user_data[3], $user_data[4], $user_data[5], $user_data[6]);
            if ($user) {
                $this->created_users[] = $user;
                echo "  ✓ Created user: {$user_data[0]} {$user_data[1]} ({$user_data[3]})\n";
            }
        }
    }
    
    private function createUser($first_name, $last_name, $username, $title, $department, $is_admin, $portal_only) {
        $user = new User();
        $user->id = create_guid();
        $user->user_name = $username;
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->title = $title;
        $user->department = $department;
        $user->is_admin = $is_admin ? 1 : 0;
        $user->portal_only = $portal_only ? 1 : 0;
        $user->status = 'Active';
        $user->employee_status = 'Active';
        $user->sugar_login = 1;
        $user->system_generated_password = 0;
        $user->receive_notifications = 1;
        $user->show_on_employees = 1;
        $user->date_entered = date('Y-m-d H:i:s');
        $user->date_modified = date('Y-m-d H:i:s');
        $user->created_by = '1'; // Admin user
        $user->modified_user_id = '1';
        $user->deleted = 0;
        
        // Set a default password (should be changed in production)
        $user->user_hash = $user->encrypt_password('password123');
        
        // Add some realistic contact info
        $user->phone_work = $this->generatePhoneNumber();
        $user->address_street = $this->generateAddress();
        $user->address_city = 'Los Angeles';
        $user->address_state = 'CA';
        $user->address_country = 'USA';
        $user->address_postalcode = '90210';
        
        return $user->save() ? $user : null;
    }
    
    private function createAccounts() {
        echo "\nCreating Accounts (Courts, Opposing Counsel, Expert Witnesses)...\n";
        
        $accounts_data = [
            // Courts
            ['Los Angeles County Superior Court', 'Court', 'Government'],
            ['Beverly Hills Municipal Court', 'Court', 'Government'],
            ['Santa Monica Courthouse', 'Court', 'Government'],
            ['Van Nuys Courthouse East', 'Court', 'Government'],
            ['Torrance Courthouse', 'Court', 'Government'],
            
            // District Attorney Offices
            ['Los Angeles County District Attorney', 'Prosecutor Office', 'Government'],
            ['Beverly Hills City Attorney', 'Prosecutor Office', 'Government'],
            ['Santa Monica City Attorney', 'Prosecutor Office', 'Government'],
            
            // Law Firms (Opposing Counsel)
            ['Morrison & Associates', 'Law Firm', 'Legal Services'],
            ['Pacific Legal Group', 'Law Firm', 'Legal Services'],
            ['Westside Criminal Defense', 'Law Firm', 'Legal Services'],
            ['Downtown Legal Partners', 'Law Firm', 'Legal Services'],
            
            // Expert Witness Services
            ['Forensic Analysis Associates', 'Expert Witness', 'Professional Services'],
            ['Medical Expert Consultants', 'Expert Witness', 'Professional Services'],
            ['Financial Investigation Services', 'Expert Witness', 'Professional Services'],
            ['Psychological Evaluation Center', 'Expert Witness', 'Professional Services'],
            
            // Bail Bond Companies
            ['Liberty Bail Bonds', 'Bail Bonds', 'Financial Services'],
            ['Quick Release Bail', 'Bail Bonds', 'Financial Services'],
            
            // Investigation Services
            ['Elite Private Investigations', 'Investigation', 'Professional Services'],
            ['West Coast Investigators', 'Investigation', 'Professional Services']
        ];
        
        foreach ($accounts_data as $account_data) {
            $account = $this->createAccount($account_data[0], $account_data[1], $account_data[2]);
            if ($account) {
                $this->created_accounts[] = $account;
                echo "  ✓ Created account: {$account_data[0]} ({$account_data[1]})\n";
            }
        }
    }
    
    private function createAccount($name, $account_type, $industry) {
        $account = new Account();
        $account->id = create_guid();
        $account->name = $name;
        $account->account_type = $account_type;
        $account->industry = $industry;
        $account->date_entered = date('Y-m-d H:i:s');
        $account->date_modified = date('Y-m-d H:i:s');
        $account->created_by = '1';
        $account->modified_user_id = '1';
        $account->deleted = 0;
        $account->assigned_user_id = $this->getRandomUserId();
        
        // Add contact info
        $account->phone_office = $this->generatePhoneNumber();
        $account->billing_address_street = $this->generateAddress();
        $account->billing_address_city = 'Los Angeles';
        $account->billing_address_state = 'CA';
        $account->billing_address_country = 'USA';
        $account->billing_address_postalcode = '90210';
        
        return $account->save() ? $account : null;
    }
    
    private function createContacts() {
        echo "\nCreating Contacts (Clients, Prosecutors, Judges, Witnesses)...\n";
        
        // Clients (Criminal Defendants)
        $client_names = [
            ['John', 'Smith', 'Client'],
            ['Maria', 'Gonzalez', 'Client'],
            ['Robert', 'Johnson', 'Client'],
            ['Jennifer', 'Williams', 'Client'],
            ['Michael', 'Brown', 'Client'],
            ['Lisa', 'Davis', 'Client'],
            ['David', 'Miller', 'Client'],
            ['Sarah', 'Wilson', 'Client'],
            ['James', 'Moore', 'Client'],
            ['Ashley', 'Taylor', 'Client'],
            ['Christopher', 'Anderson', 'Client'],
            ['Amanda', 'Thomas', 'Client'],
            ['Daniel', 'Jackson', 'Client'],
            ['Michelle', 'White', 'Client'],
            ['Steven', 'Harris', 'Client'],
            ['Rebecca', 'Martin', 'Client'],
            ['Kevin', 'Thompson', 'Client'],
            ['Nicole', 'Garcia', 'Client'],
            ['Thomas', 'Martinez', 'Client'],
            ['Karen', 'Robinson', 'Client']
        ];
        
        // Prosecutors
        $prosecutor_names = [
            ['Patricia', 'Clark', 'Prosecutor'],
            ['Mark', 'Stevens', 'Prosecutor'],
            ['Linda', 'Rodriguez', 'Prosecutor'],
            ['Paul', 'Mitchell', 'Prosecutor'],
            ['Donna', 'Chen', 'Prosecutor']
        ];
        
        // Judges
        $judge_names = [
            ['Hon. William', 'Foster', 'Judge'],
            ['Hon. Margaret', 'Barnes', 'Judge'],
            ['Hon. Richard', 'Coleman', 'Judge'],
            ['Hon. Sandra', 'Torres', 'Judge']
        ];
        
        // Expert Witnesses
        $expert_names = [
            ['Dr. Alan', 'Peterson', 'Expert Witness'],
            ['Dr. Nancy', 'Cooper', 'Expert Witness'],
            ['Dr. Frank', 'Lewis', 'Expert Witness']
        ];
        
        $all_contacts = array_merge($client_names, $prosecutor_names, $judge_names, $expert_names);
        
        foreach ($all_contacts as $contact_data) {
            $contact = $this->createContact($contact_data[0], $contact_data[1], $contact_data[2]);
            if ($contact) {
                $this->created_contacts[] = $contact;
                echo "  ✓ Created contact: {$contact_data[0]} {$contact_data[1]} ({$contact_data[2]})\n";
            }
        }
    }
    
    private function createContact($first_name, $last_name, $contact_type) {
        $contact = new Contact();
        $contact->id = create_guid();
        $contact->first_name = $first_name;
        $contact->last_name = $last_name;
        $contact->title = $contact_type;
        $contact->date_entered = date('Y-m-d H:i:s');
        $contact->date_modified = date('Y-m-d H:i:s');
        $contact->created_by = '1';
        $contact->modified_user_id = '1';
        $contact->deleted = 0;
        $contact->assigned_user_id = $this->getRandomUserId();
        
        // Add contact-specific info
        $contact->phone_mobile = $this->generatePhoneNumber();
        $contact->phone_home = $this->generatePhoneNumber();
        $contact->email1 = strtolower($first_name . '.' . $last_name . '@email.com');
        
        $contact->primary_address_street = $this->generateAddress();
        $contact->primary_address_city = 'Los Angeles';
        $contact->primary_address_state = 'CA';
        $contact->primary_address_country = 'USA';
        $contact->primary_address_postalcode = '90210';
        
        // Link to appropriate account
        if ($contact_type === 'Client') {
            // Don't link clients to accounts necessarily
        } else {
            $contact->account_id = $this->getRandomAccountId();
        }
        
        return $contact->save() ? $contact : null;
    }
    
    private function createCases() {
        echo "\nCreating Criminal Defense Cases...\n";
        
        $case_scenarios = [
            ['DUI - First Offense', 'DUI/DWI', 'Client charged with first-time DUI after traffic stop', 'Medium', 'Open_Assigned'],
            ['Felony Drug Possession', 'Drug Possession', 'Client arrested with substantial amount of controlled substances', 'High', 'Investigation'],
            ['Domestic Violence Allegations', 'Domestic Violence', 'Client facing domestic violence charges from ex-spouse', 'High', 'Pre_Trial'],
            ['Armed Robbery Case', 'Robbery', 'Client accused of armed robbery at convenience store', 'High', 'Plea_Negotiation'],
            ['White Collar Fraud', 'Fraud', 'Embezzlement charges related to client\'s former employment', 'High', 'Investigation'],
            ['Simple Assault Charge', 'Assault', 'Bar fight resulting in assault charges', 'Medium', 'Open_Assigned'],
            ['Burglary - Residential', 'Burglary', 'Client charged with breaking and entering residential property', 'Medium', 'Trial_Pending'],
            ['Drug Trafficking Case', 'Drug Trafficking', 'Large-scale drug distribution allegations', 'High', 'Pre_Trial'],
            ['DUI - Multiple Offense', 'DUI/DWI', 'Third DUI offense with enhanced penalties', 'High', 'Plea_Negotiation'],
            ['Identity Theft', 'Fraud', 'Credit card fraud and identity theft charges', 'Medium', 'Investigation'],
            ['Weapons Violation', 'Weapons Charge', 'Illegal firearm possession charges', 'Medium', 'Open_Assigned'],
            ['Cybercrime Case', 'Cybercrime', 'Computer fraud and hacking allegations', 'High', 'Investigation'],
            ['Traffic Violation Appeal', 'Traffic Violation', 'Reckless driving with license suspension', 'Low', 'Appeal'],
            ['Battery on Officer', 'Battery', 'Alleged battery on police officer during arrest', 'High', 'Pre_Trial'],
            ['Financial Fraud', 'White Collar Crime', 'Investment fraud allegations', 'High', 'Investigation'],
            ['Shoplifting Case', 'Theft', 'Retail theft charges with prior convictions', 'Low', 'Plea_Negotiation'],
            ['Money Laundering', 'Money Laundering', 'Complex money laundering scheme', 'High', 'Investigation'],
            ['Sexual Assault Allegations', 'Sexual Assault', 'Serious sexual assault charges', 'High', 'Pre_Trial'],
            ['Tax Evasion Case', 'Tax Evasion', 'Federal tax evasion charges', 'High', 'Investigation'],
            ['Conspiracy Charges', 'Conspiracy', 'RICO conspiracy allegations', 'High', 'Pre_Trial'],
            ['Juvenile DUI', 'DUI/DWI', 'Underage DUI with accident involvement', 'Medium', 'Open_Assigned'],
            ['Elder Abuse', 'Battery', 'Financial and physical elder abuse charges', 'High', 'Investigation'],
            ['Drug Court Case', 'Drug Possession', 'Drug possession with treatment court option', 'Medium', 'Pre_Trial'],
            ['Expungement Request', 'Traffic Violation', 'Motion to expunge prior conviction', 'Low', 'New'],
            ['Parole Violation', 'Battery', 'Alleged parole violation for new charges', 'Medium', 'Open_Assigned']
        ];
        
        $client_contacts = array_filter($this->created_contacts, function($contact) {
            return $contact->title === 'Client';
        });
        
        foreach ($case_scenarios as $index => $case_data) {
            // Assign to a random client
            $client = $client_contacts[array_rand($client_contacts)];
            
            $case = $this->createCase(
                $case_data[0], 
                $case_data[1], 
                $case_data[2], 
                $case_data[3], 
                $case_data[4],
                $client->id
            );
            
            if ($case) {
                $this->created_cases[] = $case;
                echo "  ✓ Created case: {$case_data[0]} ({$case_data[1]})\n";
            }
        }
    }
    
    private function createCase($name, $type, $description, $priority, $status, $contact_id = null) {
        $case = new aCase();  // Note: Case is a reserved word in PHP
        $case->id = create_guid();
        $case->name = $name;
        $case->type = $type;
        $case->description = $description;
        $case->priority = $priority;
        $case->status = $status;
        $case->state = $status === 'Closed_Won' || $status === 'Closed_Lost' || $status === 'Dismissed' ? 'Closed' : 'Open';
        $case->date_entered = date('Y-m-d H:i:s', strtotime('-' . rand(1, 180) . ' days'));
        $case->date_modified = date('Y-m-d H:i:s');
        $case->created_by = '1';
        $case->modified_user_id = '1';
        $case->deleted = 0;
        $case->assigned_user_id = $this->getRandomUserId();
        
        // Add AI-related fields for winnability analysis
        $case->ai_confidence_score = rand(30, 95) / 100.0;
        $case->ai_suggested_status = $this->getAISuggestedStatus($type, $priority);
        $case->ai_last_analysis = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
        $case->ai_analysis_factors = $this->generateAIFactors($type);
        $case->ai_status_needs_review = rand(0, 1);
        
        return $case->save() ? $case : null;
    }
    
    private function createActivities() {
        echo "\nCreating Activities (Calls, Meetings, Tasks, Billable Hours)...\n";
        
        // Create some sample activities for demonstration
        $activity_count = 0;
        
        foreach (array_slice($this->created_cases, 0, 10) as $case) {
            // Create client consultation call
            $this->createCall("Client Consultation - " . $case->name, $case->id, $case->assigned_user_id);
            
            // Create court hearing meeting
            $this->createMeeting("Court Hearing - " . $case->name, $case->id, $case->assigned_user_id);
            
            // Create research task
            $this->createTask("Legal Research - " . $case->type, $case->id, $case->assigned_user_id);
            
            $activity_count += 3;
        }
        
        echo "  ✓ Created {$activity_count} activities (calls, meetings, tasks)\n";
    }
    
    private function createCall($name, $case_id, $assigned_user_id) {
        $call = new Call();
        $call->id = create_guid();
        $call->name = $name;
        $call->status = 'Held';
        $call->date_start = date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'));
        $call->duration_hours = 1;
        $call->duration_minutes = rand(15, 60);
        $call->date_entered = date('Y-m-d H:i:s');
        $call->date_modified = date('Y-m-d H:i:s');
        $call->created_by = '1';
        $call->modified_user_id = '1';
        $call->assigned_user_id = $assigned_user_id;
        $call->deleted = 0;
        
        return $call->save();
    }
    
    private function createMeeting($name, $case_id, $assigned_user_id) {
        $meeting = new Meeting();
        $meeting->id = create_guid();
        $meeting->name = $name;
        $meeting->status = 'Held';
        $meeting->date_start = date('Y-m-d H:i:s', strtotime('+' . rand(1, 30) . ' days'));
        $meeting->duration_hours = 2;
        $meeting->duration_minutes = 0;
        $meeting->date_entered = date('Y-m-d H:i:s');
        $meeting->date_modified = date('Y-m-d H:i:s');
        $meeting->created_by = '1';
        $meeting->modified_user_id = '1';
        $meeting->assigned_user_id = $assigned_user_id;
        $meeting->deleted = 0;
        
        return $meeting->save();
    }
    
    private function createTask($name, $case_id, $assigned_user_id) {
        $task = new Task();
        $task->id = create_guid();
        $task->name = $name;
        $task->status = rand(0, 1) ? 'Completed' : 'In Progress';
        $task->priority = $this->priorities[array_rand($this->priorities)];
        $task->date_start = date('Y-m-d', strtotime('-' . rand(1, 14) . ' days'));
        $task->date_due = date('Y-m-d', strtotime('+' . rand(1, 14) . ' days'));
        $task->date_entered = date('Y-m-d H:i:s');
        $task->date_modified = date('Y-m-d H:i:s');
        $task->created_by = '1';
        $task->modified_user_id = '1';
        $task->assigned_user_id = $assigned_user_id;
        $task->deleted = 0;
        
        return $task->save();
    }
    
    private function createConflictSearchData() {
        echo "\nCreating Conflict Search Sample Data...\n";
        
        // This will help demonstrate the Conflict Search feature
        global $db;
        
        $conflict_searches = [
            [
                'search_term' => 'Johnson vs State',
                'search_type' => 'comprehensive',
                'total_matches_found' => 3,
                'high_confidence_matches' => 1,
                'search_status' => 'completed'
            ],
            [
                'search_term' => 'Pacific Legal Group',
                'search_type' => 'comprehensive', 
                'total_matches_found' => 5,
                'high_confidence_matches' => 2,
                'search_status' => 'completed'
            ],
            [
                'search_term' => 'Maria Rodriguez',
                'search_type' => 'comprehensive',
                'total_matches_found' => 2,
                'high_confidence_matches' => 1,
                'search_status' => 'completed'
            ]
        ];
        
        foreach ($conflict_searches as $search) {
            $id = create_guid();
            $query = "INSERT INTO conflict_search (
                id, name, search_term, search_type, modules_searched, 
                total_matches_found, high_confidence_matches, search_status,
                date_entered, date_modified, created_by, modified_user_id,
                assigned_user_id, deleted
            ) VALUES (
                '$id', 'Conflict Search: {$search['search_term']}', '{$search['search_term']}', 
                '{$search['search_type']}', 'Contacts,Accounts,Cases',
                {$search['total_matches_found']}, {$search['high_confidence_matches']}, '{$search['search_status']}',
                NOW(), NOW(), '1', '1', '" . $this->getRandomUserId() . "', 0
            )";
            
            $db->query($query);
        }
        
        echo "  ✓ Created conflict search sample data\n";
    }
    
    // Helper Methods
    
    private function getRandomUserId() {
        if (empty($this->created_users)) {
            return '1'; // Default to admin
        }
        return $this->created_users[array_rand($this->created_users)]->id;
    }
    
    private function getRandomAccountId() {
        if (empty($this->created_accounts)) {
            return null;
        }
        return $this->created_accounts[array_rand($this->created_accounts)]->id;
    }
    
    private function generatePhoneNumber() {
        return sprintf("(%03d) %03d-%04d", rand(200, 999), rand(200, 999), rand(1000, 9999));
    }
    
    private function generateAddress() {
        $street_numbers = rand(100, 9999);
        $street_names = ['Main St', 'Oak Ave', 'Park Blvd', 'First St', 'Second Ave', 'Broadway', 'Sunset Blvd', 'Hollywood Ave'];
        return $street_numbers . ' ' . $street_names[array_rand($street_names)];
    }
    
    private function getAISuggestedStatus($case_type, $priority) {
        // Simple logic for AI suggestions based on case type and priority
        $suggestions = [
            'DUI/DWI' => ['Plea_Negotiation', 'Trial_Pending'],
            'Drug Possession' => ['Plea_Negotiation', 'Pre_Trial'],
            'Assault' => ['Pre_Trial', 'Trial_Pending'],
            'Fraud' => ['Investigation', 'Pre_Trial'],
            'Traffic Violation' => ['Plea_Negotiation', 'Dismissed']
        ];
        
        $options = isset($suggestions[$case_type]) ? $suggestions[$case_type] : ['Pre_Trial', 'Investigation'];
        return $options[array_rand($options)];
    }
    
    private function generateAIFactors($case_type) {
        $factors = [
            'DUI/DWI' => 'BAC level, field sobriety test results, prior offenses, traffic stop validity',
            'Drug Possession' => 'Search warrant validity, amount possessed, intent to distribute, prior record',
            'Assault' => 'Witness testimony, self-defense claim, injury severity, video evidence',
            'Fraud' => 'Financial documentation, intent evidence, amount involved, cooperation level',
            'Traffic Violation' => 'Officer testimony, radar calibration, driving record, circumstances'
        ];
        
        return isset($factors[$case_type]) ? $factors[$case_type] : 'Case-specific factors under review';
    }
}

// Run the seeder
echo "=== CRIMINAL DEFENSE CRM DATA SEEDER ===\n";
echo "This will create comprehensive mock data for your criminal defense attorney CRM\n";
echo "including users, clients, cases, and activities.\n\n";

$seeder = new CriminalDefenseDataSeeder();
$seeder->run();

?>