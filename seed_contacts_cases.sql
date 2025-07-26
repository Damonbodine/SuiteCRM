-- Insert more users first
INSERT INTO users (id, user_name, first_name, last_name, title, department, is_admin, status, employee_status, sugar_login, receive_notifications, show_on_employees, phone_work, date_entered, date_modified, created_by, modified_user_id, deleted, user_hash) VALUES
(UUID(), 'robert.chen', 'Robert', 'Chen', 'Senior Partner', 'White Collar Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0102', NOW(), NOW(), '1', '1', 0, 'sa$password'),
(UUID(), 'maria.rodriguez', 'Maria', 'Rodriguez', 'Managing Partner', 'Criminal Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0103', NOW(), NOW(), '1', '1', 0, 'sa$password'),
(UUID(), 'david.thompson', 'David', 'Thompson', 'Associate Attorney', 'DUI/Traffic Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0104', NOW(), NOW(), '1', '1', 0, 'sa$password'),
(UUID(), 'jennifer.williams', 'Jennifer', 'Williams', 'Associate Attorney', 'Drug Crimes Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0105', NOW(), NOW(), '1', '1', 0, 'sa$password'),
(UUID(), 'michael.johnson', 'Michael', 'Johnson', 'Associate Attorney', 'Assault/Battery Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0106', NOW(), NOW(), '1', '1', 0, 'sa$password'),
(UUID(), 'lisa.anderson', 'Lisa', 'Anderson', 'Associate Attorney', 'Domestic Violence Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0107', NOW(), NOW(), '1', '1', 0, 'sa$password'),
(UUID(), 'james.brown', 'James', 'Brown', 'Junior Associate', 'General Criminal Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0108', NOW(), NOW(), '1', '1', 0, 'sa$password'),
(UUID(), 'ashley.davis', 'Ashley', 'Davis', 'Junior Associate', 'Appeals', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0109', NOW(), NOW(), '1', '1', 0, 'sa$password'),
(UUID(), 'michelle.taylor', 'Michelle', 'Taylor', 'Senior Paralegal', 'Case Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0110', NOW(), NOW(), '1', '1', 0, 'sa$password'),
(UUID(), 'steven.jackson', 'Steven', 'Jackson', 'Paralegal', 'Discovery Support', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0111', NOW(), NOW(), '1', '1', 0, 'sa$password');

-- Insert Accounts
INSERT INTO accounts (id, name, account_type, industry, phone_office, billing_address_city, billing_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'Los Angeles County Superior Court', 'Court', 'Government', '(213) 974-5411', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Beverly Hills Municipal Court', 'Court', 'Government', '(310) 285-2400', 'Beverly Hills', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'robert.chen'), 0),
(UUID(), 'Los Angeles County District Attorney', 'Prosecutor Office', 'Government', '(213) 974-3512', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'maria.rodriguez'), 0),
(UUID(), 'Morrison & Associates', 'Law Firm', 'Legal Services', '(310) 555-9001', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'david.thompson'), 0),
(UUID(), 'Pacific Legal Group', 'Law Firm', 'Legal Services', '(310) 555-9002', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'jennifer.williams'), 0),
(UUID(), 'Forensic Analysis Associates', 'Expert Witness', 'Professional Services', '(310) 555-9003', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'michael.johnson'), 0),
(UUID(), 'Liberty Bail Bonds', 'Bail Bonds', 'Financial Services', '(213) 555-9005', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'lisa.anderson'), 0);

-- Insert Active Clients
INSERT INTO contacts (id, first_name, last_name, title, phone_mobile, email1, primary_address_city, primary_address_state, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES
(UUID(), 'Marcus', 'Washington', 'Active Client', '(323) 555-1001', 'marcus.washington@gmail.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Jennifer', 'Lopez', 'Active Client', '(213) 555-1002', 'jennifer.lopez@yahoo.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'david.thompson'), 0),
(UUID(), 'Robert', 'Kim', 'Active Client', '(626) 555-1003', 'robert.kim@hotmail.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'jennifer.williams'), 0),
(UUID(), 'Maria', 'Santos', 'Active Client', '(310) 555-1004', 'maria.santos@gmail.com', 'West Hollywood', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'michael.johnson'), 0),
(UUID(), 'David', 'Johnson', 'Active Client', '(818) 555-1005', 'david.johnson@outlook.com', 'Studio City', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'lisa.anderson'), 0),

-- Former Clients
(UUID(), 'Angela', 'Davis', 'Former Client', '(310) 555-1006', 'angela.davis@gmail.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 180 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Michael', 'Brown', 'Former Client', '(323) 555-1007', 'michael.brown@yahoo.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 365 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'robert.chen'), 0),
(UUID(), 'Lisa', 'Rodriguez', 'Former Client', '(213) 555-1008', 'lisa.rodriguez@hotmail.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 90 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'david.thompson'), 0),

-- Potential Clients
(UUID(), 'James', 'Wilson', 'Potential Client', '(424) 555-1009', 'james.wilson@gmail.com', 'Santa Monica', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'jennifer.williams'), 0),
(UUID(), 'Sandra', 'Martinez', 'Potential Client', '(562) 555-1010', 'sandra.martinez@yahoo.com', 'South Gate', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'michael.johnson'), 0),

-- Opposing Parties (for conflict detection)
(UUID(), 'Thomas', 'Anderson', 'Opposing Party', '(310) 555-1011', 'thomas.anderson@email.com', 'Beverly Hills', 'CA', DATE_SUB(NOW(), INTERVAL 120 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'maria.rodriguez'), 0),
(UUID(), 'Patricia', 'White', 'Opposing Party', '(213) 555-1012', 'patricia.white@email.com', 'Los Angeles', 'CA', DATE_SUB(NOW(), INTERVAL 200 DAY), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'lisa.anderson'), 0),

-- Prosecutors
(UUID(), 'Amanda', 'Clark', 'Prosecutor', '(213) 974-1001', 'amanda.clark@da.lacounty.gov', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Mark', 'Stevens', 'Prosecutor', '(213) 974-1002', 'mark.stevens@da.lacounty.gov', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'robert.chen'), 0),

-- Expert Witnesses
(UUID(), 'Dr. Alan', 'Peterson', 'Expert Witness', '(310) 555-1020', 'dr.peterson@forensics.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'michael.johnson'), 0),
(UUID(), 'Dr. Nancy', 'Cooper', 'Expert Witness', '(310) 555-1021', 'dr.cooper@medical.com', 'Los Angeles', 'CA', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'lisa.anderson'), 0);

-- Display results
SELECT 'Data seeding completed!' as Status;
SELECT COUNT(*) as Total_Users FROM users WHERE deleted = 0;
SELECT COUNT(*) as Total_Accounts FROM accounts WHERE deleted = 0;  
SELECT COUNT(*) as Total_Contacts FROM contacts WHERE deleted = 0;
SELECT title, COUNT(*) as Count FROM contacts WHERE deleted = 0 GROUP BY title;