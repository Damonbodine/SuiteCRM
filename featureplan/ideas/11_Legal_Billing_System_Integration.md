
# Feature Plan: Legal Billing System Integration (Clio)

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 11 of 10

---

## 1. Objective

To create a one-way data sync from SuiteLegal to a popular legal billing system like Clio. When a new `Case` (Matter) is created in SuiteLegal, it will be automatically created in the billing system, eliminating double data entry and reducing administrative overhead.

This is a **medium-risk** feature. The risk is managed by making the integration **one-way (outbound)** and using SuiteCRM's official, safe **Logic Hooks** system. This ensures that the external system can never modify or corrupt the SuiteCRM database. The entire feature is encapsulated in a new, isolated custom module.

---

## 2. End-to-End Task List

### Phase 1: Setup & Configuration (Day 1)

- [ ] **Task 1: Clio Developer Account & API Keys**
    - [ ] Sign up for a Clio developer account (or other target billing system).
    - [ ] Create a new application to get API credentials (Client ID, Client Secret).

- [ ] **Task 2: Create a Custom Module for Configuration**
    - [ ] Use `Admin -> Module Builder` to create a new, simple module named `BillingIntegration`.
    - [ ] This module will not be visible in the main navigation. It will only contain an Admin screen for settings.
    - [ ] **Fields:** `clio_client_id`, `clio_client_secret`, `clio_api_token` (to store the OAuth2 token).

- [ ] **Task 3: Implement OAuth2 Authentication Flow**
    - [ ] On the `BillingIntegration` admin page, create a button: "Connect to Clio".
    - [ ] This button will redirect the admin user to Clio's OAuth2 authorization URL.
    - [ ] Create a new entry point (`index.php?entryPoint=clioCallback`) to handle the redirect back from Clio after authorization.
    - [ ] This callback will exchange the authorization code for an access token and securely save it in the `BillingIntegration` settings table.

### Phase 2: Implementation (Day 2-3)

- [ ] **Task 4: Create the Logic Hook**
    - [ ] Create a file: `custom/modules/Cases/logic_hooks.php`.
    - [ ] Define an `after_save` logic hook.

- [ ] **Task 5: Implement the Sync Logic**
    - [ ] Create the hook implementation file: `custom/modules/Cases/CaseHooks.php`.
    - [ ] **Logic (`sync_to_billing` function):**
        1. The hook will trigger after a `Case` bean is saved.
        2. It will check if this is a **newly created** case (`$bean->fetched_row === false`).
        3. **Fetch Credentials:** Load the Clio API credentials from our custom `BillingIntegration` settings.
        4. **Prepare Data:** Create a data array that maps the `Case` bean's fields to the fields required by the Clio API to create a new "Matter".
        5. **API Call:** Use cURL to make a `POST` request to the Clio API's `/matters` endpoint, sending the prepared data and the OAuth2 access token.
        6. **Store External ID:** If the API call is successful, Clio will return an ID for the new Matter. Save this ID to a new custom field on the `Case` record called `clio_matter_id_c`.
        7. **Error Logging:** If the API call fails, log the error to the SuiteCRM log for debugging, but do not interrupt the user's workflow.

### Phase 3: Testing (Day 4)

- [ ] **Task 6: Unit Testing**
    - [ ] Create a new PHPUnit test file: `tests/unit/phpunit/custom/modules/Cases/CaseHooksTest.php`.
    - [ ] **Test Strategy:** Test the hook logic in isolation by mocking the API call.
    - [ ] **Test Cases:**
        - `testHookTriggersOnNewCase()`: Create a new mock `Case` bean and trigger the `after_save` event. Verify that the cURL function is called.
        - `testHookDoesNotTriggerOnUpdate()`: Load a mock `Case` bean (simulating an update) and trigger `after_save`. Verify the cURL function is **not** called.
        - `testDataMapping()`: Verify that the data sent to the mocked cURL function correctly maps SuiteCRM fields to Clio API fields.

- [ ] **Task 7: Manual End-to-End Testing**
    - [ ] **Test Strategy:** A full, manual end-to-end test is required.
    - [ ] **Test Scenario:**
        1. Log in as an admin and configure the Clio integration, completing the OAuth2 flow.
        2. Create a new `Case` in SuiteCRM.
        3. Log in to your Clio developer account.
        4. Navigate to the "Matters" section.
        5. **Verify the new case from SuiteCRM exists as a Matter in Clio** with all the correct details.
        6. Go back to the `Case` record in SuiteCRM and verify that the `clio_matter_id_c` field has been populated.

- [ ] **Task 8: Run Existing Test Suite**
    - [ ] Run the full PHPUnit test suite (`../vendor/bin/phpunit` from the `tests/` directory) as a final safety check.

---
