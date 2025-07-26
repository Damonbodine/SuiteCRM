-- Fix Cases table AI confidence score issue
ALTER TABLE cases MODIFY COLUMN `ai_confidence_score` decimal(5,4) DEFAULT '0.0000' NULL;

-- Create ConflictSearch table (corrected SQL)
CREATE TABLE conflict_search (
    `id` char(36) NOT NULL,
    `name` varchar(255) NULL,
    `date_entered` datetime NULL,
    `date_modified` datetime NULL,
    `modified_user_id` char(36) NULL,
    `created_by` char(36) NULL,
    `description` text NULL,
    `deleted` tinyint(1) DEFAULT '0' NULL,
    `assigned_user_id` char(36) NULL,
    `search_term` varchar(255) NULL,
    `search_type` varchar(50) DEFAULT 'comprehensive' NULL,
    `modules_searched` varchar(255) DEFAULT 'Contacts,Accounts,Cases' NULL,
    `confidence_threshold` int(3) DEFAULT '75' NULL,
    `total_matches_found` int(11) DEFAULT '0' NULL,
    `high_confidence_matches` int(11) DEFAULT '0' NULL,
    `medium_confidence_matches` int(11) DEFAULT '0' NULL,
    `low_confidence_matches` int(11) DEFAULT '0' NULL,
    `search_status` varchar(50) DEFAULT 'pending' NULL,
    `search_results` longtext NULL,
    `execution_time` decimal(10,4) DEFAULT '0.0000' NULL,
    `performed_by` char(36) NULL,
    `search_criteria` text NULL,
    PRIMARY KEY (`id`),
    KEY `idx_conflict_search_name` (`name`),
    KEY `idx_conflict_search_assigned` (`assigned_user_id`),
    KEY `idx_conflict_search_term` (`search_term`),
    KEY `idx_conflict_search_status` (`search_status`),
    KEY `idx_conflict_search_performed_by` (`performed_by`),
    KEY `idx_conflict_search_date_entered` (`date_entered`),
    KEY `idx_conflict_search_deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;