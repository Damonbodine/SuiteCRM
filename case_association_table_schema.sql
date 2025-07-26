-- Create email_case_associations table for tracking automatic case linking
-- This table stores the relationships between emails and SuiteCRM cases

CREATE TABLE IF NOT EXISTS email_case_associations (
    id VARCHAR(36) NOT NULL PRIMARY KEY,
    email_id VARCHAR(255) NOT NULL,
    case_id VARCHAR(36) NOT NULL,
    match_type VARCHAR(50) NOT NULL COMMENT 'Type of match: case_number, client_name, subject_keyword, court_name, participant, etc.',
    confidence_score DECIMAL(3,2) NOT NULL DEFAULT 0.50 COMMENT 'Confidence score from 0.00 to 1.00',
    match_details TEXT NULL COMMENT 'JSON data with additional match information',
    created_by VARCHAR(36) NOT NULL,
    date_created DATETIME NOT NULL,
    date_modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted TINYINT(1) DEFAULT 0
);

-- Add indexes for performance
CREATE INDEX idx_email_case_assoc_email_id ON email_case_associations(email_id);
CREATE INDEX idx_email_case_assoc_case_id ON email_case_associations(case_id);
CREATE INDEX idx_email_case_assoc_confidence ON email_case_associations(confidence_score DESC);
CREATE INDEX idx_email_case_assoc_deleted ON email_case_associations(deleted);
CREATE INDEX idx_email_case_assoc_created ON email_case_associations(date_created);

-- Composite indexes for common queries
CREATE INDEX idx_email_case_assoc_lookup ON email_case_associations(email_id, case_id, deleted);
CREATE INDEX idx_email_case_assoc_case_emails ON email_case_associations(case_id, deleted, date_created DESC);

-- Add foreign key constraints if cases table exists
-- ALTER TABLE email_case_associations 
-- ADD CONSTRAINT fk_email_case_assoc_case 
-- FOREIGN KEY (case_id) REFERENCES cases(id) ON DELETE CASCADE;

-- Sample data verification query (comment out for production)
-- SELECT 
--     eca.id,
--     eca.email_id,
--     eca.match_type,
--     eca.confidence_score,
--     c.case_number,
--     c.name as case_name,
--     eca.date_created
-- FROM email_case_associations eca
-- LEFT JOIN cases c ON eca.case_id = c.id
-- WHERE eca.deleted = 0
-- ORDER BY eca.confidence_score DESC, eca.date_created DESC
-- LIMIT 10;

-- Performance verification
-- EXPLAIN SELECT * FROM email_case_associations WHERE email_id = 'sample_email_id' AND deleted = 0;
-- EXPLAIN SELECT * FROM email_case_associations WHERE case_id = 'sample_case_id' AND deleted = 0 ORDER BY date_created DESC;