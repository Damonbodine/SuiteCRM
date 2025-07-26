
# Feature Plan: Secure Client Intake Form

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 3 of 10

---

## 1. Objective

To create a secure, standalone web form that law firms can link to from their website or send directly to potential new clients. This form will capture initial case details and automatically create a `Lead` record in SuiteCRM, reducing manual data entry and ensuring data is captured consistently.

This is a **very low-risk** feature. The form itself is a new, completely isolated PHP file. It will not be part of the main SuiteCRM application UI. It will only interact with the core application through the modern, stable, and secure V8 REST API, which is designed for this exact purpose. No core files will be modified.

---

## 2. End-to-End Task List

### Phase 1: Backend API Preparation (Day 1)

- [ ] **Task 1: Verify V8 API is Enabled**
    - [ ] Ensure the V8 API is active and accessible. My previous analysis confirmed it is modern and well-designed.

- [ ] **Task 2: Configure API User & Client Credentials**
    - [ ] In SuiteCRM, navigate to `Admin -> OAuth2 Clients and Tokens`.
    - [ ] Create a new "Client Credentials Grant" client. This client will be used exclusively by our intake form for server-to-server authentication.
    - [ ] Securely note the generated Client ID and Client Secret.

### Phase 2: Standalone Form Implementation (Day 1-2)

- [ ] **Task 3: Create the Intake Form File**
    - [ ] Create a new file in the SuiteCRM root directory: `intake_form.php`.
    - [ ] This file will contain all the HTML and PHP logic for the form.

- [ ] **Task 4: Build the HTML Form**
    - [ ] Inside `intake_form.php`, create a simple, clean HTML form using a modern CSS framework like Bootstrap (via CDN) for a professional look and feel.
    - [ ] **Form Fields:**
        - First Name (`first_name`)
        - Last Name (`last_name`)
        - Email Address (`email1`)
        - Phone Number (`phone_work`)
        - Brief Description of Matter (`description`)

- [ ] **Task 5: Implement Form Submission Logic (PHP)**
    - [ ] At the top of `intake_form.php`, add a PHP block to handle `POST` requests.
    - [ ] **Step 5a: Data Sanitization:** Sanitize all incoming `$_POST` data (e.g., using `htmlspecialchars`) to prevent XSS attacks.
    - [ ] **Step 5b: API Authentication:**
        - Use cURL to make a `POST` request to the SuiteCRM API endpoint: `/api/oauth/access_token`.
        - Send the `grant_type` (client_credentials), `client_id`, and `client_secret` from Task 2 to get an access token.
    - [ ] **Step 5c: Create the Lead Record:**
        - Use cURL again to make a `POST` request to the V8 API endpoint: `/api/v8/module`.
        - Construct the JSON payload for a new `Lead` record, mapping the sanitized form fields to the corresponding Lead attributes (`first_name`, `last_name`, etc.).
        - Include the obtained access token in the `Authorization: Bearer` header.
    - [ ] **Step 5d: Display Success/Error Message:**
        - If the API call is successful (HTTP 201), display a "Thank you for your submission" message to the user.
        - If the API call fails, display a generic "An error occurred, please try again later" message.

### Phase 3: Testing (Day 2)

- [ ] **Task 6: Manual End-to-End Testing**
    - [ ] **Test Strategy:** Direct manual testing is the most effective way to validate this feature.
    - [ ] **Test Cases:**
        1. Access `intake_form.php` directly in a web browser. Verify the form renders correctly.
        2. Fill out the form with valid data and submit. Verify the success message is shown.
        3. Log in to SuiteCRM and navigate to the Leads module. Verify the new lead has been created with all the correct information.
        4. Submit the form with an invalid email address. While the form will submit, this helps confirm the data is passed as-is to the API.
        5. Submit the form with script tags (`<script>alert('xss')</script>`) in the description field. Verify the lead is created in SuiteCRM but the script tags are rendered as plain text (due to sanitization), not executed.

- [ ] **Task 7: Run Existing Test Suite**
    - [ ] Run the full PHPUnit test suite (`../vendor/bin/phpunit` from the `tests/` directory). Since we have not modified any core files, this is a safety check to ensure no environmental changes have caused regressions.

---
