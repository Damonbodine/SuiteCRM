# Feature Plan: Secure Client Messaging Portal - REVISED

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 10 of 10  
**Risk Level:** CRITICAL (Updated from Medium - Major Security and Compliance Concerns)

---

## 1. Objective - SIGNIFICANTLY SCALED BACK

~~To provide a secure, modern, and isolated web portal where clients can log in to view the status of their case, see key documents, and exchange messages with their attorney.~~

**REVISED OBJECTIVE**: To provide a minimal, security-hardened internal messaging system for attorney-to-client communications that operates entirely within SuiteCRM's authenticated environment, eliminating the need for a separate client-facing portal.

**CRITICAL SECURITY FINDINGS THAT REQUIRE COMPLETE REDESIGN:**
- SuiteCRM has critical MD5 password hashing vulnerability (CVSS 9.1)
- Existing session management has fixation vulnerabilities  
- SQL injection risks throughout ACL system
- Creating client-facing portal dramatically increases attack surface
- Separate PHP application creates additional security maintenance burden
- Legal industry compliance requirements (attorney-client privilege, data confidentiality)
- Small law firms typically lack dedicated security staff to maintain client portals

---

## 2. Security-First Alternative Architecture

### Recommendation: INTERNAL MESSAGING SYSTEM INSTEAD OF CLIENT PORTAL

Rather than creating a security-vulnerable client portal, implement an internal secure messaging system that attorneys can use to communicate with clients via secure email integration.

### Phase 1: Risk Assessment & Security Requirements (Days 1-2)

- [ ] **Task 1: Abandon Client-Facing Portal Approach**
    - [ ] **SECURITY RATIONALE**: Client portals represent the #1 security risk for law firms
    - [ ] **COMPLIANCE RATIONALE**: Attorney-client privilege requires end-to-end control
    - [ ] **RESOURCE RATIONALE**: Small firms cannot maintain separate security infrastructure

- [ ] **Task 2: Design Internal Attorney-to-Client Communication System**  
    - [ ] Create secure internal messaging within SuiteCRM authenticated environment
    - [ ] Integrate with existing email system for client delivery
    - [ ] Use SuiteCRM's existing security controls (ACL, SecurityGroups)
    ```php
    // Internal messaging approach - no client login required
    class SecureClientCommunication {
        public function sendSecureMessage($caseId, $messageText, $attorneyUserId) {
            // All security handled by existing SuiteCRM authentication
            // No separate client authentication system needed
            // Messages delivered via secure email with controlled access
        }
    }
    ```

### Phase 2: Secure Internal Implementation (Days 2-4)

- [ ] **Task 3: Create Case Communication Module**
    - [ ] Use Module Builder to create `CaseMessages` module
    - [ ] **Security-enhanced field definitions**:
    ```php
    $dictionary['CaseMessages'] = array(
        'table' => 'case_messages',
        'fields' => array(
            'case_id' => array('name' => 'case_id', 'type' => 'id', 'required' => true),
            'message_content' => array('name' => 'message_content', 'type' => 'text'),
            'message_type' => array('name' => 'message_type', 'type' => 'enum', 
                'options' => 'case_message_types', 
                'default' => 'internal_note'),
            'attorney_user_id' => array('name' => 'attorney_user_id', 'type' => 'id'),
            'is_client_visible' => array('name' => 'is_client_visible', 'type' => 'bool', 'default' => 0),
            'encryption_hash' => array('name' => 'encryption_hash', 'type' => 'varchar', 'len' => 64),
            'client_email_sent' => array('name' => 'client_email_sent', 'type' => 'bool', 'default' => 0),
        ),
        'indices' => array(
            'idx_case_messages_case' => array('name' => 'idx_case_messages_case', 
                'type' => 'index', 'fields' => array('case_id', 'deleted')),
        ),
    );
    ```

- [ ] **Task 4: Secure Message Handling**
    - [ ] Implement message encryption for sensitive content
    - [ ] Use proper ACL integration for attorney access control
    ```php
    class CaseMessagesBean extends Basic {
        public function save($check_notify = false, $fts_index_bean = true) {
            // Encrypt sensitive content before saving
            if (!empty($this->message_content)) {
                $this->message_content = $this->encryptMessage($this->message_content);
                $this->encryption_hash = $this->generateMessageHash();
            }
            
            // Ensure proper ACL enforcement
            if (!$this->ACLAccess('save')) {
                $GLOBALS['log']->security("Unauthorized case message save attempt");
                return false;
            }
            
            return parent::save($check_notify, $fts_index_bean);
        }
        
        private function encryptMessage($content) {
            // Use stronger encryption than core SuiteCRM
            $key = $this->getEncryptionKey();
            return openssl_encrypt($content, 'AES-256-CBC', $key, 0, random_bytes(16));
        }
    }
    ```

### Phase 3: Secure Client Delivery System (Days 4-5)

- [ ] **Task 5: Secure Email Integration**
    - [ ] Create controlled email delivery system for client communications  
    - [ ] Implement email encryption and digital signatures
    ```php
    class SecureClientEmailer {
        public function sendSecureMessageToClient($caseMessage) {
            $case = BeanFactory::getBean('Cases', $caseMessage->case_id);
            $client = $this->getClientFromCase($case);
            
            if (!$client || !$this->validateClientEmail($client->email1)) {
                return false;
            }
            
            // Generate secure message with limited information
            $secureMessage = $this->generateSecureClientEmail($caseMessage);
            
            // Send via existing SuiteCRM email system but with encryption
            $emailBean = BeanFactory::newBean('Emails');
            $emailBean->name = 'Secure Message from ' . $case->assigned_user_name;
            $emailBean->description_html = $this->encryptEmailContent($secureMessage);
            $emailBean->to_addrs = $client->email1;
            
            return $this->sendEncryptedEmail($emailBean);
        }
        
        private function generateSecureClientEmail($caseMessage) {
            // Minimal information disclosure - no case details
            return "You have received a secure message regarding your legal matter. " .
                   "Please contact our office at [phone] to discuss. " .
                   "Reference: " . substr($caseMessage->id, 0, 8);
        }
    }
    ```

- [ ] **Task 6: Attorney Interface for Client Communication**
    - [ ] Create secure interface within SuiteCRM for attorneys
    - [ ] Add to Cases DetailView as subpanel with enhanced security
    ```php
    // Custom subpanel for case messages with security controls
    class CaseMessagesSubpanel extends SubPanelView {
        public function display() {
            // Enhanced ACL checking for case messages
            if (!$this->validateAttorneyAccess()) {
                return '<div class="error">Access Denied: Insufficient permissions for case communications</div>';
            }
            
            // Load messages with encryption handling
            $messages = $this->loadDecryptedMessages();
            $this->ss->assign('messages', $messages);
            return parent::display();
        }
        
        private function validateAttorneyAccess() {
            // Multi-layer security validation
            $currentUser = $GLOBALS['current_user'];
            $case = $this->focus;
            
            // Check case access
            if (!$case->ACLAccess('view')) return false;
            
            // Check if user is assigned attorney or has appropriate role
            if ($case->assigned_user_id !== $currentUser->id && 
                !$this->hasAttorneyRole($currentUser)) return false;
            
            // Check security group membership
            if (!$this->hasSecurityGroupAccess($case)) return false;
            
            return true;
        }
    }
    ```

### Phase 4: Compliance & Documentation (Days 5-6)

- [ ] **Task 7: Legal Compliance Features**
    - [ ] Implement comprehensive audit logging for all client communications
    - [ ] Add attorney-client privilege protection markers
    - [ ] Create compliance reporting for legal requirements
    ```php
    class ClientCommunicationAuditor {
        public function logCommunication($caseMessage, $action, $userId) {
            // Enhanced audit logging for legal compliance
            $auditRecord = array(
                'case_id' => $caseMessage->case_id,
                'message_id' => $caseMessage->id,
                'action' => $action, // 'created', 'viewed', 'sent_to_client', 'encrypted'
                'user_id' => $userId,
                'timestamp' => date('Y-m-d H:i:s'),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'privilege_claimed' => 1, // Attorney-client privilege
            );
            
            $this->createAuditRecord($auditRecord);
            
            // Also log to secure external audit system if required
            $this->logToExternalComplianceSystem($auditRecord);
        }
    }
    ```

- [ ] **Task 8: Security Documentation**
    - [ ] Create security procedures documentation
    - [ ] Document encryption and access control procedures
    - [ ] Create incident response plan for communication security

### Phase 5: Security Testing & Validation (Days 6-7)

- [ ] **Task 9: Security Penetration Testing**
    - [ ] **Access Control Testing**:
        - Verify attorneys cannot access messages from cases not assigned to them
        - Test SecurityGroup boundary enforcement
        - Validate proper ACL integration
    
    - [ ] **Encryption Validation**:
        - Verify message content is properly encrypted at rest
        - Test decryption only works for authorized users
        - Validate encryption key management
    
    - [ ] **Audit Trail Testing**:
        - Verify all communications are properly logged
        - Test audit log integrity and tamper detection
        - Validate compliance reporting accuracy

- [ ] **Task 10: Legal Compliance Validation**
    - [ ] **Attorney-Client Privilege Protection**:
        - Verify privilege markers are properly applied
        - Test access restrictions for non-attorney staff
        - Validate proper handling of privileged communications
    
    - [ ] **Data Protection Compliance**:
        - Test data retention policies
        - Verify secure deletion procedures
        - Validate client consent and notification procedures

---

## 3. Why Client Portal Approach Was Rejected

### CRITICAL SECURITY RISKS IDENTIFIED:

1. **Catastrophic Risk: Client-Facing Attack Surface**
   - Client portals are the #1 attack vector for law firm breaches
   - Small firms lack security expertise to maintain client-facing applications
   - Creates separate authentication system with own vulnerabilities

2. **SuiteCRM Core Security Vulnerabilities**  
   - MD5 password hashing (CVSS 9.1) makes any authentication system vulnerable
   - Session fixation vulnerabilities could compromise client accounts
   - SQL injection risks could expose all client data

3. **Legal Industry Compliance Failures**
   - Client portals often violate attorney-client privilege requirements
   - Create data residency and access control complications
   - Require extensive security auditing small firms cannot afford

4. **Operational Security Burden**
   - Separate application requires separate security patching
   - Client password management becomes firm's responsibility
   - Incident response complexity increases dramatically

---

## 4. Revised Success Criteria (Internal System)

1. **Security**: All communications encrypted and audit logged
2. **Compliance**: Attorney-client privilege protection enforced  
3. **Usability**: Attorneys can communicate securely within SuiteCRM
4. **Integration**: Seamless integration with existing Cases module
5. **Auditability**: Complete compliance reporting for legal requirements

---

## 5. Risk Assessment - Revised Approach

### ELIMINATED RISKS (Client Portal Removed):
- ~~Client authentication vulnerabilities~~
- ~~Public-facing attack surface~~  
- ~~Separate application maintenance~~
- ~~Client data exposure through portal~~

### REMAINING MANAGEABLE RISKS:

**MEDIUM RISK: Internal Message Security**
**Mitigation**: Use strong encryption, proper ACL enforcement, comprehensive auditing

**LOW RISK: Email Delivery Security** 
**Mitigation**: Use secure email with minimal information disclosure

---

## 6. Revised Effort Estimate

**Security-Enhanced Internal System:** 7 days (vs. 15+ for secure client portal)
**Compliance Integration:** 2 days  
**Security Testing:** 2 days
**Total:** 11 days (reduced from original 15+ due to eliminated client portal complexity)

**Risk-Adjusted Timeline:** 13 days (significantly reduced risk profile)

---

## 7. Recommendation Summary

**STRONGLY RECOMMEND**: Implement internal secure messaging system as described above.

**STRONGLY DISCOURAGE**: Any client-facing portal given:
- SuiteCRM's critical security vulnerabilities  
- Small law firm resource constraints
- Legal industry compliance requirements
- Dramatic increase in attack surface and maintenance burden

This revised approach provides secure attorney-client communication while working within SuiteCRM's security constraints and legal industry compliance requirements.