# Comprehensive Implementation Plan

This document provides a detailed, step-by-step guide for implementing two high-impact features in SuiteCRM. The plan is grounded in a deep analysis of the existing codebase to ensure accuracy, prevent common pitfalls, and provide a reliable path for development.

---

## Feature 1: AI-Powered Case Intake and Triage

### Objective
Automate the creation and initial assignment of a new `Case` by having an AI parse an uploaded legal document. This feature will use a background Scheduler job to process documents dropped into a dedicated directory.

### Phase 1: Deep Codebase Analysis & Findings

*   **Schedulers (Background Jobs):**
    *   **Registration:** The definitive method for adding custom jobs is to add a function name to the `$job_strings` array in a file located at `custom/Extension/modules/Schedulers/Ext/ScheduledTasks/`.
    *   **Execution Context:** The job function is passed a `SchedulersJob` object. It is **mandatory** to call `$job->succeedJob('message')` or `$job->failJob('reason')` on this object to ensure the job's status is correctly logged in the system.
    *   **Function Signature:** A standalone named function is the standard pattern; a class is not required.

*   **File Handling & `upload` Directory:**
    *   **Core Class:** `modules/UploadFile.php` is the primary API for file operations.
    *   **File Naming Convention:** SuiteCRM does not preserve original filenames. It generates a UUID-based name and stores the file in the `upload/` directory. The original name is stored in the database.
    *   **Implication:** Our process must be filename-agnostic. It should scan a directory, process any file found, and then move it based on the outcome.

*   **Bean Creation (`Case` Module):**
    *   **Core Class:** `modules/Cases/Case.php` defines the `Case` bean. A robust implementation should populate not just `name` and `description`, but also default values for `status` and `priority`.
    *   **Logic Hooks:** The `SugarBean::save()` method triggers `'before_save'` and `'after_save'` logic hooks. We must be aware that our programmatic creation of a Case could initiate other system processes.

### Phase 2: Step-by-Step Implementation Plan

**Step 1: Create Dedicated File Directories**
*   **Action:** Manually create three new directories for robust error handling and processing:
    1.  `upload/intake_new/`
    2.  `upload/intake_processed/`
    3.  `upload/intake_failed/`

**Step 2: Create the Scheduler Job File**
*   **Action:** Create the file `custom/modules/Schedulers/ProcessIntakeDocuments.php`.
*   **Content:**

```php
<?php
// custom/modules/Schedulers/ProcessIntakeDocuments.php

function processIntakeDocuments(SchedulersJob $job): bool
{
    $GLOBALS['log']->info("Scheduler: ProcessIntakeDocuments starting.");
    $processed_count = 0;
    $failed_count = 0;

    $intake_dir = 'upload/intake_new/';
    $files = array_diff(scandir($intake_dir), array('..', '.'));

    if (empty($files)) {
        $job->succeedJob('No new documents to process.');
        return true;
    }

    foreach ($files as $file) {
        $file_path = $intake_dir . $file;
        try {
            $content = file_get_contents($file_path);
            if ($content === false) {
                throw new Exception("Could not read file content.");
            }

            // AI_SERVICE_CALL_PLACEHOLDER
            // This is where you would make a cURL call to your AI service
            // and get back a JSON response.
            // For now, we'll use a placeholder array.
            // $ai_response = callAIService($content);
            $ai_response = [
                'case_name' => 'Case from ' . $file,
                'summary' => 'This is an AI-generated summary.'
            ];


            $case = BeanFactory::newBean('Cases');
            $case->name = $ai_response['case_name'] ?? 'Untitled Case from Document';
            $case->description = $ai_response['summary'] ?? 'No summary available.';
            $case->status = 'New'; // Default status
            $case->priority = 'P1'; // Default priority

            // TODO: Add logic to find or create an Account and set $case->account_id
            // TODO: Add logic to determine assigned user
            $case->assigned_user_id = '1'; // Assign to admin by default

            $case->save();
            $GLOBALS['log']->info("Scheduler: Created Case ID: " . $case->id);

            rename($file_path, 'upload/intake_processed/' . $file);
            $processed_count++;

        } catch (Exception $e) {
            $GLOBALS['log']->fatal("Scheduler: Failed to process file $file. Error: " . $e->getMessage());
            rename($file_path, 'upload/intake_failed/' . $file);
            $failed_count++;
        }
    }

    $message = "Processing complete. Processed: $processed_count, Failed: $failed_count.";
    $GLOBALS['log']->info("Scheduler: " . $message);
    $job->succeedJob($message);
    return true;
}
```

**Step 3: Register the Job**
*   **Action:** Create the file `custom/Extension/modules/Schedulers/Ext/ScheduledTasks/process_intake_documents.php`.
*   **Content:**
    ```php
    <?php
    $job_strings[] = 'processIntakeDocuments';
    ```

**Step 4: Admin Configuration**
1.  Navigate to **Admin -> Repair -> Quick Repair and Rebuild**.
2.  Navigate to **Admin -> Schedulers**.
3.  Create a new Scheduler, select "Process Intake Documents" from the "Job" dropdown, and set the interval (e.g., "Every 5 Minutes").

### Phase 3: AI-Driven Task Breakdown

1.  **For the Developer:** "Based on my analysis of `modules/Schedulers/SchedulersJob.php`, create the file `custom/modules/Schedulers/ProcessIntakeDocuments.php`. It must contain a function `processIntakeDocuments` that accepts a `SchedulersJob` object. Inside, implement a loop to scan the `upload/intake_new/` directory. For each file, wrap the processing in a `try/catch` block. On success, move the file to `upload/intake_processed/`; on failure, move it to `upload/intake_failed/`. At the end of the function, you **must** call `$job->succeedJob()` with a summary message."
2.  **For the Developer:** "Inside the `try` block, after reading the file content, create a new `Case` bean using `BeanFactory::newBean('Cases')`. Populate the `name`, `description`, `status` (default to 'New'), and `priority` (default to 'P1') fields from a placeholder AI response. Assign it to the admin user (ID '1') by setting `assigned_user_id`. Then, call `$case->save()`."

---

## Feature 2: Interactive Case Analytics Dashboard

### Objective
Create a new, full-page analytics view with interactive charts powered by Chart.js. This feature will be a new module in the main navigation, providing a modern UI that is completely separate from the legacy dashlet system.

### Phase 1: Deep Codebase Analysis & Findings

*   **Module Creation & Navigation:** The process outlined in `@CLAUDE.md` is correct. A module requires registration in `$moduleList`, `$beanList`, `$beanFiles`, and the creation of 8 standard ACL Actions to appear correctly in the navigation bar after a "Quick Repair and Rebuild".
*   **Custom Views & JavaScript Inclusion:**
    *   **Superior Method:** The cleanest way to add page-specific JavaScript is to override the `_getJS()` method in the custom view controller (which extends `SugarView`). This is more robust and maintainable than embedding `<script>` tags directly in the template.
    *   **Data Passing:** The correct way to pass data from the PHP controller to the frontend is via the Smarty template engine using `$this->ss->assign('var_name', $value)`.

*   **Database Access:** The global `$db` object (an instance of `DBManager`) is the standard way to perform queries. For queries without user input, `$db->query()` is acceptable.

### Phase 2: Step-by-Step Implementation Plan

**Step 1: Create the Module Structure**
*   **Action:** Follow the "Module Development Pattern" from `@CLAUDE.md` to create the files and directories for a new module named `AnalyticsDashboard`.
    *   `custom/modules/AnalyticsDashboard/`
    *   `custom/modules/AnalyticsDashboard/metadata/`
    *   `custom/modules/AnalyticsDashboard/views/`
    *   `custom/modules/AnalyticsDashboard/tpls/`
    *   `custom/Extension/application/Ext/Include/AnalyticsDashboard.php` (Registration)
    *   `custom/modules/AnalyticsDashboard/AnalyticsDashboard.php` (Minimal Bean)
    *   Run SQL to create the 8 ACL Actions for the `AnalyticsDashboard` category.

**Step 2: Create the Custom View Controller**
*   **Action:** Create `custom/modules/AnalyticsDashboard/views/view.list.php`.
*   **Content:**
    ```php
    <?php
    // custom/modules/AnalyticsDashboard/views/view.list.php
    if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

    class AnalyticsDashboardViewList extends ViewList
    {
        public function _getJS()
        {
            // This is the superior method for including page-specific JavaScript.
            return parent::_getJS() .
                '<script src="jssource/src_files/include/javascript/chartjs/Chart.min.js"></script>' .
                '<script src="custom/modules/AnalyticsDashboard/analytics.js"></script>';
        }

        public function display()
        {
            $db = DBManagerFactory::getInstance();

            // Query 1: Case Status
            $status_query = "SELECT status, COUNT(*) as count FROM cases WHERE deleted=0 GROUP BY status";
            $status_result = $db->query($status_query);
            $caseStatusData = [];
            while ($row = $db->fetchByAssoc($status_result)) {
                $caseStatusData[] = $row;
            }

            // Assign the data to Smarty for the template
            $this->ss->assign('caseStatusDataJSON', json_encode($caseStatusData));

            // Use a custom template instead of the default
            echo $this->ss->fetch('custom/modules/AnalyticsDashboard/tpls/dashboard.tpl');
        }
    }
    ```

**Step 3: Add the Chart.js Library**
*   **Action:** Download Chart.js and place the `Chart.min.js` file at `jssource/src_files/include/javascript/chartjs/Chart.min.js`. Create the directory if it doesn't exist.

**Step 4: Create the Smarty Template**
*   **Action:** Create `custom/modules/AnalyticsDashboard/tpls/dashboard.tpl`.
*   **Content:**
    ```html
    <h3>Case Analytics Dashboard</h3>
    <div style="display: flex; flex-wrap: wrap;">
        <div style="width: 45%; margin: 10px;">
            <h4>Cases by Status</h4>
            <canvas id="caseStatusChart"></canvas>
        </div>
        <!-- Add more canvas elements for other charts here -->
    </div>

    <!-- This hidden div is the bridge that passes data from PHP to our JS file -->
    <div id="chartDataContainer" style="display: none;" data-casestatus='{$caseStatusDataJSON}'></div>
    ```

**Step 5: Create the Custom JavaScript File**
*   **Action:** Create `custom/modules/AnalyticsDashboard/analytics.js`.
*   **Content:**
    ```javascript
    document.addEventListener('DOMContentLoaded', function() {
        const dataContainer = document.getElementById('chartDataContainer');
        
        // Chart 1: Case Status
        const statusCanvas = document.getElementById('caseStatusChart');
        if (statusCanvas && dataContainer.dataset.casestatus) {
            const chartData = JSON.parse(dataContainer.dataset.casestatus);
            new Chart(statusCanvas, {
                type: 'bar',
                data: {
                    labels: chartData.map(row => row.status),
                    datasets: [{
                        label: '# of Cases',
                        data: chartData.map(row => row.count),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
    ```

**Step 6: Repair and Test**
1.  Navigate to **Admin -> Repair -> Quick Repair and Rebuild**.
2.  The "Analytics Dashboard" module should now be in the navigation bar. Clicking it should display your chart.

### Phase 3: AI-Driven Task Breakdown

1.  **For the Developer:** "Create a custom view controller at `custom/modules/AnalyticsDashboard/views/view.list.php`. The class `AnalyticsDashboardViewList` must override the `_getJS()` method to return the paths to Chart.js and a new file, `custom/modules/AnalyticsDashboard/analytics.js`. This is the correct inclusion method for a full-page application."
2.  **For the Developer:** "In the `display()` method of that same class, query the database for case status counts. Then, instead of calling `parent::display()`, call `$this->ss->fetch()` with the path to a custom template: `custom/modules/AnalyticsDashboard/tpls/dashboard.tpl`. Pass the query results to the template by using `$this->ss->assign()`."
3.  **For the Developer:** "Create the `analytics.js` file. It should contain a `DOMContentLoaded` event listener. Inside, it will find a hidden `<div>` in the DOM, read a `data-` attribute containing the JSON data, parse it, and use that data to initialize the Chart.js chart. This decouples the JS from the template."
