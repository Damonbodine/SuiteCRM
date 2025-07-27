-- Final Working Criminal Defense CRM Seed - Simplified but Complete

-- Clean up any test data first
DELETE FROM users WHERE user_name = 'test.user';

-- Get admin ID
SET @admin_id = '1'; -- Direct admin ID

-- Create Users - Criminal Defense Law Firm
INSERT INTO users (id, user_name, first_name, last_name, title, department, is_admin, status, employee_status, sugar_login, receive_notifications, show_on_employees, phone_work, email1, date_entered, date_modified, created_by, modified_user_id, deleted, user_hash) VALUES
('usr-robert-001', 'robert.chen', 'Robert', 'Chen', 'Senior Partner', 'White Collar Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0102', 'robert.chen@lawfirm.com', NOW(), NOW(), @admin_id, @admin_id, 0, 'password_hash'),
('usr-maria-002', 'maria.rodriguez', 'Maria', 'Rodriguez', 'Managing Partner', 'Criminal Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0103', 'maria.rodriguez@lawfirm.com', NOW(), NOW(), @admin_id, @admin_id, 0, 'password_hash'),
('usr-david-003', 'david.thompson', 'David', 'Thompson', 'Associate Attorney', 'DUI/Traffic Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0104', 'david.thompson@lawfirm.com', NOW(), NOW(), @admin_id, @admin_id, 0, 'password_hash'),
('usr-jennifer-004', 'jennifer.williams', 'Jennifer', 'Williams', 'Associate Attorney', 'Drug Crimes Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0105', 'jennifer.williams@lawfirm.com', NOW(), NOW(), @admin_id, @admin_id, 0, 'password_hash'),
('usr-michael-005', 'michael.johnson', 'Michael', 'Johnson', 'Associate Attorney', 'Assault/Battery Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0106', 'michael.johnson@lawfirm.com', NOW(), NOW(), @admin_id, @admin_id, 0, 'password_hash'),
('usr-lisa-006', 'lisa.anderson', 'Lisa', 'Anderson', 'Associate Attorney', 'Domestic Violence Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0107', 'lisa.anderson@lawfirm.com', NOW(), NOW(), @admin_id, @admin_id, 0, 'password_hash'),
('usr-michelle-007', 'michelle.taylor', 'Michelle', 'Taylor', 'Senior Paralegal', 'Case Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0110', 'michelle.taylor@lawfirm.com', NOW(), NOW(), @admin_id, @admin_id, 0, 'password_hash'),
('usr-steven-008', 'steven.jackson', 'Steven', 'Jackson', 'Paralegal', 'Discovery Support', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0111', 'steven.jackson@lawfirm.com', NOW(), NOW(), @admin_id, @admin_id, 0, 'password_hash');

-- Create Accounts - External Organizations
INSERT INTO accounts (id, name, account_type, industry, phone_office, billing_address_city, billing_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
('acc-court-001', 'Los Angeles County Superior Court', 'Court', 'Government', '(213) 974-5411', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, 'usr-robert-001', 0),
('acc-da-002', 'Los Angeles County District Attorney', 'Prosecutor Office', 'Government', '(213) 974-3512', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, 'usr-maria-002', 0),
('acc-pacific-003', 'Pacific Legal Group', 'Law Firm', 'Legal Services', '(310) 555-9002', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, 'usr-david-003', 0),
('acc-expert-004', 'Forensic Analysis Associates', 'Expert Witness', 'Professional Services', '(310) 555-9003', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, 'usr-jennifer-004', 0);

-- Create Contacts - Clients, Prosecutors, Experts
INSERT INTO contacts (id, first_name, last_name, title, phone_mobile, email1, primary_address_city, primary_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, account_id, deleted) VALUES
-- Active Clients
('con-marcus-001', 'Marcus', 'Washington', 'Active Client', '(323) 555-1001', 'marcus.washington@gmail.com', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, 'usr-robert-001', NULL, 0),
('con-jennifer-002', 'Jennifer', 'Lopez', 'Active Client', '(213) 555-1002', 'jennifer.lopez@yahoo.com', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, 'usr-david-003', NULL, 0),
('con-robert-003', 'Robert', 'Kim', 'Active Client', '(626) 555-1003', 'robert.kim@hotmail.com', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, 'usr-jennifer-004', NULL, 0),
('con-maria-004', 'Maria', 'Santos', 'Active Client', '(310) 555-1004', 'maria.santos@gmail.com', 'West Hollywood', 'CA', NOW(), NOW(), @admin_id, @admin_id, 'usr-michael-005', NULL, 0),
('con-david-005', 'David', 'Johnson', 'Active Client', '(818) 555-1005', 'david.johnson@outlook.com', 'Studio City', 'CA', NOW(), NOW(), @admin_id, @admin_id, 'usr-lisa-006', NULL, 0),

-- Former Clients (for conflict detection)
('con-angela-006', 'Angela', 'Davis', 'Former Client', '(310) 555-1006', 'angela.davis@gmail.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 180 DAY), NOW(), @admin_id, @admin_id, 'usr-robert-001', NULL, 0),
('con-michael-007', 'Michael', 'Brown', 'Former Client', '(323) 555-1007', 'michael.brown@yahoo.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 365 DAY), NOW(), @admin_id, @admin_id, 'usr-maria-002', NULL, 0),

-- Opposing Parties (conflict risks)
('con-thomas-008', 'Thomas', 'Anderson', 'Opposing Party', '(310) 555-1011', 'thomas.anderson@email.com', 'Beverly Hills', 'CA', DATE_SUB(NOW(), INTERVAL 120 DAY), NOW(), @admin_id, @admin_id, 'usr-maria-002', 'acc-pacific-003', 0),

-- Prosecutors
('con-amanda-009', 'Amanda', 'Clark', 'Prosecutor', '(213) 974-1001', 'amanda.clark@da.lacounty.gov', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, 'usr-robert-001', 'acc-da-002', 0),

-- Expert Witnesses
('con-alan-010', 'Dr. Alan', 'Peterson', 'Expert Witness', '(310) 555-1020', 'dr.peterson@forensics.com', 'Los Angeles', 'CA', NOW(), NOW(), @admin_id, @admin_id, 'usr-michael-005', 'acc-expert-004', 0);

-- Create Cases - Criminal Defense Portfolio
INSERT INTO cases (id, name, type, description, priority, status, state, ai_confidence_score, ai_suggested_status, ai_last_analysis, ai_analysis_factors, ai_status_needs_review, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, account_id, deleted) VALUES
-- Active Cases
('case-marcus-001', 'People v. Washington - Drug Trafficking', 'Drug Trafficking', 'Marcus Washington charged with large-scale cocaine distribution. Search warrant validity questioned. Client maintains innocence.', 'High', 'Pre_Trial', 'Open', 0.65, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 3 DAY), 'Search warrant timing suspicious, CI reliability questionable, large quantity suggests trafficking intent', 1, DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), @admin_id, @admin_id, 'usr-robert-001', 'acc-da-002', 0),

('case-jennifer-002', 'People v. Lopez - DUI Second Offense', 'DUI/DWI', 'Jennifer Lopez arrested for DUI - second offense within 5 years. BAC 0.15. Breath test machine calibration issues discovered.', 'Medium', 'Plea_Negotiation', 'Open', 0.72, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 2 DAY), 'High BAC concerning but breath test machine calibration records missing for critical period', 0, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), @admin_id, @admin_id, 'usr-david-003', 'acc-da-002', 0),

('case-robert-003', 'People v. Kim - Drug Possession', 'Drug Possession', 'Robert Kim found with methamphetamine during questionable traffic stop. Intent to distribute charges added.', 'High', 'Investigation', 'Open', 0.58, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 5 DAY), 'Traffic stop pretext questionable, packaging suggests personal use, client cooperative', 1, DATE_SUB(NOW(), INTERVAL 60 DAY), NOW(), @admin_id, @admin_id, 'usr-jennifer-004', 'acc-da-002', 0),

('case-maria-004', 'People v. Santos - Domestic Violence', 'Domestic Violence', 'Maria Santos charged with DV against estranged husband. Self-defense claim viable based on injury patterns.', 'High', 'Pre_Trial', 'Open', 0.49, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 7 DAY), 'Self-defense claim viable, photos show mutual injuries, husband history of violence', 1, DATE_SUB(NOW(), INTERVAL 75 DAY), NOW(), @admin_id, @admin_id, 'usr-michael-005', 'acc-da-002', 0),

('case-david-005', 'People v. Johnson - Assault with Deadly Weapon', 'Assault', 'David Johnson charged with ADW after bar fight. Video evidence supports self-defense claim.', 'Medium', 'Trial_Pending', 'Open', 0.61, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 4 DAY), 'Video evidence inconclusive but witnesses support self-defense, victim reputation for violence', 0, DATE_SUB(NOW(), INTERVAL 50 DAY), NOW(), @admin_id, @admin_id, 'usr-lisa-006', 'acc-da-002', 0),

-- Closed Cases (for conflict detection)
('case-angela-006', 'People v. Davis - Embezzlement RESOLVED', 'White Collar Crime', 'Angela Davis embezzlement case successfully resolved with plea agreement and full restitution.', 'High', 'Closed_Won', 'Closed', 0.85, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 180 DAY), 'Strong plea negotiation resulted in probation and full restitution', 0, DATE_SUB(NOW(), INTERVAL 200 DAY), DATE_SUB(NOW(), INTERVAL 180 DAY), @admin_id, @admin_id, 'usr-robert-001', 'acc-da-002', 0),

-- Conflict Case
('case-thomas-007', 'Anderson v. Pacific Legal Group - Malpractice', 'Civil Litigation', 'Thomas Anderson malpractice case against Pacific Legal Group - WON $350K settlement. CONFLICT if Pacific Legal seeks representation.', 'Medium', 'Closed_Won', 'Closed', 0.92, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 120 DAY), 'Successful malpractice claim, substantial settlement achieved', 0, DATE_SUB(NOW(), INTERVAL 150 DAY), DATE_SUB(NOW(), INTERVAL 120 DAY), @admin_id, @admin_id, 'usr-maria-002', 'acc-pacific-003', 0);

-- Create Contact-Case Relationships
INSERT INTO contacts_cases (id, contact_id, case_id, contact_role, date_modified, deleted) VALUES
(UUID(), 'con-marcus-001', 'case-marcus-001', 'Client', NOW(), 0),
(UUID(), 'con-jennifer-002', 'case-jennifer-002', 'Client', NOW(), 0),
(UUID(), 'con-robert-003', 'case-robert-003', 'Client', NOW(), 0),
(UUID(), 'con-maria-004', 'case-maria-004', 'Client', NOW(), 0),
(UUID(), 'con-david-005', 'case-david-005', 'Client', NOW(), 0),
(UUID(), 'con-angela-006', 'case-angela-006', 'Client', DATE_SUB(NOW(), INTERVAL 180 DAY), 0),
(UUID(), 'con-thomas-008', 'case-thomas-007', 'Client', DATE_SUB(NOW(), INTERVAL 120 DAY), 0),
(UUID(), 'con-amanda-009', 'case-marcus-001', 'Opposing Counsel', NOW(), 0),
(UUID(), 'con-alan-010', 'case-marcus-001', 'Expert Witness', NOW(), 0);

-- Create Billable Activities (Calls)
INSERT INTO calls (id, name, status, direction, date_start, duration_hours, duration_minutes, description, parent_type, parent_id, assigned_user_id, contact_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES
('call-marcus-001', 'Client Consultation - Washington Drug Case', 'Held', 'Inbound', DATE_SUB(NOW(), INTERVAL 2 DAY), 1, 30, 'Strategy discussion for drug trafficking case. Reviewed search warrant issues.', 'Cases', 'case-marcus-001', 'usr-robert-001', 'con-marcus-001', DATE_SUB(NOW(), INTERVAL 2 DAY), NOW(), @admin_id, @admin_id, 0),
('call-jennifer-002', 'Case Update - Lopez DUI Progress', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 1 DAY), 0, 45, 'Updated client on plea negotiation progress. DMV hearing successful.', 'Cases', 'case-jennifer-002', 'usr-david-003', 'con-jennifer-002', DATE_SUB(NOW(), INTERVAL 1 DAY), NOW(), @admin_id, @admin_id, 0),
('call-robert-003', 'Client Consultation - Kim Drug Case', 'Held', 'Inbound', NOW(), 1, 15, 'Initial consultation about drug possession charges and defense options.', 'Cases', 'case-robert-003', 'usr-jennifer-004', 'con-robert-003', NOW(), NOW(), @admin_id, @admin_id, 0),
('call-prosecutor-004', 'Plea Negotiation - Amanda Clark', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 3 DAY), 0, 30, 'Discussed plea options for Washington case with prosecutor.', 'Cases', 'case-marcus-001', 'usr-robert-001', 'con-amanda-009', DATE_SUB(NOW(), INTERVAL 3 DAY), NOW(), @admin_id, @admin_id, 0);

-- Create Court Meetings
INSERT INTO meetings (id, name, status, date_start, duration_hours, duration_minutes, description, location, parent_type, parent_id, assigned_user_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES
('meet-motion-001', 'Motion Hearing - Washington Suppression', 'Held', DATE_SUB(NOW(), INTERVAL 10 DAY), 3, 0, 'Pre-trial motion to suppress evidence based on invalid search warrant.', 'LA Superior Court Dept 100', 'Cases', 'case-marcus-001', 'usr-robert-001', DATE_SUB(NOW(), INTERVAL 10 DAY), NOW(), @admin_id, @admin_id, 0),
('meet-dmv-002', 'DMV Hearing - Lopez License', 'Held', DATE_SUB(NOW(), INTERVAL 15 DAY), 2, 30, 'DMV hearing - successfully stayed license suspension.', 'DMV Hearing Office', 'Cases', 'case-jennifer-002', 'usr-david-003', DATE_SUB(NOW(), INTERVAL 15 DAY), NOW(), @admin_id, @admin_id, 0);

-- Create Legal Research Tasks
INSERT INTO tasks (id, name, status, priority, date_start, date_due, description, parent_type, parent_id, assigned_user_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES
('task-research-001', 'Legal Research - Search Warrant Validity', 'In Progress', 'High', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 'Research CI reliability and stale information in search warrants.', 'Cases', 'case-marcus-001', 'usr-steven-008', DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), @admin_id, @admin_id, 0),
('task-motion-002', 'Motion Preparation - Breath Test Suppression', 'Completed', 'Medium', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 'Prepare motion to suppress breath test - calibration issues.', 'Cases', 'case-jennifer-002', 'usr-michelle-007', DATE_SUB(NOW(), INTERVAL 10 DAY), NOW(), @admin_id, @admin_id, 0);

-- Create Conflict Search Records
INSERT INTO conflict_search (id, name, search_term, search_type, modules_searched, confidence_threshold, total_matches_found, high_confidence_matches, medium_confidence_matches, low_confidence_matches, search_status, execution_time, performed_by, search_criteria, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
('conflict-pacific-001', 'CRITICAL CONFLICT: Pacific Legal Group', 'Pacific Legal Group', 'comprehensive', 'Contacts,Accounts,Cases', 75, 3, 2, 1, 0, 'completed', 0.2347, 'usr-robert-001', 'New client conflict check - Pacific Legal wants representation', NOW(), NOW(), @admin_id, @admin_id, 'usr-robert-001', 0),
('conflict-angela-002', 'Former Client Conflict: Angela Davis', 'Angela Davis', 'comprehensive', 'Contacts,Accounts,Cases', 75, 1, 1, 0, 0, 'completed', 0.1543, 'usr-maria-002', 'Former client - employer wants to sue her', NOW(), NOW(), @admin_id, @admin_id, 'usr-maria-002', 0),
('conflict-prosecutor-003', 'Prosecutor Analysis: Amanda Clark', 'Amanda Clark', 'comprehensive', 'Contacts,Cases,Meetings,Calls', 75, 5, 3, 2, 0, 'completed', 0.3156, 'usr-david-003', 'Prosecutor relationship and caseload analysis', NOW(), NOW(), @admin_id, @admin_id, 'usr-david-003', 0);

-- Display Results
SELECT '=== FINAL CRIMINAL DEFENSE CRM SEEDING COMPLETE ===' as 'Status';
SELECT CONCAT('✅ Users Created: ', COUNT(*)) as 'Users' FROM users WHERE deleted = 0 AND first_name IS NOT NULL;
SELECT CONCAT('✅ Accounts Created: ', COUNT(*)) as 'Accounts' FROM accounts WHERE deleted = 0;
SELECT CONCAT('✅ Contacts Created: ', COUNT(*)) as 'Contacts' FROM contacts WHERE deleted = 0;
SELECT CONCAT('✅ Cases Created: ', COUNT(*)) as 'Cases' FROM cases WHERE deleted = 0 AND name NOT LIKE 'AI Test%';
SELECT CONCAT('✅ Calls Created: ', COUNT(*)) as 'Calls' FROM calls WHERE deleted = 0;
SELECT CONCAT('✅ Meetings Created: ', COUNT(*)) as 'Meetings' FROM meetings WHERE deleted = 0;
SELECT CONCAT('✅ Tasks Created: ', COUNT(*)) as 'Tasks' FROM tasks WHERE deleted = 0;
SELECT CONCAT('✅ Conflict Searches: ', COUNT(*)) as 'Conflicts' FROM conflict_search WHERE deleted = 0;
SELECT CONCAT('✅ Contact-Case Links: ', COUNT(*)) as 'Relationships' FROM contacts_cases WHERE deleted = 0;

SELECT '' as '';
SELECT '🎯 READY FOR MODULE TESTING:' as 'Module_Integration';
SELECT '• Users Module: Law firm staff hierarchy' as 'Users_Module';
SELECT '• Contacts Module: Clients, prosecutors, experts' as 'Contacts_Module';
SELECT '• Cases Module: Active criminal defense portfolio' as 'Cases_Module';
SELECT '• Accounts Module: Courts, DA offices, law firms' as 'Accounts_Module';
SELECT '• Calls Module: Billable client communications' as 'Calls_Module';
SELECT '• Meetings Module: Court hearings and conferences' as 'Meetings_Module';
SELECT '• Tasks Module: Legal research and case prep' as 'Tasks_Module';
SELECT '• ConflictSearch Module: Critical conflict scenarios' as 'ConflictSearch_Module';
SELECT '• BillableHours Dashlet: Ready with call data' as 'BillableHours_Dashlet';

SELECT '' as '';
SELECT '📊 BILLABLE HOURS DATA FOR DASHLET:' as 'Billable_Hours_Summary';
SELECT 
    CONCAT(u.first_name, ' ', u.last_name) as 'Attorney',
    COUNT(c.id) as 'Calls',
    CONCAT(SUM(c.duration_hours), 'h ', SUM(c.duration_minutes), 'm') as 'Total_Time'
FROM calls c
JOIN users u ON c.assigned_user_id = u.id
WHERE c.deleted = 0 AND c.status = 'Held' AND u.first_name IS NOT NULL
GROUP BY u.id, u.first_name, u.last_name
ORDER BY (SUM(c.duration_hours) * 60 + SUM(c.duration_minutes)) DESC;