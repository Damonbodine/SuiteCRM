# SuiteCRM Development Best Practices for AI Agents

## 🎯 PURPOSE

This guide provides proven strategies for developing SuiteCRM features efficiently, based on analysis of both successful and failed implementations. Following these practices will significantly reduce development time and prevent architectural dead ends.

---

## ⚠️ CRITICAL SUCCESS PRINCIPLES

### **1. Architecture-First Research**
Never start coding without understanding SuiteCRM's specific requirements for your feature type.

### **2. Working Example Discovery**
Always find and study existing SuiteCRM implementations that match your use case.

### **3. Systematic Implementation**
Build incrementally with validation at each step, rather than trying to implement everything at once.

### **4. Systematic Debugging**
When issues arise, debug the specific problem rather than trying completely different approaches.

---

## 📋 MANDATORY PRE-IMPLEMENTATION RESEARCH

### **Phase 1: Feature Type Analysis**

#### **For Module Navigation Features:**
**Key Insight**: SuiteCRM navigation requires multiple integrated components, not just module registration.

**Required Research:**
1. **Study Navigation Architecture**:
   ```bash
   # Essential files to understand BEFORE coding:
   modules/MySettings/TabController.php          # Tab management system
   modules/ACL/ACLController.php                 # Permission filtering  
   include/modules.php                           # Module registration
   cache/application/Ext/Include/modules.ext.php # Compiled module list
   ```

2. **Understand Complete Requirements**:
   - Module registration (`$moduleList`, `$beanList`, `$beanFiles`)
   - ACL actions in database (`acl_actions` table with 8 standard actions)
   - Admin configuration (Display Modules and Subpanels)
   - TabController user preferences integration
   - Cache invalidation across multiple layers
   - Theme-specific navigation rendering

#### **For Action/Button Features:**
**Key Insight**: SuiteCRM has specific action routing patterns that must be followed exactly.

**Required Research:**
1. **Study Action Routing System**:
   ```bash
   # Study these working examples BEFORE implementing:
   modules/Cases/controller.php                  # Controller action patterns
   modules/Accounts/controller.php               # Working action examples
   include/MVC/Controller/SugarController.php    # Base routing logic
   ```

2. **Understand Action Requirements**:
   - Action file naming: exact case sensitivity matters
   - File placement: `/modules/ModuleName/actionName.php`
   - Authentication context preservation
   - Form parameter handling
   - Response format (redirect vs JSON)

### **Phase 2: Working Example Discovery**

#### **Navigation Example Discovery:**
```bash
# Find modules successfully appearing in navigation:
grep -r "moduleList\[\]" custom/Extension/application/Ext/Include/ | head -5

# Check which modules have proper ACL actions:
mysql -e "SELECT DISTINCT category FROM acl_actions WHERE deleted=0" | head -10
```

#### **Action Example Discovery:**
```bash
# Find working action file patterns:
find modules/ -name "*.php" | grep -E "(controller|action)" | head -5

# Look for successful form handling patterns:
grep -r "action=" modules/*/metadata/detailviewdefs.php | head -3
```

**Critical Step**: Don't just find examples - understand WHY they work by tracing through the complete execution flow.

---

## 🔧 SYSTEMATIC IMPLEMENTATION APPROACH

### **Module Development Pattern:**

#### **Step 1: Core Module Structure**
```bash
# Create basic module structure first:
mkdir -p modules/YourModule/{metadata,views,language}
mkdir -p custom/Extension/application/Ext/Include/
```

#### **Step 2: Minimal Working Implementation**
```php
// modules/YourModule/YourModule.php
<?php
class YourModule extends SugarBean {
    public $table_name = 'your_module';
    public $object_name = 'YourModule';
    public $module_name = 'YourModule';
    public $module_dir = 'YourModule';
}
?>
```

#### **Step 3: Registration and Validation**
```php
// custom/Extension/application/Ext/Include/YourModule.php
<?php
$moduleList[] = 'YourModule';
$beanList['YourModule'] = 'YourModule';
$beanFiles['YourModule'] = 'modules/YourModule/YourModule.php';
?>
```

**Validation Command:**
```bash
# After each step, verify registration worked:
grep -r "YourModule" cache/application/Ext/Include/modules.ext.php
```

#### **Step 4: ACL Actions (Required for Navigation)**
```sql
-- Create ALL required ACL actions:
INSERT INTO acl_actions (id, category, acltype, aclaccess, deleted) VALUES
(UUID(), 'YourModule', 'access', 90, 0),
(UUID(), 'YourModule', 'view', 90, 0),
(UUID(), 'YourModule', 'list', 90, 0),
(UUID(), 'YourModule', 'edit', 90, 0),
(UUID(), 'YourModule', 'delete', 90, 0),
(UUID(), 'YourModule', 'export', 90, 0),
(UUID(), 'YourModule', 'import', 90, 0),
(UUID(), 'YourModule', 'massupdate', 90, 0);
```

### **Action Implementation Pattern:**

#### **Step 1: Minimal Test Action**
```php
// modules/YourModule/testAction.php
<?php
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

error_log("TEST ACTION CALLED - SUCCESS");
echo "Action file is working!";
die();
?>
```

#### **Step 2: Test with Simple Form**
```html
<!-- Test in browser -->
<form method="post" action="index.php">
    <input name="module" value="YourModule">
    <input name="action" value="testAction">
    <input type="submit" value="Test Action">
</form>
```

#### **Step 3: Verify Routing Works**
```bash
# Check logs to confirm action was called:
tail -f suitecrm.log | grep "TEST ACTION"
```

#### **Step 4: Build Full Implementation**
Only after confirming the basic routing works, build the full action logic.

---

## 🐛 SYSTEMATIC DEBUGGING FRAMEWORK

### **When Things Don't Work - Follow This Order:**

#### **Step 1: Isolate the Specific Issue**
**Don't try different approaches - debug the current one.**

**For Navigation Issues:**
```bash
# Check each requirement systematically:

# 1. Module registration
grep -r "YourModule" cache/application/Ext/Include/modules.ext.php

# 2. ACL actions exist
mysql -e "SELECT category, acltype FROM acl_actions WHERE category='YourModule'"

# 3. Admin configuration
# Go to Admin > Display Modules - is your module in "Displayed Modules"?

# 4. User preferences
mysql -e "SELECT value FROM user_preferences WHERE category='display_tabs' LIMIT 1" | base64 -d | grep YourModule

# 5. Cache state
ls -la cache/application/Ext/Include/
```

**For Action Issues:**
```bash
# 1. Verify action file exists and is readable
ls -la modules/YourModule/yourAction.php

# 2. Test with minimal action (like testAction example above)

# 3. Check browser Network tab when submitting form
# - Does form actually submit?
# - What URL does it POST to?
# - What's the response?

# 4. Check logs for ANY activity
tail -f suitecrm.log | grep -i "yourmodule\|youraction\|error"
```

#### **Step 2: Fix One Issue at a Time**
**Critical**: Fix the first failing validation before moving to the next.

#### **Step 3: Compare with Working Examples**
```bash
# For navigation - find a working module:
mysql -e "SELECT category FROM acl_actions WHERE category='Accounts'" # Should return 8 rows

# For actions - study working action files:
ls modules/Accounts/*.php | grep -v "controller\|\.js\|\.css"
```

### **Common Issue Resolution:**

#### **"Module doesn't appear in navigation"**
**Resolution Order:**
1. Verify ACL actions exist (most common missing piece)
2. Check Admin > Display Modules configuration
3. Clear cache and rebuild
4. Check user permissions

#### **"Action file not found/not executing"**
**Resolution Order:**
1. Verify exact file name and case sensitivity
2. Test with minimal action file
3. Check form submission in browser Network tab
4. Verify authentication context

---

## ✅ VALIDATION CHECKLISTS

### **Module Navigation Checklist:**

**Before declaring navigation "complete", verify ALL of these:**

- [ ] Module in `$moduleList` array
- [ ] Module in `$beanList` array  
- [ ] Module in `$beanFiles` array
- [ ] Module NOT in `$modInvisList` array
- [ ] 8 ACL actions exist in database (`access`, `view`, `list`, `edit`, `delete`, `export`, `import`, `massupdate`)
- [ ] Module marked as "Displayed" in Admin > Display Modules and Subpanels
- [ ] Cache cleared and rebuilt (Admin > Repair > Quick Repair and Rebuild)
- [ ] Module appears in `cache/application/Ext/Include/modules.ext.php`
- [ ] Browser cache cleared (Ctrl+Shift+R)
- [ ] Tested with different user account
- [ ] Module accessible via direct URL: `index.php?module=YourModule&action=index`

**Validation Commands:**
```bash
# Quick validation script:
echo "=== Module Registration Check ==="
grep -c "YourModule" cache/application/Ext/Include/modules.ext.php

echo "=== ACL Actions Check ==="
mysql -e "SELECT COUNT(*) as acl_count FROM acl_actions WHERE category='YourModule' AND deleted=0"

echo "=== Direct Access Test ==="
curl -s "http://localhost/index.php?module=YourModule&action=index" | grep -q "YourModule" && echo "✅ Direct access works" || echo "❌ Direct access failed"
```

### **Action Implementation Checklist:**

- [ ] Action file exists at exact path: `modules/YourModule/actionName.php`
- [ ] File starts with proper SuiteCRM bootstrap
- [ ] Authentication check implemented
- [ ] Error logging added for debugging
- [ ] Test with minimal action file first
- [ ] Form submits to correct action name (case sensitive)
- [ ] Browser Network tab shows form submission
- [ ] Logs show action file being called
- [ ] Proper response handling (redirect or output)

---

## 🔄 EMERGENCY PROCEDURES

### **If You Break SuiteCRM:**

#### **Immediate Recovery:**
```bash
# 1. Disable your modifications
mv custom/modules/YourModule custom/modules/YourModule.disabled
mv custom/Extension/application/Ext/Include/YourModule.php custom/Extension/application/Ext/Include/YourModule.php.disabled

# 2. Clear caches
rm -rf cache/*

# 3. Test basic functionality
curl "http://localhost/index.php?module=Home&action=index"
```

#### **Systematic Recovery:**
```bash
# 1. Check git status
git status

# 2. Stash current changes
git stash push -m "broken_implementation_backup"

# 3. Return to last working state
git checkout HEAD -- custom/

# 4. Verify SuiteCRM works
# 5. Re-implement using systematic approach above
```

---

## 📚 SUITECRM-SPECIFIC KNOWLEDGE BASE

### **Navigation System Architecture:**

**Key Files and Their Roles:**
- `include/modules.php` - Master module registry
- `modules/MySettings/TabController.php` - Tab management and user preferences
- `modules/ACL/ACLController.php` - Permission filtering (`filterModuleList()` method)
- `themes/SuiteP/include/generic/SugarSpot.php` - Theme-specific navigation rendering
- `cache/application/Ext/Include/modules.ext.php` - Compiled module list

**Navigation Flow:**
```
User Request → TabController::getSystemTabs() → ACLController::filterModuleList() → Theme Rendering
```

**Critical Insight**: A module can be perfectly registered but still not appear if ANY step in this chain fails.

### **Action Routing Architecture:**

**Routing Process:**
```
1. Form POST to index.php
2. SugarApplication::execute() extracts $_REQUEST['module'] and $_REQUEST['action']
3. ControllerFactory::getController() creates controller instance
4. Controller looks for action_methodName() method OR methodName.php file
5. Executes with current authentication context
```

**File Naming Rules:**
- Action files: `/modules/ModuleName/actionName.php` (exact case matters)
- Action methods: `action_methodName()` in controller class
- Entry points: Different system entirely - register in EntryPointRegistry

### **Database Schema Patterns:**

**Required Tables for Custom Modules:**
```sql
-- Your module's main table
CREATE TABLE your_module (
    id varchar(36) PRIMARY KEY,
    name varchar(255),
    date_entered datetime,
    date_modified datetime,
    modified_user_id varchar(36),
    created_by varchar(36),
    deleted tinyint(1) default 0
);

-- ACL actions (required for navigation)
INSERT INTO acl_actions (id, category, acltype, aclaccess, deleted) VALUES
(UUID(), 'YourModule', 'access', 90, 0);
-- ... repeat for all 8 action types
```

### **Cache Management:**

**Cache Layers in SuiteCRM:**
1. **File Cache**: `cache/` directory - cleared by deleting files
2. **Extension Cache**: `cache/application/Ext/` - rebuilt by Quick Repair
3. **SugarCache**: In-memory cache - cleared by SugarCache::flush()
4. **Browser Cache**: Client-side - cleared by Ctrl+Shift+R
5. **Smarty Templates**: `cache/smarty/` - cleared with file cache

**Proper Cache Clearing Sequence:**
```bash
# 1. Clear file cache
rm -rf cache/*

# 2. Rebuild via SuiteCRM
# Admin > Repair > Quick Repair and Rebuild

# 3. Clear browser cache
# Ctrl+Shift+R or equivalent

# 4. Verify cache regeneration
ls -la cache/application/Ext/Include/modules.ext.php
```

---

## 🛠️ PRACTICAL CODE TEMPLATES

### **Module Registration Template:**
```php
// File: custom/Extension/application/Ext/Include/YourModule.php
<?php
// Module registration - ALL required arrays
$moduleList[] = 'YourModule';
$beanList['YourModule'] = 'YourModule';
$beanFiles['YourModule'] = 'modules/YourModule/YourModule.php';

// Remove from invisible list if present
if (isset($modInvisList) && in_array('YourModule', $modInvisList)) {
    $modInvisList = array_diff($modInvisList, array('YourModule'));
}
?>
```

### **Basic Module Class Template:**
```php
// File: modules/YourModule/YourModule.php
<?php
class YourModule extends SugarBean {
    public $table_name = 'your_module';
    public $object_name = 'YourModule';
    public $module_name = 'YourModule';
    public $module_dir = 'YourModule';
    
    public $new_schema = true;
    public $importable = true;
    public $disable_row_level_security = true;
    
    public function __construct() {
        parent::__construct();
    }
}
?>
```

### **Action File Template:**
```php
// File: modules/YourModule/yourAction.php
<?php
// Required SuiteCRM bootstrap
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

// Authentication verification
global $current_user;
if (empty($current_user->id)) {
    sugar_die("Authentication required");
}

// Logging for debugging
$GLOBALS['log']->info("Action yourAction called by user: " . $current_user->user_name);

// Get and validate parameters
$record_id = $_REQUEST['record'] ?? '';
if (empty($record_id)) {
    $GLOBALS['log']->error("yourAction: Missing record ID");
    die("Missing required parameter: record");
}

try {
    // Your business logic here
    $bean = BeanFactory::retrieveBean('YourModule', $record_id);
    if (!$bean) {
        throw new Exception("Record not found: " . $record_id);
    }
    
    // Perform your action
    // ...
    
    $GLOBALS['log']->info("yourAction completed successfully for record: " . $record_id);
    
    // Proper response - redirect back to record
    SugarApplication::redirect("index.php?module=YourModule&action=DetailView&record=" . $record_id);
    
} catch (Exception $e) {
    $GLOBALS['log']->error("yourAction failed: " . $e->getMessage());
    die("Action failed: " . $e->getMessage());
}
?>
```

### **ACL Actions Creation Script:**
```sql
-- File: scripts/create_acl_actions.sql
-- Creates all required ACL actions for module navigation

INSERT INTO acl_actions (id, category, acltype, aclaccess, deleted) VALUES
(UUID(), 'YourModule', 'access', 90, 0),
(UUID(), 'YourModule', 'view', 90, 0),
(UUID(), 'YourModule', 'list', 90, 0),
(UUID(), 'YourModule', 'edit', 90, 0),
(UUID(), 'YourModule', 'delete', 90, 0),
(UUID(), 'YourModule', 'export', 90, 0),
(UUID(), 'YourModule', 'import', 90, 0),
(UUID(), 'YourModule', 'massupdate', 90, 0);
```

---

## 📈 SUCCESS PATTERNS

### **Proven Development Sequence:**

1. **Research Phase** (30-60 minutes)
   - Study similar working modules
   - Understand complete requirements
   - Plan implementation approach

2. **Minimal Implementation** (15-30 minutes)
   - Create basic structure
   - Test core functionality
   - Validate each component

3. **Incremental Building** (varies by feature)
   - Add one component at a time
   - Validate after each addition
   - Debug issues immediately

4. **Final Integration** (15-30 minutes)
   - Complete testing
   - Clean up debug code
   - Documentation updates

### **Time Investment vs. Savings:**
- **Initial Research**: 1-2 hours
- **Systematic Implementation**: 2-4 hours
- **Total**: 3-6 hours for most features

**Compared to trial-and-error approach**: 15+ failed attempts over weeks

---

## 🎯 MEASURING SUCCESS

### **Development Velocity Metrics:**
- **Research Time**: Should be front-loaded (1-2 hours initially)
- **Implementation Time**: Should decrease with each component
- **Debug Time**: Should be minimal with systematic approach
- **Rework Time**: Should be near zero with proper validation

### **Quality Indicators:**
- First implementation attempt succeeds
- No major architectural changes needed
- Components work together without conflicts
- Code follows SuiteCRM patterns and conventions

---

## 🔍 GETTING HELP

### **When Stuck - Escalation Order:**

1. **Check This Guide** - Have you followed all protocols?
2. **Study Working Examples** - Find similar working SuiteCRM implementations
3. **Systematic Debugging** - Use validation checklists and debugging commands
4. **Check Logs** - SuiteCRM logs, PHP error logs, browser console
5. **Emergency Recovery** - Use rollback procedures if system is broken

### **Key Diagnostic Commands:**
```bash
# Module registration check
grep -r "YourModule" cache/application/Ext/Include/

# ACL actions verification
mysql -e "SELECT category, acltype, aclaccess FROM acl_actions WHERE category='YourModule'"

# Direct access test
curl -s "http://localhost/index.php?module=YourModule&action=index"

# Action file verification
ls -la modules/YourModule/yourAction.php

# Log monitoring
tail -f suitecrm.log | grep -i "yourmodule\|error"
```

---

## 📝 FINAL REMINDERS

1. **Architecture First**: Always understand SuiteCRM's requirements before coding
2. **Working Examples**: Copy proven patterns rather than inventing new ones
3. **Incremental Validation**: Test each component as you build it
4. **Systematic Debugging**: Fix specific issues rather than trying different approaches
5. **Documentation**: Update this guide with new patterns you discover

**The goal is to work WITH SuiteCRM's architecture, not against it.**

---

*This guide is based on analysis of both successful implementations and 29+ failed approaches across multiple SuiteCRM features. It will be updated as new patterns and solutions are discovered.*

## Task Master AI Instructions
**Import Task Master's development workflow commands and guidelines, treat as if import is in the main CLAUDE.md file.**
@./.taskmaster/CLAUDE.md
