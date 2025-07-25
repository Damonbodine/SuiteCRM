# Feature Plan: Asynchronous Email Sending - REVISED

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 8 of 10  
**Risk Level:** HIGH (Updated from Medium - Architecture and Performance Concerns)

---

## 1. Objective

To prevent UI blocking during email sending while working within SuiteCRM's PHP 7.4 environment and existing email infrastructure. This implementation must use SuiteCRM's existing queue system and avoid breaking core email functionality.

**CRITICAL ARCHITECTURAL CONSTRAINTS IDENTIFIED:**
- PHP 7.4 has no native async/await or modern queue capabilities
- SuiteCRM has existing SugarQueue/JobQueue system that should be used
- Intercepting email save operations is high-risk and could break functionality
- SugarPHPMailer is tightly integrated with global state and session context
- N+1 query problems exist in email-related operations
- Heavy global state dependencies in email system

---

## 2. Revised Architecture-Aware Implementation

### Phase 1: Integration with Existing Queue System (Days 1-2)

- [ ] **Task 1: Leverage Existing SuiteCRM Queue Infrastructure**
    - [ ] Use existing `include/SugarQueue/SugarJobQueue.php` instead of custom table
    - [ ] Create new job type: `custom/include/SugarQueue/jobs/SugarJobSendEmail.php`
    - [ ] **Integration with existing patterns**:
    ```php
    class SugarJobSendEmail implements RunnableSchedulerJob {
        public function run($arguments) {
            $emailId = $arguments['email_id'];
            $emailBean = BeanFactory::getBean('Emails', $emailId);
            
            if (!$emailBean) {
                $GLOBALS['log']->error("Email job: Email not found: {$emailId}");
                return false;
            }
            
            // Use existing SugarPHPMailer infrastructure
            return $this->sendEmailViaExistingSystem($emailBean);
        }
    }
    ```

- [ ] **Task 2: Create Email Queue Manager**
    - [ ] Create `custom/include/email/AsyncEmailManager.php`
    - [ ] **Work within PHP 7.4 limitations**:
    ```php
    class AsyncEmailManager {
        public function queueEmail($emailBean) {
            // Use existing SugarJobQueue instead of custom table
            $job = new SchedulersJob();
            $job->name = "Send Email Async: " . $emailBean->name;
            $job->job = 'function::SugarJobSendEmail';
            $job->data = json_encode(['email_id' => $emailBean->id]);
            $job->assigned_user_id = $GLOBALS['current_user']->id;
            
            return $job->save();
        }
        
        // Avoid PHP 7.4+ features, use traditional patterns
        private function validateEmailForQueue($emailBean) {
            // Traditional PHP validation patterns
            if (empty($emailBean->id)) return false;
            if (empty($emailBean->to_addrs)) return false;
            return true;
        }
    }
    ```

### Phase 2: Safe Email Interception Strategy (Days 2-3)

- [ ] **Task 3: Implement Non-Breaking Email Hook**
    - [ ] **SAFER APPROACH**: Instead of intercepting before_save, create custom email action
    - [ ] Create `custom/modules/Emails/views/view.asyncsend.php`
    - [ ] **Logic**: Create separate "Send Async" action instead of modifying core send
    ```php
    class EmailViewAsyncSend extends ViewDetail {
        public function display() {
            $emailBean = $this->bean;
            
            // Validate email is ready to send
            if (!$this->validateEmailReadyToSend($emailBean)) {
                SugarApplication::appendErrorMessage('Email not ready for async sending');
                return;
            }
            
            // Queue instead of immediate send
            $asyncManager = new AsyncEmailManager();
            $success = $asyncManager->queueEmail($emailBean);
            
            if ($success) {
                $emailBean->status = 'queued';
                $emailBean->save();
                SugarApplication::appendSuccessMessage('Email queued for sending');
            }
        }
    }
    ```

- [ ] **Task 4: UI Integration via Custom Button**
    - [ ] Create `custom/modules/Emails/metadata/detailviewdefs.php` override
    - [ ] Add "Send Async" button instead of modifying core "Send" button
    - [ ] **Safer UI pattern**:
    ```php
    $viewdefs['Emails']['DetailView']['templateMeta']['form']['buttons'] = array_merge(
        $viewdefs['Emails']['DetailView']['templateMeta']['form']['buttons'],
        array(
            array(
                'customCode' => '<input type="button" onclick="location.href=\'index.php?module=Emails&action=asyncsend&record={$id}\'" value="Send Async" class="button">',
            )
        )
    );
    ```

### Phase 3: Performance-Optimized Queue Processing (Days 3-4)

- [ ] **Task 5: Implement Efficient Email Sending**
    - [ ] Address identified N+1 query issues in email processing
    - [ ] Create optimized batch processing:
    ```php
    class SugarJobSendEmail implements RunnableSchedulerJob {
        public function run($arguments) {
            $emailId = $arguments['email_id'];
            
            // Optimize: Load email with related data in single query
            $emailBean = BeanFactory::getBean('Emails', $emailId);
            if (!$emailBean) return false;
            
            // Preserve global state context for SugarPHPMailer
            $originalUser = $GLOBALS['current_user'];
            if (!empty($arguments['user_context'])) {
                $GLOBALS['current_user'] = BeanFactory::getBean('Users', $arguments['user_context']);
            }
            
            try {
                return $this->sendEmailSafely($emailBean);
            } finally {
                // Restore original context
                $GLOBALS['current_user'] = $originalUser;
            }
        }
        
        private function sendEmailSafely($emailBean) {
            // Use existing SugarPHPMailer but with error handling
            try {
                $mailer = new SugarPHPMailer();
                $mailer->prepForOutbound();
                $mailer->From = $emailBean->from_addr;
                $mailer->FromName = $emailBean->from_name;
                $mailer->Subject = $emailBean->name;
                $mailer->Body = $emailBean->description_html ?: $emailBean->description;
                $mailer->IsHTML(!empty($emailBean->description_html));
                
                // Handle recipients properly
                $recipients = explode(',', $emailBean->to_addrs);
                foreach ($recipients as $recipient) {
                    $mailer->AddAddress(trim($recipient));
                }
                
                if ($mailer->Send()) {
                    $emailBean->status = 'sent';
                    $emailBean->date_sent = date('Y-m-d H:i:s');
                    $emailBean->save();
                    return true;
                } else {
                    $GLOBALS['log']->error("Email send failed: " . $mailer->ErrorInfo);
                    $emailBean->status = 'failed';
                    $emailBean->save();
                    return false;
                }
            } catch (Exception $e) {
                $GLOBALS['log']->error("Email send exception: " . $e->getMessage());
                $emailBean->status = 'failed';
                $emailBean->save();
                return false;
            }
        }
    }
    ```

### Phase 4: Scheduler Integration & Configuration (Day 4)

- [ ] **Task 6: Proper Scheduler Registration**
    - [ ] Work within existing scheduler framework
    - [ ] Create `custom/Extension/modules/Schedulers/Ext/ScheduledTasks/async_email.php`
    ```php
    $job_strings[] = 'AsyncEmailProcessor';
    
    array_push($job_strings, 'AsyncEmailProcessor');
    
    function AsyncEmailProcessor() {
        // Process queued email jobs
        return processQueuedEmailJobs();
    }
    
    function processQueuedEmailJobs() {
        $scheduler = new Scheduler();
        $jobs = $scheduler->getJobs('function::SugarJobSendEmail', 'queued');
        
        $processed = 0;
        foreach ($jobs as $job) {
            if ($job->runJob()) {
                $processed++;
            }
            
            // Rate limiting to avoid overwhelming SMTP server
            if ($processed >= 10) break; // Max 10 emails per run
        }
        
        return $processed > 0;
    }
    ```

- [ ] **Task 7: Configuration Management**
    - [ ] Add async email settings to SuiteCRM configuration
    - [ ] Create admin interface for queue management
    - [ ] **Configuration options**:
        - Max emails per batch
        - Queue processing frequency
        - SMTP rate limiting
        - Failure retry settings

### Phase 5: Testing & Performance Validation (Days 5-6)

- [ ] **Task 8: Performance Testing**
    - [ ] **Load Testing**: Test with 100+ queued emails
    - [ ] **Memory Testing**: Verify no memory leaks in queue processing  
    - [ ] **SMTP Testing**: Test with various SMTP configurations
    - [ ] **Global State Testing**: Verify user context preservation

- [ ] **Task 9: Integration Testing**
    - [ ] **Scheduler Integration**: Test with existing scheduled jobs
    - [ ] **Email System Integration**: Verify no interference with synchronous emails
    - [ ] **UI Flow Testing**: Test complete async send workflow
    - [ ] **Failure Recovery**: Test queue recovery after failures

- [ ] **Task 10: Compatibility Testing**
    - [ ] **PHP 7.4 Compatibility**: Verify no modern PHP features used
    - [ ] **MySQL 5.7 Compatibility**: Test queue table performance
    - [ ] **SuiteCRM Integration**: Test with existing modules and customizations

---

## 3. Risk Assessment & Mitigation

### CRITICAL RISK: Breaking Core Email Functionality
**Risk:** Intercepting core email operations could break existing functionality
**Mitigation:**
- Use separate "Send Async" action instead of modifying core send
- Preserve all existing email functionality unchanged
- Extensive testing of both sync and async email paths

### HIGH RISK: PHP 7.4 Performance Limitations  
**Risk:** No true async capabilities may cause performance bottlenecks
**Mitigation:**
- Use existing SugarJobQueue for proven queue processing
- Implement rate limiting and batch processing
- Add comprehensive performance monitoring

### HIGH RISK: Global State Dependencies
**Risk:** SugarPHPMailer depends heavily on global state (user, session, config)
**Mitigation:**
- Preserve user context in queue job data
- Restore global state before email processing
- Handle edge cases where context is invalid

### MEDIUM RISK: SMTP Server Overload
**Risk:** Bulk async processing could overwhelm SMTP servers
**Mitigation:**
- Implement rate limiting (max emails per batch)
- Add configurable delays between sends
- Monitor SMTP server response and adapt accordingly

---

## 4. Technical Constraints & Adaptations

### PHP 7.4 Environment Constraints
- No async/await syntax - use traditional job queue pattern
- Limited modern array/string functions - use PHP 7.4 compatible syntax
- No modern HTTP clients - use existing cURL/SugarPHPMailer infrastructure

### SuiteCRM Architecture Integration
- Use existing SugarJobQueue instead of custom queue table
- Integrate with existing scheduler infrastructure
- Preserve SuiteCRM's vardefs and metadata patterns
- Work within existing ACL and security group constraints

### Performance Considerations
- Address identified N+1 query issues in email loading
- Use batched processing to avoid memory issues
- Implement proper database indexing for queue operations
- Monitor and limit resource usage during queue processing

---

## 5. Success Criteria

1. **Performance**: UI response time <1 second for email queueing
2. **Reliability**: 99%+ successful delivery rate for queued emails  
3. **Compatibility**: Zero impact on existing synchronous email functionality
4. **Scalability**: Handle 1000+ queued emails without memory issues
5. **Recovery**: Automatic retry and failure handling for failed sends

---

## 6. Revised Effort Estimate

**Queue System Integration:** 2 days (using existing infrastructure)
**Safe Email Interception:** 2 days (non-breaking approach)
**Performance Optimization:** 2 days (addressing N+1 issues)
**Testing & Validation:** 2 days (performance and compatibility)
**Total:** 8 days (increased from 3 due to architectural constraints)

**Risk-Adjusted Timeline:** 10 days (including contingency for PHP 7.4 limitations)

This revised plan transforms the feature into a properly integrated solution that works within SuiteCRM's architectural constraints while addressing the performance and compatibility issues identified in the codebase analysis.