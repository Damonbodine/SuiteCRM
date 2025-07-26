-- Complete Criminal Defense CRM Seeding (Simple approach)

-- First, let's get the admin user ID for assignment
SET @admin_id = (SELECT id FROM users WHERE user_name = 'admin');

-- Add more law firm staff users
INSERT INTO users (id, user_name, first_name, last_name, title, department, is_admin, status, employee_status, sugar_login, receive_notifications, show_on_employees, phone_work, date_entered, date_modified, created_by, modified_user_id, deleted, user_hash) VALUES
(UUID(), 'robert.chen', 'Robert', 'Chen', 'Senior Partner', 'White Collar Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0102', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
(UUID(), 'maria.rodriguez', 'Maria', 'Rodriguez', 'Managing Partner', 'Criminal Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0103', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
(UUID(), 'david.thompson', 'David', 'Thompson', 'Associate Attorney', 'DUI/Traffic Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0104', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
(UUID(), 'jennifer.williams', 'Jennifer', 'Williams', 'Associate Attorney', 'Drug Crimes Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0105', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
(UUID(), 'michael.johnson', 'Michael', 'Johnson', 'Associate Attorney', 'Assault/Battery Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0106', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
(UUID(), 'lisa.anderson', 'Lisa', 'Anderson', 'Associate Attorney', 'Domestic Violence Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0107', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
(UUID(), 'james.brown', 'James', 'Brown', 'Junior Associate', 'General Criminal Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0108', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
(UUID(), 'ashley.davis', 'Ashley', 'Davis', 'Junior Associate', 'Appeals', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0109', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
(UUID(), 'michelle.taylor', 'Michelle', 'Taylor', 'Senior Paralegal', 'Case Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0110', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
(UUID(), 'steven.jackson', 'Steven', 'Jackson', 'Paralegal', 'Discovery Support', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0111', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
(UUID(), 'rebecca.white', 'Rebecca', 'White', 'Paralegal', 'Client Intake', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0112', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
(UUID(), 'patricia.garcia', 'Patricia', 'Garcia', 'Office Manager', 'Administration', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0113', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password');

-- Get some user IDs for assignments
SET @sarah_id = (SELECT id FROM users WHERE user_name = 'sarah.mitchell');
SET @robert_id = (SELECT id FROM users WHERE user_name = 'robert.chen');
SET @maria_id = (SELECT id FROM users WHERE user_name = 'maria.rodriguez');
SET @david_id = (SELECT id FROM users WHERE user_name = 'david.thompson');
SET @jennifer_id = (SELECT id FROM users WHERE user_name = 'jennifer.williams');
SET @michael_id = (SELECT id FROM users WHERE user_name = 'michael.johnson');

-- Add Criminal Defense Clients
INSERT INTO contacts (id, first_name, last_name, title, phone_mobile, email1, primary_address_city, primary_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'Marcus', 'Washington', 'Active Client', '(323) 555-1001', 'marcus.washington@gmail.com', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, @sarah_id, 0),
(UUID(), 'Jennifer', 'Lopez', 'Active Client', '(213) 555-1002', 'jennifer.lopez@yahoo.com', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, @david_id, 0),
(UUID(), 'Robert', 'Kim', 'Active Client', '(626) 555-1003', 'robert.kim@hotmail.com', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, @jennifer_id, 0),
(UUID(), 'Maria', 'Santos', 'Active Client', '(310) 555-1004', 'maria.santos@gmail.com', 'West Hollywood', 'CA', NOW(), NOW(), @admin_id, @admin_id, @michael_id, 0),
(UUID(), 'David', 'Johnson', 'Active Client', '(818) 555-1005', 'david.johnson@outlook.com', 'Studio City', 'CA', NOW(), NOW(), @admin_id, @admin_id, @sarah_id, 0),
(UUID(), 'Angela', 'Davis', 'Former Client', '(310) 555-1006', 'angela.davis@gmail.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 180 DAY), NOW(), @admin_id, @admin_id, @sarah_id, 0),
(UUID(), 'Michael', 'Brown', 'Former Client', '(323) 555-1007', 'michael.brown@yahoo.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 365 DAY), NOW(), @admin_id, @admin_id, @robert_id, 0),
(UUID(), 'Lisa', 'Rodriguez', 'Former Client', '(213) 555-1008', 'lisa.rodriguez@hotmail.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 90 DAY), NOW(), @admin_id, @admin_id, @david_id, 0),
(UUID(), 'James', 'Wilson', 'Potential Client', '(424) 555-1009', 'james.wilson@gmail.com', 'Santa Monica', 'CA', NOW(), NOW(), @admin_id, @admin_id, @jennifer_id, 0),
(UUID(), 'Sandra', 'Martinez', 'Potential Client', '(562) 555-1010', 'sandra.martinez@yahoo.com', 'South Gate', 'CA', NOW(), NOW(), @admin_id, @admin_id, @michael_id, 0),
(UUID(), 'Thomas', 'Anderson', 'Opposing Party', '(310) 555-1011', 'thomas.anderson@email.com', 'Beverly Hills', 'CA', DATE_SUB(NOW(), INTERVAL 120 DAY), NOW(), @admin_id, @admin_id, @maria_id, 0),
(UUID(), 'Patricia', 'White', 'Opposing Party', '(213) 555-1012', 'patricia.white@email.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 200 DAY), NOW(), @admin_id, @admin_id, @sarah_id, 0),
(UUID(), 'Amanda', 'Clark', 'Prosecutor', '(213) 974-1001', 'amanda.clark@da.lacounty.gov', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, @sarah_id, 0),
(UUID(), 'Mark', 'Stevens', 'Prosecutor', '(213) 974-1002', 'mark.stevens@da.lacounty.gov', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, @robert_id, 0),
(UUID(), 'Dr. Alan', 'Peterson', 'Expert Witness', '(310) 555-1020', 'dr.peterson@forensics.com', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, @michael_id, 0);

-- Add Criminal Defense Cases
INSERT INTO cases (id, name, type, description, priority, status, state, ai_confidence_score, ai_suggested_status, ai_last_analysis, ai_analysis_factors, ai_status_needs_review, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'People v. Washington - Drug Trafficking', 'Drug Trafficking', 'Marcus Washington charged with large-scale cocaine distribution. Search warrant validity questioned.', 'High', 'Pre_Trial', 'Open', 0.65, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 3 DAY), 'Search warrant timing suspicious, CI reliability questionable', 1, DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), @admin_id, @admin_id, @sarah_id, 0),
(UUID(), 'People v. Lopez - DUI Second Offense', 'DUI/DWI', 'Jennifer Lopez arrested for DUI - second offense within 5 years. BAC 0.15.', 'Medium', 'Plea_Negotiation', 'Open', 0.72, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 2 DAY), 'High BAC but breath test machine calibration issues', 0, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), @admin_id, @admin_id, @david_id, 0),
(UUID(), 'People v. Kim - Drug Possession', 'Drug Possession', 'Robert Kim found with 2oz methamphetamine during traffic stop.', 'High', 'Investigation', 'Open', 0.58, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 5 DAY), 'Traffic stop pretext questionable, client cooperative', 1, DATE_SUB(NOW(), INTERVAL 60 DAY), NOW(), @admin_id, @admin_id, @jennifer_id, 0),
(UUID(), 'People v. Santos - Domestic Violence', 'Domestic Violence', 'Maria Santos charged with DV against estranged husband.', 'High', 'Pre_Trial', 'Open', 0.49, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 7 DAY), 'Self-defense claim viable, mutual combat evidence', 1, DATE_SUB(NOW(), INTERVAL 75 DAY), NOW(), @admin_id, @admin_id, @michael_id, 0),
(UUID(), 'People v. D. Johnson - Assault with Deadly Weapon', 'Assault', 'David Johnson charged with ADW after bar fight.', 'Medium', 'Trial_Pending', 'Open', 0.61, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 4 DAY), 'Video evidence inconclusive, self-defense supported', 0, DATE_SUB(NOW(), INTERVAL 50 DAY), NOW(), @admin_id, @admin_id, @sarah_id, 0),
(UUID(), 'People v. Davis - Embezzlement RESOLVED', 'White Collar Crime', 'Angela Davis embezzlement case - successfully resolved with plea.', 'High', 'Closed_Won', 'Closed', 0.85, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 180 DAY), 'Plea negotiation successful, restitution complete', 0, DATE_SUB(NOW(), INTERVAL 200 DAY), DATE_SUB(NOW(), INTERVAL 180 DAY), @admin_id, @admin_id, @sarah_id, 0),
(UUID(), 'People v. Brown - Tax Evasion RESOLVED', 'Tax Evasion', 'Michael Brown tax case - criminal charges dismissed.', 'High', 'Dismissed', 'Closed', 0.90, 'Dismissed', DATE_SUB(NOW(), INTERVAL 365 DAY), 'Civil settlement achieved, charges dropped', 0, DATE_SUB(NOW(), INTERVAL 400 DAY), DATE_SUB(NOW(), INTERVAL 365 DAY), @admin_id, @admin_id, @robert_id, 0),
(UUID(), 'People v. Rodriguez - DUI RESOLVED', 'DUI/DWI', 'Lisa Rodriguez DUI - evidence suppressed, charges reduced.', 'Low', 'Closed_Won', 'Closed', 0.88, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 90 DAY), 'Procedural violations, excellent outcome', 0, DATE_SUB(NOW(), INTERVAL 120 DAY), DATE_SUB(NOW(), INTERVAL 90 DAY), @admin_id, @admin_id, @david_id, 0);

-- Add Accounts (External Organizations)
INSERT INTO accounts (id, name, account_type, industry, phone_office, billing_address_city, billing_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'Los Angeles County Superior Court', 'Court', 'Government', '(213) 974-5411', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, @sarah_id, 0),
(UUID(), 'Los Angeles County District Attorney', 'Prosecutor Office', 'Government', '(213) 974-3512', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, @david_id, 0),
(UUID(), 'Pacific Legal Group', 'Law Firm', 'Legal Services', '(310) 555-9002', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, @jennifer_id, 0),
(UUID(), 'Forensic Analysis Associates', 'Expert Witness', 'Professional Services', '(310) 555-9003', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, @michael_id, 0),
(UUID(), 'Liberty Bail Bonds', 'Bail Bonds', 'Financial Services', '(213) 555-9005', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, @sarah_id, 0);

-- Add Conflict Search Records
INSERT INTO conflict_search (id, name, search_term, search_type, modules_searched, confidence_threshold, total_matches_found, high_confidence_matches, medium_confidence_matches, low_confidence_matches, search_status, execution_time, performed_by, search_criteria, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'Conflict Search: Pacific Legal Group', 'Pacific Legal Group', 'comprehensive', 'Contacts,Accounts,Cases', 75, 3, 2, 1, 0, 'completed', 0.2347, @sarah_id, 'New client intake - opposing counsel check', NOW(), NOW(), @admin_id, @admin_id, @sarah_id, 0),
(UUID(), 'Conflict Search: Angela Davis', 'Angela Davis', 'comprehensive', 'Contacts,Accounts,Cases', 75, 1, 1, 0, 0, 'completed', 0.1543, @robert_id, 'Former client conflict check', NOW(), NOW(), @admin_id, @admin_id, @robert_id, 0),
(UUID(), 'Conflict Search: Amanda Clark', 'Amanda Clark', 'comprehensive', 'Contacts,Accounts,Cases', 75, 5, 3, 2, 0, 'completed', 0.3156, @david_id, 'Prosecutor relationship check', NOW(), NOW(), @admin_id, @admin_id, @david_id, 0);

-- Display Results
SELECT '=== CRIMINAL DEFENSE CRM SEEDING COMPLETE ===' as 'Status';
SELECT '' as '';
SELECT 'SUMMARY REPORT:' as 'Report';
SELECT CONCAT('Total Users: ', COUNT(*)) as 'Users' FROM users WHERE deleted = 0;
SELECT CONCAT('Total Contacts: ', COUNT(*)) as 'Contacts' FROM contacts WHERE deleted = 0;  
SELECT CONCAT('Total Cases: ', COUNT(*)) as 'Cases' FROM cases WHERE deleted = 0;
SELECT CONCAT('Total Accounts: ', COUNT(*)) as 'Accounts' FROM accounts WHERE deleted = 0;
SELECT CONCAT('Total Conflict Searches: ', COUNT(*)) as 'Conflict_Searches' FROM conflict_search WHERE deleted = 0;

SELECT '' as '';
SELECT 'CONTACT BREAKDOWN:' as 'Breakdown';
SELECT title as 'Contact_Type', COUNT(*) as 'Count' FROM contacts WHERE deleted = 0 GROUP BY title ORDER BY COUNT(*) DESC;

SELECT '' as '';
SELECT 'CASE STATUS BREAKDOWN:' as 'Case_Status';
SELECT status as 'Status', COUNT(*) as 'Count' FROM cases WHERE deleted = 0 GROUP BY status ORDER BY COUNT(*) DESC;

SELECT '' as '';
SELECT 'ACTIVE CASES WITH CLIENTS:' as 'Active_Cases';
SELECT 
    c.name as 'Case_Name',
    c.type as 'Case_Type',
    c.status as 'Status',
    CONCAT(ROUND(c.ai_confidence_score * 100), '%') as 'AI_Confidence',
    CONCAT(u.first_name, ' ', u.last_name) as 'Attorney'
FROM cases c
JOIN users u ON c.assigned_user_id = u.id
WHERE c.state = 'Open' AND c.deleted = 0
ORDER BY c.date_entered DESC
LIMIT 5;