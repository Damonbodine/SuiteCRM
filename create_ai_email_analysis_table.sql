-- AI Email Analysis Cache Table
-- This table stores AI analysis results for email processing

CREATE TABLE ai_email_analysis (
    id VARCHAR(36) PRIMARY KEY,
    email_message_id VARCHAR(255) NOT NULL,
    user_id VARCHAR(36) NOT NULL,
    category VARCHAR(100),
    priority_score INT DEFAULT 5,
    ai_summary TEXT,
    suggested_actions JSON,
    analysis_date DATETIME NOT NULL,
    expires_at DATETIME NOT NULL,
    deleted TINYINT(1) DEFAULT 0,
    date_entered DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_user_email (user_id, email_message_id),
    INDEX idx_expires (expires_at),
    INDEX idx_user_priority (user_id, priority_score),
    INDEX idx_category (category),
    INDEX idx_deleted (deleted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Insert some test data to verify table structure
-- (This will be removed in production)
INSERT INTO ai_email_analysis 
(id, email_message_id, user_id, category, priority_score, ai_summary, analysis_date, expires_at, deleted) 
VALUES 
(UUID(), 'test_email_1', '1', 'general', 5, 'This is a test email analysis entry.', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), 0);

-- Verify table creation
SELECT 'ai_email_analysis table created successfully' as status;