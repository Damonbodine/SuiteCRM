-- Create ACL actions for ConflictSearch module
-- This enables the module to appear in SuiteCRM navigation

-- Standard ACL actions that every module needs
INSERT INTO acl_actions (id, name, category, acltype, aclaccess, deleted) VALUES 
('conflictsearch-access-001', 'access', 'ConflictSearch', 'module', 90, 0),
('conflictsearch-view-002', 'view', 'ConflictSearch', 'module', 90, 0),
('conflictsearch-list-003', 'list', 'ConflictSearch', 'module', 90, 0),
('conflictsearch-edit-004', 'edit', 'ConflictSearch', 'module', 90, 0),
('conflictsearch-delete-005', 'delete', 'ConflictSearch', 'module', 90, 0),
('conflictsearch-export-006', 'export', 'ConflictSearch', 'module', 90, 0),
('conflictsearch-import-007', 'import', 'ConflictSearch', 'module', 90, 0),
('conflictsearch-massupdate-008', 'massupdate', 'ConflictSearch', 'module', 90, 0);

-- Verify the ACL actions were created
SELECT * FROM acl_actions WHERE category = 'ConflictSearch' ORDER BY name;