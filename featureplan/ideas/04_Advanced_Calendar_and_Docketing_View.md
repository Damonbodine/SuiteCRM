
# Feature Plan: Advanced Calendar & Docketing View

**Target User:** Small Law Firms (1-10 Attorneys)  
**Product:** SuiteLegal  
**Feature Priority:** 4 of 10

---

## 1. Objective

To provide attorneys with a modern, fast, and comprehensive calendar view that consolidates all critical dates, including meetings, calls, and custom-defined case deadlines. This replaces the outdated and high-risk YUI-based calendar with a superior, isolated alternative.

This is a **very low-risk** feature. We will **not modify the existing calendar**. Instead, we will create a completely new, read-only page that is populated with data from the safe and modern V8 API. This approach guarantees zero impact on the existing core functionality.

---

## 2. End-to-End Task List

### Phase 1: Backend API Endpoints (Day 1)

- [ ] **Task 1: Create a Custom API Endpoint for Calendar Data**
    - [ ] Create a new file: `custom/Api/V8/Controller/CalendarDataController.php`.
    - [ ] This controller will define a new `GET` route, e.g., `/api/v8/calendar-data`.
    - [ ] The endpoint will accept `start_date` and `end_date` as query parameters.

- [ ] **Task 2: Implement Data Fetching Logic**
    - [ ] Inside the new controller, use `BeanFactory` to query for `Meetings`, `Calls`, and any other relevant modules within the provided date range.
    - [ ] For each record found, format it into a simple JSON object that the calendar library can understand (e.g., `{title: 'Meeting with John', start: '2025-08-15T10:00:00', end: '2025-08-15T11:00:00', url: '#/Meetings/record_id'}`).
    - [ ] Combine all events into a single JSON array and return it as the API response.

### Phase 2: Frontend Implementation (Day 1-2)

- [ ] **Task 3: Create the New Calendar Page**
    - [ ] Create a new file: `custom/modules/Calendar/docket.php`.
    - [ ] This file will be the entry point for our new calendar view.

- [ ] **Task 4: Create the View and Template**
    - [ ] Create a view file: `custom/modules/Calendar/views/view.docket.php`.
    - [ ] Create a Smarty template: `custom/modules/Calendar/tpls/docket.tpl`.
    - [ ] In the template, include the CSS and JS for a modern, open-source calendar library like **FullCalendar.js** via a CDN. This avoids adding new libraries to the local file system.
    - [ ] Add a single `<div id="docket-calendar"></div>` where the calendar will be rendered.

- [ ] **Task 5: Implement Frontend JavaScript**
    - [ ] In a `<script>` tag within `docket.tpl`, initialize the FullCalendar library on the `docket-calendar` div.
    - [ ] Configure FullCalendar to fetch events using its `events` property, pointing it to our new API endpoint (`/api/v8/calendar-data`). FullCalendar will automatically pass the `start` and `end` parameters.
    - [ ] Configure the `eventClick` handler so that when a user clicks an event on the calendar, it redirects them to the appropriate record's DetailView in SuiteCRM.

### Phase 3: Integration (Day 2)

- [ ] **Task 6: Create the Menu Link**
    - [ ] Create a file: `custom/Extension/modules/Calendar/Ext/clients/base/menus/header/docket.php`.
    - [ ] This file will add a new menu item named "Firm Docket" to the main navigation bar under the "All" menu.
    - [ ] The link will point to `index.php?module=Calendar&action=docket`.

- [ ] **Task 7: Run Quick Repair and Rebuild**
    - [ ] Go to `Admin -> Repair -> Quick Repair and Rebuild` to make SuiteCRM recognize the new files and menu link.

### Phase 4: Testing (Day 2)

- [ ] **Task 8: Manual End-to-End Testing**
    - [ ] **Test Strategy:** Manual testing is ideal for this visual, read-only feature.
    - [ ] **Test Cases:**
        1. Create a new Meeting and a new Call for the upcoming week.
        2. Navigate to the "Firm Docket" via the new menu item.
        3. Verify the calendar loads and displays the created Meeting and Call correctly.
        4. Click on the Meeting event. Verify it redirects to the Meeting's DetailView.
        5. Click on the Call event. Verify it redirects to the Call's DetailView.
        6. Use the calendar's navigation to move to the next month. Verify the calendar makes a new API call and displays the correct events for that month.

- [ ] **Task 9: Run Existing Test Suite**
    - [ ] Run the full PHPUnit test suite (`../vendor/bin/phpunit` from the `tests/` directory) as a safety check to ensure no regressions were introduced.

---
