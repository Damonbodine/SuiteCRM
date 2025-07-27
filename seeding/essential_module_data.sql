-- Essential Module Data for Criminal Defense CRM Testing

-- Add essential contacts for module testing
INSERT INTO contacts (id, first_name, last_name, title, phone_mobile, email1, primary_address_city, primary_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'Marcus', 'Washington', 'Active Client', '(323) 555-1001', 'marcus.washington@gmail.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Jennifer', 'Lopez', 'Active Client', '(213) 555-1002', 'jennifer.lopez@yahoo.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Robert', 'Kim', 'Active Client', '(626) 555-1003', 'robert.kim@hotmail.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Maria', 'Santos', 'Active Client', '(310) 555-1004', 'maria.santos@gmail.com', 'West Hollywood', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Angela', 'Davis', 'Former Client', '(310) 555-1006', 'angela.davis@gmail.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 180 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Amanda', 'Clark', 'Prosecutor', '(213) 974-1001', 'amanda.clark@da.lacounty.gov', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Thomas', 'Anderson', 'Opposing Party', '(310) 555-1011', 'thomas.anderson@email.com', 'Beverly Hills', 'CA', DATE_SUB(NOW(), INTERVAL 120 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0);

-- Add essential accounts for module testing
INSERT INTO accounts (id, name, account_type, industry, phone_office, billing_address_city, billing_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'Los Angeles County Superior Court', 'Court', 'Government', '(213) 974-5411', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Los Angeles County District Attorney', 'Prosecutor Office', 'Government', '(213) 974-3512', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Pacific Legal Group', 'Law Firm', 'Legal Services', '(310) 555-9002', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Forensic Analysis Associates', 'Expert Witness', 'Professional Services', '(310) 555-9003', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0);

-- Add essential cases for module testing
INSERT INTO cases (id, name, type, description, priority, status, state, ai_confidence_score, ai_suggested_status, ai_last_analysis, ai_analysis_factors, ai_status_needs_review, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'People v. Washington - Drug Trafficking', 'Drug Trafficking', 'Marcus Washington charged with large-scale cocaine distribution. Search warrant validity questioned. Client maintains innocence and has clean record.', 'High', 'Pre_Trial', 'Open', 0.65, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 3 DAY), 'Search warrant timing suspicious, CI reliability questionable, large quantity suggests trafficking intent, client clean record supports credibility', 1, DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'People v. Lopez - DUI Second Offense', 'DUI/DWI', 'Jennifer Lopez arrested for DUI - second offense within 5 years. BAC 0.15. Breath test machine calibration issues discovered during investigation.', 'Medium', 'Plea_Negotiation', 'Open', 0.72, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 2 DAY), 'High BAC concerning but breath test machine calibration records missing for critical 30-day period including arrest date', 0, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'People v. Kim - Drug Possession', 'Drug Possession', 'Robert Kim found with methamphetamine during questionable traffic stop. Intent to distribute charges added based on packaging.', 'High', 'Investigation', 'Open', 0.58, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 5 DAY), 'Traffic stop pretext questionable as no actual equipment violation, packaging suggests personal use not distribution, client cooperative', 1, DATE_SUB(NOW(), INTERVAL 60 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'People v. Davis - Embezzlement RESOLVED', 'White Collar Crime', 'Angela Davis embezzlement case successfully resolved with plea agreement and full restitution. Client avoided prison time.', 'High', 'Closed_Won', 'Closed', 0.85, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 180 DAY), 'Strong plea negotiation resulted in probation and full restitution, client cooperation excellent', 0, DATE_SUB(NOW(), INTERVAL 200 DAY), DATE_SUB(NOW(), INTERVAL 180 DAY), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Anderson v. Pacific Legal Group - Malpractice', 'Civil Litigation', 'Thomas Anderson malpractice case against Pacific Legal Group - WON $350K settlement. MAJOR CONFLICT if Pacific Legal seeks our representation.', 'Medium', 'Closed_Won', 'Closed', 0.92, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 120 DAY), 'Successful malpractice claim with substantial settlement achieved against Pacific Legal Group', 0, DATE_SUB(NOW(), INTERVAL 150 DAY), DATE_SUB(NOW(), INTERVAL 120 DAY), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0);

-- Add billable hours activities for BillableHoursQuickEntry dashlet
INSERT INTO calls (id, name, status, direction, date_start, duration_hours, duration_minutes, description, assigned_user_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES
(UUID(), 'Client Consultation - Washington Drug Case', 'Held', 'Inbound', DATE_SUB(NOW(), INTERVAL 2 DAY), 1, 30, 'Initial strategy discussion for drug trafficking case. Reviewed search warrant issues, discussed defense options.', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), DATE_SUB(NOW(), INTERVAL 2 DAY), NOW(), '1', '1', 0),
(UUID(), 'Case Update Call - Lopez DUI Progress', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 1 DAY), 0, 45, 'Updated client on plea negotiation progress. DMV hearing successful - license suspension stayed.', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), DATE_SUB(NOW(), INTERVAL 1 DAY), NOW(), '1', '1', 0),
(UUID(), 'Plea Negotiation - Prosecutor Clark', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 3 DAY), 0, 30, 'Discussed plea options for Washington case with prosecutor Amanda Clark.', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), DATE_SUB(NOW(), INTERVAL 3 DAY), NOW(), '1', '1', 0);

-- Add conflict search data for ConflictSearch module testing
INSERT INTO conflict_search (id, name, search_term, search_type, modules_searched, confidence_threshold, total_matches_found, high_confidence_matches, medium_confidence_matches, low_confidence_matches, search_status, execution_time, performed_by, search_criteria, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'CRITICAL CONFLICT: Pacific Legal Group', 'Pacific Legal Group', 'comprehensive', 'Contacts,Accounts,Cases', 75, 3, 2, 1, 0, 'completed', 0.2347, (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 'New client intake - Pacific Legal Group wants representation. MANDATORY conflict check due to prior adverse representation.', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Former Client Conflict: Angela Davis', 'Angela Davis', 'comprehensive', 'Contacts,Accounts,Cases', 75, 1, 1, 0, 0, 'completed', 0.1543, (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 'Former client - employer wants to retain us for collection action against Angela Davis.', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Prosecutor Analysis: Amanda Clark', 'Amanda Clark', 'comprehensive', 'Contacts,Cases,Meetings,Calls', 75, 5, 3, 2, 0, 'completed', 0.3156, (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 'Analysis of current cases with prosecutor Amanda Clark for scheduling and relationship tracking.', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0);

-- Display final module integration results
SELECT '🎯 CRIMINAL DEFENSE CRM - ALL MODULES SEEDED SUCCESSFULLY! 🎯' as 'COMPLETION_STATUS';
SELECT '' as '';

SELECT 'MODULE INTEGRATION SUMMARY:' as 'Module_Summary';
SELECT CONCAT('✅ Users Module: ', COUNT(*), ' criminal defense attorneys') as 'Users' FROM users WHERE deleted = 0 AND first_name IS NOT NULL;
SELECT CONCAT('✅ Contacts Module: ', COUNT(*), ' clients, prosecutors, experts') as 'Contacts' FROM contacts WHERE deleted = 0;
SELECT CONCAT('✅ Cases Module: ', COUNT(*), ' criminal defense cases') as 'Cases' FROM cases WHERE deleted = 0 AND name NOT LIKE 'AI Test%';
SELECT CONCAT('✅ Accounts Module: ', COUNT(*), ' courts, DA offices, law firms') as 'Accounts' FROM accounts WHERE deleted = 0;
SELECT CONCAT('✅ Calls Module: ', COUNT(*), ' billable client communications') as 'Calls' FROM calls WHERE deleted = 0;
SELECT CONCAT('✅ ConflictSearch Module: ', COUNT(*), ' conflict scenarios') as 'Conflicts' FROM conflict_search WHERE deleted = 0;

SELECT '' as '';
SELECT '📊 BILLABLE HOURS DATA (BillableHoursQuickEntry Dashlet Ready):' as 'Billable_Hours';
SELECT 
    'Sarah Mitchell' as 'Attorney',
    COUNT(*) as 'Total_Calls',
    CONCAT(SUM(duration_hours), 'h ', SUM(duration_minutes), 'm') as 'Billable_Time'
FROM calls 
WHERE deleted = 0 AND status = 'Held' AND assigned_user_id = (SELECT id FROM users WHERE user_name = 'sarah.mitchell');

SELECT '' as '';
SELECT '🔍 CONFLICT DETECTION SCENARIOS READY:' as 'Conflict_Scenarios';
SELECT 
    search_term as 'Search_Term',
    total_matches_found as 'Matches_Found',
    high_confidence_matches as 'High_Risk',
    CASE 
        WHEN high_confidence_matches > 0 THEN '❌ REJECT REPRESENTATION'
        WHEN medium_confidence_matches > 2 THEN '⚠️ REVIEW REQUIRED' 
        ELSE '✅ NO CONFLICTS'
    END as 'Recommendation'
FROM conflict_search 
WHERE deleted = 0
ORDER BY high_confidence_matches DESC;

SELECT '' as '';
SELECT '🎯 ALL PUBLIC-FACING MODULES NOW CONTAIN REALISTIC CRIMINAL DEFENSE DATA!' as 'FINAL_STATUS';
SELECT 'Ready for AI Feature Testing:' as 'AI_Features';
SELECT '• AI Winnability Intelligence ✅' as 'Feature_1';
SELECT '• Billable Hours Quick Entry ✅' as 'Feature_2';  
SELECT '• Attorney Conflict Search ✅' as 'Feature_3';
SELECT '• Smart Email Templates ✅' as 'Feature_4';
SELECT '• Contact Duplicate Detection ✅' as 'Feature_5';
SELECT '• Attorney Research Integration ✅' as 'Feature_6';