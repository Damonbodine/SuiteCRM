# Billable Hours Quick Entry Dashlet

A specialized dashlet designed for criminal defense attorneys to quickly and efficiently log billable time directly from the SuiteCRM dashboard.

## Features

### Core Functionality
- **Quick Time Entry**: Log billable hours with minimal clicks
- **Built-in Timer**: Start/stop timer functionality with automatic duration calculation  
- **Case Integration**: Link time entries directly to criminal cases
- **Criminal Defense Activity Types**: Pre-configured activity types specific to criminal defense work:
  - Court Appearance
  - Client Meeting/Conference
  - Legal Research
  - Document Review/Analysis
  - Legal Writing/Brief Preparation
  - Phone Call (Client/Court/Counsel)
  - Investigation/Fact Gathering
  - Trial Preparation
  - Other Legal Work

### Daily Summary
- Real-time tracking of daily entries, total hours, and total billable amount
- Updates automatically as new time entries are logged

### Configuration Options
- Customizable dashlet title
- Default activity type selection
- Default hourly rate setting
- Auto-save functionality toggle

## Installation

### Manual Installation
1. Copy all files to `modules/Home/Dashlets/BillableHoursQuickEntryDashlet/`
2. Run Quick Repair & Rebuild from Admin > Repair
3. Clear browser cache

### Files Structure
```
modules/Home/Dashlets/BillableHoursQuickEntryDashlet/
├── BillableHoursQuickEntryDashlet.php          # Main dashlet class
├── BillableHoursQuickEntryDashlet.tpl          # Main template
├── BillableHoursQuickEntryDashletScript.tpl    # JavaScript functionality
├── BillableHoursQuickEntryDashletOptions.tpl   # Configuration template
├── BillableHoursQuickEntryDashlet.en_us.lang.php # Language strings
├── BillableHoursQuickEntryDashlet.meta.php     # Dashlet metadata
└── README.md                                   # This file
```

## Usage

### Adding to Dashboard
1. Navigate to Home > Dashboard
2. Click "Add Dashlets"
3. Find "Billable Hours Quick Entry" in the Tools category
4. Click to add to your dashboard

### Logging Time
1. **Select Case** (optional): Choose from active cases assigned to you
2. **Activity Type**: Select the type of legal work performed
3. **Duration**: Enter hours (e.g., 1.5 for 1 hour 30 minutes) OR use the timer
4. **Rate**: Hourly rate (defaults to configured amount)
5. **Date/Time**: When the work was performed
6. **Description**: Detailed description of work performed
7. Click "Log Time" to save

### Using the Timer
1. Click "Start Timer" to begin timing
2. Perform your legal work
3. Click "Stop Timer" - duration field will auto-populate
4. Complete the rest of the form and click "Log Time"

### Configuration
1. Click the dashlet options menu (gear icon)
2. Modify:
   - Dashlet title
   - Default activity type
   - Default hourly rate
   - Auto-save preference
3. Click "Save"

## Technical Details

### Data Storage
- Time entries are stored as Tasks in the SuiteCRM Tasks module
- Tasks are marked as "Completed" status
- Billing details are appended to the task description
- Tasks are linked to Cases when a case is selected

### Integration Points
- **Cases Module**: Links time entries to specific cases
- **Tasks Module**: Stores time entry records
- **Users Module**: Associates entries with current user
- **Dashboard System**: Integrates with SuiteCRM's dashlet framework

### Security
- Respects SuiteCRM's ACL (Access Control List) system
- Only shows cases assigned to current user
- Input validation and sanitization
- SQL injection protection through prepared statements

## Testing

### Unit Tests
Run PHPUnit tests:
```bash
cd tests/unit/phpunit/modules/Home/
phpunit BillableHoursQuickEntryDashletTest.php
```

### Acceptance Tests
Run Codeception acceptance tests:
```bash
cd tests/
codecept run acceptance modules/Home/BillableHoursQuickEntryDashletCest.php
```

### Integration Tests
Run custom integration test:
```bash
php tests/integration/BillableHoursIntegrationTest.php
```

## Development

### Contributing
1. Follow SuiteCRM coding standards
2. Add unit tests for new functionality
3. Update documentation as needed
4. Test with multiple browsers and screen sizes

### Extending
The dashlet can be extended by:
- Adding new activity types in the language file
- Customizing the template for different layouts
- Adding additional configuration options
- Integrating with other modules

### Troubleshooting

**Dashlet not appearing in Add Dashlets:**
- Verify all files are in correct location
- Run Admin > Repair > Quick Repair & Rebuild
- Check Apache error logs for PHP errors

**Timer not working:**
- Ensure JavaScript is enabled
- Check browser console for JavaScript errors
- Verify jQuery is loaded on the page

**Time entries not saving:**
- Check user permissions for Tasks module
- Verify database connectivity
- Check SuiteCRM logs in logs/ directory

**Cases not showing in dropdown:**
- Verify user has cases assigned to them
- Check case status (only Open cases shown)
- Verify ACL permissions for Cases module

## License

This dashlet is part of the SuiteCRM modernization project and follows the same licensing as SuiteCRM (AGPLv3).

## Support

For issues, feature requests, or contributions, please refer to the main SuiteCRM project documentation and community resources.