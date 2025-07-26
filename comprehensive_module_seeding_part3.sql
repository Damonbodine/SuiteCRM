-- Comprehensive Criminal Defense CRM Module Seeding - Part 3
-- Activities, Billable Hours, Conflict Search, and Module Integration

-- ====================
-- STEP 8: CREATE CALLS (Client Communications & Billable Hours)
-- ====================

INSERT INTO calls (id, name, status, direction, date_start, date_end, duration_hours, duration_minutes, description, parent_type, parent_id, assigned_user_id, contact_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES

-- Client consultation calls (BILLABLE HOURS for dashlet)
('call-marcus-consult-001', 'Client Consultation - Marcus Washington Drug Case Strategy', 'Held', 'Inbound', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 1, 30, 'Initial strategy discussion for drug trafficking case. Reviewed search warrant issues, discussed defense options, explained legal process. Client maintains innocence. Authorized to file suppression motion.', 'Cases', 'case-active-marcus-001', 'partner-sarah-001', 'client-active-marcus-001', DATE_SUB(NOW(), INTERVAL 2 DAY), NOW(), @admin_id, @admin_id, 0),

('call-jennifer-update-002', 'Case Update Call - Jennifer Lopez DUI Progress', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 0, 45, 'Updated client on plea negotiation progress. DMV hearing successful - license suspension stayed. Discussed treatment program enrollment. Client relieved about license. Scheduled follow-up.', 'Cases', 'case-active-jennifer-002', 'associate-david-004', 'client-active-jennifer-002', DATE_SUB(NOW(), INTERVAL 1 DAY), NOW(), @admin_id, @admin_id, 0),

('call-robert-family-003', 'Family Conference - Robert Kim Support System', 'Held', 'Conference', NOW(), NOW(), 1, 0, 'Conference call with client and parents about drug possession case. Explained defense strategy, discussed treatment options. Parents very supportive, willing to testify as character witnesses.', 'Cases', 'case-active-robert-003', 'associate-jennifer-005', 'client-active-robert-003', NOW(), NOW(), @admin_id, @admin_id, 0),

('call-maria-emergency-004', 'Emergency Call - Maria Santos Safety Concerns', 'Held', 'Inbound', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 0, 30, 'Emergency call from client about husband violating restraining order. Advised to call police immediately. Documented new violations for case. Connected with victim advocate.', 'Cases', 'case-active-maria-004', 'associate-michael-006', 'client-active-maria-004', DATE_SUB(NOW(), INTERVAL 3 DAY), NOW(), @admin_id, @admin_id, 0),

('call-david-witness-005', 'Witness Interview Prep - David Johnson Bar Fight', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), 0, 25, 'Prepared client for witness interviews. Reviewed timeline of bar fight, discussed self-defense claim. Client confident in version of events. Witnesses support his account.', 'Cases', 'case-active-david-005', 'associate-lisa-007', 'client-active-david-005', DATE_SUB(NOW(), INTERVAL 4 DAY), NOW(), @admin_id, @admin_id, 0),

-- Prosecutor negotiation calls
('call-prosecutor-amanda-006', 'Plea Negotiation - Amanda Clark Washington Case', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 0, 20, 'Initial plea discussion with prosecutor. Presented suppression motion arguments. Prosecutor concerned about warrant validity. May be willing to reduce charges if evidence suppressed.', 'Cases', 'case-active-marcus-001', 'partner-sarah-001', 'prosecutor-amanda-015', DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), @admin_id, @admin_id, 0),

-- Expert witness coordination calls  
('call-expert-peterson-007', 'Expert Consultation - Dr. Peterson DNA Analysis', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), 1, 15, 'Consulted with forensic expert about DNA evidence in Washington case. Dr. Peterson reviewed lab reports, found contamination issues. Will testify about evidence reliability problems.', 'Cases', 'case-active-marcus-001', 'partner-sarah-001', 'expert-alan-020', DATE_SUB(NOW(), INTERVAL 6 DAY), NOW(), @admin_id, @admin_id, 0),

-- Follow-up calls for closed cases (demonstrates relationship tracking)
('call-angela-followup-008', 'Post-Case Follow-up - Angela Davis Compliance Check', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 30 DAY), 0, 15, 'Follow-up call with former client about probation compliance. All restitution payments current, community service completed. Client doing well in new position.', 'Cases', 'case-closed-angela-006', 'partner-sarah-001', 'client-former-angela-006', DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), @admin_id, @admin_id, 0);

-- ====================
-- STEP 9: CREATE MEETINGS (Court Appearances & Client Meetings)
-- ====================

INSERT INTO meetings (id, name, status, date_start, date_end, duration_hours, duration_minutes, description, location, parent_type, parent_id, assigned_user_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES

-- Court hearings (BILLABLE HOURS)
('meeting-marcus-motion-001', 'Motion Hearing - People v. Washington Suppression', 'Held', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), 3, 0, 'Pre-trial motion hearing to suppress evidence. Argued search warrant invalidity based on stale information and CI credibility. Judge took matter under submission. Ruling expected next week.', 'LA Superior Court Dept 100', 'Cases', 'case-active-marcus-001', 'partner-sarah-001', DATE_SUB(NOW(), INTERVAL 10 DAY), NOW(), @admin_id, @admin_id, 0),

('meeting-jennifer-dmv-002', 'DMV Hearing - Lopez License Suspension', 'Held', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY), 2, 30, 'DMV administrative hearing on license suspension. Successfully argued breath test machine calibration issues. Hearing officer granted stay of suspension pending criminal case resolution.', 'DMV Hearing Office', 'Cases', 'case-active-jennifer-002', 'associate-david-004', DATE_SUB(NOW(), INTERVAL 15 DAY), NOW(), @admin_id, @admin_id, 0),

('meeting-robert-arraignment-003', 'Arraignment - People v. Kim Drug Possession', 'Held', DATE_SUB(NOW(), INTERVAL 45 DAY), DATE_SUB(NOW(), INTERVAL 45 DAY), 1, 0, 'Initial arraignment hearing. Entered not guilty plea to all charges. Discovery motions filed. Bail continued at $25,000. Next hearing scheduled for pretrial conference.', 'LA Superior Court Dept 102', 'Cases', 'case-active-robert-003', 'associate-jennifer-005', DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), @admin_id, @admin_id, 0),

-- Upcoming court dates
('meeting-maria-pretrial-004', 'Pre-Trial Conference - People v. Santos DV Case', 'Planned', DATE_ADD(NOW(), INTERVAL 14 DAY), DATE_ADD(NOW(), INTERVAL 14 DAY), 2, 0, 'Pre-trial conference with prosecutor and judge. Will present self-defense evidence package. Expert witness testimony on domestic violence dynamics scheduled.', 'LA Superior Court Dept 105', 'Cases', 'case-active-maria-004', 'associate-michael-006', NOW(), NOW(), @admin_id, @admin_id, 0),

('meeting-david-trial-005', 'Jury Trial - People v. Johnson Assault Case', 'Planned', DATE_ADD(NOW(), INTERVAL 30 DAY), DATE_ADD(NOW(), INTERVAL 30 DAY), 8, 0, 'Jury trial scheduled for assault with deadly weapon charges. Witness testimony and video evidence to be presented. Self-defense claim will be primary defense strategy.', 'LA Superior Court Dept 108', 'Cases', 'case-active-david-005', 'associate-lisa-007', NOW(), NOW(), @admin_id, @admin_id, 0),

-- Client strategy meetings
('meeting-marcus-strategy-006', 'Defense Strategy Session - Washington Case Team', 'Held', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY), 2, 0, 'Internal strategy meeting with client and legal team. Reviewed suppression motion prospects, discussed trial strategy if motion denied. Client authorized plea negotiations as backup.', 'Law Firm Conference Room A', 'Cases', 'case-active-marcus-001', 'partner-sarah-001', DATE_SUB(NOW(), INTERVAL 7 DAY), NOW(), @admin_id, @admin_id, 0),

-- Expert witness meetings
('meeting-expert-nancy-007', 'Expert Witness Prep - Dr. Cooper DV Testimony', 'Held', DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY), 1, 30, 'Preparation meeting with domestic violence expert. Reviewed client medical records, injury photos, psychological evaluation. Dr. Cooper prepared to testify about battered woman syndrome.', 'Medical Expert Office', 'Cases', 'case-active-maria-004', 'associate-michael-006', DATE_SUB(NOW(), INTERVAL 12 DAY), NOW(), @admin_id, @admin_id, 0);

-- ====================
-- STEP 10: CREATE TASKS (Legal Research & Case Preparation)
-- ====================

INSERT INTO tasks (id, name, status, priority, date_start, date_due, description, parent_type, parent_id, assigned_user_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES

-- Active case research tasks
('task-marcus-research-001', 'Legal Research - Search Warrant Validity Standards', 'In Progress', 'High', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(NOW(), INTERVAL 2 DAY), 'Research recent cases on confidential informant reliability and stale information in search warrant affidavits. Focus on 9th Circuit decisions and California Supreme Court precedents.', 'Cases', 'case-active-marcus-001', 'paralegal-steven-011', DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), @admin_id, @admin_id, 0),

('task-jennifer-motion-002', 'Motion Preparation - Breath Test Suppression Lopez', 'Completed', 'Medium', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 'Prepare comprehensive motion to suppress breath test results based on machine calibration issues. Include expert testimony about accuracy standards and maintenance records.', 'Cases', 'case-active-jennifer-002', 'paralegal-rebecca-012', DATE_SUB(NOW(), INTERVAL 10 DAY), NOW(), @admin_id, @admin_id, 0),

('task-robert-witnesses-003', 'Witness Interview Coordination - Kim Traffic Stop', 'Pending', 'High', NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY), 'Interview witnesses present during traffic stop. Obtain written statements about lack of equipment violations. Coordinate with investigator for additional witness canvassing.', 'Cases', 'case-active-robert-003', 'paralegal-michelle-010', NOW(), NOW(), @admin_id, @admin_id, 0),

('task-maria-records-004', 'Medical Records Review - Santos DV History', 'In Progress', 'High', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(NOW(), INTERVAL 5 DAY), 'Compile comprehensive medical records showing pattern of abuse. Include emergency room visits, physician notes, psychiatric treatment records. Prepare chronological summary.', 'Cases', 'case-active-maria-004', 'paralegal-steven-011', DATE_SUB(NOW(), INTERVAL 3 DAY), NOW(), @admin_id, @admin_id, 0),

('task-david-video-005', 'Video Enhancement - Johnson Bar Fight Analysis', 'Completed', 'Medium', DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY), 'Coordinate with video expert to enhance surveillance footage. Focus on lighting correction and frame-by-frame analysis of initial aggression. Prepare enhanced video for court presentation.', 'Cases', 'case-active-david-005', 'paralegal-rebecca-012', DATE_SUB(NOW(), INTERVAL 14 DAY), NOW(), @admin_id, @admin_id, 0),

-- General legal research tasks
('task-legal-update-006', 'Legal Update Research - Recent Criminal Procedure Changes', 'In Progress', 'Low', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_ADD(NOW(), INTERVAL 14 DAY), 'Research recent changes in criminal procedure law affecting search and seizure cases. Prepare memo for attorney review and case application analysis.', 'Administration', NULL, 'paralegal-steven-011', DATE_SUB(NOW(), INTERVAL 7 DAY), NOW(), @admin_id, @admin_id, 0),

-- Administrative tasks
('task-billing-review-007', 'Monthly Billing Review - Client Account Reconciliation', 'Pending', 'Medium', NOW(), DATE_ADD(NOW(), INTERVAL 3 DAY), 'Review all client billing for current month. Verify time entries, check billing rates, prepare client invoices. Focus on active cases with significant activity.', 'Administration', NULL, 'admin-patricia-013', NOW(), NOW(), @admin_id, @admin_id, 0),

-- Follow-up tasks for closed cases
('task-angela-compliance-008', 'Compliance Monitoring - Davis Probation Status', 'Completed', 'Low', DATE_SUB(NOW(), INTERVAL 60 DAY), DATE_SUB(NOW(), INTERVAL 30 DAY), 'Monitor former client probation compliance. Check payment status, community service completion, employment verification. Maintain relationship for potential future referrals.', 'Cases', 'case-closed-angela-006', 'paralegal-michelle-010', DATE_SUB(NOW(), INTERVAL 60 DAY), NOW(), @admin_id, @admin_id, 0);

-- ====================
-- STEP 11: CREATE COMPREHENSIVE CONFLICT SEARCH DATA
-- ====================

INSERT INTO conflict_search (id, name, search_term, search_type, modules_searched, confidence_threshold, total_matches_found, high_confidence_matches, medium_confidence_matches, low_confidence_matches, search_status, execution_time, performed_by, search_criteria, search_results, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, deleted) VALUES

-- Critical conflict detection - Pacific Legal Group scenario
('conflict-pacific-legal-001', 'CONFLICT ALERT: Pacific Legal Group Representation Request', 'Pacific Legal Group', 'comprehensive', 'Contacts,Accounts,Cases,Documents', 75, 4, 3, 1, 0, 'completed', 0.2847, 'partner-sarah-001', 'New client intake - Pacific Legal Group wants to retain us for business litigation. MANDATORY conflict check due to prior adverse representation.', '{"high_confidence_matches": [{"type": "Account", "name": "Pacific Legal Group", "conflict_type": "Former Opposing Party", "case": "Anderson v. Pacific Legal Group", "outcome": "Won $350K malpractice settlement against them", "attorney": "Maria Rodriguez", "risk_level": "CRITICAL"}, {"type": "Contact", "name": "Thomas Anderson", "relationship": "Former Client vs Current Opposing Party", "case_connection": "We represented Anderson against Pacific Legal", "conflict_details": "Cannot represent firm we successfully sued for malpractice"}, {"type": "Case", "name": "Anderson v. Pacific Legal Group", "status": "Closed_Won", "settlement": "$350K", "conflict_basis": "Direct adverse representation"}], "recommendation": "REJECT REPRESENTATION - Clear conflict of interest under Rule 1.9"}', NOW(), NOW(), @admin_id, @admin_id, 'partner-sarah-001', 0),

-- Family member conflict detection
('conflict-carlos-santos-002', 'Conflict Check: Carlos Santos - Multiple Family Connections', 'Carlos Santos', 'comprehensive', 'Contacts,Accounts,Cases', 75, 3, 2, 1, 0, 'completed', 0.1892, 'associate-michael-006', 'Intake for Carlos Santos - domestic violence case. Check for family relationships and prior representations.', '{"high_confidence_matches": [{"type": "Contact", "name": "Carlos Santos", "relationship": "Brother of current client Maria Santos", "conflict_type": "Family Member", "current_case": "People v. Santos - Domestic Violence", "role": "Estranged husband of client", "risk_level": "HIGH"}, {"type": "Contact", "name": "Maria Santos", "relationship": "Current Active Client", "case": "People v. Santos - DV charges against Carlos", "conflict_details": "Cannot represent both parties in DV case"}], "medium_confidence_matches": [{"type": "Case", "name": "People v. Santos", "involvement": "Carlos is alleged victim/opposing party", "attorney": "Michael Johnson"}], "recommendation": "CANNOT REPRESENT - Direct conflict representing both parties in DV case"}', NOW(), NOW(), @admin_id, @admin_id, 'associate-michael-006', 0),

-- Former client conflict detection
('conflict-angela-davis-003', 'Former Client Conflict: Angela Davis Opposition Request', 'Angela Davis', 'comprehensive', 'Contacts,Accounts,Cases,Documents', 75, 2, 2, 0, 0, 'completed', 0.1543, 'partner-robert-002', 'Davis former employer wants to retain us for collection action against Angela Davis. Check for prior representation conflicts.', '{"high_confidence_matches": [{"type": "Contact", "name": "Angela Davis", "conflict_type": "Former Client", "case": "People v. Davis - Embezzlement", "outcome": "Successful plea agreement", "attorney": "Sarah Mitchell", "restitution_status": "Fully paid", "risk_level": "ABSOLUTE CONFLICT"}, {"type": "Case", "name": "People v. Davis - Embezzlement RESOLVED", "result": "Plea agreement with probation", "client_relationship": "Successfully defended", "conflict_basis": "Rule 1.9 - Former client adverse representation"}], "recommendation": "ABSOLUTELY CANNOT REPRESENT - Clear violation of Rule 1.9 former client conflict"}', NOW(), NOW(), @admin_id, @admin_id, 'partner-robert-002', 0),

-- Prosecutor relationship tracking
('conflict-amanda-clark-004', 'Prosecutor Relationship Analysis: Amanda Clark Caseload', 'Amanda Clark', 'comprehensive', 'Contacts,Accounts,Cases,Meetings,Calls', 75, 8, 5, 3, 0, 'completed', 0.3456, 'associate-david-004', 'Analysis of current cases with prosecutor Amanda Clark. Check for potential scheduling conflicts and case overlap patterns.', '{"high_confidence_matches": [{"type": "Contact", "name": "Amanda Clark", "role": "Prosecutor - Frequent Opposing Counsel", "cases_opposed": ["People v. Washington", "People v. Lopez", "People v. Davis (closed)"], "relationship": "Professional - Regular Opponent"}, {"type": "Cases", "active_oppositions": 2, "closed_oppositions": 1, "win_rate_against": "67%", "negotiation_success": "High"}], "medium_confidence_matches": [{"type": "Meetings", "court_appearances": 5, "plea_negotiations": 3}, {"type": "Calls", "professional_contacts": 4}], "recommendation": "No conflicts - Normal prosecutorial relationship"}', NOW(), NOW(), @admin_id, @admin_id, 'associate-david-004', 0),

-- Expert witness availability and conflicts
('conflict-dr-peterson-005', 'Expert Witness Conflict: Dr. Peterson Retention Request', 'Dr. Peterson', 'comprehensive', 'Contacts,Accounts,Cases,Meetings', 75, 3, 1, 2, 0, 'completed', 0.2089, 'associate-jennifer-005', 'Dr. Peterson requested by opposing counsel in Kim case. Check current retention status and potential conflicts.', '{"high_confidence_matches": [{"type": "Contact", "name": "Dr. Alan Peterson", "current_status": "Retained by our firm", "case": "People v. Washington", "expert_area": "DNA/Forensic Analysis"}], "medium_confidence_matches": [{"type": "Cases", "current_retention": "People v. Washington - Active", "opposing_case": "People v. Kim - Same court"}, {"type": "Account", "expert_firm": "Forensic Analysis Associates", "business_relationship": "Active"}], "recommendation": "CONFLICT - Expert currently retained by us in active case. Cannot work for opposition simultaneously"}', NOW(), NOW(), @admin_id, @admin_id, 'associate-jennifer-005', 0),

-- Comprehensive firm conflict analysis
('conflict-comprehensive-006', 'Monthly Comprehensive Conflict Analysis - All Active Matters', 'Comprehensive Firm Analysis', 'comprehensive', 'Contacts,Accounts,Cases,Opportunities,Leads', 85, 45, 8, 15, 22, 'completed', 1.2567, 'admin-alex-015', 'Monthly comprehensive analysis of all firm relationships for potential conflicts. Automated system check of all active matters, contacts, and business relationships.', '{"summary": {"total_active_cases": 5, "total_clients": 22, "potential_conflicts": 8, "resolved_conflicts": 3}, "high_risk_conflicts": [{"entity": "Pacific Legal Group", "risk": "Former adverse party seeking representation"}, {"entity": "Carlos Santos", "risk": "Family member of current client"}], "monitoring_required": ["Thomas Anderson relationship", "All prosecutor relationships", "Expert witness exclusive arrangements"], "recommendations": ["Continue monthly comprehensive scans", "Update conflict database", "Train staff on conflict identification"]}', NOW(), NOW(), @admin_id, @admin_id, 'admin-alex-015', 0);

-- ====================
-- STEP 12: CREATE ADDITIONAL ACTIVITIES FOR BILLABLE HOURS DASHLET
-- ====================

-- Create additional calls for robust billable hours data
INSERT INTO calls (id, name, status, direction, date_start, duration_hours, duration_minutes, description, parent_type, parent_id, assigned_user_id, date_entered, date_modified, created_by, modified_user_id, deleted) VALUES
('call-billing-001', 'Client Billing Discussion - Payment Plan Setup', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 8 DAY), 0, 15, 'Discussed payment plan options with client. Set up monthly payment schedule.', 'Contacts', 'client-active-marcus-001', 'admin-patricia-013', NOW(), NOW(), @admin_id, @admin_id, 0),
('call-billing-002', 'Follow-up Legal Research Call - Case Law Update', 'Held', 'Internal', DATE_SUB(NOW(), INTERVAL 6 DAY), 0, 30, 'Research coordination call between attorney and paralegal on recent case law.', 'Cases', 'case-active-robert-003', 'paralegal-steven-011', NOW(), NOW(), @admin_id, @admin_id, 0),
('call-billing-003', 'Court Clerk Coordination - Filing Schedule', 'Held', 'Outbound', DATE_SUB(NOW(), INTERVAL 4 DAY), 0, 10, 'Coordinated with court clerk on motion filing deadlines and hearing dates.', 'Cases', 'case-active-jennifer-002', 'paralegal-rebecca-012', NOW(), NOW(), @admin_id, @admin_id, 0);

-- Display comprehensive results
SELECT '=== COMPREHENSIVE CRIMINAL DEFENSE CRM SEEDING COMPLETE ===' as 'Status';
SELECT '' as '';

-- Summary statistics  
SELECT 'DATA SUMMARY:' as 'Summary';
SELECT CONCAT('Users: ', COUNT(*)) as 'Count' FROM users WHERE deleted = 0 AND first_name IS NOT NULL;
SELECT CONCAT('Accounts: ', COUNT(*)) as 'Count' FROM accounts WHERE deleted = 0;
SELECT CONCAT('Contacts: ', COUNT(*)) as 'Count' FROM contacts WHERE deleted = 0;
SELECT CONCAT('Cases: ', COUNT(*)) as 'Count' FROM cases WHERE deleted = 0 AND name NOT LIKE 'AI Test%';
SELECT CONCAT('Calls: ', COUNT(*)) as 'Count' FROM calls WHERE deleted = 0;
SELECT CONCAT('Meetings: ', COUNT(*)) as 'Count' FROM meetings WHERE deleted = 0;
SELECT CONCAT('Tasks: ', COUNT(*)) as 'Count' FROM tasks WHERE deleted = 0;
SELECT CONCAT('Conflict Searches: ', COUNT(*)) as 'Count' FROM conflict_search WHERE deleted = 0;

SELECT '' as '';
SELECT 'RELATIONSHIP SUMMARY:' as 'Relationships';
SELECT CONCAT('Contact-Case Links: ', COUNT(*)) as 'Count' FROM contacts_cases WHERE deleted = 0;
SELECT CONCAT('Account-Case Links: ', COUNT(*)) as 'Count' FROM accounts_cases WHERE deleted = 0;
SELECT CONCAT('Account-Contact Links: ', COUNT(*)) as 'Count' FROM accounts_contacts WHERE deleted = 0;

SELECT '' as '';
SELECT 'BILLABLE HOURS SUMMARY (for BillableHoursQuickEntry Dashlet):' as 'Billable_Hours';
SELECT 
    CONCAT(u.first_name, ' ', u.last_name) as 'Attorney',
    COUNT(c.id) as 'Total_Calls',
    SUM(c.duration_hours) as 'Call_Hours',
    SUM(c.duration_minutes) as 'Call_Minutes'
FROM calls c
JOIN users u ON c.assigned_user_id = u.id
WHERE c.deleted = 0 AND c.status = 'Held'
GROUP BY u.id, u.first_name, u.last_name
ORDER BY (SUM(c.duration_hours) * 60 + SUM(c.duration_minutes)) DESC;

SELECT '' as '';
SELECT 'ACTIVE CASE PORTFOLIO:' as 'Active_Cases';
SELECT 
    c.name as 'Case_Name',
    c.type as 'Case_Type', 
    c.status as 'Status',
    CONCAT(ROUND(c.ai_confidence_score * 100), '%') as 'AI_Confidence',
    CONCAT(u.first_name, ' ', u.last_name) as 'Attorney',
    co.first_name as 'Client_First',
    co.last_name as 'Client_Last'
FROM cases c
JOIN users u ON c.assigned_user_id = u.id
LEFT JOIN contacts_cases cc ON c.id = cc.case_id AND cc.deleted = 0
LEFT JOIN contacts co ON cc.contact_id = co.id AND co.deleted = 0
WHERE c.state = 'Open' AND c.deleted = 0 AND c.name NOT LIKE 'AI Test%'
ORDER BY c.date_entered DESC;

SELECT '' as '';
SELECT '🎯 CRITICAL CONFLICT SCENARIOS READY FOR TESTING:' as 'Conflict_Tests';
SELECT 
    search_term as 'Search_Term',
    total_matches_found as 'Matches',
    high_confidence_matches as 'High_Risk',
    CASE 
        WHEN high_confidence_matches > 0 THEN '❌ REJECT'
        WHEN medium_confidence_matches > 2 THEN '⚠️ CAUTION' 
        ELSE '✅ CLEAR'
    END as 'Recommendation'
FROM conflict_search 
WHERE deleted = 0
ORDER BY high_confidence_matches DESC, total_matches_found DESC;