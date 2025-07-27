-- Comprehensive Criminal Defense CRM Module Seeding
-- This ensures all modules and relationships are properly populated

-- ====================
-- STEP 1: ENSURE BASE DATA EXISTS  
-- ====================

-- Clear any incomplete data first
DELETE FROM users WHERE user_name IN ('sarah.mitchell', 'robert.chen', 'maria.rodriguez', 'david.thompson', 'jennifer.williams', 'michael.johnson', 'lisa.anderson', 'james.brown', 'ashley.davis', 'michelle.taylor', 'steven.jackson', 'rebecca.white', 'patricia.garcia', 'thomas.martinez', 'alex.cruz');

-- Get admin ID for assignments
SET @admin_id = (SELECT id FROM users WHERE user_name = 'admin');

-- Create comprehensive user base
INSERT INTO users (id, user_name, first_name, last_name, title, department, is_admin, status, employee_status, sugar_login, receive_notifications, show_on_employees, phone_work, phone_mobile, email1, address_street, address_city, address_state, address_country, address_postalcode, date_entered, date_modified, created_by, modified_user_id, deleted, user_hash) VALUES
-- Partners
('partner-sarah-001', 'sarah.mitchell', 'Sarah', 'Mitchell', 'Senior Partner', 'Criminal Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0101', '(310) 555-1101', 'sarah.mitchell@lawfirm.com', '1234 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
('partner-robert-002', 'robert.chen', 'Robert', 'Chen', 'Senior Partner', 'White Collar Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0102', '(310) 555-1102', 'robert.chen@lawfirm.com', '1235 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
('partner-maria-003', 'maria.rodriguez', 'Maria', 'Rodriguez', 'Managing Partner', 'Criminal Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0103', '(310) 555-1103', 'maria.rodriguez@lawfirm.com', '1236 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),

-- Associates  
('associate-david-004', 'david.thompson', 'David', 'Thompson', 'Associate Attorney', 'DUI/Traffic Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0104', '(310) 555-1104', 'david.thompson@lawfirm.com', '1237 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
('associate-jennifer-005', 'jennifer.williams', 'Jennifer', 'Williams', 'Associate Attorney', 'Drug Crimes Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0105', '(310) 555-1105', 'jennifer.williams@lawfirm.com', '1238 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
('associate-michael-006', 'michael.johnson', 'Michael', 'Johnson', 'Associate Attorney', 'Assault/Battery Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0106', '(310) 555-1106', 'michael.johnson@lawfirm.com', '1239 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
('associate-lisa-007', 'lisa.anderson', 'Lisa', 'Anderson', 'Associate Attorney', 'Domestic Violence Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0107', '(310) 555-1107', 'lisa.anderson@lawfirm.com', '1240 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),

-- Junior Staff
('junior-james-008', 'james.brown', 'James', 'Brown', 'Junior Associate', 'General Criminal Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0108', '(310) 555-1108', 'james.brown@lawfirm.com', '1241 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
('junior-ashley-009', 'ashley.davis', 'Ashley', 'Davis', 'Junior Associate', 'Appeals', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0109', '(310) 555-1109', 'ashley.davis@lawfirm.com', '1242 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),

-- Support Staff
('paralegal-michelle-010', 'michelle.taylor', 'Michelle', 'Taylor', 'Senior Paralegal', 'Case Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0110', '(310) 555-1110', 'michelle.taylor@lawfirm.com', '1243 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
('paralegal-steven-011', 'steven.jackson', 'Steven', 'Jackson', 'Paralegal', 'Discovery Support', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0111', '(310) 555-1111', 'steven.jackson@lawfirm.com', '1244 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
('paralegal-rebecca-012', 'rebecca.white', 'Rebecca', 'White', 'Paralegal', 'Client Intake', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0112', '(310) 555-1112', 'rebecca.white@lawfirm.com', '1245 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),

-- Administrative
('admin-patricia-013', 'patricia.garcia', 'Patricia', 'Garcia', 'Office Manager', 'Administration', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0113', '(310) 555-1113', 'patricia.garcia@lawfirm.com', '1246 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
('admin-thomas-014', 'thomas.martinez', 'Thomas', 'Martinez', 'Legal Secretary', 'Document Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0114', '(310) 555-1114', 'thomas.martinez@lawfirm.com', '1247 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password'),
('admin-alex-015', 'alex.cruz', 'Alex', 'Cruz', 'System Administrator', 'CRM Management', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0115', '(310) 555-1115', 'alex.cruz@lawfirm.com', '1248 Wilshire Blvd', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), @admin_id, @admin_id, 0, 'encrypted_password');

-- ====================
-- STEP 2: CREATE ACCOUNTS (External Organizations)
-- ====================

INSERT INTO accounts (id, name, account_type, industry, phone_office, phone_fax, website, email1, billing_address_street, billing_address_city, billing_address_state, billing_address_country, billing_address_postalcode, shipping_address_street, shipping_address_city, shipping_address_state, shipping_address_country, shipping_address_postalcode, description, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES

-- Courts
('court-la-superior-001', 'Los Angeles County Superior Court', 'Court', 'Government', '(213) 974-5411', '(213) 974-5412', 'www.lacourt.org', 'info@lacourt.org', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', 'Main courthouse for Los Angeles County criminal and civil matters', NOW(), NOW(), @admin_id, @admin_id, 'partner-sarah-001', 0),
('court-beverly-hills-002', 'Beverly Hills Municipal Court', 'Court', 'Government', '(310) 285-2400', '(310) 285-2401', 'www.beverlyhills.org', 'court@beverlyhills.org', '9355 Burton Way', 'Beverly Hills', 'CA', 'USA', '90210', '9355 Burton Way', 'Beverly Hills', 'CA', 'USA', '90210', 'Municipal court handling traffic and misdemeanor cases', NOW(), NOW(), @admin_id, @admin_id, 'partner-robert-002', 0),
('court-santa-monica-003', 'Santa Monica Courthouse', 'Court', 'Government', '(310) 260-3644', '(310) 260-3645', 'www.santamonica.gov', 'court@santamonica.gov', '1725 Main St', 'Santa Monica', 'CA', 'USA', '90401', '1725 Main St', 'Santa Monica', 'CA', 'USA', '90401', 'Westside courthouse for Santa Monica area cases', NOW(), NOW(), @admin_id, @admin_id, 'partner-maria-003', 0),

-- Prosecutor Offices
('prosecutor-la-da-004', 'Los Angeles County District Attorney', 'Prosecutor Office', 'Government', '(213) 974-3512', '(213) 974-3513', 'www.da.lacounty.gov', 'info@da.lacounty.gov', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', 'Main prosecutor office for Los Angeles County criminal cases', NOW(), NOW(), @admin_id, @admin_id, 'associate-david-004', 0),
('prosecutor-bh-ca-005', 'Beverly Hills City Attorney', 'Prosecutor Office', 'Government', '(310) 285-2400', '(310) 285-2401', 'www.beverlyhills.org', 'attorney@beverlyhills.org', '455 N Rexford Dr', 'Beverly Hills', 'CA', 'USA', '90210', '455 N Rexford Dr', 'Beverly Hills', 'CA', 'USA', '90210', 'City attorney handling municipal prosecutions', NOW(), NOW(), @admin_id, @admin_id, 'associate-jennifer-005', 0),

-- Opposing Law Firms (Important for conflict detection)
('law-firm-morrison-006', 'Morrison & Associates', 'Law Firm', 'Legal Services', '(310) 555-9001', '(310) 555-9101', 'www.morrisonlaw.com', 'info@morrisonlaw.com', '2000 Avenue of the Stars', 'Los Angeles', 'CA', 'USA', '90067', '2000 Avenue of the Stars', 'Los Angeles', 'CA', 'USA', '90067', 'Large law firm - frequent opposing counsel in complex cases', NOW(), NOW(), @admin_id, @admin_id, 'associate-michael-006', 0),
('law-firm-pacific-007', 'Pacific Legal Group', 'Law Firm', 'Legal Services', '(310) 555-9002', '(310) 555-9102', 'www.pacificlegal.com', 'contact@pacificlegal.com', '1901 Avenue of the Stars', 'Los Angeles', 'CA', 'USA', '90067', '1901 Avenue of the Stars', 'Los Angeles', 'CA', 'USA', '90067', 'Mid-size firm specializing in business litigation - CONFLICT RISK', NOW(), NOW(), @admin_id, @admin_id, 'associate-lisa-007', 0),
('law-firm-westside-008', 'Westside Criminal Defense', 'Law Firm', 'Legal Services', '(310) 555-9003', '(310) 555-9103', 'www.westsidedefense.com', 'info@westsidedefense.com', '11777 San Vicente Blvd', 'Los Angeles', 'CA', 'USA', '90049', '11777 San Vicente Blvd', 'Los Angeles', 'CA', 'USA', '90049', 'Competitor criminal defense firm', NOW(), NOW(), @admin_id, @admin_id, 'junior-james-008', 0),

-- Expert Witness Services
('expert-forensic-009', 'Forensic Analysis Associates', 'Expert Witness', 'Professional Services', '(310) 555-9004', '(310) 555-9104', 'www.forensicanalysis.com', 'experts@forensicanalysis.com', '1800 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', '1800 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', 'Leading forensic analysis and expert testimony services', NOW(), NOW(), @admin_id, @admin_id, 'junior-ashley-009', 0),
('expert-medical-010', 'Medical Expert Consultants', 'Expert Witness', 'Professional Services', '(310) 555-9005', '(310) 555-9105', 'www.medicalexperts.com', 'doctors@medicalexperts.com', '1801 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', '1801 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', 'Medical expert witnesses for personal injury and criminal cases', NOW(), NOW(), @admin_id, @admin_id, 'paralegal-michelle-010', 0),
('expert-financial-011', 'Financial Investigation Services', 'Expert Witness', 'Professional Services', '(310) 555-9006', '(310) 555-9106', 'www.financialinvestigation.com', 'investigators@financialinvestigation.com', '1802 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', '1802 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', 'Financial forensics and white-collar crime investigation', NOW(), NOW(), @admin_id, @admin_id, 'paralegal-steven-011', 0),

-- Service Providers
('service-bail-bonds-012', 'Liberty Bail Bonds', 'Bail Bonds', 'Financial Services', '(213) 555-9007', '(213) 555-9107', 'www.libertybail.com', 'help@libertybail.com', '500 S Spring St', 'Los Angeles', 'CA', 'USA', '90013', '500 S Spring St', 'Los Angeles', 'CA', 'USA', '90013', '24/7 bail bond services for criminal defendants', NOW(), NOW(), @admin_id, @admin_id, 'paralegal-rebecca-012', 0),
('service-investigations-013', 'Elite Private Investigations', 'Investigation', 'Professional Services', '(310) 555-9008', '(310) 555-9108', 'www.eliteinvestigations.com', 'cases@eliteinvestigations.com', '9100 Wilshire Blvd', 'Beverly Hills', 'CA', 'USA', '90212', '9100 Wilshire Blvd', 'Beverly Hills', 'CA', 'USA', '90212', 'Private investigation services for criminal defense cases', NOW(), NOW(), @admin_id, @admin_id, 'admin-patricia-013', 0);

-- ====================
-- STEP 3: CREATE CONTACTS WITH PROPER CATEGORIZATION
-- ====================

INSERT INTO contacts (id, first_name, last_name, title, salutation, phone_work, phone_mobile, phone_home, phone_other, phone_fax, email1, email2, primary_address_street, primary_address_city, primary_address_state, primary_address_country, primary_address_postalcode, alt_address_street, alt_address_city, alt_address_state, alt_address_country, alt_address_postalcode, description, birthdate, assistant, assistant_phone, lead_source, account_id, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES

-- ACTIVE CLIENTS (Currently represented)
('client-active-marcus-001', 'Marcus', 'Washington', 'Active Client', 'Mr.', '(323) 555-2001', '(323) 555-1001', '(323) 555-3001', '', '', 'marcus.washington@gmail.com', 'marcus.work@company.com', '1201 Crenshaw Blvd', 'Los Angeles', 'CA', 'USA', '90019', '1201 Crenshaw Blvd', 'Los Angeles', 'CA', 'USA', '90019', 'Active client - drug trafficking case. Cooperative and maintains innocence. Construction worker.', '1985-03-15', '', '', 'Referral', NULL, NOW(), NOW(), @admin_id, @admin_id, 'partner-sarah-001', 0),

('client-active-jennifer-002', 'Jennifer', 'Lopez', 'Active Client', 'Ms.', '(213) 555-2002', '(213) 555-1002', '(213) 555-3002', '', '', 'jennifer.lopez@yahoo.com', 'j.lopez.alt@gmail.com', '3245 Whittier Blvd', 'Los Angeles', 'CA', 'USA', '90023', '3245 Whittier Blvd', 'Los Angeles', 'CA', 'USA', '90023', 'Active client - DUI second offense. School teacher, struggling with alcohol issues. Seeking treatment.', '1978-08-22', '', '', 'Walk-in', NULL, NOW(), NOW(), @admin_id, @admin_id, 'associate-david-004', 0),

('client-active-robert-003', 'Robert', 'Kim', 'Active Client', 'Mr.', '(626) 555-2003', '(626) 555-1003', '(626) 555-3003', '', '', 'robert.kim@hotmail.com', 'robert.k.personal@gmail.com', '945 N Broadway', 'Los Angeles', 'CA', 'USA', '90012', '945 N Broadway', 'Los Angeles', 'CA', 'USA', '90012', 'Active client - drug possession case. College student, first offense. Family supportive.', '1995-11-10', '', '', 'Family Referral', NULL, NOW(), NOW(), @admin_id, @admin_id, 'associate-jennifer-005', 0),

('client-active-maria-004', 'Maria', 'Santos', 'Active Client', 'Ms.', '(310) 555-2004', '(310) 555-1004', '(310) 555-3004', '', '', 'maria.santos@gmail.com', 'maria.santos.work@restaurant.com', '8765 Sunset Blvd', 'West Hollywood', 'CA', 'USA', '90069', '2200 Safe House Ave', 'Los Angeles', 'CA', 'USA', '90028', 'Active client - domestic violence case. Restaurant manager, estranged from abusive husband. Strong self-defense claim.', '1982-06-18', '', '', 'DV Hotline Referral', NULL, NOW(), NOW(), @admin_id, @admin_id, 'associate-michael-006', 0),

('client-active-david-005', 'David', 'Johnson', 'Active Client', 'Mr.', '(818) 555-2005', '(818) 555-1005', '(818) 555-3005', '', '', 'david.johnson@outlook.com', 'david.j.work@studios.com', '12456 Ventura Blvd', 'Studio City', 'CA', 'USA', '91604', '12456 Ventura Blvd', 'Studio City', 'CA', 'USA', '91604', 'Active client - assault with deadly weapon. Film industry grip, bar fight case. Good reputation, self-defense claim.', '1988-12-03', 'Michelle Johnson', '(818) 555-4005', 'Referral', NULL, NOW(), NOW(), @admin_id, @admin_id, 'associate-lisa-007', 0),

-- FORMER CLIENTS (Successfully defended - CRITICAL for conflict detection)
('client-former-angela-006', 'Angela', 'Davis', 'Former Client', 'Ms.', '(310) 555-2006', '(310) 555-1006', '(310) 555-3006', '', '', 'angela.davis@gmail.com', 'angela.davis.personal@yahoo.com', '2134 MLK Jr Blvd', 'Los Angeles', 'CA', 'USA', '90062', '2134 MLK Jr Blvd', 'Los Angeles', 'CA', 'USA', '90062', 'FORMER CLIENT - embezzlement case successfully resolved. Accountant, full restitution made. Case closed favorably.', '1975-09-12', '', '', 'CPA Referral', NULL, DATE_SUB(NOW(), INTERVAL 200 DAY), NOW(), @admin_id, @admin_id, 'partner-sarah-001', 0),

('client-former-michael-007', 'Michael', 'Brown', 'Former Client', 'Mr.', '(323) 555-2007', '(323) 555-1007', '(323) 555-3007', '', '', 'michael.brown@yahoo.com', 'michael.brown.business@company.com', '5678 Vermont Ave', 'Los Angeles', 'CA', 'USA', '90037', '5678 Vermont Ave', 'Los Angeles', 'CA', 'USA', '90037', 'FORMER CLIENT - tax evasion case dismissed. Business owner, civil settlement reached. Excellent outcome.', '1970-04-08', '', '', 'Attorney Referral', NULL, DATE_SUB(NOW(), INTERVAL 400 DAY), NOW(), @admin_id, @admin_id, 'partner-robert-002', 0),

('client-former-lisa-008', 'Lisa', 'Rodriguez', 'Former Client', 'Ms.', '(213) 555-2008', '(213) 555-1008', '(213) 555-3008', '', '', 'lisa.rodriguez@hotmail.com', 'lisa.r.work@hospital.com', '7890 Olympic Blvd', 'Los Angeles', 'CA', 'USA', '90015', '7890 Olympic Blvd', 'Los Angeles', 'CA', 'USA', '90015', 'FORMER CLIENT - DUI case won on suppression motion. Nurse, evidence excluded due to procedural violations.', '1983-07-25', '', '', 'Medical Professional Referral', NULL, DATE_SUB(NOW(), INTERVAL 120 DAY), NOW(), @admin_id, @admin_id, 'associate-david-004', 0),

-- POTENTIAL CLIENTS (Consultations scheduled, intake in progress)
('client-potential-james-009', 'James', 'Wilson', 'Potential Client', 'Mr.', '(424) 555-2009', '(424) 555-1009', '(424) 555-3009', '', '', 'james.wilson@gmail.com', '', '3456 Pico Blvd', 'Santa Monica', 'CA', 'USA', '90405', '3456 Pico Blvd', 'Santa Monica', 'CA', 'USA', '90405', 'Potential client - consultation scheduled for theft charges. Tech worker, first offense.', '1990-01-20', '', '', 'Web Search', NULL, NOW(), NOW(), @admin_id, @admin_id, 'associate-jennifer-005', 0),

('client-potential-sandra-010', 'Sandra', 'Martinez', 'Potential Client', 'Ms.', '(562) 555-2010', '(562) 555-1010', '(562) 555-3010', '', '', 'sandra.martinez@yahoo.com', '', '9876 Atlantic Ave', 'South Gate', 'CA', 'USA', '90280', '9876 Atlantic Ave', 'South Gate', 'CA', 'USA', '90280', 'Potential client - needs representation for fraud charges. Small business owner.', '1987-05-14', '', '', 'Business Network', NULL, NOW(), NOW(), @admin_id, @admin_id, 'associate-michael-006', 0),

-- OPPOSING PARTIES (People we have opposed - MAJOR conflict risk)
('opposing-thomas-011', 'Thomas', 'Anderson', 'Opposing Party', 'Mr.', '(310) 555-2011', '(310) 555-1011', '(310) 555-3011', '', '', 'thomas.anderson@email.com', 'thomas.anderson.business@company.com', '1234 Beverly Dr', 'Beverly Hills', 'CA', 'USA', '90210', '1234 Beverly Dr', 'Beverly Hills', 'CA', 'USA', '90210', 'OPPOSING PARTY - we sued Pacific Legal Group on his behalf for malpractice. WON substantial settlement. MAJOR CONFLICT if Pacific Legal wants to hire us.', '1965-03-28', '', '', 'Legal Malpractice', 'law-firm-pacific-007', DATE_SUB(NOW(), INTERVAL 150 DAY), NOW(), @admin_id, @admin_id, 'partner-maria-003', 0),

('opposing-patricia-012', 'Patricia', 'White', 'Opposing Party', 'Ms.', '(213) 555-2012', '(213) 555-1012', '(213) 555-3012', '', '', 'patricia.white@email.com', '', '5678 Figueroa St', 'Los Angeles', 'CA', 'USA', '90037', '5678 Figueroa St', 'Los Angeles', 'CA', 'USA', '90037', 'OPPOSING PARTY - we successfully defended her against insurance fraud. CONFLICT if her ex-husband wants representation against her.', '1972-11-05', '', '', 'Insurance Defense', NULL, DATE_SUB(NOW(), INTERVAL 230 DAY), NOW(), @admin_id, @admin_id, 'associate-lisa-007', 0),

-- FAMILY MEMBERS (Related to clients - creates conflict scenarios)
('family-carlos-013', 'Carlos', 'Santos', 'Family Member', 'Mr.', '(310) 555-2013', '(310) 555-1013', '(310) 555-3013', '', '', 'carlos.santos@gmail.com', '', '8765 Sunset Blvd', 'West Hollywood', 'CA', 'USA', '90069', '1500 Different St', 'Los Angeles', 'CA', 'USA', '90028', 'FAMILY MEMBER - Brother of Maria Santos (DV case). Potential witness. Estranged husband of Maria is also Carlos Santos - CONFLICT SCENARIO.', '1980-06-18', '', '', 'Family Connection', NULL, NOW(), NOW(), @admin_id, @admin_id, 'associate-michael-006', 0),

('family-michelle-014', 'Michelle', 'Johnson', 'Family Member', 'Ms.', '(818) 555-2014', '(818) 555-1014', '(818) 555-3014', '', '', 'michelle.johnson@outlook.com', '', '12456 Ventura Blvd', 'Studio City', 'CA', 'USA', '91604', '12456 Ventura Blvd', 'Studio City', 'CA', 'USA', '91604', 'FAMILY MEMBER - Wife of David Johnson (assault case). Supportive spouse, potential character witness.', '1990-02-14', '', '', 'Family Connection', NULL, NOW(), NOW(), @admin_id, @admin_id, 'associate-lisa-007', 0),

-- PROSECUTORS (Opposing counsel in criminal cases)
('prosecutor-amanda-015', 'Amanda', 'Clark', 'Prosecutor', 'Ms.', '(213) 974-2001', '(213) 974-1001', '', '', '(213) 974-2501', 'amanda.clark@da.lacounty.gov', '', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', 'Senior Deputy District Attorney - handles major felony cases. Professional relationship, frequent opposing counsel.', '1975-08-15', 'Legal Assistant Jane', '(213) 974-1501', 'Government', 'prosecutor-la-da-004', NOW(), NOW(), @admin_id, @admin_id, 'partner-sarah-001', 0),

('prosecutor-mark-016', 'Mark', 'Stevens', 'Prosecutor', 'Mr.', '(213) 974-2002', '(213) 974-1002', '', '', '(213) 974-2502', 'mark.stevens@da.lacounty.gov', '', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', 'Deputy District Attorney - specializes in white collar crime. Handles complex financial cases.', '1968-12-22', '', '', 'Government', 'prosecutor-la-da-004', NOW(), NOW(), @admin_id, @admin_id, 'partner-robert-002', 0),

('prosecutor-diana-017', 'Diana', 'Rodriguez', 'Prosecutor', 'Ms.', '(310) 285-2001', '(310) 285-1001', '', '', '(310) 285-2501', 'diana.rodriguez@beverlyhills.org', '', '455 N Rexford Dr', 'Beverly Hills', 'CA', 'USA', '90210', '455 N Rexford Dr', 'Beverly Hills', 'CA', 'USA', '90210', 'Beverly Hills City Attorney - handles municipal prosecutions and traffic cases.', '1980-04-10', '', '', 'Government', 'prosecutor-bh-ca-005', NOW(), NOW(), @admin_id, @admin_id, 'partner-maria-003', 0),

-- JUDGES (Court officials)
('judge-william-018', 'Hon. William', 'Foster', 'Judge', 'Hon.', '(213) 974-2003', '(213) 974-1003', '', '', '(213) 974-2503', 'chambers.foster@lacourt.org', '', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', 'Superior Court Judge - Department 100. Handles major felony cases. Fair but strict on procedure.', '1955-06-12', 'Judicial Assistant Mary', '(213) 974-1503', 'Judicial', 'court-la-superior-001', NOW(), NOW(), @admin_id, @admin_id, 'associate-david-004', 0),

('judge-margaret-019', 'Hon. Margaret', 'Barnes', 'Judge', 'Hon.', '(213) 974-2004', '(213) 974-1004', '', '', '(213) 974-2504', 'chambers.barnes@lacourt.org', '', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', 'Superior Court Judge - Department 102. Former prosecutor, understanding of defense needs.', '1962-09-08', '', '', 'Judicial', 'court-la-superior-001', NOW(), NOW(), @admin_id, @admin_id, 'associate-jennifer-005', 0),

-- EXPERT WITNESSES
('expert-alan-020', 'Dr. Alan', 'Peterson', 'Expert Witness', 'Dr.', '(310) 555-2020', '(310) 555-1020', '', '', '(310) 555-2520', 'dr.peterson@forensics.com', 'alan.peterson.personal@gmail.com', '1800 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', '1800 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', 'Forensic expert - DNA analysis, crime scene reconstruction. Excellent witness, clear testimony.', '1965-02-28', 'Research Assistant Tom', '(310) 555-1520', 'Expert Network', 'expert-forensic-009', NOW(), NOW(), @admin_id, @admin_id, 'associate-michael-006', 0),

('expert-nancy-021', 'Dr. Nancy', 'Cooper', 'Expert Witness', 'Dr.', '(310) 555-2021', '(310) 555-1021', '', '', '(310) 555-2521', 'dr.cooper@medical.com', '', '1801 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', '1801 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', 'Medical expert - trauma specialist, domestic violence injuries. Strong credentials.', '1970-11-15', '', '', 'Medical Network', 'expert-medical-010', NOW(), NOW(), @admin_id, @admin_id, 'associate-lisa-007', 0),

('expert-frank-022', 'Dr. Frank', 'Lewis', 'Expert Witness', 'Dr.', '(310) 555-2022', '(310) 555-1022', '', '', '(310) 555-2522', 'dr.lewis@psych.com', '', '1803 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', '1803 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', 'Psychological expert - competency evaluations, mental health defenses.', '1958-07-20', '', '', 'Professional Network', 'expert-financial-011', NOW(), NOW(), @admin_id, @admin_id, 'junior-james-008', 0);

-- Continue with the rest of the comprehensive seeding...
-- This is getting quite long, let me break it into parts