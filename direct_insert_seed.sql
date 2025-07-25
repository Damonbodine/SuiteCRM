-- Direct Insert Criminal Defense CRM Seed - No Subqueries

-- Insert contacts directly
INSERT INTO contacts (id, first_name, last_name, title, phone_mobile, email1, primary_address_city, primary_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'Jennifer', 'Lopez', 'Active Client', '(213) 555-1002', 'jennifer.lopez@yahoo.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Robert', 'Kim', 'Active Client', '(626) 555-1003', 'robert.kim@hotmail.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Maria', 'Santos', 'Active Client', '(310) 555-1004', 'maria.santos@gmail.com', 'West Hollywood', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'David', 'Johnson', 'Active Client', '(818) 555-1005', 'david.johnson@outlook.com', 'Studio City', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Angela', 'Davis', 'Former Client', '(310) 555-1006', 'angela.davis@gmail.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 180 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'Amanda', 'Clark', 'Prosecutor', '(213) 974-1001', 'amanda.clark@da.lacounty.gov', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Thomas', 'Anderson', 'Opposing Party', '(310) 555-1011', 'thomas.anderson@email.com', 'Beverly Hills', 'CA', DATE_SUB(NOW(), INTERVAL 120 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'Dr. Alan', 'Peterson', 'Expert Witness', '(310) 555-1020', 'dr.peterson@forensics.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Michael', 'Brown', 'Former Client', '(323) 555-1007', 'michael.brown@yahoo.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 365 DAY), NOW(), '1', '1', '1', 0);

-- Insert accounts directly
INSERT INTO accounts (id, name, account_type, industry, phone_office, billing_address_city, billing_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'Los Angeles County Superior Court', 'Court', 'Government', '(213) 974-5411', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Los Angeles County District Attorney', 'Prosecutor Office', 'Government', '(213) 974-3512', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Pacific Legal Group', 'Law Firm', 'Legal Services', '(310) 555-9002', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Morrison & Associates', 'Law Firm', 'Legal Services', '(310) 555-9001', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Forensic Analysis Associates', 'Expert Witness', 'Professional Services', '(310) 555-9003', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Beverly Hills Municipal Court', 'Court', 'Government', '(310) 285-2400', 'Beverly Hills', 'CA', NOW(), NOW(), '1', '1', '1', 0);

-- Insert criminal defense cases directly
INSERT INTO cases (id, name, case_number, type, description, priority, status, state, ai_confidence_score, ai_suggested_status, ai_last_analysis, ai_analysis_factors, ai_status_needs_review, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'People v. Washington - Drug Trafficking', '2024001', 'Drug Trafficking', 'Marcus Washington charged with large-scale cocaine distribution. Search warrant validity questioned.', 'High', 'Pre_Trial', 'Open', 0.65, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 3 DAY), 'Search warrant timing suspicious, CI reliability questionable', 1, DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'People v. Lopez - DUI Second Offense', '2024002', 'DUI/DWI', 'Jennifer Lopez arrested for DUI - second offense within 5 years. BAC 0.15.', 'Medium', 'Plea_Negotiation', 'Open', 0.72, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 2 DAY), 'High BAC concerning but breath test machine calibration issues', 0, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'People v. Kim - Drug Possession', '2024003', 'Drug Possession', 'Robert Kim found with methamphetamine during questionable traffic stop.', 'High', 'Investigation', 'Open', 0.58, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 5 DAY), 'Traffic stop pretext questionable, client cooperative', 1, DATE_SUB(NOW(), INTERVAL 60 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'People v. Santos - Domestic Violence', '2024004', 'Domestic Violence', 'Maria Santos charged with DV against estranged husband. Self-defense claim viable.', 'High', 'Pre_Trial', 'Open', 0.49, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 7 DAY), 'Self-defense claim viable, photos show mutual injuries', 1, DATE_SUB(NOW(), INTERVAL 75 DAY), NOW(), '1', '1', '1', 0),
(UUID(), 'People v. Davis - Embezzlement RESOLVED', '2023015', 'White Collar Crime', 'Angela Davis embezzlement case successfully resolved with plea agreement.', 'High', 'Closed_Won', 'Closed', 0.85, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 180 DAY), 'Strong plea negotiation resulted in probation', 0, DATE_SUB(NOW(), INTERVAL 200 DAY), DATE_SUB(NOW(), INTERVAL 180 DAY), '1', '1', '1', 0);

-- Insert more billable activities
INSERT INTO calls (id, name, status, direction, date_start, duration_hours, duration_minutes, description, assigned_user_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES
(UUID(), 'Client Consultation - Washington Drug Case Strategy', 'Held', 'Inbound', DATE_SUB(NOW(), INTERVAL 2 DAY), 1, 30, 'Initial strategy discussion for drug trafficking case.', '1', DATE_SUB(NOW(), INTERVAL 2 DAY), NOW(), '1', '1', 0),
(UUID(), 'Case Update Call - Lopez DUI Progress', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 1 DAY), 0, 45, 'Updated client on plea negotiation progress.', '1', DATE_SUB(NOW(), INTERVAL 1 DAY), NOW(), '1', '1', 0),
(UUID(), 'Plea Negotiation - Prosecutor Clark', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 3 DAY), 0, 30, 'Discussed plea options for Washington case.', '1', DATE_SUB(NOW(), INTERVAL 3 DAY), NOW(), '1', '1', 0),
(UUID(), 'Expert Witness Coordination - Dr. Peterson', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 5 DAY), 1, 0, 'Consulted with forensic expert about DNA evidence.', '1', DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), '1', '1', 0);

-- Insert conflict search data
INSERT INTO conflict_search (id, name, search_term, search_type, modules_searched, confidence_threshold, total_matches_found, high_confidence_matches, medium_confidence_matches, low_confidence_matches, search_status, execution_time, performed_by, search_criteria, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'CRITICAL CONFLICT: Pacific Legal Group', 'Pacific Legal Group', 'comprehensive', 'Contacts,Accounts,Cases', 75, 3, 2, 1, 0, 'completed', 0.2347, '1', 'New client intake - Pacific Legal Group wants representation.', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Former Client Conflict: Angela Davis', 'Angela Davis', 'comprehensive', 'Contacts,Accounts,Cases', 75, 1, 1, 0, 0, 'completed', 0.1543, '1', 'Former client - employer wants to retain us.', NOW(), NOW(), '1', '1', '1', 0),
(UUID(), 'Prosecutor Analysis: Amanda Clark', 'Amanda Clark', 'comprehensive', 'Contacts,Cases,Meetings,Calls', 75, 5, 3, 2, 0, 'completed', 0.3156, '1', 'Prosecutor relationship and caseload analysis.', NOW(), NOW(), '1', '1', '1', 0);

-- Display results
SELECT '✅ CRIMINAL DEFENSE CRM DATA SEEDED SUCCESSFULLY!' as 'STATUS';
SELECT CONCAT('Contacts: ', COUNT(*)) as 'Count' FROM contacts WHERE deleted = 0;
SELECT CONCAT('Cases: ', COUNT(*)) as 'Count' FROM cases WHERE deleted = 0 AND name NOT LIKE 'AI Test%';
SELECT CONCAT('Accounts: ', COUNT(*)) as 'Count' FROM accounts WHERE deleted = 0;
SELECT CONCAT('Calls: ', COUNT(*)) as 'Count' FROM calls WHERE deleted = 0;
SELECT CONCAT('Conflict Searches: ', COUNT(*)) as 'Count' FROM conflict_search WHERE deleted = 0;