# Feature Plan: Immutable Audit Trails - REVISED

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 6 of 10  
**Risk Level:** MEDIUM (Updated from Low - Schema and Security Considerations)

---

## 1. Objective

To provide a secure, user-friendly interface for viewing record audit trails with tamper-evident integrity verification. This feature addresses legal compliance requirements while working within SuiteCRM's existing architecture constraints and security limitations.

**CRITICAL ARCHITECTURAL CONSTRAINTS IDENTIFIED:**
- PHP 7.4 and MySQL 5.7 environment limitations
- Legacy MD5 hashing vulnerability in core system requires avoiding weak hashing
- SuiteCRM's vardefs-based schema management (not raw SQL)
- Performance concerns with audit table queries (potential N+1 issues)
- Global state dependencies in scheduler system

---

## 2. Revised Implementation Approach

### Phase 1: Schema Definition via Vardefs (Days 1-2)

- [ ] **Task 1: Create Proper Schema via Vardefs**
    - [ ] Create `custom/Extension/modules/audit_log_hashes/Ext/Vardefs/vardefs.ext.php`
    - [ ] Use SuiteCRM's standard vardefs pattern instead of raw SQL:
    ```php
    $dictionary['audit_log_hashes'] = array(
        'table' => 'audit_log_hashes',
        'fields' => array(
            'id' => array('name' => 'id', 'type' => 'id', 'required' => true),
            'log_date' => array('name' => 'log_date', 'type' => 'date', 'required' => true),
            'log_hash' => array('name' => 'log_hash', 'type' => 'varchar', 'len' => '64'), // SHA-256 length
            'record_count' => array('name' => 'record_count', 'type' => 'int'),
            'date_created' => array('name' => 'date_created', 'type' => 'datetime'),
        ),
        'indices' => array(
            'idx_log_date' => array('name' => 'idx_log_date', 'type' => 'index', 'fields' => array('log_date')),
        ),
    );
    ```

- [ ] **Task 2: Create SugarBean Class**
    - [ ] Create `custom/modules/audit_log_hashes/audit_log_hashes.php`
    - [ ] Extend SugarBean properly for database integration
    ```php
    class audit_log_hashes extends Basic {
        public $table_name = 'audit_log_hashes';
        public $object_name = 'audit_log_hashes';
        public $module_dir = 'audit_log_hashes';
    }
    ```

### Phase 2: Secure Hash Implementation (Days 2-3)

- [ ] **Task 3: Implement Secure Hashing Logic**
    - [ ] Create `custom/modules/Schedulers/jobs/process_audit_hashes.php`
    - [ ] **SECURITY ENHANCEMENT**: Use SHA-256 instead of MD5 due to identified core system vulnerabilities
    - [ ] **Performance Optimization**: Implement batched processing to handle large audit logs
    ```php
    function processAuditHashes() {
        // Use SHA-256 for cryptographic security
        $hash = hash('sha256', $concatenatedAuditData);
        
        // Batch process to avoid N+1 query issues
        $batchSize = 1000;
        $auditRecords = $this->getAuditRecordsBatch($targetDate, $batchSize);
    }
    ```

- [ ] **Task 4: Integrate with Existing Scheduler Infrastructure**
    - [ ] Register scheduler via `custom/Extension/modules/Schedulers/Ext/ScheduledTasks/`
    - [ ] Use existing `SugarJobQueue` system instead of direct scheduler creation
    - [ ] Add proper error handling and logging via `$GLOBALS['log']`

### Phase 3: Frontend Implementation with Architecture Considerations (Days 3-4)

- [ ] **Task 5: Create Entry Point with Proper Security**
    - [ ] Create `custom/include/MVC/Controller/entry_point_registry.php` entry
    - [ ] Implement proper ACL checks using existing `ACLController::checkAccess()`
    - [ ] Use SuiteCRM's standard authentication patterns

- [ ] **Task 6: Database Query Optimization**
    - [ ] Implement efficient audit log queries to avoid N+1 issues:
    ```php
    // Use SugarBean's get_full_list with proper WHERE clauses
    $auditBean = BeanFactory::newBean('audit');
    $auditRecords = $auditBean->get_full_list(
        'date_created ASC',
        "DATE(date_created) = '{$selectedDate}'"
    );
    ```
    - [ ] Add database indexing recommendations for audit table performance

- [ ] **Task 7: Frontend Display with SuiteCRM Standards**
    - [ ] Use SuiteCRM's Sugar_Smarty templating engine
    - [ ] Implement proper CSS classes from existing theme system
    - [ ] Add language file support: `custom/include/language/en_us.auditviewer.php`

### Phase 4: Integration & Security Enhancements (Day 4)

- [ ] **Task 8: Admin Integration via Extension Framework**
    - [ ] Use proper extension: `custom/Extension/modules/Administration/Ext/Administration/`
    - [ ] Add ACL permission checks for admin-only access
    - [ ] Integrate with existing admin menu structure

- [ ] **Task 9: Enhanced Security Features**
    - [ ] Add audit log export restrictions (prevent data exfiltration)
    - [ ] Implement proper user session validation
    - [ ] Add rate limiting for hash verification requests
    - [ ] Log all audit viewing activities for compliance

### Phase 5: Testing & Validation (Days 5-6)

- [ ] **Task 10: Database Performance Testing**
    - [ ] Test with realistic audit log sizes (10k+ records)
    - [ ] Verify MySQL 5.7 compatibility
    - [ ] Test index performance on audit table queries

- [ ] **Task 11: Security Testing**
    - [ ] Verify SHA-256 hash integrity under various tampering scenarios
    - [ ] Test ACL enforcement for different user roles
    - [ ] Validate proper session handling and CSRF protection

- [ ] **Task 12: Integration Testing**
    - [ ] Test scheduler integration with existing SuiteCRM jobs
    - [ ] Verify proper error handling and logging
    - [ ] Test Quick Repair and Rebuild compatibility

---

## 3. Risk Assessment & Mitigation

### HIGH RISK: Database Schema Management
**Risk:** SuiteCRM's vardefs system is complex and schema changes can break upgrades
**Mitigation:** 
- Use standard vardefs pattern
- Test with Quick Repair and Rebuild
- Document schema changes for upgrade compatibility

### MEDIUM RISK: Performance Impact on Audit Queries  
**Risk:** Large audit tables could cause performance issues
**Mitigation:**
- Implement proper database indexing
- Use batched processing for hash generation
- Add query optimization for date-range selections

### MEDIUM RISK: Security Implementation
**Risk:** Weak hashing could undermine audit trail integrity
**Mitigation:**
- Use SHA-256 instead of MD5
- Implement proper access controls
- Add comprehensive logging of audit access

### LOW RISK: Scheduler Integration
**Risk:** Custom scheduler jobs might conflict with existing jobs
**Mitigation:**
- Use existing SugarJobQueue infrastructure
- Implement proper error handling
- Test with existing scheduler workload

---

## 4. Technical Constraints & Adaptations

### PHP 7.4 Compatibility
- Avoid using PHP 8+ features
- Use traditional array syntax instead of short syntax
- Maintain compatibility with SuiteCRM's global state patterns

### MySQL 5.7 Limitations
- Use standard MySQL 5.7 date/time functions
- Avoid newer JSON column features
- Implement proper indexing for older MySQL versions

### SuiteCRM Architecture Constraints
- Work within SugarBean ORM limitations
- Use global `$GLOBALS` patterns where necessary
- Integrate with existing ACL and SecurityGroups systems

---

## 5. Success Criteria

1. **Functional**: Hash verification correctly detects audit log tampering
2. **Performance**: Page loads under 3 seconds for 1 month of audit data
3. **Security**: All access properly controlled via ACL system
4. **Compatibility**: No impact on existing SuiteCRM functionality
5. **Maintainable**: Standard SuiteCRM patterns for future updates

---

## 6. Estimated Effort

**Total Development Time:** 6 days (increased from 3 due to architectural constraints)
**Additional Testing:** 2 days for performance and security validation
**Documentation:** 1 day for deployment and maintenance guides

**Risk-Adjusted Timeline:** 9 days total (3x original estimate due to complexity)

This revised plan addresses the architectural realities of the SuiteCRM PHP 7.4/MySQL 5.7 environment while maintaining the core functionality of immutable audit trails with proper security considerations.