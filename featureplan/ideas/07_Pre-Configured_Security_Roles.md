
# Feature Plan: Pre-Configured Security Roles

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 7 of 10

---

## 1. Objective

To simplify the complex process of user permission setup by providing a one-click solution to apply pre-defined security templates for common law firm roles (e.g., Partner, Associate, Paralegal). This eliminates a major administrative bottleneck and reduces the risk of misconfiguration.

This is a **very low-risk** feature. It is a UI layer built on top of the existing, high-risk ACL and Security Groups system. It **does not modify any core security logic**. It simply automates the process of creating and assigning roles and groups, which is currently a manual task in the admin panel.

---

## 2. End-to-End Task List

### Phase 1: Define Role Templates (Day 1)

- [ ] **Task 1: Create Role Template Definitions**
    - [ ] Create a new PHP file: `custom/include/roles/role_templates.php`.
    - [ ] In this file, define a PHP array that holds the permission templates for each role. This makes it easy to add new roles in the future.
    - [ ] **Example Template Structure:**
        ```php
        $suitelegal_roles = [
            'Paralegal' => [
                'description' => 'Paralegals have view/edit access to cases but cannot delete them.',
                'modules' => [
                    'Cases' => ['access' => 89, 'view' => 'all', 'list' => 'all', 'edit' => 'all', 'delete' => 'none'],
                    'Contacts' => ['access' => 89, 'view' => 'all', 'list' => 'all', 'edit' => 'all', 'delete' => 'none'],
                    // ... other module permissions
                ]
            ],
            'Partner' => [ /* ... Partner permissions ... */ ],
        ];
        ```

### Phase 2: Backend Implementation (Day 1-2)

- [ ] **Task 2: Create the Admin Interface**
    - [ ] Create a new entry point: `index.php?entryPoint=applyRoleTemplate`.
    - [ ] This page will display a simple interface with:
        - A dropdown to select a `User`.
        - A dropdown to select a `Role Template` (e.g., "Paralegal", "Partner"), populated from the `role_templates.php` file.
        - An "Apply Template" button.

- [ ] **Task 3: Implement the Role Application Logic**
    - [ ] The form will submit to itself (`POST`).
    - [ ] On submission, the PHP logic will:
        1. **Fetch the User:** Load the selected `User` bean using `BeanFactory`.
        2. **Create a New Role:** Create a new `ACLRole` bean. The role name will be something unique, like `Paralegal - [User Name]`.
        3. **Apply Permissions:** Loop through the selected role template's module permissions (from `role_templates.php`). For each module, use the standard `ACLRole::setAction()` method to apply the defined permissions (access, view, edit, delete, etc.).
        4. **Create a New Security Group:** Create a new `SecurityGroup` bean with a corresponding name (e.g., `Paralegal - [User Name]`).
        5. **Assign User to Group:** Add the user to the new security group.
        6. **Assign Role to Group:** Assign the new ACL Role to the new security group.
        7. Display a success message: "The 'Paralegal' role has been successfully applied to [User Name]."

### Phase 3: Integration & Access (Day 2)

- [ ] **Task 4: Create Admin Menu Link**
    - [ ] Create a file: `custom/Extension/modules/Administration/Ext/Administration/rolehelper.php`.
    - [ ] This will add a new link, "Apply Role Template," to the main Admin page, pointing to our new entry point.

- [ ] **Task 5: Run Quick Repair and Rebuild**
    - [ ] Go to `Admin -> Repair -> Quick Repair and Rebuild` to register the new files and admin link.

### Phase 4: Testing (Day 3)

- [ ] **Task 6: Manual End-to-End Testing**
    - [ ] **Test Strategy:** Manual verification is the only way to ensure permissions are applied correctly across the UI.
    - [ ] **Test Scenario:**
        1. Create a new test user, "Test Paralegal".
        2. Log in as an admin and navigate to the "Apply Role Template" tool.
        3. Select the "Test Paralegal" user and the "Paralegal" template. Click "Apply".
        4. Verify the success message.
        5. Navigate to `Admin -> Role Management`. Verify the new `Paralegal - Test Paralegal` role exists and has the correct permissions set.
        6. Navigate to `Admin -> Security Suite Group Management`. Verify the new security group exists and has the correct user and role assigned.
        7. **Crucial Step:** Log out. Log back in as the "Test Paralegal" user.
        8. Navigate to the `Cases` module. Verify you can view and edit cases, but the "Delete" button is not visible.
        9. Attempt to access a disallowed module (e.g., `Administration`). Verify you get a "You are not authorized to access this area" message.

- [ ] **Task 7: Run Existing Test Suite**
    - [ ] Run the full PHPUnit test suite (`../vendor/bin/phpunit` from the `tests/` directory) as a safety check.

---
