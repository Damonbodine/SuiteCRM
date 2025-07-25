
# Feature Plan: Asynchronous Email Sending

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 8 of 10

---

## 1. Objective

To prevent the UI from freezing when sending emails (especially bulk emails from a campaign or to a case's participants). This will be achieved by offloading the actual email sending process to a background job, providing a much smoother and more responsive user experience.

This is a **medium-risk** feature. The risk is carefully managed by making only a very small, targeted change to the existing email sending logic. Instead of sending the email, the "Send" button will now just add it to a queue. The core `SugarPHPMailer` class is not modified at all.

---

## 2. End-to-End Task List

### Phase 1: Backend Queue System (Day 1)

- [ ] **Task 1: Create the Email Queue Database Table**
    - [ ] In a new file `custom/sql/install.sql`, define a new table: `email_queue`.
    - [ ] **Columns:** `id` (primary key), `email_id` (varchar 36, foreign key to `emails` table), `status` (varchar, e.g., 'pending', 'sent', 'failed'), `date_created`, `date_modified`.

- [ ] **Task 2: Create the Email Queue Scheduled Job**
    - [ ] Create a new file: `custom/modules/Schedulers/jobs/process_email_queue.php`.
    - [ ] This file will contain the logic for the new scheduled job.
    - [ ] **Logic:**
        1. Query the `email_queue` table for all emails with `status = 'pending'`.
        2. For each pending email, retrieve the full `Email` bean using `BeanFactory`.
        3. Use the standard `SugarPHPMailer` class to send the email, exactly as the core system does now.
        4. If sending is successful, update the queue item's status to `'sent'`.
        5. If sending fails, update the status to `'failed'` and log the error for later review.

- [ ] **Task 3: Register the New Scheduled Job**
    - [ ] Go to `Admin -> Schedulers`.
    - [ ] Create a new Scheduler to run our `process_email_queue.php` job.
    - [ ] Schedule it to run frequently (e.g., every 1 minute).

### Phase 2: Implementation (Day 2)

- [ ] **Task 4: Intercept the Email Sending Process**
    - [ ] The core logic for sending emails is in `modules/Emails/Email.php`, specifically in the `send()` method. This is a high-risk file to change directly.
    - [ ] **Safe Approach:** I will use a `before_save` logic hook on the `Emails` module.
    - [ ] Create a file: `custom/modules/Emails/logic_hooks.php`.
    - [ ] Create the hook implementation file: `custom/modules/Emails/EmailHooks.php`.
    - [ ] **Hook Logic (`intercept_send` function):**
        1. The hook will check if the email's status is being set to `'sent'`.
        2. If it is, the hook will **prevent the original save** by returning `false`.
        3. It will then save the email bean with a status of `'queued'` instead.
        4. Finally, it will create a new record in our `email_queue` table with the `email_id` and a status of `'pending'`.

### Phase 3: Testing (Day 3)

- [ ] **Task 5: Unit Testing**
    - [ ] Create a new PHPUnit test file: `tests/unit/phpunit/custom/modules/Schedulers/ProcessEmailQueueTest.php`.
    - [ ] **Test Strategy:** Test the queue processing logic in isolation.
    - [ ] **Test Cases:**
        - `testJobSendsPendingEmails()`: Create a mock `Email` bean and a record in the `email_queue`. Run the job's logic and verify that the `SugarPHPMailer->send()` method is called.
        - `testJobUpdatesStatusOnSuccess()`: Verify the queue item status is updated to `'sent'` after a successful mock send.
        - `testJobUpdatesStatusOnFailure()`: Verify the queue item status is updated to `'failed'` when the mock send fails.

- [ ] **Task 6: Acceptance Testing**
    - [ ] **Test Strategy:** An end-to-end manual test is required.
    - [ ] **Test Scenario:**
        1. Log in as a user.
        2. Compose a new email from the `Emails` module.
        3. Click the "Send" button.
        4. **Immediate Verification:** The UI should return to the previous screen instantly, without any delay.
        5. Navigate to the `Emails` module list view. Verify the email appears with a status of `'Queued'`.
        6. Manually run the "Process Email Queue" scheduler from the Admin panel.
        7. Refresh the `Emails` list view. Verify the email's status has changed to `'Sent'`.
        8. Check the recipient's inbox to confirm the email was actually delivered.

- [ ] **Task 7: Run Existing Test Suite**
    - [ ] Run the full PHPUnit test suite (`../vendor/bin/phpunit` from the `tests/` directory) to ensure no core functionality has been impacted.

---
