-- AI Status Intelligence - Test Data Creation Script
-- Run this script to create sample data for testing the AI feature

USE `suitecrm`;

-- Create test case with specific ID for consistent testing
SET @test_case_id = 'ai-test-case-001';
SET @admin_user_id = '1';

-- Remove existing test data if present
DELETE FROM cases WHERE id = @test_case_id;
DELETE FROM tasks WHERE parent_type = 'Cases' AND parent_id = @test_case_id;
DELETE FROM calls WHERE parent_type = 'Cases' AND parent_id = @test_case_id;
DELETE FROM meetings WHERE parent_type = 'Cases' AND parent_id = @test_case_id;

-- Create main test case
INSERT INTO cases (
    id,
    name,
    case_number,
    status,
    priority,
    type,
    assigned_user_id,
    description,
    date_entered,
    date_modified,
    created_by,
    modified_user_id,
    deleted
) VALUES (
    @test_case_id,
    'AI Test Case - Personal Injury Claim',
    1,
    'Open_Assigned',
    'Medium',
    'Personal Injury',
    @admin_user_id,
    'Client injured in vehicle accident. Need to gather evidence, medical records, and negotiate with insurance company.',
    DATE_SUB(NOW(), INTERVAL 15 DAY),  -- Case created 15 days ago
    NOW(),
    @admin_user_id,
    @admin_user_id,
    0
);

-- Create tasks with different completion states to test AI analysis
INSERT INTO tasks (
    id,
    name,
    status,
    priority,
    parent_type,
    parent_id,
    assigned_user_id,
    description,
    date_entered,
    date_modified,
    date_due,
    created_by,
    modified_user_id,
    deleted
) VALUES 
-- Completed tasks (should suggest case is progressing)
(UUID(), 'Initial client consultation', 'Completed', 'High', 'Cases', @test_case_id, @admin_user_id, 'Conducted initial intake and assessment', DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_SUB(NOW(), INTERVAL 14 DAY), @admin_user_id, @admin_user_id, 0),

(UUID(), 'Obtain police report', 'Completed', 'High', 'Cases', @test_case_id, @admin_user_id, 'Retrieved official accident report from police department', DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY), @admin_user_id, @admin_user_id, 0),

(UUID(), 'Request medical records', 'Completed', 'High', 'Cases', @test_case_id, @admin_user_id, 'Submitted medical records request to hospital and clinic', DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 11 DAY), @admin_user_id, @admin_user_id, 0),

(UUID(), 'Interview witness #1', 'Completed', 'Medium', 'Cases', @test_case_id, @admin_user_id, 'Conducted phone interview with primary witness', DATE_SUB(NOW(), INTERVAL 9 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 9 DAY), @admin_user_id, @admin_user_id, 0),

-- In Progress tasks (shows active work)
(UUID(), 'Interview witness #2', 'In Progress', 'Medium', 'Cases', @test_case_id, @admin_user_id, 'Second witness difficult to reach, attempting contact', DATE_SUB(NOW(), INTERVAL 8 DAY), NOW(), DATE_ADD(NOW(), INTERVAL 3 DAY), @admin_user_id, @admin_user_id, 0),

(UUID(), 'Calculate damages', 'In Progress', 'High', 'Cases', @test_case_id, @admin_user_id, 'Reviewing medical bills and lost wages documentation', DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), @admin_user_id, @admin_user_id, 0),

-- Not Started tasks (pending items)
(UUID(), 'Prepare demand letter', 'Not Started', 'High', 'Cases', @test_case_id, @admin_user_id, 'Draft demand letter to insurance company', DATE_SUB(NOW(), INTERVAL 3 DAY), NOW(), DATE_ADD(NOW(), INTERVAL 10 DAY), @admin_user_id, @admin_user_id, 0),

(UUID(), 'Schedule mediation', 'Not Started', 'Medium', 'Cases', @test_case_id, @admin_user_id, 'Arrange mediation session if settlement negotiations stall', NOW(), NOW(), DATE_ADD(NOW(), INTERVAL 21 DAY), @admin_user_id, @admin_user_id, 0);

-- Create communication records (calls and meetings)
INSERT INTO calls (
    id,
    name,
    status,
    direction,
    parent_type,
    parent_id,
    assigned_user_id,
    description,
    date_entered,
    date_modified,
    date_start,
    duration_hours,
    duration_minutes,
    created_by,
    modified_user_id,
    deleted
) VALUES 
-- Recent client communication (good sign)
(UUID(), 'Client status update call', 'Held', 'Outbound', 'Cases', @test_case_id, @admin_user_id, 'Updated client on case progress and next steps', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 0, 30, @admin_user_id, @admin_user_id, 0),

-- Insurance company contact
(UUID(), 'Insurance adjuster discussion', 'Held', 'Outbound', 'Cases', @test_case_id, @admin_user_id, 'Discussed liability and preliminary settlement range', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), 0, 45, @admin_user_id, @admin_user_id, 0),

-- Medical provider coordination
(UUID(), 'Doctor consultation', 'Held', 'Outbound', 'Cases', @test_case_id, @admin_user_id, 'Discussed prognosis and treatment plan with treating physician', DATE_SUB(NOW(), INTERVAL 9 DAY), DATE_SUB(NOW(), INTERVAL 9 DAY), DATE_SUB(NOW(), INTERVAL 9 DAY), 0, 20, @admin_user_id, @admin_user_id, 0);

-- Create meetings for additional activity
INSERT INTO meetings (
    id,
    name,
    status,
    parent_type,
    parent_id,
    assigned_user_id,
    description,
    date_entered,
    date_modified,
    date_start,
    date_end,
    duration_hours,
    duration_minutes,
    created_by,
    modified_user_id,
    deleted
) VALUES 
-- Client meeting
(UUID(), 'In-person client consultation', 'Held', 'Cases', @test_case_id, @admin_user_id, 'Reviewed case documents and discussed settlement expectations', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(DATE_SUB(NOW(), INTERVAL 5 DAY), INTERVAL 30 MINUTE), 1, 30, @admin_user_id, @admin_user_id, 0);

-- Create a second test case with different characteristics for comparison
SET @test_case_id_2 = 'ai-test-case-002';

INSERT INTO cases (
    id,
    name,
    case_number,
    status,
    priority,
    type,
    assigned_user_id,
    description,
    date_entered,
    date_modified,
    created_by,
    modified_user_id,
    deleted
) VALUES (
    @test_case_id_2,
    'AI Test Case - Contract Dispute (Inactive)',
    2,
    'Open_Assigned',
    'Low',
    'Contract',
    @admin_user_id,
    'Commercial contract dispute requiring document review and negotiation.',
    DATE_SUB(NOW(), INTERVAL 45 DAY),  -- Older case, should suggest needs attention
    DATE_SUB(NOW(), INTERVAL 30 DAY),  -- Last modified 30 days ago
    @admin_user_id,
    @admin_user_id,
    0
);

-- Add minimal tasks for inactive case (should trigger "needs input" suggestion)
INSERT INTO tasks (
    id,
    name,
    status,
    priority,
    parent_type,
    parent_id,
    assigned_user_id,
    description,
    date_entered,
    date_modified,
    date_due,
    created_by,
    modified_user_id,
    deleted
) VALUES 
(UUID(), 'Review contract documents', 'Completed', 'Medium', 'Cases', @test_case_id_2, @admin_user_id, 'Initial document review completed', DATE_SUB(NOW(), INTERVAL 40 DAY), DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(NOW(), INTERVAL 40 DAY), @admin_user_id, @admin_user_id, 0),

(UUID(), 'Contact opposing counsel', 'Not Started', 'High', 'Cases', @test_case_id_2, @admin_user_id, 'Need to initiate settlement discussions', DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY), @admin_user_id, @admin_user_id, 0);

-- Create summary of test data for verification
SELECT 
    'TEST DATA SUMMARY' as section,
    'Created test cases and related activities for AI analysis testing' as description
UNION ALL
SELECT 'Case 1 (Active)', CONCAT('ID: ', @test_case_id, ' - Should suggest progression or pending input based on 50% task completion')
UNION ALL  
SELECT 'Case 2 (Inactive)', CONCAT('ID: ', @test_case_id_2, ' - Should suggest needs attention due to 30+ days inactivity')
UNION ALL
SELECT 'Tasks Created', CONCAT((SELECT COUNT(*) FROM tasks WHERE parent_type = 'Cases' AND parent_id IN (@test_case_id, @test_case_id_2)), ' tasks total')
UNION ALL
SELECT 'Communications', CONCAT((SELECT COUNT(*) FROM calls WHERE parent_type = 'Cases' AND parent_id IN (@test_case_id, @test_case_id_2)), ' calls + ', (SELECT COUNT(*) FROM meetings WHERE parent_type = 'Cases' AND parent_id IN (@test_case_id, @test_case_id_2)), ' meetings');

-- Verification queries to run after script
SELECT 
    c.id,
    c.name,
    c.status,
    c.date_entered,
    c.date_modified,
    (SELECT COUNT(*) FROM tasks t WHERE t.parent_id = c.id AND t.status = 'Completed') as completed_tasks,
    (SELECT COUNT(*) FROM tasks t WHERE t.parent_id = c.id) as total_tasks,
    (SELECT COUNT(*) FROM calls cl WHERE cl.parent_id = c.id AND cl.status = 'Held') as total_calls
FROM cases c 
WHERE c.id IN (@test_case_id, @test_case_id_2)
ORDER BY c.date_entered DESC;