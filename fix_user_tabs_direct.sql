-- Direct SQL approach to add ConflictSearch to user navigation tabs
-- This updates user preferences to include ConflictSearch in display tabs

-- First, let's see current user preferences for tabs
-- SELECT assigned_user_id, category, contents FROM user_preferences WHERE category IN ('display_tabs', 'hide_tabs', 'remove_tabs');

-- For now, let's create a system-wide preference that adds ConflictSearch to display tabs
-- This will be inherited by users who don't have custom tab preferences

-- Insert system display tabs preference (if it doesn't exist)
INSERT INTO user_preferences (id, assigned_user_id, category, contents, deleted) 
SELECT 
    UUID() as id,
    'global' as assigned_user_id,
    'display_tabs' as category,
    'YToxOntzOjEzOiJDb25mbGljdFNlYXJjaCI7czoxMzoiQ29uZmxpY3RTZWFyY2giO30=' as contents,  -- Base64 encoded array('ConflictSearch' => 'ConflictSearch')
    0 as deleted
WHERE NOT EXISTS (
    SELECT 1 FROM user_preferences 
    WHERE assigned_user_id = 'global' AND category = 'display_tabs'
);

-- Update the config table to ensure ConflictSearch is in system tabs
INSERT INTO config (category, name, value) VALUES ('system', 'system_tabs', 'YToxOntzOjEzOiJDb25mbGljdFNlYXJjaCI7czoxMzoiQ29uZmxpY3RTZWFyY2giO30=')
ON DUPLICATE KEY UPDATE value = 'YToxOntzOjEzOiJDb25mbGljdFNlYXJjaCI7czoxMzoiQ29uZmxpY3RTZWFyY2giO30=';

-- Verify the changes
SELECT 'user_preferences' as table_name, COUNT(*) as count FROM user_preferences WHERE category = 'display_tabs' AND contents LIKE '%ConflictSearch%'
UNION ALL
SELECT 'config' as table_name, COUNT(*) as count FROM config WHERE name = 'system_tabs' AND value LIKE '%ConflictSearch%';