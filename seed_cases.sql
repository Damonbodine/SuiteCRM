-- Insert Criminal Defense Cases with AI analysis
INSERT INTO cases (
    id, name, type, description, priority, status, state,
    ai_confidence_score, ai_suggested_status, ai_last_analysis, ai_analysis_factors, ai_status_needs_review,
    date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted
) VALUES

-- Active Cases
(UUID(), 'People v. Washington - Felony Drug Trafficking', 'Drug Trafficking', 'Marcus Washington charged with large-scale cocaine distribution. Search warrant validity questioned. Client maintains innocence. Undercover operation involved multiple agencies.', 'High', 'Pre_Trial', 'Open', 0.65, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 3 DAY), 'Search warrant timing suspicious, CI reliability questionable, large quantity suggests trafficking intent, client has clean record', 1, DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),

(UUID(), 'People v. Lopez - DUI Second Offense', 'DUI/DWI', 'Jennifer Lopez arrested for DUI - second offense within 5 years. BAC 0.15. Prior conviction complicates case but breath test machine calibration issues discovered.', 'Medium', 'Plea_Negotiation', 'Open', 0.72, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 2 DAY), 'High BAC concerning, but breath test machine calibration records missing for 30-day period including arrest date', 0, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'david.thompson'), 0),

(UUID(), 'People v. Kim - Possession with Intent', 'Drug Possession', 'Robert Kim found with 2oz methamphetamine during traffic stop. Intent to distribute charges added based on packaging. Traffic stop circumstances questionable.', 'High', 'Investigation', 'Open', 0.58, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 5 DAY), 'Traffic stop pretext questionable, packaging suggests personal use not distribution, client cooperative and seeking treatment', 1, DATE_SUB(NOW(), INTERVAL 60 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'jennifer.williams'), 0),

(UUID(), 'People v. Santos - Domestic Violence', 'Domestic Violence', 'Maria Santos charged with DV against estranged husband Carlos Santos. Conflicting witness statements and history of mutual abuse. Self-defense claim viable.', 'High', 'Pre_Trial', 'Open', 0.49, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 7 DAY), 'Self-defense claim viable, photos show mutual injuries, 911 calls support client story, husband has history of violence', 1, DATE_SUB(NOW(), INTERVAL 75 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'michael.johnson'), 0),

(UUID(), 'People v. D. Johnson - Assault with Deadly Weapon', 'Assault', 'David Johnson charged with ADW after bar fight. Video surveillance available but quality poor. Self-defense claimed. Multiple witnesses support client version.', 'Medium', 'Trial_Pending', 'Open', 0.61, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 4 DAY), 'Video evidence inconclusive, multiple witnesses support self-defense, victim had reputation for violence and initiated confrontation', 0, DATE_SUB(NOW(), INTERVAL 50 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'lisa.anderson'), 0),

-- Closed Cases (Former clients - important for conflict detection)
(UUID(), 'People v. Davis - Embezzlement RESOLVED', 'White Collar Crime', 'Angela Davis charged with embezzling $75K from employer. Case resolved with plea agreement and restitution. Client avoided prison time.', 'High', 'Closed_Won', 'Closed', 0.85, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 180 DAY), 'Strong plea negotiation resulted in probation and full restitution, client cooperation excellent, employer satisfied', 0, DATE_SUB(NOW(), INTERVAL 200 DAY), DATE_SUB(NOW(), INTERVAL 180 DAY), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),

(UUID(), 'People v. M. Brown - Tax Evasion RESOLVED', 'Tax Evasion', 'Michael Brown federal tax evasion case. Negotiated settlement with IRS avoided criminal charges. Civil penalties paid in full.', 'High', 'Dismissed', 'Closed', 0.90, 'Dismissed', DATE_SUB(NOW(), INTERVAL 365 DAY), 'Civil settlement with IRS, criminal charges dropped, client fully compliant now, excellent cooperation with government', 0, DATE_SUB(NOW(), INTERVAL 400 DAY), DATE_SUB(NOW(), INTERVAL 365 DAY), '1', '1', (SELECT id FROM users WHERE user_name = 'robert.chen'), 0),

(UUID(), 'People v. L. Rodriguez - DUI RESOLVED', 'DUI/DWI', 'Lisa Rodriguez DUI case. Breath test suppressed due to procedural violations. Charges reduced to reckless driving.', 'Low', 'Closed_Won', 'Closed', 0.88, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 90 DAY), 'Procedural violations led to evidence suppression, excellent outcome for client, officer training issues discovered', 0, DATE_SUB(NOW(), INTERVAL 120 DAY), DATE_SUB(NOW(), INTERVAL 90 DAY), '1', '1', (SELECT id FROM users WHERE user_name = 'david.thompson'), 0),

-- Conflict Cases (Cases involving opposing parties)
(UUID(), 'Anderson v. Pacific Legal - Civil Lawsuit RESOLVED', 'Civil Litigation', 'Thomas Anderson sued Pacific Legal Group for malpractice. We represented Anderson successfully. Now Pacific Legal wants to hire us - MAJOR CONFLICT!', 'Medium', 'Closed_Won', 'Closed', 0.92, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 120 DAY), 'Successful malpractice claim, client received substantial settlement, clear attorney misconduct proven', 0, DATE_SUB(NOW(), INTERVAL 150 DAY), DATE_SUB(NOW(), INTERVAL 120 DAY), '1', '1', (SELECT id FROM users WHERE user_name = 'maria.rodriguez'), 0),

(UUID(), 'People v. White - Insurance Fraud RESOLVED', 'Fraud', 'Patricia White charged with insurance fraud. We successfully defended her. Now her ex-husband wants representation against her in civil matter - CONFLICT!', 'Medium', 'Dismissed', 'Closed', 0.89, 'Dismissed', DATE_SUB(NOW(), INTERVAL 200 DAY), 'Insurance company investigation flawed, charges dismissed, client reputation restored, clear prosecutorial overreach', 0, DATE_SUB(NOW(), INTERVAL 230 DAY), DATE_SUB(NOW(), INTERVAL 200 DAY), '1', '1', (SELECT id FROM users WHERE user_name = 'lisa.anderson'), 0);

-- Insert some conflict search records to demonstrate the feature
INSERT INTO conflict_search (
    id, name, search_term, search_type, modules_searched, confidence_threshold,
    total_matches_found, high_confidence_matches, medium_confidence_matches, low_confidence_matches,
    search_status, execution_time, performed_by, search_criteria,
    date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted
) VALUES

(UUID(), 'Conflict Search: Pacific Legal Group', 'Pacific Legal Group', 'comprehensive', 'Contacts,Accounts,Cases', 75, 3, 2, 1, 0, 'completed', 0.2347, (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 'New client intake - opposing counsel check', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),

(UUID(), 'Conflict Search: Carlos Santos', 'Carlos Santos', 'comprehensive', 'Contacts,Accounts,Cases', 75, 2, 1, 1, 0, 'completed', 0.1892, (SELECT id FROM users WHERE user_name = 'michael.johnson'), 'New DV case intake - family member check', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'michael.johnson'), 0),

(UUID(), 'Conflict Search: Angela Davis', 'Angela Davis', 'comprehensive', 'Contacts,Accounts,Cases', 75, 1, 1, 0, 0, 'completed', 0.1543, (SELECT id FROM users WHERE user_name = 'robert.chen'), 'Opposing counsel wants to hire us against former client', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'robert.chen'), 0),

(UUID(), 'Conflict Search: Amanda Clark', 'Amanda Clark', 'comprehensive', 'Contacts,Accounts,Cases', 75, 5, 3, 2, 0, 'completed', 0.3156, (SELECT id FROM users WHERE user_name = 'david.thompson'), 'Check prosecutor caseload and relationships', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'david.thompson'), 0);

-- Display final results
SELECT 'Criminal Defense CRM Seeding Complete!' as Status;
SELECT COUNT(*) as Total_Users FROM users WHERE deleted = 0;
SELECT COUNT(*) as Total_Accounts FROM accounts WHERE deleted = 0;  
SELECT COUNT(*) as Total_Contacts FROM contacts WHERE deleted = 0;
SELECT COUNT(*) as Total_Cases FROM cases WHERE deleted = 0;
SELECT COUNT(*) as Total_Conflict_Searches FROM conflict_search WHERE deleted = 0;

-- Show case summary by status
SELECT status, COUNT(*) as case_count FROM cases WHERE deleted = 0 GROUP BY status;

-- Show contact types
SELECT title, COUNT(*) as contact_count FROM contacts WHERE deleted = 0 GROUP BY title ORDER BY contact_count DESC;