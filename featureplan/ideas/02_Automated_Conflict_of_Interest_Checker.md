
# Feature Plan: Automated Conflict of Interest Checker

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 2 of 10

---

## 1. Objective

To provide a simple, dedicated interface where a user can enter a name (individual or company) and instantly search all `Contacts` and `Accounts` in the CRM to check for potential conflicts of interest. This is a critical, time-saving feature for legal compliance.

This is a **very low-risk** feature. It is entirely **read-only** and does not modify any existing data or core files. It will be a new, standalone page that uses the standard, stable `BeanFactory` to query the database.

---

## 2. End-to-End Task List

### Phase 1: Backend Logic (Day 1)

- [ ] **Task 1: Create the Controller**
    - [ ] Create a new file: `custom/modules/Contacts/ConflictCheckController.php`.
    - [ ] This controller will define a new action, `search`.
    - [ ] The `search` action will retrieve the search term from the `$_REQUEST` object.

- [ ] **Task 2: Implement the Search Logic**
    - [ ] Inside the `search` action, use `BeanFactory::newBean('Contacts')` and `BeanFactory::newBean('Accounts')` to get access to the data models.
    - [ ] Construct a `WHERE` clause to search for the term in relevant fields (e.g., `first_name`, `last_name`, `name`). The search will use a `LIKE '%term%'` query to be comprehensive.
    - [ ] Use the `get_list()` method on the beans to execute the search. This is a standard and safe way to query data.
    - [ ] Combine the results from both `Contacts` and `Accounts` into a single array.

### Phase 2: Frontend UI (Day 1)

- [ ] **Task 3: Create the View**
    - [ ] Create a new file: `custom/modules/Contacts/views/view.conflictcheck.php`.
    - [ ] This view will handle the display logic.
    - [ ] It will contain a simple HTML form with a single text input for the search term and a "Search" button.

- [ ] **Task 4: Create the Template**
    - [ ] Create a new Smarty template file: `custom/modules/Contacts/tpls/conflictcheck.tpl`.
    - [ ] This template will render the search form.
    - [ ] It will also contain a section to display the search results. It will loop through the results array (assigned to Smarty from the controller) and display them in a simple table with columns for Name, Module (Contact/Account), and a link to the record.

### Phase 3: Integration & Access (Day 2)

- [ ] **Task 5: Create the Menu Link**
    - [ ] To make the page accessible, I will add a new menu item.
    - [ ] Create a file: `custom/Extension/modules/Contacts/Ext/clients/base/menus/header/conflictcheck.php`.
    - [ ] This file will add a "Conflict Check" link to the `Contacts` module's action menu.
    - [ ] The link will point to `index.php?module=Contacts&action=ConflictCheck`.

- [ ] **Task 6: Run Quick Repair and Rebuild**
    - [ ] Go to `Admin -> Repair -> Quick Repair and Rebuild` to make SuiteCRM recognize the new files and menu link.

### Phase 4: Testing (Day 2)

- [ ] **Task 7: Manual End-to-End Testing**
    - [ ] **Test Strategy:** Since this is a simple, read-only feature, manual testing is sufficient and efficient.
    - [ ] **Test Cases:**
        1. Navigate to the Contacts module and click the "Conflict Check" link.
        2. Verify the search page loads correctly.
        3. Enter a name that exists in both a Contact and an Account. Verify both records appear in the results.
        4. Enter a partial name. Verify all matching records appear.
        5. Enter a name that does not exist. Verify a "No results found" message is displayed.
        6. Click the link on a search result. Verify it correctly navigates to the DetailView of that record.

- [ ] **Task 8: Run Existing Test Suite**
    - [ ] Run the full PHPUnit test suite (`../vendor/bin/phpunit` from the `tests/` directory) to ensure our new, isolated files have not caused any regressions.

---
