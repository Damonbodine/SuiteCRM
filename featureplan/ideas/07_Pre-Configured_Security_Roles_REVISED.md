# Feature Plan: Pre-Configured Security Roles - REVISED

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 7 of 10  
**Risk Level:** HIGH (Updated from Low - Critical Security Architecture Concerns)

---

## 1. Objective

To provide secure, pre-configured security role templates for common law firm roles while addressing critical security vulnerabilities in SuiteCRM's ACL system. This feature must work within the constraints of the existing security architecture while implementing additional safeguards.

**CRITICAL SECURITY CONSTRAINTS IDENTIFIED:**
- Existing ACL system has potential bypass vulnerabilities
- SQL injection risks in ACL query construction  
- Complex security group inheritance with privilege escalation risks
- MD5 password hashing vulnerability affects user creation
- Heavy global state dependencies in security code
- SecurityGroups system has inconsistent validation patterns

---

## 2. Security-First Implementation Approach

### Phase 1: Security Hardening Prerequisites (Days 1-2)

- [ ] **Task 1: Implement SQL Injection Prevention**
    - [ ] Create secure wrapper for ACL operations: `custom/include/security/SecureACLManager.php`
    - [ ] Use prepared statements for all security-related queries:
    ```php
    class SecureACLManager {
        public function createRoleSecurely($roleName, $description) {
            // Use BeanFactory with proper input validation
            $role = BeanFactory::newBean('ACLRoles');
            $role->name = $this->sanitizeInput($roleName);
            $role->description = $this->sanitizeInput($description);
            return $role->save();
        }
        
        private function sanitizeInput($input) {
            // Implement comprehensive input validation
            return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
        }
    }
    ```

- [ ] **Task 2: Implement Permission Validation Layer**
    - [ ] Create validation wrapper to prevent privilege escalation:
    ```php
    class RoleTemplateValidator {
        private $maxPermissionLevels = [
            'Paralegal' => ACL_ALLOW_NORMAL,      // Level 1
            'Associate' => ACL_ALLOW_ENABLED,     // Level 89  
            'Partner' => ACL_ALLOW_ALL,           // Level 90 (but NOT admin)
        ];
        
        public function validateRoleTemplate($roleType, $permissions) {
            $maxLevel = $this->maxPermissionLevels[$roleType] ?? ACL_ALLOW_DEFAULT;
            return $this->enforceMaximumPermissions($permissions, $maxLevel);
        }
    }
    ```

### Phase 2: Secure Role Template Definitions (Days 2-3)

- [ ] **Task 3: Create Secure Role Template System**
    - [ ] Create `custom/include/security/LegalRoleTemplates.php`
    - [ ] **SECURITY ENHANCEMENT**: Define role templates with explicit security boundaries:
    ```php
    class LegalRoleTemplates {
        private static $templates = [
            'Paralegal' => [
                'description' => 'Limited access paralegal role',
                'max_permission_level' => ACL_ALLOW_NORMAL,
                'security_group_required' => true,
                'modules' => [
                    'Cases' => [
                        'access' => ACL_ALLOW_ENABLED,
                        'view' => ACL_ALLOW_OWNER,     // Only own cases
                        'list' => ACL_ALLOW_OWNER,
                        'edit' => ACL_ALLOW_OWNER,
                        'delete' => ACL_ALLOW_NONE,    // No delete permission
                        'export' => ACL_ALLOW_NONE,    // No data export
                        'import' => ACL_ALLOW_NONE,    // No data import
                    ],
                    'Contacts' => [
                        'access' => ACL_ALLOW_ENABLED,
                        'view' => ACL_ALLOW_OWNER,
                        'list' => ACL_ALLOW_OWNER,
                        'edit' => ACL_ALLOW_OWNER,
                        'delete' => ACL_ALLOW_NONE,
                    ],
                    'Administration' => [
                        'access' => ACL_ALLOW_NONE,    // No admin access
                    ],
                    'Users' => [
                        'access' => ACL_ALLOW_NONE,    // No user management
                    ],
                ],
            ],
            'Associate' => [
                'description' => 'Standard attorney access',
                'max_permission_level' => ACL_ALLOW_ENABLED,
                'security_group_required' => true,
                'modules' => [
                    'Cases' => [
                        'access' => ACL_ALLOW_ENABLED,
                        'view' => ACL_ALLOW_ALL,
                        'list' => ACL_ALLOW_ALL,
                        'edit' => ACL_ALLOW_ALL,
                        'delete' => ACL_ALLOW_OWNER,   // Only own cases
                        'export' => ACL_ALLOW_OWNER,
                        'import' => ACL_ALLOW_NONE,
                    ],
                    'Administration' => [
                        'access' => ACL_ALLOW_NONE,    // No admin access
                    ],
                ],
            ],
            'Partner' => [
                'description' => 'Senior attorney with management access',
                'max_permission_level' => ACL_ALLOW_ALL,
                'security_group_required' => false, // Can access all groups
                'modules' => [
                    'Cases' => [
                        'access' => ACL_ALLOW_ALL,
                        'view' => ACL_ALLOW_ALL,
                        'list' => ACL_ALLOW_ALL,
                        'edit' => ACL_ALLOW_ALL,
                        'delete' => ACL_ALLOW_ALL,
                        'export' => ACL_ALLOW_ALL,
                        'import' => ACL_ALLOW_ALL,
                    ],
                    'Users' => [
                        'access' => ACL_ALLOW_ENABLED,
                        'view' => ACL_ALLOW_ALL,
                        'edit' => ACL_ALLOW_NORMAL,    // Can edit non-admin users
                        'delete' => ACL_ALLOW_NONE,    // No user deletion
                    ],
                    'Administration' => [
                        'access' => ACL_ALLOW_NONE,    // Still no full admin
                    ],
                ],
            ],
        ];
    }
    ```

### Phase 3: Secure Implementation with Audit Trail (Days 3-4)

- [ ] **Task 4: Create Secure Role Application Engine**
    - [ ] Create `custom/modules/Administration/RoleTemplateManager.php`
    - [ ] **SECURITY FEATURES**: 
        - Comprehensive input validation
        - Permission boundary enforcement  
        - Complete audit logging
        - Rollback capability for failed applications
    ```php
    class RoleTemplateManager {
        private $auditLogger;
        private $validator;
        private $aclManager;
        
        public function applyRoleTemplate($userId, $templateName, $adminUserId) {
            // Validate admin permissions first
            if (!$this->validateAdminPermissions($adminUserId)) {
                throw new SecurityException('Insufficient permissions to apply role templates');
            }
            
            // Start transaction for rollback capability
            $this->db->startTransaction();
            
            try {
                $result = $this->performRoleApplication($userId, $templateName);
                $this->auditLogger->logRoleApplication($userId, $templateName, $adminUserId, 'SUCCESS');
                $this->db->commit();
                return $result;
            } catch (Exception $e) {
                $this->db->rollback();
                $this->auditLogger->logRoleApplication($userId, $templateName, $adminUserId, 'FAILED', $e->getMessage());
                throw $e;
            }
        }
    }
    ```

- [ ] **Task 5: Implement Security Group Integration**
    - [ ] Create secure security group management:
    ```php
    private function createSecurityGroupSecurely($userId, $roleType) {
        // Validate security group inheritance patterns
        $securityGroup = BeanFactory::newBean('SecurityGroups');
        $securityGroup->name = $this->generateSecureGroupName($userId, $roleType);
        $securityGroup->description = "Auto-generated security group for {$roleType}";
        $securityGroup->noninheritable = ($roleType === 'Partner') ? 0 : 1;
        
        // Validate group creation doesn't create privilege escalation
        if (!$this->validateGroupSecurity($securityGroup)) {
            throw new SecurityException('Security group would create privilege escalation');
        }
        
        return $securityGroup->save();
    }
    ```

### Phase 4: User Interface with Security Controls (Days 4-5)

- [ ] **Task 6: Create Secure Admin Interface**
    - [ ] Create `custom/modules/Administration/views/view.roletemplate.php`
    - [ ] **SECURITY ENHANCEMENTS**:
        - CSRF token validation
        - Session validation
        - Permission verification at UI level
        - Rate limiting for role applications
        - Confirmation dialogs for destructive actions

- [ ] **Task 7: Implement Comprehensive Logging**
    - [ ] Create security audit trail for all role operations:
    ```php
    class RoleSecurityAudit {
        public function logRoleApplication($userId, $templateName, $adminId, $status, $details = '') {
            $GLOBALS['log']->security(sprintf(
                'ROLE_TEMPLATE_APPLICATION: user_id=%s, template=%s, admin_id=%s, status=%s, details=%s, ip=%s',
                $userId, $templateName, $adminId, $status, $details, $_SERVER['REMOTE_ADDR']
            ));
            
            // Also log to database for compliance
            $this->createAuditRecord($userId, $templateName, $adminId, $status, $details);
        }
    }
    ```

### Phase 5: Testing & Security Validation (Days 5-7)

- [ ] **Task 8: Security Penetration Testing**
    - [ ] **Privilege Escalation Tests**:
        - Attempt to apply Partner template to Paralegal user
        - Test role boundary enforcement
        - Validate security group inheritance limits
        - Test SQL injection attempts on role creation
    
    - [ ] **Access Control Tests**:
        - Verify role templates don't grant admin access
        - Test module permission enforcement
        - Validate field-level access controls
        - Test export/import permission restrictions

- [ ] **Task 9: Integration Security Testing**  
    - [ ] **ACL System Integration**:
        - Test with existing ACL roles
        - Verify no conflicts with SecurityGroups
        - Test permission inheritance patterns
        - Validate admin bypass prevention
    
    - [ ] **Performance Security Testing**:
        - Test with large numbers of roles
        - Verify no security group explosion
        - Test ACL query performance impact
        - Validate memory usage for security operations

### Phase 6: Documentation & Deployment (Day 7)

- [ ] **Task 10: Security Documentation**
    - [ ] Create security implementation guide
    - [ ] Document permission boundaries for each role
    - [ ] Create incident response procedures
    - [ ] Document rollback procedures for security issues

---

## 3. Critical Security Risk Assessment

### CRITICAL RISK: Privilege Escalation
**Risk:** Role templates could inadvertently grant excessive permissions
**Mitigation:** 
- Hard-coded maximum permission levels per role type
- Multi-layer validation (template → validation → ACL → SecurityGroups)
- Comprehensive audit logging of all permission changes

### HIGH RISK: SQL Injection in ACL Operations
**Risk:** SuiteCRM's ACL system has identified SQL injection vulnerabilities  
**Mitigation:**
- Use BeanFactory for all database operations
- Implement input sanitization wrapper
- Use prepared statements where possible
- Add comprehensive input validation

### HIGH RISK: Security Group Inheritance Complexity
**Risk:** Complex inheritance could lead to unintended access
**Mitigation:**
- Simplify security group creation patterns
- Use non-inheritable groups for restrictive roles
- Add validation for group membership conflicts
- Implement group permission auditing

### MEDIUM RISK: Admin Permission Bypass
**Risk:** Existing ACL bypass patterns could affect role template system
**Mitigation:**
- Explicit admin permission checking
- Role template system operates within ACL constraints
- No modification of core admin bypass logic
- Comprehensive permission boundary enforcement

---

## 4. Implementation Constraints & Adaptations

### SuiteCRM Security Architecture Constraints
- Work within existing ACL action definitions
- Respect SecurityGroups inheritance patterns  
- Use standard permission level constants
- Integrate with existing admin user detection

### PHP 7.4 Security Limitations
- Limited to older security functions
- No modern password hashing for new users (core limitation)
- Use available input validation methods
- Work within session management constraints

### Database Security Considerations
- MySQL 5.7 transaction support for rollbacks
- Use existing audit table structure
- Implement proper indexing for security queries
- Consider performance impact of security checks

---

## 5. Success Criteria (Security-Focused)

1. **Security**: Zero privilege escalation vulnerabilities
2. **Audit**: Complete audit trail for all role operations
3. **Performance**: Security checks add <500ms to role application
4. **Validation**: All inputs properly sanitized and validated
5. **Rollback**: Failed applications leave no partial state
6. **Compliance**: All actions logged for legal compliance

---

## 6. Revised Effort Estimate

**Security Development:** 7 days (increased from 3)
**Security Testing:** 3 days (penetration testing and validation)
**Documentation:** 1 day (security procedures and compliance)
**Total:** 11 days (3.7x original due to security requirements)

**Risk-Adjusted Timeline:** 14 days (including contingency for security issues)

This revised plan transforms a "low-risk UI feature" into a comprehensive security enhancement that addresses the critical vulnerabilities identified in SuiteCRM's security architecture while maintaining the core functionality of role template application.