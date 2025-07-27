# AI Status Intelligence - Practical Testing Guide

## Prerequisites Checklist

### 1. System Requirements
- [ ] SuiteCRM 7.x/8.x installed and running
- [ ] PHP 7.4+ with MySQL 5.7+
- [ ] Web server (Apache/Nginx) configured
- [ ] Admin access to SuiteCRM instance

### 2. Development Environment
- [ ] SSH/terminal access to server
- [ ] Database admin credentials
- [ ] File system write permissions
- [ ] PHP error logging enabled

## Step 1: Verify Current Installation

### Check SuiteCRM is Working
```bash
# Navigate to SuiteCRM directory
cd /Users/damonbodine/suitecrm/SuiteCRM

# Check if we can access the instance
curl -I http://localhost/suitecrm/ || echo "Need to start web server"

# Check file permissions
ls -la custom/include/ai/
ls -la custom/modules/Cases/
```

### Verify Database Connection
```bash
# Test database connection (replace with your credentials)
mysql -u [username] -p -e "SELECT COUNT(*) FROM cases;" [database_name]
```

## Step 2: Install AI Status Intelligence Feature

### Apply Database Schema
```bash
# Run the database upgrade script
mysql -u [username] -p [database_name] < scripts/upgrade_ai_status_fields.sql

# Verify new fields were added
mysql -u [username] -p -e "DESCRIBE cases;" [database_name] | grep ai_
```

Expected output should show:
```
ai_suggested_status | varchar(100) | YES
ai_confidence_score | decimal(5,4) | YES  
ai_last_analysis    | datetime     | YES
ai_analysis_factors | text         | YES
ai_status_needs_review | tinyint(1) | YES
```

### SuiteCRM Integration
1. **Login as Administrator**
   - Navigate to: `http://[your-domain]/suitecrm/`
   - Login with admin credentials

2. **Run Quick Repair and Rebuild**
   - Go to: Admin → System Settings → Repair
   - Click "Quick Repair and Rebuild"
   - Execute any displayed SQL statements
   - Clear cache: Admin → System Settings → Repair → Clear Cache

3. **Verify Extension Loading**
   - Check: Admin → Developer Tools → Display Module API
   - Look for Cases module AI fields

## Step 3: Create Test Environment Configuration

### Enable Development Mode
Create/edit `config_override.php`:
```php
<?php
// Add to config_override.php
$sugar_config['developer_mode'] = true;
$sugar_config['log_level'] = 'debug';
$sugar_config['display_errors'] = true;

// AI Testing Configuration
$sugar_config['ai_status_intelligence'] = array(
    'enabled' => true,
    'mock_mode' => true,  // Use mock AI for testing
    'openai_api_key' => '', // Leave empty for mock mode
    'analysis_threshold' => 0.6,
    'rate_limit_seconds' => 60
);
```

### Enable Error Logging
Edit `php.ini` or `.htaccess`:
```
log_errors = On
error_log = /path/to/suitecrm/suitecrm_errors.log
display_errors = On
```

## Step 4: Create Sample Test Data

### Sample Case for Testing
```sql
-- Insert a test case with specific data for AI analysis
INSERT INTO cases (
    id, name, status, priority, type, 
    assigned_user_id, description, date_entered, date_modified
) VALUES (
    'test-case-ai-001', 
    'Personal Injury - Auto Accident Case',
    'Open_Assigned',
    'Medium',
    'Personal Injury',
    '1',  -- Admin user ID
    'Client involved in auto accident. Need medical records and witness statements.',
    NOW(),
    NOW()
);
```

### Sample Related Tasks
```sql
-- Add tasks to trigger AI analysis
INSERT INTO tasks (
    id, name, status, parent_type, parent_id,
    assigned_user_id, description, date_entered, date_modified
) VALUES 
(UUID(), 'Obtain medical records', 'Completed', 'Cases', 'test-case-ai-001', '1', 'Requested from hospital', NOW(), NOW()),
(UUID(), 'Interview witness #1', 'Completed', 'Cases', 'test-case-ai-001', '1', 'Completed phone interview', NOW(), NOW()),
(UUID(), 'Interview witness #2', 'In Progress', 'Cases', 'test-case-ai-001', '1', 'Scheduled for next week', NOW(), NOW()),
(UUID(), 'File insurance claim', 'Not Started', 'Cases', 'test-case-ai-001', '1', 'Waiting for medical records', NOW(), NOW()),
(UUID(), 'Client consultation', 'Completed', 'Cases', 'test-case-ai-001', '1', 'Initial consultation completed', NOW(), NOW());
```

### Sample Communication Records
```sql
-- Add calls to simulate communication activity
INSERT INTO calls (
    id, name, status, direction, parent_type, parent_id,
    assigned_user_id, description, date_entered, date_modified
) VALUES
(UUID(), 'Client check-in call', 'Held', 'Outbound', 'Cases', 'test-case-ai-001', '1', 'Discussed case progress', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
(UUID(), 'Insurance adjuster call', 'Held', 'Inbound', 'Cases', 'test-case-ai-001', '1', 'Discussed settlement options', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY));
```

## Step 5: Test Basic Functionality

### Manual UI Testing

1. **Navigate to Test Case**
   - Go to Cases module
   - Open "Personal Injury - Auto Accident Case"
   - Look for AI Status Intelligence widget in detail view

2. **Trigger AI Analysis**
   - Edit the case (change priority from Medium to High)
   - Save the case
   - Wait 2-3 seconds for analysis to complete
   - Refresh the page

3. **Expected Results**
   - AI widget should appear with suggested status
   - Confidence score should be displayed
   - Analysis factors should be shown
   - Accept/Dismiss buttons should be visible

### Browser Developer Tools Check
```javascript
// Open browser console and check for errors
console.log("Checking AI widget...");

// Verify widget loaded
if (document.querySelector('.ai-status-widget')) {
    console.log("✓ AI widget found");
} else {
    console.log("✗ AI widget not found");
}

// Check for JavaScript errors
window.addEventListener('error', function(e) {
    console.error('JS Error:', e.message, 'File:', e.filename, 'Line:', e.lineno);
});
```

## Step 6: Backend Testing

### Check PHP Logs
```bash
# Monitor SuiteCRM logs during testing
tail -f suitecrm.log | grep -i "ai\|error"

# Check PHP error log
tail -f /path/to/php/error.log
```

### Database Verification
```sql
-- Check if AI analysis ran
SELECT id, name, ai_suggested_status, ai_confidence_score, 
       ai_last_analysis, ai_status_needs_review 
FROM cases 
WHERE id = 'test-case-ai-001';

-- Check analysis factors
SELECT ai_analysis_factors FROM cases WHERE id = 'test-case-ai-001';
```

### Direct API Testing
```bash
# Test the AI analysis directly via PHP
php -r "
define('sugarEntry', true);
require_once 'sugar_version.php';
require_once 'include/entrypoint.php';
require_once 'custom/include/ai/CaseStatusAnalyzer.php';

\$analyzer = new CaseStatusAnalyzer();
\$result = \$analyzer->analyzeCase('test-case-ai-001');
echo 'AI Analysis Result: ' . print_r(\$result, true);
"
```

## Step 7: Advanced Feature Testing

### AJAX Endpoint Testing
```bash
# Test accept suggestion endpoint
curl -X POST "http://localhost/suitecrm/index.php?entryPoint=aiStatusAction" \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "action=accept_suggestion&case_id=test-case-ai-001&suggested_status=Open_Pending Input&csrf_token=[token]"
```

### Security Testing
```bash
# Test unauthorized access (should fail)
curl -X POST "http://localhost/suitecrm/index.php?entryPoint=aiStatusAction" \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "action=accept_suggestion&case_id=test-case-ai-001"
     
# Expected: 400 Bad Request with "User not authenticated" or "Invalid security token"
```

## Step 8: Performance Testing

### Analysis Speed Test
```php
<?php
// Performance test script - save as test_ai_performance.php
define('sugarEntry', true);
require_once 'sugar_version.php';
require_once 'include/entrypoint.php';
require_once 'custom/include/ai/CaseStatusAnalyzer.php';

$analyzer = new CaseStatusAnalyzer();
$startTime = microtime(true);

// Run analysis 10 times
for ($i = 0; $i < 10; $i++) {
    $result = $analyzer->analyzeCase('test-case-ai-001');
}

$endTime = microtime(true);
$avgTime = ($endTime - $startTime) / 10;

echo "Average analysis time: " . ($avgTime * 1000) . " milliseconds\n";
echo "Target: <500ms per analysis\n";
echo ($avgTime < 0.5) ? "✓ PASS" : "✗ FAIL - Too slow";
?>
```

Run with: `php test_ai_performance.php`

## Step 9: Integration Testing Scenarios

### Scenario 1: New Case Analysis
1. Create new case with status "New"
2. Add 1-2 tasks (not completed)
3. Save case - should suggest "Assigned"

### Scenario 2: High Task Completion
1. Create case with 5 tasks
2. Mark 4 tasks as completed (80% completion)
3. Update case - should suggest "Pending Input" or "Closed"

### Scenario 3: Inactive Case
1. Create case 30+ days ago
2. No recent activities
3. Update case - should suggest "Pending Input"

### Scenario 4: Permission Testing
1. Create non-admin user
2. Assign case to non-admin user
3. Login as non-admin
4. Verify limited AI widget access

## Step 10: Troubleshooting Common Issues

### AI Widget Not Showing
```bash
# Check file permissions
chmod -R 755 custom/
chown -R www-data:www-data custom/

# Clear SuiteCRM cache
rm -rf cache/modules/Cases/
rm -rf cache/javascript/

# Verify template path
ls -la custom/modules/Cases/tpls/ai_status_widget.tpl
```

### Database Errors
```sql
-- Verify AI fields exist
SHOW COLUMNS FROM cases LIKE 'ai_%';

-- Check for data in AI fields
SELECT COUNT(*) FROM cases WHERE ai_suggested_status IS NOT NULL;
```

### Analysis Not Running
```bash
# Check if logic hooks are loaded
php -r "
define('sugarEntry', true);
require_once 'sugar_version.php';
require_once 'include/entrypoint.php';

\$case = BeanFactory::getBean('Cases');
\$hooks = \$case->getHookArray('after_save');
print_r(\$hooks);
"
```

### JavaScript Errors
```javascript
// Add to browser console for debugging
jQuery(document).ready(function($) {
    console.log("AI Widget Debug Info:");
    console.log("Widget element:", $('.ai-status-widget').length);
    console.log("AJAX endpoint:", "index.php?entryPoint=aiStatusAction");
    console.log("Current case ID:", $('input[name="record"]').val());
});
```

## Step 11: Production Readiness Checklist

### Before Going Live
- [ ] All tests passing
- [ ] Database backup created
- [ ] Error handling verified
- [ ] Performance benchmarks met
- [ ] Security audit completed
- [ ] User training documentation ready
- [ ] Rollback plan prepared

### OpenAI API Integration (Future)
```php
// When ready for real AI, modify config_override.php:
$sugar_config['ai_status_intelligence']['mock_mode'] = false;
$sugar_config['ai_status_intelligence']['openai_api_key'] = 'your-api-key-here';
```

## Expected Test Results

### Successful Installation
- Database schema updated with 5 new AI fields
- SuiteCRM cache cleared without errors
- Quick Repair shows no issues
- No PHP errors in logs

### Successful Analysis
- AI widget appears in case detail view
- Analysis runs in <500ms
- Confidence score between 0.0-1.0
- Suggested status matches case situation
- Analysis factors show relevant data

### Successful UI Interaction
- Accept/Dismiss buttons work
- AJAX calls succeed
- Status updates properly
- Audit logs created
- Widget disappears after action

This comprehensive testing approach ensures the AI Status Intelligence feature works correctly in your specific SuiteCRM environment before any real-world deployment.