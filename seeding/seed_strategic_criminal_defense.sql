-- Strategic Criminal Defense Attorney CRM - Relationship-Based Data Seeder
-- This creates a realistic ecosystem of interconnected data

-- ====================
-- STEP 1: CREATE USERS (Law Firm Staff)
-- ====================

INSERT INTO users (
    id, user_name, first_name, last_name, title, department, is_admin, 
    status, employee_status, sugar_login, receive_notifications, show_on_employees,
    phone_work, address_street, address_city, address_state, address_country, address_postalcode,
    date_entered, date_modified, created_by, modified_user_id, deleted, user_hash
) VALUES 
-- Senior Partners (Handle complex cases)
('sarah-mitchell-001', 'sarah.mitchell', 'Sarah', 'Mitchell', 'Senior Partner', 'Criminal Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0101', '1234 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
('robert-chen-002', 'robert.chen', 'Robert', 'Chen', 'Senior Partner', 'White Collar Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0102', '1235 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
('maria-rodriguez-003', 'maria.rodriguez', 'Maria', 'Rodriguez', 'Managing Partner', 'Criminal Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0103', '1236 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),

-- Associates (Handle routine cases)  
('david-thompson-004', 'david.thompson', 'David', 'Thompson', 'Associate Attorney', 'DUI/Traffic Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0104', '1237 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
('jennifer-williams-005', 'jennifer.williams', 'Jennifer', 'Williams', 'Associate Attorney', 'Drug Crimes Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0105', '1238 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
('michael-johnson-006', 'michael.johnson', 'Michael', 'Johnson', 'Associate Attorney', 'Assault/Battery Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0106', '1239 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
('lisa-anderson-007', 'lisa.anderson', 'Lisa', 'Anderson', 'Associate Attorney', 'Domestic Violence Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0107', '1240 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),

-- Junior Staff
('james-brown-008', 'james.brown', 'James', 'Brown', 'Junior Associate', 'General Criminal Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0108', '1241 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
('ashley-davis-009', 'ashley.davis', 'Ashley', 'Davis', 'Junior Associate', 'Appeals', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0109', '1242 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),

-- Support Staff
('michelle-taylor-010', 'michelle.taylor', 'Michelle', 'Taylor', 'Senior Paralegal', 'Case Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0110', '1243 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
('steven-jackson-011', 'steven.jackson', 'Steven', 'Jackson', 'Paralegal', 'Discovery Support', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0111', '1244 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
('rebecca-white-012', 'rebecca.white', 'Rebecca', 'White', 'Paralegal', 'Client Intake', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0112', '1245 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),

-- Administrative
('patricia-garcia-013', 'patricia.garcia', 'Patricia', 'Garcia', 'Office Manager', 'Administration', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0113', '1246 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
('thomas-martinez-014', 'thomas.martinez', 'Thomas', 'Martinez', 'Legal Secretary', 'Document Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0114', '1247 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
('alex-cruz-015', 'alex.cruz', 'Alex', 'Cruz', 'System Administrator', 'CRM Management', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0115', '1248 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123');

-- ====================
-- STEP 2: CREATE ACCOUNTS (External Organizations)
-- ====================

INSERT INTO accounts (
    id, name, account_type, industry, phone_office, 
    billing_address_street, billing_address_city, billing_address_state, billing_address_country, billing_address_postalcode,
    date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted
) VALUES
-- Courts
('la-superior-court-001', 'Los Angeles County Superior Court', 'Court', 'Government', '(213) 974-5411', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', 'sarah-mitchell-001', 0),
('beverly-hills-court-002', 'Beverly Hills Municipal Court', 'Court', 'Government', '(310) 285-2400', '9355 Burton Way', 'Beverly Hills', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 'robert-chen-002', 0),
('santa-monica-court-003', 'Santa Monica Courthouse', 'Court', 'Government', '(310) 260-3644', '1725 Main St', 'Santa Monica', 'CA', 'USA', '90401', NOW(), NOW(), '1', '1', 'maria-rodriguez-003', 0),

-- Prosecutor Offices
('la-district-attorney-004', 'Los Angeles County District Attorney', 'Prosecutor Office', 'Government', '(213) 974-3512', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', 'david-thompson-004', 0),
('beverly-hills-ca-005', 'Beverly Hills City Attorney', 'Prosecutor Office', 'Government', '(310) 285-2400', '455 N Rexford Dr', 'Beverly Hills', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 'jennifer-williams-005', 0),

-- Opposing Law Firms (for conflict detection)
('morrison-associates-006', 'Morrison & Associates', 'Law Firm', 'Legal Services', '(310) 555-9001', '2000 Avenue of the Stars', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', 'michael-johnson-006', 0),
('pacific-legal-group-007', 'Pacific Legal Group', 'Law Firm', 'Legal Services', '(310) 555-9002', '1901 Avenue of the Stars', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', 'lisa-anderson-007', 0),

-- Expert Witnesses
('forensic-associates-008', 'Forensic Analysis Associates', 'Expert Witness', 'Professional Services', '(310) 555-9003', '1800 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', 'james-brown-008', 0),
('medical-experts-009', 'Medical Expert Consultants', 'Expert Witness', 'Professional Services', '(310) 555-9004', '1801 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', 'ashley-davis-009', 0),

-- Bail Bond Companies
('liberty-bail-010', 'Liberty Bail Bonds', 'Bail Bonds', 'Financial Services', '(213) 555-9005', '500 S Spring St', 'Los Angeles', 'CA', 'USA', '90013', NOW(), NOW(), '1', '1', 'michelle-taylor-010', 0);

-- ====================
-- STEP 3: CREATE CONTACTS WITH STRATEGIC RELATIONSHIPS
-- ====================

-- CURRENT ACTIVE CLIENTS (have ongoing cases)
INSERT INTO contacts (
    id, first_name, last_name, title, phone_mobile, phone_home, email1,
    primary_address_street, primary_address_city, primary_address_state, primary_address_country, primary_address_postalcode,
    date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted
) VALUES
('client-active-001', 'Marcus', 'Washington', 'Active Client', '(323) 555-1001', '(323) 555-2001', 'marcus.washington@gmail.com', '1201 Crenshaw Blvd', 'Los Angeles', 'CA', 'USA', '90019', NOW(), NOW(), '1', '1', 'sarah-mitchell-001', 0),
('client-active-002', 'Jennifer', 'Lopez', 'Active Client', '(213) 555-1002', '(213) 555-2002', 'jennifer.lopez@yahoo.com', '3245 Whittier Blvd', 'Los Angeles', 'CA', 'USA', '90023', NOW(), NOW(), '1', '1', 'david-thompson-004', 0),
('client-active-003', 'Robert', 'Kim', 'Active Client', '(626) 555-1003', '(626) 555-2003', 'robert.kim@hotmail.com', '945 N Broadway', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', 'jennifer-williams-005', 0),
('client-active-004', 'Maria', 'Santos', 'Active Client', '(310) 555-1004', '(310) 555-2004', 'maria.santos@gmail.com', '8765 Sunset Blvd', 'West Hollywood', 'CA', 'USA', '90069', NOW(), NOW(), '1', '1', 'michael-johnson-006', 0),
('client-active-005', 'David', 'Johnson', 'Active Client', '(818) 555-1005', '(818) 555-2005', 'david.johnson@outlook.com', '12456 Ventura Blvd', 'Studio City', 'CA', 'USA', '91604', NOW(), NOW(), '1', '1', 'lisa-anderson-007', 0),

-- FORMER CLIENTS (closed cases - important for conflict detection)
('client-former-006', 'Angela', 'Davis', 'Former Client', '(310) 555-1006', '(310) 555-2006', 'angela.davis@gmail.com', '2134 MLK Jr Blvd', 'Los Angeles', 'CA', 'USA', '90062', DATE_SUB(NOW(), INTERVAL 180 DAY), NOW(), '1', '1', 'sarah-mitchell-001', 0),
('client-former-007', 'Michael', 'Brown', 'Former Client', '(323) 555-1007', '(323) 555-2007', 'michael.brown@yahoo.com', '5678 Vermont Ave', 'Los Angeles', 'CA', 'USA', '90037', DATE_SUB(NOW(), INTERVAL 365 DAY), NOW(), '1', '1', 'robert-chen-002', 0),
('client-former-008', 'Lisa', 'Rodriguez', 'Former Client', '(213) 555-1008', '(213) 555-2008', 'lisa.rodriguez@hotmail.com', '7890 Olympic Blvd', 'Los Angeles', 'CA', 'USA', '90015', DATE_SUB(NOW(), INTERVAL 90 DAY), NOW(), '1', '1', 'david-thompson-004', 0),

-- POTENTIAL CLIENTS (consultations scheduled, no cases yet)
('client-potential-009', 'James', 'Wilson', 'Potential Client', '(424) 555-1009', '(424) 555-2009', 'james.wilson@gmail.com', '3456 Pico Blvd', 'Santa Monica', 'CA', 'USA', '90405', NOW(), NOW(), '1', '1', 'jennifer-williams-005', 0),
('client-potential-010', 'Sandra', 'Martinez', 'Potential Client', '(562) 555-1010', '(562) 555-2010', 'sandra.martinez@yahoo.com', '9876 Atlantic Ave', 'South Gate', 'CA', 'USA', '90280', NOW(), NOW(), '1', '1', 'michael-johnson-006', 0),

-- OPPOSING PARTIES (people we've opposed in cases - major conflict risk)
('opposing-party-011', 'Thomas', 'Anderson', 'Opposing Party', '(310) 555-1011', '(310) 555-2011', 'thomas.anderson@email.com', '1234 Beverly Dr', 'Beverly Hills', 'CA', 'USA', '90210', DATE_SUB(NOW(), INTERVAL 120 DAY), NOW(), '1', '1', 'maria-rodriguez-003', 0),
('opposing-party-012', 'Patricia', 'White', 'Opposing Party', '(213) 555-1012', '(213) 555-2012', 'patricia.white@email.com', '5678 Figueroa St', 'Los Angeles', 'CA', 'USA', '90037', DATE_SUB(NOW(), INTERVAL 200 DAY), NOW(), '1', '1', 'lisa-anderson-007', 0),

-- FAMILY MEMBERS (related to clients - potential conflicts)
('family-member-013', 'Carlos', 'Santos', 'Family Member', '(310) 555-1013', '(310) 555-2013', 'carlos.santos@gmail.com', '8765 Sunset Blvd', 'West Hollywood', 'CA', 'USA', '90069', NOW(), NOW(), '1', '1', 'michael-johnson-006', 0), -- Brother of Maria Santos
('family-member-014', 'Michelle', 'Johnson', 'Family Member', '(818) 555-1014', '(818) 555-2014', 'michelle.johnson@outlook.com', '12456 Ventura Blvd', 'Studio City', 'CA', 'USA', '91604', NOW(), NOW(), '1', '1', 'lisa-anderson-007', 0), -- Wife of David Johnson

-- PROSECUTORS (opposing counsel)
('prosecutor-015', 'Amanda', 'Clark', 'Prosecutor', '(213) 974-1001', '(213) 974-2001', 'amanda.clark@da.lacounty.gov', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', 'sarah-mitchell-001', 0),
('prosecutor-016', 'Mark', 'Stevens', 'Prosecutor', '(213) 974-1002', '(213) 974-2002', 'mark.stevens@da.lacounty.gov', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', 'robert-chen-002', 0),
('prosecutor-017', 'Diana', 'Rodriguez', 'Prosecutor', '(310) 285-1001', '(310) 285-2001', 'diana.rodriguez@beverlyhills.org', '455 N Rexford Dr', 'Beverly Hills', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 'maria-rodriguez-003', 0),

-- JUDGES
('judge-018', 'Hon. William', 'Foster', 'Judge', '(213) 974-1003', '(213) 974-2003', 'chambers.foster@lacourt.org', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', 'david-thompson-004', 0),
('judge-019', 'Hon. Margaret', 'Barnes', 'Judge', '(213) 974-1004', '(213) 974-2004', 'chambers.barnes@lacourt.org', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', 'jennifer-williams-005', 0),

-- EXPERT WITNESSES
('expert-020', 'Dr. Alan', 'Peterson', 'Expert Witness', '(310) 555-1020', '(310) 555-2020', 'dr.peterson@forensics.com', '1800 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', 'michael-johnson-006', 0),
('expert-021', 'Dr. Nancy', 'Cooper', 'Expert Witness', '(310) 555-1021', '(310) 555-2021', 'dr.cooper@medical.com', '1801 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', 'lisa-anderson-007', 0);

-- ====================
-- STEP 4: CREATE CASES WITH STRATEGIC RELATIONSHIPS
-- ====================

INSERT INTO cases (
    id, name, type, description, priority, status, state,
    ai_confidence_score, ai_suggested_status, ai_last_analysis, ai_analysis_factors, ai_status_needs_review,
    date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted
) VALUES

-- ACTIVE CASES (Current clients with ongoing cases)
('case-active-001', 'People v. Washington - Felony Drug Trafficking', 'Drug Trafficking', 'Marcus Washington charged with large-scale cocaine distribution. Search warrant validity questioned. Client maintains innocence.', 'High', 'Pre_Trial', 'Open', 0.65, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 3 DAY), 'Search warrant timing suspicious, CI reliability questionable, large quantity suggests trafficking intent', 1, DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), '1', '1', 'sarah-mitchell-001', 0),

('case-active-002', 'People v. Lopez - DUI Second Offense', 'DUI/DWI', 'Jennifer Lopez arrested for DUI - second offense within 5 years. BAC 0.15. Prior conviction complicates case.', 'Medium', 'Plea_Negotiation', 'Open', 0.72, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 2 DAY), 'High BAC concerning, but breath test machine calibration records missing for 30-day period', 0, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), '1', '1', 'david-thompson-004', 0),

('case-active-003', 'People v. Kim - Possession with Intent', 'Drug Possession', 'Robert Kim found with 2oz methamphetamine during traffic stop. Intent to distribute charges added based on packaging.', 'High', 'Investigation', 'Open', 0.58, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 5 DAY), 'Traffic stop pretext questionable, packaging suggests personal use not distribution, client cooperative', 1, DATE_SUB(NOW(), INTERVAL 60 DAY), NOW(), '1', '1', 'jennifer-williams-005', 0),

('case-active-004', 'People v. Santos - Domestic Violence', 'Domestic Violence', 'Maria Santos charged with DV against estranged husband Carlos Santos. Conflicting witness statements and history of mutual abuse.', 'High', 'Pre_Trial', 'Open', 0.49, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 7 DAY), 'Self-defense claim viable, photos show mutual injuries, 911 calls support client story', 1, DATE_SUB(NOW(), INTERVAL 75 DAY), NOW(), '1', '1', 'michael-johnson-006', 0),

('case-active-005', 'People v. D. Johnson - Assault with Deadly Weapon', 'Assault', 'David Johnson charged with ADW after bar fight. Video surveillance available but quality poor. Self-defense claimed.', 'Medium', 'Trial_Pending', 'Open', 0.61, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 4 DAY), 'Video evidence inconclusive, multiple witnesses support self-defense, victim had reputation for violence', 0, DATE_SUB(NOW(), INTERVAL 50 DAY), NOW(), '1', '1', 'lisa-anderson-007', 0),

-- CLOSED CASES (Former clients - important for conflict detection)
('case-closed-006', 'People v. Davis - Embezzlement RESOLVED', 'White Collar Crime', 'Angela Davis charged with embezzling $75K from employer. Case resolved with plea agreement and restitution.', 'High', 'Closed_Won', 'Closed', 0.85, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 180 DAY), 'Strong plea negotiation resulted in probation and full restitution, client avoided prison time', 0, DATE_SUB(NOW(), INTERVAL 200 DAY), DATE_SUB(NOW(), INTERVAL 180 DAY), '1', '1', 'sarah-mitchell-001', 0),

('case-closed-007', 'People v. M. Brown - Tax Evasion RESOLVED', 'Tax Evasion', 'Michael Brown federal tax evasion case. Negotiated settlement with IRS avoided criminal charges.', 'High', 'Dismissed', 'Closed', 0.90, 'Dismissed', DATE_SUB(NOW(), INTERVAL 365 DAY), 'Civil settlement with IRS, criminal charges dropped, client fully compliant now', 0, DATE_SUB(NOW(), INTERVAL 400 DAY), DATE_SUB(NOW(), INTERVAL 365 DAY), '1', '1', 'robert-chen-002', 0),

('case-closed-008', 'People v. L. Rodriguez - DUI RESOLVED', 'DUI/DWI', 'Lisa Rodriguez DUI case. Breath test suppressed due to procedural violations. Charges reduced to reckless.', 'Low', 'Closed_Won', 'Closed', 0.88, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 90 DAY), 'Procedural violations led to evidence suppression, excellent outcome for client', 0, DATE_SUB(NOW(), INTERVAL 120 DAY), DATE_SUB(NOW(), INTERVAL 90 DAY), '1', '1', 'david-thompson-004', 0),

-- CONFLICTED CASES (Cases involving opposing parties - demonstrates conflict detection)
('case-conflict-009', 'Anderson v. Pacific Legal - Civil Lawsuit', 'Civil Litigation', 'Thomas Anderson sued Pacific Legal Group for malpractice. We represented Anderson. Now Pacific Legal wants to hire us - CONFLICT!', 'Medium', 'Closed_Won', 'Closed', 0.92, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 120 DAY), 'Successful malpractice claim, client received substantial settlement', 0, DATE_SUB(NOW(), INTERVAL 150 DAY), DATE_SUB(NOW(), INTERVAL 120 DAY), '1', '1', 'maria-rodriguez-003', 0),

('case-conflict-010', 'People v. White - Fraud Case', 'Fraud', 'Patricia White charged with insurance fraud. We successfully defended. Now her ex-husband wants representation against her - CONFLICT!', 'Medium', 'Dismissed', 'Closed', 0.89, 'Dismissed', DATE_SUB(NOW(), INTERVAL 200 DAY), 'Insurance company investigation flawed, charges dismissed, client reputation restored', 0, DATE_SUB(NOW(), INTERVAL 230 DAY), DATE_SUB(NOW(), INTERVAL 200 DAY), '1', '1', 'lisa-anderson-007', 0);

-- ====================
-- STEP 5: CREATE CONTACT-CASE RELATIONSHIPS
-- ====================

-- Link active clients to their active cases
INSERT INTO contacts_cases (id, contact_id, case_id, date_modified, deleted) VALUES
(UUID(), 'client-active-001', 'case-active-001', NOW(), 0), -- Marcus Washington -> Drug Trafficking
(UUID(), 'client-active-002', 'case-active-002', NOW(), 0), -- Jennifer Lopez -> DUI
(UUID(), 'client-active-003', 'case-active-003', NOW(), 0), -- Robert Kim -> Drug Possession  
(UUID(), 'client-active-004', 'case-active-004', NOW(), 0), -- Maria Santos -> DV
(UUID(), 'client-active-005', 'case-active-005', NOW(), 0), -- David Johnson -> Assault

-- Link family member to Maria Santos case (creates conflict scenario)
(UUID(), 'family-member-013', 'case-active-004', NOW(), 0), -- Carlos Santos (brother) -> DV case

-- Link former clients to their closed cases  
(UUID(), 'client-former-006', 'case-closed-006', NOW(), 0), -- Angela Davis -> Embezzlement
(UUID(), 'client-former-007', 'case-closed-007', NOW(), 0), -- Michael Brown -> Tax Evasion
(UUID(), 'client-former-008', 'case-closed-008', NOW(), 0), -- Lisa Rodriguez -> DUI

-- Link opposing parties to conflict cases
(UUID(), 'opposing-party-011', 'case-conflict-009', NOW(), 0), -- Thomas Anderson -> Civil case
(UUID(), 'opposing-party-012', 'case-conflict-010', NOW(), 0); -- Patricia White -> Fraud case

-- ====================
-- STEP 6: CREATE CONFLICT SEARCH DATA
-- ====================

INSERT INTO conflict_search (
    id, name, search_term, search_type, modules_searched, confidence_threshold,
    total_matches_found, high_confidence_matches, medium_confidence_matches, low_confidence_matches,
    search_status, execution_time, performed_by, search_criteria, search_results,
    date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted
) VALUES

-- Successful conflict detection - Pacific Legal Group
('conflict-001', 'Conflict Search: Pacific Legal Group', 'Pacific Legal Group', 'comprehensive', 'Contacts,Accounts,Cases', 75, 3, 2, 1, 0, 'completed', 0.2347, 'sarah-mitchell-001', 'New client intake - opposing counsel check', 
'{"high_confidence": [{"type": "Account", "name": "Pacific Legal Group", "conflict_type": "Opposing Counsel", "case": "Anderson v. Pacific Legal"}], "medium_confidence": [{"type": "Contact", "name": "Thomas Anderson", "relationship": "Former Client vs. Potential Opposing Party"}]}', 
NOW(), NOW(), '1', '1', 'sarah-mitchell-001', 0),

-- Family relationship conflict detected
('conflict-002', 'Conflict Search: Carlos Santos', 'Carlos Santos', 'comprehensive', 'Contacts,Accounts,Cases', 75, 2, 1, 1, 0, 'completed', 0.1892, 'michael-johnson-006', 'New DV case intake - family member check',
'{"high_confidence": [{"type": "Contact", "name": "Carlos Santos", "conflict_type": "Family Member", "related_case": "People v. Santos - Domestic Violence", "relationship": "Brother of opposing party"}]}',
NOW(), NOW(), '1', '1', 'michael-johnson-006', 0),

-- Former client conflict - clear match
('conflict-003', 'Conflict Search: Angela Davis', 'Angela Davis', 'comprehensive', 'Contacts,Accounts,Cases', 75, 1, 1, 0, 0, 'completed', 0.1543, 'robert-chen-002', 'Opposing counsel wants to hire us against former client',
'{"high_confidence": [{"type": "Contact", "name": "Angela Davis", "conflict_type": "Former Client", "case": "People v. Davis - Embezzlement RESOLVED", "attorney": "Sarah Mitchell"}]}',
NOW(), NOW(), '1', '1', 'robert-chen-002', 0),

-- Prosecutor relationship tracking
('conflict-004', 'Conflict Search: Amanda Clark', 'Amanda Clark', 'comprehensive', 'Contacts,Accounts,Cases', 75, 5, 3, 2, 0, 'completed', 0.3156, 'david-thompson-004', 'Check prosecutor caseload and relationships',
'{"high_confidence": [{"type": "Contact", "name": "Amanda Clark", "role": "Prosecutor", "cases_opposed": ["People v. Washington", "People v. Kim"], "frequency": "Regular opposing counsel"}]}',
NOW(), NOW(), '1', '1', 'david-thompson-004', 0),

-- Expert witness availability and conflicts
('conflict-005', 'Conflict Search: Dr. Peterson', 'Dr. Peterson', 'comprehensive', 'Contacts,Accounts,Cases', 75, 2, 1, 1, 0, 'completed', 0.2089, 'jennifer-williams-005', 'Expert witness conflict check for new case',
'{"medium_confidence": [{"type": "Contact", "name": "Dr. Alan Peterson", "conflict_type": "Expert Witness", "availability": "Currently retained by opposing counsel in similar case type"}]}',
NOW(), NOW(), '1', '1', 'jennifer-williams-005', 0);

-- ====================
-- STEP 7: CREATE SAMPLE ACTIVITIES FOR BILLABLE HOURS
-- ====================

-- Client consultations
INSERT INTO calls (
    id, name, status, direction, date_start, duration_hours, duration_minutes,
    parent_type, parent_id, assigned_user_id,
    date_entered, date_modified, created_by, modified_user_id, deleted
) VALUES
(UUID(), 'Client Consultation - Marcus Washington Drug Case', 'Held', 'Inbound', DATE_SUB(NOW(), INTERVAL 2 DAY), 1, 30, 'Cases', 'case-active-001', 'sarah-mitchell-001', NOW(), NOW(), '1', '1', 0),
(UUID(), 'Strategy Discussion - Jennifer Lopez DUI', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 1 DAY), 0, 45, 'Cases', 'case-active-002', 'david-thompson-004', NOW(), NOW(), '1', '1', 0),
(UUID(), 'Client Update - Robert Kim Case Status', 'Held', 'Outbound', NOW(), 0, 30, 'Cases', 'case-active-003', 'jennifer-williams-005', NOW(), NOW(), '1', '1', 0);

-- Court hearings and meetings
INSERT INTO meetings (
    id, name, status, date_start, duration_hours, duration_minutes,
    parent_type, parent_id, assigned_user_id,
    date_entered, date_modified, created_by, modified_user_id, deleted
) VALUES
(UUID(), 'Pre-Trial Hearing - People v. Washington', 'Held', DATE_SUB(NOW(), INTERVAL 5 DAY), 2, 0, 'Cases', 'case-active-001', 'sarah-mitchell-001', NOW(), NOW(), '1', '1', 0),
(UUID(), 'Plea Negotiation - People v. Lopez', 'Planned', DATE_ADD(NOW(), INTERVAL 7 DAY), 1, 30, 'Cases', 'case-active-002', 'david-thompson-004', NOW(), NOW(), '1', '1', 0),
(UUID(), 'Motion Hearing - People v. Kim Evidence Suppression', 'Planned', DATE_ADD(NOW(), INTERVAL 14 DAY), 3, 0, 'Cases', 'case-active-003', 'jennifer-williams-005', NOW(), NOW(), '1', '1', 0);

-- Research and preparation tasks
INSERT INTO tasks (
    id, name, status, priority, date_start, date_due,
    parent_type, parent_id, assigned_user_id,
    date_entered, date_modified, created_by, modified_user_id, deleted
) VALUES
(UUID(), 'Research Search Warrant Validity - Washington Case', 'In Progress', 'High', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 'Cases', 'case-active-001', 'steven-jackson-011', NOW(), NOW(), '1', '1', 0),
(UUID(), 'Prepare Motion to Suppress Breath Test - Lopez', 'Completed', 'Medium', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 'Cases', 'case-active-002', 'rebecca-white-012', NOW(), NOW(), '1', '1', 0),
(UUID(), 'Interview Witnesses - Kim Traffic Stop', 'Pending', 'High', NOW(), DATE_ADD(NOW(), INTERVAL 5 DAY), 'Cases', 'case-active-003', 'michelle-taylor-010', NOW(), NOW(), '1', '1', 0),
(UUID(), 'Prepare DV Expert Testimony - Santos Case', 'In Progress', 'High', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 10 DAY), 'Cases', 'case-active-004', 'steven-jackson-011', NOW(), NOW(), '1', '1', 0);

-- Display summary
SELECT 
    'Criminal Defense CRM - Strategic Data Seeding Complete!' as Status,
    (SELECT COUNT(*) FROM users WHERE deleted = 0 AND id != '1') as New_Users_Created,
    (SELECT COUNT(*) FROM accounts WHERE deleted = 0) as Accounts_Created,
    (SELECT COUNT(*) FROM contacts WHERE deleted = 0) as Contacts_Created,
    (SELECT COUNT(*) FROM cases WHERE deleted = 0 AND id NOT IN ('ai-test-case-001', 'ai-test-case-002')) as New_Cases_Created,
    (SELECT COUNT(*) FROM conflict_search WHERE deleted = 0) as Conflict_Searches_Created,
    (SELECT COUNT(*) FROM contacts_cases WHERE deleted = 0) as Contact_Case_Relationships,
    (SELECT COUNT(*) FROM calls WHERE deleted = 0) as Billable_Calls_Created,
    (SELECT COUNT(*) FROM meetings WHERE deleted = 0) as Court_Meetings_Created,
    (SELECT COUNT(*) FROM tasks WHERE deleted = 0) as Research_Tasks_Created;

-- Show active cases with their clients
SELECT 
    'Active Cases Overview:' as Section,
    '' as Spacer1,
    'Case Name' as Case_Name,
    'Client' as Client_Name, 
    'Attorney' as Assigned_Attorney,
    'Status' as Case_Status,
    'AI Confidence' as AI_Score;

SELECT 
    '' as Section,
    '' as Spacer1,
    c.name as Case_Name,
    CONCAT(con.first_name, ' ', con.last_name) as Client_Name,
    CONCAT(u.first_name, ' ', u.last_name) as Assigned_Attorney,
    c.status as Case_Status,
    CONCAT(ROUND(c.ai_confidence_score * 100), '%') as AI_Score
FROM cases c
JOIN contacts_cases cc ON c.id = cc.case_id
JOIN contacts con ON cc.contact_id = con.id  
JOIN users u ON c.assigned_user_id = u.id
WHERE c.state = 'Open' AND c.deleted = 0 AND cc.deleted = 0
ORDER BY c.date_entered DESC;