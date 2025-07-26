
# Feature Plan: Immutable Audit Trails

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 6 of 10

---

## 1. Objective

To provide a secure, user-friendly interface for viewing record audit trails and to implement a simple, effective mechanism to ensure the integrity of these logs, making them tamper-evident. This is crucial for legal compliance and internal security.

This is a **very low-risk** feature. It is almost entirely **read-only**. The only write operation is performed by a new, isolated cron job that does not touch any core tables. The feature leverages the existing, stable audit tables.

---

## 2. End-to-End Task List

### Phase 1: Backend Hashing Mechanism (Day 1)

- [ ] **Task 1: Create a New Database Table for Hashes**
    - [ ] In a new file `custom/sql/install.sql`, define a new table: `audit_log_hashes`.
    - [ ] **Columns:** `id` (primary key), `log_date` (date), `log_hash` (varchar 255).

- [ ] **Task 2: Create a Custom Scheduled Job**
    - [ ] Create a new file: `custom/modules/Schedulers/jobs/process_audit_hashes.php`.
    - [ ] This file will contain the logic for the new scheduled job.
    - [ ] **Logic:**
        1. Get all audit log entries from the `audit` table for the previous day.
        2. Concatenate the relevant fields of all entries into a single, long string.
        3. Generate a strong hash (e.g., SHA-256) of this string.
        4. Save the date and the resulting hash into our new `audit_log_hashes` table.

- [ ] **Task 3: Register the New Scheduled Job**
    - [ ] Go to `Admin -> Schedulers`.
    - [ ] Create a new Scheduler to run our `process_audit_hashes.php` job.
    - [ ] Schedule it to run once daily, shortly after midnight.

### Phase 2: Frontend Viewer (Day 2)

- [ ] **Task 4: Create the Audit Viewer Page**
    - [ ] Create a new entry point: `index.php?entryPoint=viewAuditLog`.
    - [ ] This page will be the main interface for viewing the audit logs.

- [ ] **Task 5: Implement the View Logic**
    - [ ] The entry point will display a date picker, allowing the user to select a day to review.
    - [ ] When a date is selected, the page will:
        1. Fetch all audit log entries for that day from the `audit` table.
        2. Re-calculate the hash of these entries on-the-fly using the exact same logic as the cron job.
        3. Fetch the *stored* hash for that day from the `audit_log_hashes` table.
        4. **Compare the hashes.**

- [ ] **Task 6: Display the Results**
    - [ ] If the calculated hash matches the stored hash, display a green "Verified" status message.
    - [ ] If the hashes do **not** match, display a prominent red "TAMPERED" warning. This indicates that the audit log for that day has been altered since it was originally recorded.
    - [ ] Display the fetched audit log entries in a clean, readable table format.

### Phase 3: Integration & Access (Day 2)

- [ ] **Task 7: Create Admin Menu Link**
    - [ ] Create a file: `custom/Extension/modules/Administration/Ext/Administration/auditviewer.php`.
    - [ ] This will add a new link, "Immutable Audit Log Viewer," to the main Admin page, pointing to our new entry point.

- [ ] **Task 8: Run Quick Repair and Rebuild**
    - [ ] Go to `Admin -> Repair -> Quick Repair and Rebuild` to register the new files and admin link.

### Phase 4: Testing (Day 3)

- [ ] **Task 9: Manual End-to-End Testing**
    - [ ] **Test Strategy:** Manual testing is the best way to verify this workflow.
    - [ ] **Test Cases:**
        1. Log in as an admin.
        2. Create and then edit a Contact record to generate some audit entries for today.
        3. Manually run the "Process Audit Hashes" scheduler.
        4. Navigate to the "Immutable Audit Log Viewer" from the Admin page.
        5. Select today's date. Verify that the audit entries are displayed and the status is "Verified".
        6. **Tampering Test:**
            - Manually connect to the database and `UPDATE` one of the audit log entries for today (e.g., change a `before_value_string`).
            - Refresh the Audit Log Viewer page for today.
            - **Verify the status message now shows "TAMPERED".**

- [ ] **Task 10: Run Existing Test Suite**
    - [ ] Run the full PHPUnit test suite (`../vendor/bin/phpunit` from the `tests/` directory) as a safety check.

---
