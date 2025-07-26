# Phase 1 Testing Integration Plan

## Testing Framework Analysis

SuiteCRM has a comprehensive testing framework that we can leverage for our Phase 1 changes:

### 🧪 **Available Testing Types**

1. **Unit Tests** (PHPUnit) - `/tests/unit/phpunit/`
2. **Acceptance Tests** (Codeception) - `/tests/acceptance/`
3. **API Tests** (Codeception) - `/tests/api/`
4. **Install Tests** - `/tests/install/`

### 🎯 **Relevant Existing Tests for Phase 1**

#### **Theme-Related Tests**
- **`tests/unit/phpunit/includes/SugarTheme/SugarThemeTest.php`**
  - Tests theme MIME type detection
  - Tests `SugarThemeRegistry::current()` functionality
  - **Critical for Step 1**: Ensures theme switching doesn't break

#### **Survey Module Tests**
- **`tests/acceptance/modules/Surveys/SurveysCest.php`**
  - Tests survey module navigation and display
  - **Critical for Step 2**: Validates survey pages load correctly
  - Uses admin login and list view verification

#### **MVC Framework Tests**
- **`tests/unit/phpunit/includes/MVC/View/ViewFactoryTest.php`**
  - Tests view factory pattern we rely on
- **`tests/unit/phpunit/includes/MVC/SugarApplicationTest.php`**
  - Tests core application flow

#### **Utils Tests**
- **`tests/unit/phpunit/includes/UtilsTest.php`**
  - **Critical for Step 1**: Tests functions in `utils.php` we're modifying

---

## Enhanced Phase 1 Testing Strategy

### **Pre-Change Testing (Baseline)**

Before making any changes, run comprehensive tests to establish baseline:

```bash
# Run all relevant tests to establish baseline
cd /Users/damonbodine/suitecrm/SuiteCRM

# 1. Theme-related unit tests
./vendor/bin/phpunit tests/unit/phpunit/includes/SugarTheme/SugarThemeTest.php

# 2. Utils tests (covers our utils.php changes)
./vendor/bin/phpunit tests/unit/phpunit/includes/UtilsTest.php

# 3. MVC tests (covers view factory changes)
./vendor/bin/phpunit tests/unit/phpunit/includes/MVC/

# 4. Survey acceptance tests
./vendor/bin/codecept run acceptance tests/acceptance/modules/Surveys/SurveysCest.php

# 5. Core functionality smoke test
./vendor/bin/codecept run acceptance tests/acceptance/LoginCest.php
```

### **Step-by-Step Testing Integration**

#### **Step 1: System Default Theme Configuration**

**Files Modified:**
- `include/utils.php`
- `install/install_defaults.php`

**Required Testing:**
```bash
# Before changes
./vendor/bin/phpunit tests/unit/phpunit/includes/UtilsTest.php

# After changes - verify utils.php functions still work
./vendor/bin/phpunit tests/unit/phpunit/includes/UtilsTest.php

# Theme switching tests
./vendor/bin/phpunit tests/unit/phpunit/includes/SugarTheme/SugarThemeTest.php

# Installation tests (if install tests exist)
./vendor/bin/codecept run install
```

**Custom Test Cases to Add:**
```php
// Add to SugarThemeTest.php
public function testThemeFallbackMechanism(): void
{
    // Test that non-existent theme falls back properly
    $originalTheme = $GLOBALS['sugar_config']['default_theme'] ?? 'SuiteP';
    
    // Set invalid theme
    $GLOBALS['sugar_config']['default_theme'] = 'NonExistentTheme';
    
    // Should fallback to SuiteP
    $theme = SugarThemeRegistry::current();
    self::assertEquals('SuiteP', $theme->__toString());
    
    // Restore
    $GLOBALS['sugar_config']['default_theme'] = $originalTheme;
}
```

#### **Step 2: Survey Entry Point CSS Dependencies**

**Files Modified:**
- `modules/Surveys/Entry/Survey.php`
- `modules/Surveys/Entry/Thanks.php`

**Required Testing:**
```bash
# Survey module functionality
./vendor/bin/codecept run acceptance tests/acceptance/modules/Surveys/SurveysCest.php

# Test with different themes (manual verification needed)
# 1. Set theme to SuiteP - verify surveys work
# 2. Change theme in admin - verify surveys still work
# 3. Test responsive design on mobile
```

**Custom Test Cases to Add:**
```php
// Add to SurveysCest.php
public function testSurveyPageLoadsWithDifferentThemes(AcceptanceTester $I)
{
    $I->wantTo('Verify survey pages work with different themes');
    
    $I->loginAsAdmin();
    
    // Test with SuiteP theme
    $I->amOnPage('/modules/Surveys/Entry/Survey.php?survey_id=test');
    $I->see('Survey'); // Basic page load verification
    
    // TODO: Add theme switching and re-test
    // This requires admin theme change functionality
}
```

#### **Step 3: Social Media JavaScript Image Paths**

**Files Modified:**
- `include/social/twitter/twitter_feed.js`
- `include/social/facebook/facebook_subpanel.js`

**Required Testing:**
```bash
# No existing automated tests for social media
# Manual testing required:

# 1. Check if social media features are enabled
# 2. Test Twitter feed display
# 3. Test Facebook integration
# 4. Verify image paths resolve correctly
```

**New Test Cases to Create:**
```javascript
// JavaScript unit test (if framework supports)
describe('Social Media Theme Integration', function() {
    it('should use dynamic theme paths for images', function() {
        // Mock SUGAR.App.config.theme
        SUGAR.App.config = { theme: 'TestTheme' };
        
        // Test dynamic path generation
        var expectedPath = 'themes/TestTheme/images/twitter_icon.png';
        var actualPath = getThemeAssetPath('images/twitter_icon.png');
        
        expect(actualPath).toBe(expectedPath);
    });
});
```

---

## **Enhanced Testing Commands**

### **Complete Test Suite Run**
```bash
#!/bin/bash
# run_phase1_tests.sh

echo "=== Phase 1 Testing Suite ==="

echo "1. Running Theme Tests..."
./vendor/bin/phpunit tests/unit/phpunit/includes/SugarTheme/SugarThemeTest.php

echo "2. Running Utils Tests..."
./vendor/bin/phpunit tests/unit/phpunit/includes/UtilsTest.php

echo "3. Running MVC Tests..."
./vendor/bin/phpunit tests/unit/phpunit/includes/MVC/

echo "4. Running Survey Acceptance Tests..."
./vendor/bin/codecept run acceptance tests/acceptance/modules/Surveys/SurveysCest.php

echo "5. Running Core Login Tests..."
./vendor/bin/codecept run acceptance tests/acceptance/LoginCest.php

echo "6. Running API Tests (basic)..."
./vendor/bin/codecept run api tests/api/V8/GetModulesCest.php

echo "=== Testing Complete ==="
```

### **Regression Test After Each Step**
```bash
#!/bin/bash
# regression_test.sh

echo "Running regression tests after Phase 1 changes..."

# Quick smoke tests
echo "1. Theme functionality..."
./vendor/bin/phpunit tests/unit/phpunit/includes/SugarTheme/SugarThemeTest.php

echo "2. Basic login and navigation..."
./vendor/bin/codecept run acceptance tests/acceptance/LoginCest.php

echo "3. Survey module..."
./vendor/bin/codecept run acceptance tests/acceptance/modules/Surveys/SurveysCest.php

echo "4. Core modules (sample)..."
./vendor/bin/codecept run acceptance tests/acceptance/modules/Accounts/AccountsCest.php

echo "Regression testing complete."
```

---

## **Test-Driven Phase 1 Execution**

### **Modified Step Sequence**

1. **Pre-Flight Testing** ✈️
   - Run complete baseline test suite
   - Document any existing test failures
   - Ensure testing environment is functional

2. **Step 1A: Test-First Development**
   - Write additional theme fallback tests
   - Modify `utils.php` with theme fallback logic
   - Run tests to verify functionality
   - Only proceed if all tests pass

3. **Step 1B: Validation**
   - Run full theme and utils test suite
   - Manual verification of theme switching
   - Performance check (no degradation)

4. **Step 2A: Survey Tests**
   - Enhance survey test coverage
   - Modify survey entry point files
   - Test with multiple themes
   - Verify responsive design integrity

5. **Step 3A: Social Media Testing**
   - Create basic social media tests
   - Modify JavaScript files
   - Manual verification of image loading
   - Cross-theme compatibility check

6. **Final Validation**
   - Complete regression test suite
   - Performance benchmarking
   - User acceptance testing checklist

---

## **Test Coverage Metrics**

### **Target Coverage for Phase 1**

- **Theme System**: 100% of modified functions tested
- **Survey Module**: Basic functionality + theme compatibility
- **Social Media**: Manual verification (automated if possible)
- **Core Functions**: No regression in existing functionality

### **Success Criteria**

✅ **All existing tests continue to pass**  
✅ **New theme fallback logic tested and working**  
✅ **Survey pages load correctly with different themes**  
✅ **Social media images resolve with dynamic paths**  
✅ **No performance degradation**  
✅ **Manual verification checklist completed**

---

## **Integration with Existing CI/CD**

If SuiteCRM has continuous integration:

```yaml
# .github/workflows/phase1-testing.yml (example)
name: Phase 1 UI Modernization Tests

on: [push, pull_request]

jobs:
  phase1-tests:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '7.4'
        
    - name: Install dependencies
      run: composer install
      
    - name: Run Theme Tests
      run: ./vendor/bin/phpunit tests/unit/phpunit/includes/SugarTheme/
      
    - name: Run Utils Tests
      run: ./vendor/bin/phpunit tests/unit/phpunit/includes/UtilsTest.php
      
    - name: Run Survey Tests
      run: ./vendor/bin/codecept run acceptance tests/acceptance/modules/Surveys/
```

---

This testing integration ensures that every Phase 1 change is validated systematically using the existing robust testing framework, providing confidence that UI modernization won't break core functionality.