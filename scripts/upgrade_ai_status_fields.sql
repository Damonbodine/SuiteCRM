-- AI Status Intelligence Database Schema Changes
-- Safe SQL script for SuiteCRM MySQL 5.7+ 

USE `suitecrm`;

-- Add AI fields to cases table
-- Check if columns exist before adding to prevent upgrade errors

SET @ai_suggested_status_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'cases' 
    AND COLUMN_NAME = 'ai_suggested_status'
);

SET @ai_confidence_score_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'cases' 
    AND COLUMN_NAME = 'ai_confidence_score'
);

SET @ai_last_analysis_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'cases' 
    AND COLUMN_NAME = 'ai_last_analysis'
);

SET @ai_analysis_factors_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'cases' 
    AND COLUMN_NAME = 'ai_analysis_factors'
);

SET @ai_status_needs_review_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'cases' 
    AND COLUMN_NAME = 'ai_status_needs_review'
);

-- Add ai_suggested_status column if it doesn't exist
SET @sql = IF(@ai_suggested_status_exists = 0,
    'ALTER TABLE cases ADD COLUMN ai_suggested_status VARCHAR(100) DEFAULT NULL COMMENT "AI suggested case status"',
    'SELECT "ai_suggested_status column already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add ai_confidence_score column if it doesn't exist  
SET @sql = IF(@ai_confidence_score_exists = 0,
    'ALTER TABLE cases ADD COLUMN ai_confidence_score DECIMAL(5,4) DEFAULT 0.0000 COMMENT "AI confidence score 0.0-1.0"',
    'SELECT "ai_confidence_score column already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add ai_last_analysis column if it doesn't exist
SET @sql = IF(@ai_last_analysis_exists = 0,
    'ALTER TABLE cases ADD COLUMN ai_last_analysis DATETIME DEFAULT NULL COMMENT "Timestamp of last AI analysis"',
    'SELECT "ai_last_analysis column already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add ai_analysis_factors column if it doesn't exist
SET @sql = IF(@ai_analysis_factors_exists = 0,
    'ALTER TABLE cases ADD COLUMN ai_analysis_factors TEXT DEFAULT NULL COMMENT "JSON data of analysis factors"',
    'SELECT "ai_analysis_factors column already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add ai_status_needs_review column if it doesn't exist
SET @sql = IF(@ai_status_needs_review_exists = 0,
    'ALTER TABLE cases ADD COLUMN ai_status_needs_review TINYINT(1) DEFAULT 0 COMMENT "Whether AI suggestion needs review"',
    'SELECT "ai_status_needs_review column already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create index for performance on commonly queried fields
SET @index_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'cases'
    AND INDEX_NAME = 'idx_cases_ai_status_review'
);

SET @sql = IF(@index_exists = 0,
    'CREATE INDEX idx_cases_ai_status_review ON cases (ai_status_needs_review, ai_last_analysis)',
    'SELECT "AI status index already exists" as message'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create audit trail table for AI actions (if needed)
CREATE TABLE IF NOT EXISTS ai_status_audit (
    id VARCHAR(36) NOT NULL PRIMARY KEY,
    case_id VARCHAR(36) NOT NULL,
    user_id VARCHAR(36) NOT NULL,
    action VARCHAR(50) NOT NULL,
    old_status VARCHAR(100) DEFAULT NULL,
    new_status VARCHAR(100) DEFAULT NULL,
    confidence_score DECIMAL(5,4) DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    date_entered DATETIME NOT NULL,
    deleted TINYINT(1) DEFAULT 0,
    
    INDEX idx_ai_audit_case (case_id, date_entered),
    INDEX idx_ai_audit_user (user_id, date_entered),
    INDEX idx_ai_audit_action (action, date_entered)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Audit trail for AI status intelligence actions';

-- Verification queries to confirm schema changes
SELECT 
    'Schema Update Complete' as status,
    COUNT(*) as ai_fields_added
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'cases' 
AND COLUMN_NAME IN (
    'ai_suggested_status', 
    'ai_confidence_score', 
    'ai_last_analysis', 
    'ai_analysis_factors', 
    'ai_status_needs_review'
);

-- Show the new AI fields structure
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'cases' 
AND COLUMN_NAME LIKE 'ai_%'
ORDER BY COLUMN_NAME;

-- End of AI Status Intelligence Schema Update