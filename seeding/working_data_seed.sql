-- Working Criminal Defense CRM Data Seed - Direct Inserts

-- Insert more contacts for comprehensive testing
INSERT INTO contacts (id, first_name, last_name, title, phone_mobile, email1, primary_address_city, primary_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'Jennifer', 'Lopez', 'Active Client', '(213) 555-1002', 'jennifer.lopez@yahoo.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Robert', 'Kim', 'Active Client', '(626) 555-1003', 'robert.kim@hotmail.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Maria', 'Santos', 'Active Client', '(310) 555-1004', 'maria.santos@gmail.com', 'West Hollywood', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'David', 'Johnson', 'Active Client', '(818) 555-1005', 'david.johnson@outlook.com', 'Studio City', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Angela', 'Davis', 'Former Client', '(310) 555-1006', 'angela.davis@gmail.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 180 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'Michael', 'Brown', 'Former Client', '(323) 555-1007', 'michael.brown@yahoo.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 365 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'Thomas', 'Anderson', 'Opposing Party', '(310) 555-1011', 'thomas.anderson@email.com', 'Beverly Hills', 'CA', DATE_SUB(NOW(), INTERVAL 120 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'Amanda', 'Clark', 'Prosecutor', '(213) 974-1001', 'amanda.clark@da.lacounty.gov', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Dr. Alan', 'Peterson', 'Expert Witness', '(310) 555-1020', 'dr.peterson@forensics.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Carlos', 'Santos', 'Family Member', '(310) 555-1013', 'carlos.santos@gmail.com', 'West Hollywood', 'CA', NOW(), NOW(), '1', '1', '1', 0);

-- Insert accounts (external organizations)
INSERT INTO accounts (id, name, account_type, industry, phone_office, billing_address_city, billing_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'Los Angeles County Superior Court', 'Court', 'Government', '(213) 974-5411', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Los Angeles County District Attorney', 'Prosecutor Office', 'Government', '(213) 974-3512', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Pacific Legal Group', 'Law Firm', 'Legal Services', '(310) 555-9002', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Morrison & Associates', 'Law Firm', 'Legal Services', '(310) 555-9001', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Forensic Analysis Associates', 'Expert Witness', 'Professional Services', '(310) 555-9003', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Beverly Hills Municipal Court', 'Court', 'Government', '(310) 285-2400', 'Beverly Hills', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Liberty Bail Bonds', 'Bail Bonds', 'Financial Services', '(213) 555-9005', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0);

-- Insert criminal defense cases
INSERT INTO cases (id, name, case_number, type, description, priority, status, state, ai_confidence_score, ai_suggested_status, ai_last_analysis, ai_analysis_factors, ai_status_needs_review, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'People v. Washington - Drug Trafficking', 2024001, 'Drug Trafficking', 'Marcus Washington charged with large-scale cocaine distribution. Search warrant validity questioned. Client maintains innocence and has clean record.', 'High', 'Pre_Trial', 'Open', 0.65, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 3 DAY), 'Search warrant timing suspicious, CI reliability questionable, large quantity suggests trafficking intent', 1, DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'People v. Lopez - DUI Second Offense', 2024002, 'DUI/DWI', 'Jennifer Lopez arrested for DUI - second offense within 5 years. BAC 0.15. Breath test machine calibration issues discovered.', 'Medium', 'Plea_Negotiation', 'Open', 0.72, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 2 DAY), 'High BAC concerning but breath test machine calibration records missing for critical period', 0, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'People v. Kim - Drug Possession', 2024003, 'Drug Possession', 'Robert Kim found with methamphetamine during questionable traffic stop. Intent to distribute charges added based on packaging.', 'High', 'Investigation', 'Open', 0.58, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 5 DAY), 'Traffic stop pretext questionable, packaging suggests personal use, client cooperative', 1, DATE_SUB(NOW(), INTERVAL 60 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'People v. Santos - Domestic Violence', 2024004, 'Domestic Violence', 'Maria Santos charged with DV against estranged husband. Self-defense claim viable based on injury patterns.', 'High', 'Pre_Trial', 'Open', 0.49, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 7 DAY), 'Self-defense claim viable, photos show mutual injuries, husband history of violence', 1, DATE_SUB(NOW(), INTERVAL 75 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'People v. Johnson - Assault with Deadly Weapon', 2024005, 'Assault', 'David Johnson charged with ADW after bar fight. Video evidence supports self-defense claim.', 'Medium', 'Trial_Pending', 'Open', 0.61, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 4 DAY), 'Video evidence inconclusive but witnesses support self-defense, victim reputation for violence', 0, DATE_SUB(NOW(), INTERVAL 50 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'People v. Davis - Embezzlement RESOLVED', 2023015, 'White Collar Crime', 'Angela Davis embezzlement case successfully resolved with plea agreement and full restitution.', 'High', 'Closed_Won', 'Closed', 0.85, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 180 DAY), 'Strong plea negotiation resulted in probation and full restitution', 0, DATE_SUB(NOW(), INTERVAL 200 DAY), DATE_SUB(NOW(), INTERVAL 180 DAY), '1', '1', '1', 0),
(UUID(), 'Anderson v. Pacific Legal Group - Malpractice', 2023005, 'Civil Litigation', 'Thomas Anderson malpractice case against Pacific Legal Group - WON $350K settlement. CONFLICT if Pacific Legal seeks representation.', 'Medium', 'Closed_Won', 'Closed', 0.92, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 120 DAY), 'Successful malpractice claim with substantial settlement achieved', 0, DATE_SUB(NOW(), INTERVAL 150 DAY), DATE_SUB(NOW(), INTERVAL 120 DAY), '1', '1', '1', 0);

-- Insert more billable activities
INSERT INTO calls (id, name, status, direction, date_start, duration_hours, duration_minutes, description, assigned_user_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES
(UUID(), 'Client Consultation - Washington Drug Case Strategy', 'Held', 'Inbound', DATE_SUB(NOW(), INTERVAL 2 DAY), 1, 30, 'Initial strategy discussion for drug trafficking case. Reviewed search warrant issues.', '1', DATE_SUB(NOW(), INTERVAL 2 DAY), NOW(), '1', '1', 0),
(UUID(), 'Case Update Call - Lopez DUI Progress', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 1 DAY), 0, 45, 'Updated client on plea negotiation progress. DMV hearing successful.', '1', DATE_SUB(NOW(), INTERVAL 1 DAY), NOW(), '1', '1', 0),
(UUID(), 'Client Consultation - Kim Drug Case', 'Held', 'Inbound', NOW(), 1, 15, 'Initial consultation about drug possession charges and defense options.', '1', NOW(), NOW(), '1', '1', 0),
(UUID(), 'Plea Negotiation - Prosecutor Clark', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 3 DAY), 0, 30, 'Discussed plea options for Washington case with prosecutor.', '1', DATE_SUB(NOW(), INTERVAL 3 DAY), NOW(), '1', '1', 0),
(UUID(), 'Expert Witness Coordination - Dr. Peterson', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 5 DAY), 1, 0, 'Consulted with forensic expert about DNA evidence issues.', '1', DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), '1', '1', 0);

-- Insert conflict search data
INSERT INTO conflict_search (id, name, search_term, search_type, modules_searched, confidence_threshold, total_matches_found, high_confidence_matches, medium_confidence_matches, low_confidence_matches, search_status, execution_time, performed_by, search_criteria, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'CRITICAL CONFLICT: Pacific Legal Group', 'Pacific Legal Group', 'comprehensive', 'Contacts,Accounts,Cases', 75, 3, 2, 1, 0, 'completed', 0.2347, '1', 'New client intake - Pacific Legal Group wants representation. MANDATORY conflict check.', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Former Client Conflict: Angela Davis', 'Angela Davis', 'comprehensive', 'Contacts,Accounts,Cases', 75, 1, 1, 0, 0, 'completed', 0.1543, '1', 'Former client - employer wants to retain us for collection action.', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Family Conflict: Carlos Santos', 'Carlos Santos', 'comprehensive', 'Contacts,Accounts,Cases', 75, 2, 1, 1, 0, 'completed', 0.1892, '1', 'Family member of current client Maria Santos - DV case conflict.', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Prosecutor Analysis: Amanda Clark', 'Amanda Clark', 'comprehensive', 'Contacts,Cases,Meetings,Calls', 75, 5, 3, 2, 0, 'completed', 0.3156, '1', 'Prosecutor relationship and caseload analysis.', NOW(), NOW(), '1', '1', '1', 0);

-- Insert court meetings
INSERT INTO meetings (id, name, status, date_start, duration_hours, duration_minutes, description, location, assigned_user_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES
(UUID(), 'Motion Hearing - Washington Suppression', 'Held', DATE_SUB(NOW(), INTERVAL 10 DAY), 3, 0, 'Pre-trial motion to suppress evidence based on invalid search warrant.', 'LA Superior Court Dept 100', '1', DATE_SUB(NOW(), INTERVAL 10 DAY), NOW(), '1', '1', 0),
(UUID(), 'DMV Hearing - Lopez License', 'Held', DATE_SUB(NOW(), INTERVAL 15 DAY), 2, 30, 'DMV hearing - successfully stayed license suspension.', 'DMV Hearing Office', '1', DATE_SUB(NOW(), INTERVAL 15 DAY), NOW(), '1', '1', 0),
(UUID(), 'Pre-Trial Conference - Santos DV Case', 'Planned', DATE_ADD(NOW(), INTERVAL 14 DAY), 2, 0, 'Pre-trial conference with prosecutor and judge.', 'LA Superior Court Dept 105', '1', NOW(), NOW(), '1', '1', 0);

-- Insert legal research tasks
INSERT INTO tasks (id, name, status, priority, date_start, date_due, description, assigned_user_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES
(UUID(), 'Legal Research - Search Warrant Validity', 'In Progress', 'High', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 'Research CI reliability and stale information in search warrants.', '1', DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), '1', '1', 0),
(UUID(), 'Motion Preparation - Breath Test Suppression', 'Completed', 'Medium', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 'Prepare motion to suppress breath test - calibration issues.', '1', DATE_SUB(NOW(), INTERVAL 10 DAY), NOW(), '1', '1', 0),
(UUID(), 'Witness Interview Coordination - Kim Case', 'Pending', 'High', NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 'Interview witnesses present during traffic stop.', '1', NOW(), NOW(), '1', '1', 0);

-- Display results
SELECT '🎯 COMPREHENSIVE CRIMINAL DEFENSE CRM DATA SEEDED SUCCESSFULLY! 🎯' as 'STATUS';
SELECT '' as '';
SELECT 'MODULE DATA VERIFICATION:' as 'Verification';
SELECT CONCAT('✅ Users: ', COUNT(*), ' law firm staff') as 'Users' FROM users WHERE deleted = 0 AND first_name IS NOT NULL;
SELECT CONCAT('✅ Contacts: ', COUNT(*), ' clients/prosecutors/experts') as 'Contacts' FROM contacts WHERE deleted = 0;
SELECT CONCAT('✅ Cases: ', COUNT(*), ' criminal defense cases') as 'Cases' FROM cases WHERE deleted = 0 AND name NOT LIKE 'AI Test%';
SELECT CONCAT('✅ Accounts: ', COUNT(*), ' courts/law firms/experts') as 'Accounts' FROM accounts WHERE deleted = 0;
SELECT CONCAT('✅ Calls: ', COUNT(*), ' billable activities') as 'Calls' FROM calls WHERE deleted = 0;
SELECT CONCAT('✅ Meetings: ', COUNT(*), ' court hearings') as 'Meetings' FROM meetings WHERE deleted = 0;
SELECT CONCAT('✅ Tasks: ', COUNT(*), ' legal research tasks') as 'Tasks' FROM tasks WHERE deleted = 0;
SELECT CONCAT('✅ Conflict Searches: ', COUNT(*), ' conflict scenarios') as 'Conflicts' FROM conflict_search WHERE deleted = 0;

SELECT '' as '';
SELECT 'READY FOR MODULE ENDPOINT TESTING:' as 'Testing_Ready';
SELECT '• Users Module: index.php?module=Users&action=index' as 'Users_Endpoint';
SELECT '• Contacts Module: index.php?module=Contacts&action=index' as 'Contacts_Endpoint';
SELECT '• Cases Module: index.php?module=Cases&action=index' as 'Cases_Endpoint';
SELECT '• Accounts Module: index.php?module=Accounts&action=index' as 'Accounts_Endpoint';
SELECT '• Calls Module: index.php?module=Calls&action=index' as 'Calls_Endpoint';
SELECT '• ConflictSearch Module: index.php?module=ConflictSearch&action=index' as 'ConflictSearch_Endpoint';
SELECT '• Home Dashboard: index.php?module=Home&action=index' as 'Home_Endpoint';

SELECT '' as '';
SELECT 'BILLABLE HOURS DATA (for BillableHoursQuickEntry Dashlet):' as 'Billable_Hours';
SELECT 
    'Admin User' as 'Attorney',
    COUNT(*) as 'Total_Calls',
    CONCAT(SUM(duration_hours), 'h ', SUM(duration_minutes), 'm') as 'Billable_Time'
FROM calls 
WHERE deleted = 0 AND status = 'Held' AND assigned_user_id = '1';

SELECT '' as '';
SELECT 'CONFLICT DETECTION SCENARIOS:' as 'Conflict_Testing';
SELECT 
    search_term as 'Search_Term',
    total_matches_found as 'Matches',
    high_confidence_matches as 'High_Risk',
    CASE 
        WHEN high_confidence_matches > 0 THEN '❌ REJECT'
        WHEN medium_confidence_matches > 2 THEN '⚠️ CAUTION' 
        ELSE '✅ CLEAR'
    END as 'Recommendation'
FROM conflict_search 
WHERE deleted = 0
ORDER BY high_confidence_matches DESC;

SELECT '' as '';
SELECT '🚀 ALL MODULES NOW CONTAIN COMPREHENSIVE CRIMINAL DEFENSE DATA!' as 'Final_Status';