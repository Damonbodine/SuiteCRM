
# Feature Plan: Task Management Kanban Board

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 9 of 10

---

## 1. Objective

To provide a modern, visual way for legal teams to manage tasks. This feature will add a Kanban-style board to the `Tasks` module, allowing users to see tasks as cards in columns representing their status (e.g., "Not Started," "In Progress," "Completed") and to update a task's status by simply dragging and dropping the card.

This is a **very low-risk** feature. It is a purely frontend enhancement. It creates a new view for the `Tasks` module but does not alter the core `Task` bean or any backend logic. All status updates are performed through the safe, modern V8 API.

---

## 2. End-to-End Task List

### Phase 1: Backend API Endpoint (Day 1)

- [ ] **Task 1: Verify Existing V8 API for Tasks**
    - [ ] The standard V8 API already supports fetching and updating `Tasks`. I will confirm that I can fetch tasks and, crucially, update the `status` field via a `PATCH` request to `/api/v8/module/Tasks/{record_id}`. No new backend development is needed.

### Phase 2: Frontend Implementation (Day 1-2)

- [ ] **Task 2: Create the New Kanban View**
    - [ ] Create a new file: `custom/modules/Tasks/views/view.kanban.php`.
    - [ ] This view will be responsible for preparing the data for the board.

- [ ] **Task 3: Create the Kanban Template**
    - [ ] Create a new Smarty template file: `custom/modules/Tasks/tpls/kanban.tpl`.
    - [ ] **Step 3a: HTML Structure:** The template will define the columns for the Kanban board. These will be simple `<div>` elements representing each status (e.g., `<div id="status-not-started" class="kanban-column">...</div>`).
    - [ ] **Step 3b: Include JavaScript Library:** Include a lightweight, open-source drag-and-drop library like **Dragula.js** via a CDN. This provides the core drag-and-drop functionality without adding local dependencies.

- [ ] **Task 4: Implement Frontend JavaScript Logic**
    - [ ] In a `<script>` tag within `kanban.tpl`:
        1. **Fetch Tasks:** On page load, make a `GET` request to the V8 API (`/api/v8/module/Tasks`) to fetch all open tasks for the current user.
        2. **Render Task Cards:** Loop through the fetched tasks. For each task, create a "card" (`<div class="task-card">`) and append it to the appropriate status column based on its `status` field.
        3. **Initialize Drag-and-Drop:** Initialize the Dragula library on the Kanban columns.
        4. **Handle Drop Event:** Listen for the `drop` event from Dragula. When a card is dropped into a new column:
            - Get the `record_id` from the card's data attributes.
            - Get the `new_status` from the column's ID.
            - Make a `PATCH` request to the V8 API (`/api/v8/module/Tasks/{record_id}`) to update the task's `status` to the new value.
            - Display a brief success/error notification.

### Phase 3: Integration (Day 2)

- [ ] **Task 5: Create the Menu Link**
    - [ ] Create a file: `custom/Extension/modules/Tasks/Ext/clients/base/menus/header/kanban.php`.
    - [ ] This file will add a new menu item, "Kanban Board," to the `Tasks` module's action menu.
    - [ ] The link will point to `index.php?module=Tasks&action=Kanban`.

- [ ] **Task 6: Run Quick Repair and Rebuild**
    - [ ] Go to `Admin -> Repair -> Quick Repair and Rebuild` to make SuiteCRM recognize the new files and menu link.

### Phase 4: Testing (Day 3)

- [ ] **Task 7: Manual End-to-End Testing**
    - [ ] **Test Strategy:** This is a highly visual and interactive feature, so manual testing is the most effective approach.
    - [ ] **Test Scenario:**
        1. Create three new tasks with statuses "Not Started," "In Progress," and "Pending Input."
        2. Navigate to the `Tasks` module and select "Kanban Board" from the menu.
        3. Verify the board loads and the three tasks appear as cards in the correct columns.
        4. Drag the "Not Started" task to the "In Progress" column.
        5. Verify the card stays in the new column.
        6. Refresh the page. Verify the task is still in the "In Progress" column (confirming the API update was successful).
        7. Navigate to the standard `Tasks` List View. Verify the task's status has been updated there as well.

- [ ] **Task 8: Run Existing Test Suite**
    - [ ] Run the full PHPUnit test suite (`../vendor/bin/phpunit` from the `tests/` directory) as a safety check to ensure no regressions were introduced by our isolated view.

---
