
# Feature Plan: Secure Client Messaging Portal

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 10 of 10

---

## 1. Objective

To provide a secure, modern, and isolated web portal where clients can log in to view the status of their case, see key documents, and exchange messages with their attorney. This provides a more secure and professional alternative to email for sensitive communications.

This is a **medium-risk** feature. The risk is mitigated by building the portal as a **completely separate, standalone PHP application** (a "micro-site"). This application will have its own authentication and will only communicate with the SuiteCRM backend via the secure V8 API. This guarantees that no client-facing code is running within the core SuiteCRM application, ensuring maximum security and isolation.

---

## 2. End-to-End Task List

### Phase 1: Backend & Data Model (Day 1)

- [ ] **Task 1: Create a "Portal Message" Custom Module**
    - [ ] Use `Admin -> Module Builder` to create a new module named `PortalMessages`.
    - [ ] **Fields:** `message_text` (textarea), `is_from_client` (bool), `date_sent` (datetime).
    - [ ] **Relationships:** Add a one-to-many relationship from `Cases` to `PortalMessages`.

- [ ] **Task 2: Create API Endpoints for the Portal**
    - [ ] Create a new file: `custom/Api/V8/Controller/PortalDataController.php`.
    - [ ] This controller will house all the logic for the portal's data needs.
    - [ ] **Endpoints:**
        - `POST /api/v8/portal-login`: Authenticates a client. (Logic to be defined in Phase 2).
        - `GET /api/v8/portal/case/{case_id}`: Fetches basic case data and all related `PortalMessages`.
        - `POST /api/v8/portal/case/{case_id}/message`: Allows the client to post a new message.

### Phase 2: Standalone Portal Application (Day 2-4)

- [ ] **Task 3: Create the Portal Directory and Files**
    - [ ] Create a new directory in the SuiteCRM root: `/portal/`.
    - [ ] Inside, create the main files: `index.php` (login page), `dashboard.php`, `logout.php`.

- [ ] **Task 4: Implement Client Login Logic**
    - [ ] The `index.php` will show a login form (Case Number + Password).
    - [ ] **Authentication Strategy:** To avoid storing separate passwords, we will use a secure, verifiable token. A new, non-database field will be added to the `Cases` module in SuiteCRM called `portal_access_key`. An attorney can generate this key (a long, random string) and provide it to the client.
    - [ ] The login form will submit to the `/api/v8/portal-login` endpoint. This endpoint will find the `Case` by its number and verify the provided `portal_access_key` matches. If it does, it returns a secure JWT (JSON Web Token) that the portal frontend can use for subsequent requests.

- [ ] **Task 5: Build the Client Dashboard (`dashboard.php`)**
    - [ ] This page is the main client view, protected by the JWT.
    - [ ] On page load, it will use JavaScript (`fetch`) and the JWT to call the `/api/v8/portal/case/{case_id}` endpoint.
    - [ ] It will then render the case status and the message history in a clean, chat-like interface.
    - [ ] A form at the bottom will allow the client to type and `POST` a new message to the `/api/v8/portal/case/{case_id}/message` endpoint.

### Phase 3: SuiteCRM UI Integration (Day 4)

- [ ] **Task 6: Create a "Portal Messages" Subpanel**
    - [ ] In `Admin -> Studio`, add the new `PortalMessages` module as a subpanel to the `Cases` module DetailView.
    - [ ] This will allow attorneys to see and reply to client messages directly from the case record in SuiteCRM.

- [ ] **Task 7: Add Portal Management to Case View**
    - [ ] In the `Cases` DetailView, add a custom section for "Client Portal Management".
    - [ ] This section will display the `portal_access_key` and have a button to "Generate New Key" for security.

### Phase 4: Testing (Day 5)

- [ ] **Task 8: Manual End-to-End Testing**
    - [ ] **Test Strategy:** This requires testing two separate user flows.
    - [ ] **Attorney Flow:**
        1. Log in as an attorney.
        2. Open a `Case` record.
        3. Generate a `portal_access_key` for the client.
        4. Use the new `PortalMessages` subpanel to send a welcome message.
    - [ ] **Client Flow:**
        1. Open the `/portal/` URL.
        2. Log in using the Case Number and the generated access key.
        3. Verify you can see the welcome message from the attorney.
        4. Send a reply message.
    - [ ] **Verification:**
        1. Log back in as the attorney.
        2. Open the `Case` record and check the `PortalMessages` subpanel.
        3. Verify the client's reply is visible.

- [ ] **Task 9: Run Existing Test Suite**
    - [ ] Run the full PHPUnit test suite (`../vendor/bin/phpunit` from the `tests/` directory) as a safety check.

---
