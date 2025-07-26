-- Fix execution_time column definition in conflict_search table
-- This corrects the malformed decimal precision from Quick Repair and Rebuild

-- First, check if the table exists and show current structure
-- DESCRIBE conflict_search;

-- Fix the execution_time column with proper decimal(10,4) definition
ALTER TABLE conflict_search MODIFY COLUMN `execution_time` decimal(10,4) DEFAULT '0.0000' NULL;

-- Verify the fix
-- DESCRIBE conflict_search;