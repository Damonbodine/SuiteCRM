-- Criminal Defense Attorney CRM - Direct SQL Data Seeder
-- This creates comprehensive mock data for criminal defense law firm

-- Function to generate UUIDs (MySQL compatible)
SET @uuid_pattern = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx';

-- Insert Users (Attorneys, Paralegals, Staff)
INSERT INTO users (
    id, user_name, first_name, last_name, title, department, is_admin, 
    status, employee_status, sugar_login, receive_notifications, show_on_employees,
    phone_work, address_street, address_city, address_state, address_country, address_postalcode,
    date_entered, date_modified, created_by, modified_user_id, deleted, user_hash
) VALUES 
-- Senior Partners
(UUID(), 'sarah.mitchell', 'Sarah', 'Mitchell', 'Senior Partner', 'Criminal Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0101', '1234 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'robert.chen', 'Robert', 'Chen', 'Senior Partner', 'White Collar Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0102', '1235 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'maria.rodriguez', 'Maria', 'Rodriguez', 'Managing Partner', 'Criminal Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0103', '1236 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),

-- Associates
(UUID(), 'david.thompson', 'David', 'Thompson', 'Associate Attorney', 'DUI/Traffic Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0104', '1237 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'jennifer.williams', 'Jennifer', 'Williams', 'Associate Attorney', 'Drug Crimes Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0105', '1238 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'michael.johnson', 'Michael', 'Johnson', 'Associate Attorney', 'Assault/Battery Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0106', '1239 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'lisa.anderson', 'Lisa', 'Anderson', 'Associate Attorney', 'Domestic Violence Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0107', '1240 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'james.brown', 'James', 'Brown', 'Associate Attorney', 'Theft/Burglary Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0108', '1241 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'ashley.davis', 'Ashley', 'Davis', 'Associate Attorney', 'Fraud Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0109', '1242 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),

-- Junior Associates
(UUID(), 'christopher.miller', 'Christopher', 'Miller', 'Junior Associate', 'General Criminal Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0110', '1243 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'amanda.wilson', 'Amanda', 'Wilson', 'Junior Associate', 'Appeals', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0111', '1244 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'daniel.moore', 'Daniel', 'Moore', 'Junior Associate', 'Pre-Trial Motions', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0112', '1245 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),

-- Paralegals
(UUID(), 'michelle.taylor', 'Michelle', 'Taylor', 'Senior Paralegal', 'Case Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0113', '1246 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'steven.jackson', 'Steven', 'Jackson', 'Paralegal', 'Discovery Support', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0114', '1247 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'rebecca.white', 'Rebecca', 'White', 'Paralegal', 'Client Intake', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0115', '1248 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'kevin.harris', 'Kevin', 'Harris', 'Paralegal', 'Research Assistant', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0116', '1249 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'nicole.martin', 'Nicole', 'Martin', 'Paralegal', 'Document Preparation', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0117', '1250 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),

-- Administrative Staff
(UUID(), 'patricia.garcia', 'Patricia', 'Garcia', 'Office Manager', 'Administration', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0118', '1251 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'thomas.martinez', 'Thomas', 'Martinez', 'Legal Secretary', 'Document Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0119', '1252 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'karen.robinson', 'Karen', 'Robinson', 'Billing Coordinator', 'Financial Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0120', '1253 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'mark.clark', 'Mark', 'Clark', 'IT Coordinator', 'Technology Support', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0121', '1254 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),

-- Support Staff
(UUID(), 'linda.lewis', 'Linda', 'Lewis', 'Receptionist', 'Client Services', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0122', '1255 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'paul.walker', 'Paul', 'Walker', 'Investigator', 'Case Investigation', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0123', '1256 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'donna.hall', 'Donna', 'Hall', 'Records Clerk', 'File Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0124', '1257 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'gary.allen', 'Gary', 'Allen', 'Process Server', 'Legal Service', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0125', '1258 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),

-- System Admin
(UUID(), 'alex.cruz', 'Alex', 'Cruz', 'System Administrator', 'CRM Management', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0126', '1259 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123');

-- Insert Accounts (Courts, Law Firms, Expert Witnesses)
INSERT INTO accounts (
    id, name, account_type, industry, phone_office, 
    billing_address_street, billing_address_city, billing_address_state, billing_address_country, billing_address_postalcode,
    date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted
) VALUES
-- Courts
(UUID(), 'Los Angeles County Superior Court', 'Court', 'Government', '(213) 555-0201', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Beverly Hills Municipal Court', 'Court', 'Government', '(310) 555-0202', '9355 Burton Way', 'Beverly Hills', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'robert.chen'), 0),
(UUID(), 'Santa Monica Courthouse', 'Court', 'Government', '(310) 555-0203', '1725 Main St', 'Santa Monica', 'CA', 'USA', '90401', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'maria.rodriguez'), 0),
(UUID(), 'Van Nuys Courthouse East', 'Court', 'Government', '(818) 555-0204', '6230 Sylmar Ave', 'Van Nuys', 'CA', 'USA', '91401', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'david.thompson'), 0),
(UUID(), 'Torrance Courthouse', 'Court', 'Government', '(310) 555-0205', '825 Maple Ave', 'Torrance', 'CA', 'USA', '90503', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'jennifer.williams'), 0),

-- District Attorney Offices
(UUID(), 'Los Angeles County District Attorney', 'Prosecutor Office', 'Government', '(213) 555-0206', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'michael.johnson'), 0),
(UUID(), 'Beverly Hills City Attorney', 'Prosecutor Office', 'Government', '(310) 555-0207', '455 N Rexford Dr', 'Beverly Hills', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'lisa.anderson'), 0),
(UUID(), 'Santa Monica City Attorney', 'Prosecutor Office', 'Government', '(310) 555-0208', '1685 Main St', 'Santa Monica', 'CA', 'USA', '90401', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'james.brown'), 0),

-- Law Firms (Opposing Counsel)
(UUID(), 'Morrison & Associates', 'Law Firm', 'Legal Services', '(310) 555-0209', '2000 Avenue of the Stars', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'ashley.davis'), 0),
(UUID(), 'Pacific Legal Group', 'Law Firm', 'Legal Services', '(310) 555-0210', '1901 Avenue of the Stars', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'christopher.miller'), 0),
(UUID(), 'Westside Criminal Defense', 'Law Firm', 'Legal Services', '(310) 555-0211', '11777 San Vicente Blvd', 'Los Angeles', 'CA', 'USA', '90049', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'amanda.wilson'), 0),
(UUID(), 'Downtown Legal Partners', 'Law Firm', 'Legal Services', '(213) 555-0212', '633 W 5th St', 'Los Angeles', 'CA', 'USA', '90071', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'daniel.moore'), 0),

-- Expert Witness Services
(UUID(), 'Forensic Analysis Associates', 'Expert Witness', 'Professional Services', '(310) 555-0213', '1800 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'michelle.taylor'), 0),
(UUID(), 'Medical Expert Consultants', 'Expert Witness', 'Professional Services', '(310) 555-0214', '1801 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'steven.jackson'), 0),
(UUID(), 'Financial Investigation Services', 'Expert Witness', 'Professional Services', '(310) 555-0215', '1802 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'rebecca.white'), 0),
(UUID(), 'Psychological Evaluation Center', 'Expert Witness', 'Professional Services', '(310) 555-0216', '1803 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'kevin.harris'), 0),

-- Bail Bond Companies
(UUID(), 'Liberty Bail Bonds', 'Bail Bonds', 'Financial Services', '(213) 555-0217', '500 S Spring St', 'Los Angeles', 'CA', 'USA', '90013', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'nicole.martin'), 0),
(UUID(), 'Quick Release Bail', 'Bail Bonds', 'Financial Services', '(213) 555-0218', '501 S Spring St', 'Los Angeles', 'CA', 'USA', '90013', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'patricia.garcia'), 0),

-- Investigation Services
(UUID(), 'Elite Private Investigations', 'Investigation', 'Professional Services', '(310) 555-0219', '9100 Wilshire Blvd', 'Beverly Hills', 'CA', 'USA', '90212', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'thomas.martinez'), 0),
(UUID(), 'West Coast Investigators', 'Investigation', 'Professional Services', '(310) 555-0220', '9101 Wilshire Blvd', 'Beverly Hills', 'CA', 'USA', '90212', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'karen.robinson'), 0);

-- Insert Contacts (Clients, Prosecutors, Judges, Witnesses)
INSERT INTO contacts (
    id, first_name, last_name, title, phone_mobile, phone_home, email1,
    primary_address_street, primary_address_city, primary_address_state, primary_address_country, primary_address_postalcode,
    date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted
) VALUES
-- Clients (Criminal Defendants)
(UUID(), 'John', 'Smith', 'Client', '(310) 555-1001', '(310) 555-2001', 'john.smith@email.com', '2001 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Maria', 'Gonzalez', 'Client', '(310) 555-1002', '(310) 555-2002', 'maria.gonzalez@email.com', '2002 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'robert.chen'), 0),
(UUID(), 'Robert', 'Johnson', 'Client', '(310) 555-1003', '(310) 555-2003', 'robert.johnson@email.com', '2003 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'maria.rodriguez'), 0),
(UUID(), 'Jennifer', 'Williams', 'Client', '(310) 555-1004', '(310) 555-2004', 'jennifer.williams@email.com', '2004 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'david.thompson'), 0),
(UUID(), 'Michael', 'Brown', 'Client', '(310) 555-1005', '(310) 555-2005', 'michael.brown@email.com', '2005 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'jennifer.williams'), 0),
(UUID(), 'Lisa', 'Davis', 'Client', '(310) 555-1006', '(310) 555-2006', 'lisa.davis@email.com', '2006 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'michael.johnson'), 0),
(UUID(), 'David', 'Miller', 'Client', '(310) 555-1007', '(310) 555-2007', 'david.miller@email.com', '2007 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'lisa.anderson'), 0),
(UUID(), 'Sarah', 'Wilson', 'Client', '(310) 555-1008', '(310) 555-2008', 'sarah.wilson@email.com', '2008 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'james.brown'), 0),
(UUID(), 'James', 'Moore', 'Client', '(310) 555-1009', '(310) 555-2009', 'james.moore@email.com', '2009 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'ashley.davis'), 0),
(UUID(), 'Ashley', 'Taylor', 'Client', '(310) 555-1010', '(310) 555-2010', 'ashley.taylor@email.com', '2010 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'christopher.miller'), 0),
(UUID(), 'Christopher', 'Anderson', 'Client', '(310) 555-1011', '(310) 555-2011', 'christopher.anderson@email.com', '2011 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'amanda.wilson'), 0),
(UUID(), 'Amanda', 'Thomas', 'Client', '(310) 555-1012', '(310) 555-2012', 'amanda.thomas@email.com', '2012 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'daniel.moore'), 0),
(UUID(), 'Daniel', 'Jackson', 'Client', '(310) 555-1013', '(310) 555-2013', 'daniel.jackson@email.com', '2013 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'michelle.taylor'), 0),
(UUID(), 'Michelle', 'White', 'Client', '(310) 555-1014', '(310) 555-2014', 'michelle.white@email.com', '2014 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'steven.jackson'), 0),
(UUID(), 'Steven', 'Harris', 'Client', '(310) 555-1015', '(310) 555-2015', 'steven.harris@email.com', '2015 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'rebecca.white'), 0),
(UUID(), 'Rebecca', 'Martin', 'Client', '(310) 555-1016', '(310) 555-2016', 'rebecca.martin@email.com', '2016 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'kevin.harris'), 0),
(UUID(), 'Kevin', 'Thompson', 'Client', '(310) 555-1017', '(310) 555-2017', 'kevin.thompson@email.com', '2017 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'nicole.martin'), 0),
(UUID(), 'Nicole', 'Garcia', 'Client', '(310) 555-1018', '(310) 555-2018', 'nicole.garcia@email.com', '2018 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'patricia.garcia'), 0),
(UUID(), 'Thomas', 'Martinez', 'Client', '(310) 555-1019', '(310) 555-2019', 'thomas.martinez@email.com', '2019 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'thomas.martinez'), 0),
(UUID(), 'Karen', 'Robinson', 'Client', '(310) 555-1020', '(310) 555-2020', 'karen.robinson@email.com', '2020 Main St', 'Los Angeles', 'CA', 'USA', '90210', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'karen.robinson'), 0),

-- Prosecutors
(UUID(), 'Patricia', 'Clark', 'Prosecutor', '(213) 555-1101', '(213) 555-2101', 'patricia.clark@da.lacounty.gov', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'mark.clark'), 0),
(UUID(), 'Mark', 'Stevens', 'Prosecutor', '(213) 555-1102', '(213) 555-2102', 'mark.stevens@da.lacounty.gov', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'linda.lewis'), 0),
(UUID(), 'Linda', 'Rodriguez', 'Prosecutor', '(213) 555-1103', '(213) 555-2103', 'linda.rodriguez@da.lacounty.gov', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'paul.walker'), 0),
(UUID(), 'Paul', 'Mitchell', 'Prosecutor', '(213) 555-1104', '(213) 555-2104', 'paul.mitchell@da.lacounty.gov', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'donna.hall'), 0),
(UUID(), 'Donna', 'Chen', 'Prosecutor', '(213) 555-1105', '(213) 555-2105', 'donna.chen@da.lacounty.gov', '211 W Temple St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'gary.allen'), 0),

-- Judges
(UUID(), 'Hon. William', 'Foster', 'Judge', '(213) 555-1201', '(213) 555-2201', 'chambers@lacourt.org', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'alex.cruz'), 0),
(UUID(), 'Hon. Margaret', 'Barnes', 'Judge', '(213) 555-1202', '(213) 555-2202', 'chambers2@lacourt.org', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'sarah.mitchell'), 0),
(UUID(), 'Hon. Richard', 'Coleman', 'Judge', '(213) 555-1203', '(213) 555-2203', 'chambers3@lacourt.org', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'robert.chen'), 0),
(UUID(), 'Hon. Sandra', 'Torres', 'Judge', '(213) 555-1204', '(213) 555-2204', 'chambers4@lacourt.org', '111 N Hill St', 'Los Angeles', 'CA', 'USA', '90012', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'maria.rodriguez'), 0),

-- Expert Witnesses
(UUID(), 'Dr. Alan', 'Peterson', 'Expert Witness', '(310) 555-1301', '(310) 555-2301', 'dr.peterson@forensics.com', '1800 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'david.thompson'), 0),
(UUID(), 'Dr. Nancy', 'Cooper', 'Expert Witness', '(310) 555-1302', '(310) 555-2302', 'dr.cooper@medical.com', '1801 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'jennifer.williams'), 0),
(UUID(), 'Dr. Frank', 'Lewis', 'Expert Witness', '(310) 555-1303', '(310) 555-2303', 'dr.lewis@psych.com', '1803 Century Park East', 'Los Angeles', 'CA', 'USA', '90067', NOW(), NOW(), '1', '1', (SELECT id FROM users WHERE user_name = 'michael.johnson'), 0);

-- Now let's create some realistic criminal defense cases
-- We'll use a temporary table to get user IDs for assignment
CREATE TEMPORARY TABLE temp_users AS SELECT id, user_name FROM users WHERE user_name IN ('sarah.mitchell', 'robert.chen', 'maria.rodriguez', 'david.thompson', 'jennifer.williams', 'michael.johnson', 'lisa.anderson', 'james.brown', 'ashley.davis');

CREATE TEMPORARY TABLE temp_clients AS SELECT id, first_name, last_name FROM contacts WHERE title = 'Client';

-- Insert Criminal Defense Cases
INSERT INTO cases (
    id, name, type, description, priority, status, state,
    ai_confidence_score, ai_suggested_status, ai_last_analysis, ai_analysis_factors, ai_status_needs_review,
    date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted
) VALUES
(UUID(), 'DUI - First Offense - John Smith', 'DUI/DWI', 'Client charged with first-time DUI after traffic stop on PCH. BAC 0.12. Clean driving record.', 'Medium', 'Open_Assigned', 'Open', 0.73, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 5 DAY), 'BAC level moderate, clean record, cooperative client', 0, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'david.thompson'), 0),

(UUID(), 'Felony Drug Possession - Maria Gonzalez', 'Drug Possession', 'Client arrested with 2oz cocaine during traffic stop. Search warrant issues possible.', 'High', 'Investigation', 'Open', 0.65, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 3 DAY), 'Search warrant validity questionable, significant amount, prior clean record', 1, DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'jennifer.williams'), 0),

(UUID(), 'Domestic Violence - Robert Johnson', 'Domestic Violence', 'Client facing DV charges from ex-spouse. Conflicting witness statements.', 'High', 'Pre_Trial', 'Open', 0.58, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 7 DAY), 'Witness credibility issues, no physical evidence, history of false allegations', 1, DATE_SUB(NOW(), INTERVAL 60 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'lisa.anderson'), 0),

(UUID(), 'Armed Robbery - Jennifer Williams', 'Robbery', 'Client accused of armed robbery at 7-Eleven. Video evidence unclear.', 'High', 'Plea_Negotiation', 'Open', 0.42, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 2 DAY), 'Video quality poor, identification issues, client has alibi witness', 0, DATE_SUB(NOW(), INTERVAL 90 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'sarah.mitchell'), 0),

(UUID(), 'White Collar Fraud - Michael Brown', 'Fraud', 'Embezzlement charges - $50K from former employer. Complex financial records.', 'High', 'Investigation', 'Open', 0.71, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 1 DAY), 'Clear paper trail, cooperative client, restitution possible', 0, DATE_SUB(NOW(), INTERVAL 120 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'robert.chen'), 0),

(UUID(), 'Simple Assault - Lisa Davis', 'Assault', 'Bar fight resulting in assault charges. Self-defense claim.', 'Medium', 'Open_Assigned', 'Open', 0.68, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 4 DAY), 'Self-defense viable, multiple witnesses, minor injuries', 0, DATE_SUB(NOW(), INTERVAL 25 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'michael.johnson'), 0),

(UUID(), 'Burglary Residential - David Miller', 'Burglary', 'Client charged with breaking and entering. Fingerprint evidence disputed.', 'Medium', 'Trial_Pending', 'Open', 0.55, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 6 DAY), 'Fingerprint evidence contaminated, no witnesses, circumstantial case', 1, DATE_SUB(NOW(), INTERVAL 75 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'james.brown'), 0),

(UUID(), 'Drug Trafficking - Sarah Wilson', 'Drug Trafficking', 'Large-scale distribution allegations. Undercover operation involved.', 'High', 'Pre_Trial', 'Open', 0.48, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 8 DAY), 'Undercover operation procedures questionable, entrapment defense possible', 1, DATE_SUB(NOW(), INTERVAL 100 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'jennifer.williams'), 0),

(UUID(), 'Multiple DUI - James Moore', 'DUI/DWI', 'Third DUI offense with enhanced penalties. License suspension.', 'High', 'Plea_Negotiation', 'Open', 0.62, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 9 DAY), 'Prior offenses, high BAC, but breath test machine calibration issues', 0, DATE_SUB(NOW(), INTERVAL 15 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'david.thompson'), 0),

(UUID(), 'Identity Theft - Ashley Taylor', 'Fraud', 'Credit card fraud and identity theft charges. Multiple victims.', 'Medium', 'Investigation', 'Open', 0.59, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 12 DAY), 'Digital evidence chain of custody issues, client cooperation good', 0, DATE_SUB(NOW(), INTERVAL 35 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'robert.chen'), 0),

(UUID(), 'Weapons Violation - Christopher Anderson', 'Weapons Charge', 'Illegal firearm possession charges. Constitutional issues with search.', 'Medium', 'Open_Assigned', 'Open', 0.67, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 10 DAY), 'Fourth Amendment violation likely, illegal search and seizure', 0, DATE_SUB(NOW(), INTERVAL 20 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'sarah.mitchell'), 0),

(UUID(), 'Cybercrime Case - Amanda Thomas', 'Cybercrime', 'Computer fraud and hacking allegations. Federal charges possible.', 'High', 'Investigation', 'Open', 0.53, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 14 DAY), 'Federal jurisdiction issues, technical evidence complex, expert witnesses needed', 1, DATE_SUB(NOW(), INTERVAL 50 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'ashley.davis'), 0),

(UUID(), 'Traffic Appeal - Daniel Jackson', 'Traffic Violation', 'Reckless driving with license suspension. Officer testimony disputed.', 'Low', 'Appeal', 'Open', 0.75, 'Dismissed', DATE_SUB(NOW(), INTERVAL 11 DAY), 'Officer failed to appear, radar calibration records missing', 0, DATE_SUB(NOW(), INTERVAL 10 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'amanda.wilson'), 0),

(UUID(), 'Battery on Officer - Michelle White', 'Battery', 'Alleged battery on police officer during arrest. Excessive force claim.', 'High', 'Pre_Trial', 'Open', 0.45, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 13 DAY), 'Body camera footage supports excessive force defense, officer history problematic', 1, DATE_SUB(NOW(), INTERVAL 40 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'lisa.anderson'), 0),

(UUID(), 'Investment Fraud - Steven Harris', 'White Collar Crime', 'Investment fraud allegations - Ponzi scheme. SEC involvement.', 'High', 'Investigation', 'Open', 0.69, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 15 DAY), 'Complex financial scheme, cooperation with SEC beneficial, restitution fund possible', 0, DATE_SUB(NOW(), INTERVAL 80 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'robert.chen'), 0),

(UUID(), 'Shoplifting - Rebecca Martin', 'Theft', 'Retail theft charges with prior convictions. Three strikes issue.', 'Low', 'Plea_Negotiation', 'Open', 0.72, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 16 DAY), 'Minor amount, mental health issues, diversion program available', 0, DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'james.brown'), 0),

(UUID(), 'Money Laundering - Kevin Thompson', 'Money Laundering', 'Complex money laundering scheme involving multiple banks.', 'High', 'Investigation', 'Open', 0.51, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 17 DAY), 'Financial records extensive, international elements, cooperation potential', 1, DATE_SUB(NOW(), INTERVAL 110 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'robert.chen'), 0),

(UUID(), 'Sexual Assault - Nicole Garcia', 'Sexual Assault', 'Serious sexual assault charges. DNA evidence disputed.', 'High', 'Pre_Trial', 'Open', 0.47, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 18 DAY), 'DNA evidence chain of custody issues, consent defense, character witnesses', 1, DATE_SUB(NOW(), INTERVAL 95 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'maria.rodriguez'), 0),

(UUID(), 'Tax Evasion - Thomas Martinez', 'Tax Evasion', 'Federal tax evasion charges over 5-year period.', 'High', 'Investigation', 'Open', 0.63, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 19 DAY), 'IRS cooperation possible, penalty reduction available, business records complex', 0, DATE_SUB(NOW(), INTERVAL 130 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'robert.chen'), 0),

(UUID(), 'RICO Conspiracy - Karen Robinson', 'Conspiracy', 'RICO conspiracy allegations involving organized crime.', 'High', 'Pre_Trial', 'Open', 0.44, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 20 DAY), 'RICO predicate acts weak, client peripheral role, cooperation agreement possible', 1, DATE_SUB(NOW(), INTERVAL 150 DAY), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'sarah.mitchell'), 0);

-- Create Conflict Search Sample Data
INSERT INTO conflict_search (
    id, name, search_term, search_type, modules_searched, confidence_threshold,
    total_matches_found, high_confidence_matches, medium_confidence_matches, low_confidence_matches,
    search_status, execution_time, performed_by, search_criteria,
    date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted
) VALUES
(UUID(), 'Conflict Search: Johnson vs State', 'Johnson vs State', 'comprehensive', 'Contacts,Accounts,Cases', 75, 3, 1, 1, 1, 'completed', 0.2347, (SELECT id FROM temp_users WHERE user_name = 'sarah.mitchell'), 'Standard conflict check for new case intake', NOW(), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'sarah.mitchell'), 0),

(UUID(), 'Conflict Search: Pacific Legal Group', 'Pacific Legal Group', 'comprehensive', 'Contacts,Accounts,Cases', 75, 5, 2, 2, 1, 'completed', 0.1892, (SELECT id FROM temp_users WHERE user_name = 'robert.chen'), 'Opposing counsel conflict verification', NOW(), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'robert.chen'), 0),

(UUID(), 'Conflict Search: Maria Rodriguez', 'Maria Rodriguez', 'comprehensive', 'Contacts,Accounts,Cases', 75, 2, 1, 0, 1, 'completed', 0.1543, (SELECT id FROM temp_users WHERE user_name = 'maria.rodriguez'), 'Client name verification - common name check', NOW(), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'maria.rodriguez'), 0),

(UUID(), 'Conflict Search: Los Angeles County DA', 'Los Angeles County DA', 'comprehensive', 'Contacts,Accounts,Cases', 75, 12, 8, 3, 1, 'completed', 0.3156, (SELECT id FROM temp_users WHERE user_name = 'david.thompson'), 'Prosecutor office conflict check', NOW(), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'david.thompson'), 0),

(UUID(), 'Conflict Search: Dr. Peterson Expert', 'Dr. Peterson Expert', 'comprehensive', 'Contacts,Accounts,Cases', 75, 4, 2, 1, 1, 'completed', 0.2089, (SELECT id FROM temp_users WHERE user_name = 'jennifer.williams'), 'Expert witness availability check', NOW(), NOW(), '1', '1', (SELECT id FROM temp_users WHERE user_name = 'jennifer.williams'), 0);

-- Clean up temporary tables
DROP TEMPORARY TABLE temp_users;
DROP TEMPORARY TABLE temp_clients;

-- Display completion message
SELECT 'Criminal Defense CRM Data Seeding Complete!' as Status,
       (SELECT COUNT(*) FROM users WHERE deleted = 0) as Users_Created,
       (SELECT COUNT(*) FROM accounts WHERE deleted = 0) as Accounts_Created,
       (SELECT COUNT(*) FROM contacts WHERE deleted = 0) as Contacts_Created,
       (SELECT COUNT(*) FROM cases WHERE deleted = 0) as Cases_Created,
       (SELECT COUNT(*) FROM conflict_search WHERE deleted = 0) as Conflict_Searches_Created;