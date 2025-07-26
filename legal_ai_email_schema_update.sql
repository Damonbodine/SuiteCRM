-- Legal AI Email Analysis Schema Enhancement
-- This adds legal-specific fields to the existing ai_email_analysis table
-- for criminal defense attorney workflows

-- First, let's verify the table exists
SELECT 'Starting legal AI email schema update...' as status;

-- Add legal-specific categorization fields
ALTER TABLE ai_email_analysis ADD COLUMN case_related TINYINT(1) DEFAULT 0 COMMENT 'Is this email related to a specific case';
ALTER TABLE ai_email_analysis ADD COLUMN case_id VARCHAR(36) NULL COMMENT 'ID of the associated SuiteCRM case';
ALTER TABLE ai_email_analysis ADD COLUMN case_match_confidence DECIMAL(3,2) NULL COMMENT 'Confidence score for case association';
ALTER TABLE ai_email_analysis ADD COLUMN case_number VARCHAR(50) DEFAULT NULL COMMENT 'Extracted case number if detected';
ALTER TABLE ai_email_analysis ADD COLUMN client_name VARCHAR(100) DEFAULT NULL COMMENT 'Client name if identified';
ALTER TABLE ai_email_analysis ADD COLUMN legal_category VARCHAR(50) DEFAULT NULL COMMENT 'court, client, prosecution, expert, discovery, etc.';
ALTER TABLE ai_email_analysis ADD COLUMN urgency_level VARCHAR(20) DEFAULT 'routine' COMMENT 'emergency, urgent, routine, informational';
ALTER TABLE ai_email_analysis ADD COLUMN privilege_status VARCHAR(20) DEFAULT NULL COMMENT 'privileged, work_product, discoverable, public';

-- Add enhanced AI analysis fields
ALTER TABLE ai_email_analysis ADD COLUMN sentiment VARCHAR(20) DEFAULT NULL COMMENT 'positive, negative, neutral, concerned, angry';
ALTER TABLE ai_email_analysis ADD COLUMN confidence_score DECIMAL(3,2) DEFAULT NULL COMMENT 'AI confidence in analysis (0.00-1.00)';
ALTER TABLE ai_email_analysis ADD COLUMN key_people TEXT DEFAULT NULL COMMENT 'JSON array of people mentioned in email';
ALTER TABLE ai_email_analysis ADD COLUMN action_items TEXT DEFAULT NULL COMMENT 'JSON array of tasks/deadlines detected';
ALTER TABLE ai_email_analysis ADD COLUMN deadline_detected DATETIME DEFAULT NULL COMMENT 'Most urgent deadline found in email';
ALTER TABLE ai_email_analysis ADD COLUMN follow_up_required TINYINT(1) DEFAULT 0 COMMENT 'Does this email require follow-up action';
ALTER TABLE ai_email_analysis ADD COLUMN response_needed TINYINT(1) DEFAULT 0 COMMENT 'Does this email require a response';
ALTER TABLE ai_email_analysis ADD COLUMN response_urgency VARCHAR(20) DEFAULT NULL COMMENT 'immediate, today, this_week, routine';

-- Add legal practice specific fields
ALTER TABLE ai_email_analysis ADD COLUMN opposing_counsel VARCHAR(100) DEFAULT NULL COMMENT 'Opposing counsel name if detected';
ALTER TABLE ai_email_analysis ADD COLUMN court_name VARCHAR(100) DEFAULT NULL COMMENT 'Court name if mentioned';
ALTER TABLE ai_email_analysis ADD COLUMN hearing_date DATETIME DEFAULT NULL COMMENT 'Court hearing date if detected';
ALTER TABLE ai_email_analysis ADD COLUMN document_type VARCHAR(50) DEFAULT NULL COMMENT 'motion, discovery, pleading, contract, etc.';
ALTER TABLE ai_email_analysis ADD COLUMN billable_time_detected DECIMAL(4,2) DEFAULT NULL COMMENT 'Estimated billable hours for this communication';
ALTER TABLE ai_email_analysis ADD COLUMN ethical_flags TEXT DEFAULT NULL COMMENT 'JSON array of ethical considerations';

-- Add performance indexes for legal queries
CREATE INDEX idx_legal_category ON ai_email_analysis(legal_category) COMMENT 'Index for filtering by legal category';
CREATE INDEX idx_urgency_level ON ai_email_analysis(urgency_level) COMMENT 'Index for filtering by urgency';
CREATE INDEX idx_case_related ON ai_email_analysis(case_related) COMMENT 'Index for case-related emails';
CREATE INDEX idx_case_id ON ai_email_analysis(case_id) COMMENT 'Index for case association queries';
CREATE INDEX idx_deadline_detected ON ai_email_analysis(deadline_detected) COMMENT 'Index for deadline-based queries';
CREATE INDEX idx_response_needed ON ai_email_analysis(response_needed) COMMENT 'Index for emails requiring response';
CREATE INDEX idx_hearing_date ON ai_email_analysis(hearing_date) COMMENT 'Index for court hearing dates';
CREATE INDEX idx_user_category ON ai_email_analysis(user_id, legal_category) COMMENT 'Composite index for user-specific category queries';
CREATE INDEX idx_user_urgency ON ai_email_analysis(user_id, urgency_level) COMMENT 'Composite index for user-specific urgency queries';

-- Add constraint to ensure valid urgency levels
ALTER TABLE ai_email_analysis ADD CONSTRAINT chk_urgency_level 
CHECK (urgency_level IN ('emergency', 'urgent', 'routine', 'informational'));

-- Add constraint to ensure valid privilege status
ALTER TABLE ai_email_analysis ADD CONSTRAINT chk_privilege_status 
CHECK (privilege_status IN ('privileged', 'work_product', 'discoverable', 'public'));

-- Add constraint to ensure confidence score is between 0 and 1
ALTER TABLE ai_email_analysis ADD CONSTRAINT chk_confidence_score 
CHECK (confidence_score >= 0.00 AND confidence_score <= 1.00);

-- Add constraint to ensure billable time is positive
ALTER TABLE ai_email_analysis ADD CONSTRAINT chk_billable_time 
CHECK (billable_time_detected >= 0.00);

-- Verify the new schema
SELECT 'Checking new columns...' as status;

-- Show the updated table structure
DESCRIBE ai_email_analysis;

-- Count existing records to ensure no data loss
SELECT COUNT(*) as existing_records FROM ai_email_analysis;

-- Show sample of updated table structure
SELECT 
    'legal_ai_email_schema_update completed successfully' as status,
    NOW() as completion_time;

-- Create a test record to verify new fields work
INSERT INTO ai_email_analysis 
(
    id, email_message_id, user_id, category, priority_score, ai_summary,
    case_related, case_number, client_name, legal_category, urgency_level,
    privilege_status, sentiment, confidence_score, key_people, action_items,
    deadline_detected, response_needed, response_urgency, opposing_counsel,
    court_name, document_type, billable_time_detected, ethical_flags,
    analysis_date, expires_at, deleted
) 
VALUES 
(
    UUID(), 'test_legal_email_schema', '1', 'legal', 8,
    'Test record to verify legal AI schema enhancement',
    1, 'CR-2025-001234', 'John Smith', 'court', 'urgent',
    'privileged', 'neutral', 0.95, '["Judge Johnson", "DA Wilson"]', 
    '["File motion by Friday", "Prepare witness list"]',
    DATE_ADD(NOW(), INTERVAL 3 DAY), 1, 'today', 'DA Wilson',
    'Superior Court', 'motion', 2.50, '["attorney_client_privilege"]',
    NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 0
);

-- Verify test record was inserted
SELECT 'Test record verification:' as status;
SELECT email_message_id, legal_category, urgency_level, case_number, billable_time_detected 
FROM ai_email_analysis 
WHERE email_message_id = 'test_legal_email_schema';

SELECT 'Legal AI Email Schema Update Complete!' as final_status;