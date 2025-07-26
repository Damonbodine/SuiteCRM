-- Batch insert users for criminal defense law firm
INSERT INTO users (id, user_name, first_name, last_name, title, department, is_admin, status, employee_status, sugar_login, receive_notifications, show_on_employees, phone_work, date_entered, date_modified, created_by, modified_user_id, deleted, user_hash) VALUES
(UUID(), 'robert.chen', 'Robert', 'Chen', 'Senior Partner', 'White Collar Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0102', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'maria.rodriguez', 'Maria', 'Rodriguez', 'Managing Partner', 'Criminal Defense', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0103', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'david.thompson', 'David', 'Thompson', 'Associate Attorney', 'DUI/Traffic Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0104', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'jennifer.williams', 'Jennifer', 'Williams', 'Associate Attorney', 'Drug Crimes Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0105', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'michael.johnson', 'Michael', 'Johnson', 'Associate Attorney', 'Assault/Battery Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0106', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'lisa.anderson', 'Lisa', 'Anderson', 'Associate Attorney', 'Domestic Violence Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0107', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'james.brown', 'James', 'Brown', 'Junior Associate', 'General Criminal Defense', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0108', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'ashley.davis', 'Ashley', 'Davis', 'Junior Associate', 'Appeals', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0109', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'michelle.taylor', 'Michelle', 'Taylor', 'Senior Paralegal', 'Case Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0110', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'steven.jackson', 'Steven', 'Jackson', 'Paralegal', 'Discovery Support', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0111', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'rebecca.white', 'Rebecca', 'White', 'Paralegal', 'Client Intake', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0112', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'patricia.garcia', 'Patricia', 'Garcia', 'Office Manager', 'Administration', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0113', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'thomas.martinez', 'Thomas', 'Martinez', 'Legal Secretary', 'Document Management', 0, 'Active', 'Active', 1, 1, 1, '(310) 555-0114', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123'),
(UUID(), 'alex.cruz', 'Alex', 'Cruz', 'System Administrator', 'CRM Management', 1, 'Active', 'Active', 1, 1, 1, '(310) 555-0115', NOW(), NOW(), '1', '1', 0, 'sa$2y$10$example.hash.for.password123');

SELECT 'Users created successfully!' as Status, COUNT(*) as Total_Users FROM users WHERE deleted = 0;