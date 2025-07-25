
# Feature Plan: Document Template Generation

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 5 of 10

---

## 1. Objective

To allow users to upload standard `.docx` document templates (e.g., engagement letters, retainer agreements) and then generate new documents from these templates, automatically populated with data from a specific `Case` record.

This is a **medium-risk** feature. The risk is managed by creating a new, self-contained module for templates and using a well-known, isolated PHP library for document processing. This approach avoids any modification to the core `Cases` or `Documents` modules, instead adding functionality alongside them.

---

## 2. End-to-End Task List

### Phase 1: Module Creation & Setup (Day 1)

- [ ] **Task 1: Create a New Module for Templates**
    - [ ] Use `Admin -> Module Builder` to create a new, basic module named `DocumentTemplates`.
    - [ ] This module will have standard fields: `name`, `description`, and a `file` field for the `.docx` upload.
    - [ ] Deploy the new module.

- [ ] **Task 2: Install PHPWord Library**
    - [ ] Run `composer require phpoffice/phpword` to add the library for reading and writing Word documents. This is a standard, safe way to handle `.docx` files in PHP.

### Phase 2: Implementation (Day 2-3)

- [ ] **Task 3: Create the "Generate Document" Button**
    - [ ] In the `Cases` module DetailView, add a custom button labeled "Generate Document".
    - [ ] This will be done by creating a custom file: `custom/modules/Cases/views/view.detail.php`.
    - [ ] The button will open a popup window (a new, custom entry point).

- [ ] **Task 4: Create the Popup for Template Selection**
    - [ ] Create a new entry point file: `index.php?entryPoint=generateDocument`.
    - [ ] This entry point will display a simple form where the user can select a template from a dropdown list populated with records from the new `DocumentTemplates` module.
    - [ ] The form will include hidden fields for the `Case` ID.

- [ ] **Task 5: Implement the Document Generation Logic**
    - [ ] The template selection form will submit to another new, custom entry point: `index.php?entryPoint=processTemplate`.
    - [ ] **Step 5a: Fetch Data:**
        - Get the `Case` ID and the `DocumentTemplate` ID from the `$_POST` request.
        - Use `BeanFactory::retrieveBean('Cases', $case_id)` to safely load the full Case record.
        - Use `BeanFactory::retrieveBean('DocumentTemplates', $template_id)` to load the template record and get the path to the uploaded `.docx` file.
    - [ ] **Step 5b: Process the Template:**
        - Use the **PHPWord** library to load the `.docx` template file.
        - The library will search the document for placeholders (e.g., `{$case_name}`, `{$contact_full_name}`).
        - For each placeholder, use the data from the retrieved `Case` bean to replace it with the correct value.
    - [ ] **Step 5c: Save and Download the New Document:**
        - Save the newly populated document to a temporary server location.
        - Force a download of the new file to the user's browser.
        - Immediately delete the temporary file from the server.

### Phase 3: Testing (Day 4)

- [ ] **Task 6: Unit Testing**
    - [ ] Create a new PHPUnit test file: `tests/unit/phpunit/custom/entrypoints/ProcessTemplateTest.php`.
    - [ ] **Test Strategy:** Test the document generation logic in isolation.
    - [ ] **Test Cases:**
        - `testPlaceholderReplacement()`: Create a mock `Case` bean and a sample `.docx` template in memory. Run the generation logic and verify that the placeholders in the resulting document are correctly replaced.

- [ ] **Task 7: Acceptance Testing**
    - [ ] **Test Strategy:** An end-to-end manual test is required to validate the full user workflow.
    - [ ] **Test Scenario:**
        1. Log in as an admin user.
        2. Navigate to the `DocumentTemplates` module and upload a new template with placeholders like `{$case_name}`.
        3. Navigate to an existing `Case` record.
        4. Click the "Generate Document" button.
        5. In the popup, select the template you just uploaded and submit.
        6. Verify that a new `.docx` file is downloaded.
        7. Open the downloaded file and confirm that the placeholders have been replaced with the correct data from the `Case` record.

- [ ] **Task 8: Run Existing Test Suite**
    - [ ] Run the full PHPUnit test suite (`../vendor/bin/phpunit` from the `tests/` directory) to ensure no core functionality has been impacted.

---
