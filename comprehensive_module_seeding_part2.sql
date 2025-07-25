-- Comprehensive Criminal Defense CRM Module Seeding - Part 2
-- Cases, Relationships, Activities, and Billable Hours

-- ====================
-- STEP 4: CREATE CRIMINAL DEFENSE CASES WITH FULL DETAILS
-- ====================

INSERT INTO cases (id, name, case_number, type, description, priority, status, state, resolution, work_log, ai_confidence_score, ai_suggested_status, ai_last_analysis, ai_analysis_factors, ai_status_needs_review, date_entered, date_modified, created_by, modified_user_id, assigned_user_id, account_id, deleted) VALUES

-- ACTIVE CASES (Current client representation)  
('case-active-marcus-001', 'People v. Washington - Drug Trafficking', 2024001, 'Drug Trafficking', 'Marcus Washington charged with large-scale cocaine distribution (5kg). Search warrant executed at 6AM residence. Client maintains innocence, claims drugs were planted. Warrant based on CI testimony - reliability questionable. Defense: Invalid search warrant (stale information), CI credibility issues, lack of direct evidence.', 'High', 'Pre_Trial', 'Open', '', 'Initial motion filed to suppress evidence based on invalid search warrant. Discovery shows CI has history of false testimony. Client passed polygraph. Warrant affidavit contains material misstatements.', 0.65, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 3 DAY), 'Search warrant timing suspicious after CI contact, CI reliability questionable due to pending charges, large quantity suggests trafficking but possession vs. distribution unclear, client clean record supports credibility', 1, DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), @admin_id, @admin_id, 'partner-sarah-001', 'prosecutor-la-da-004', 0),

('case-active-jennifer-002', 'People v. Lopez - DUI Second Offense', 2024002, 'DUI/DWI', 'Jennifer Lopez arrested for DUI - second offense within 5 years (first was 2019). BAC 0.15 via breath test. Traffic stop for swerving. Field sobriety test failed. Prior conviction complicates case but breath test machine calibration records missing for critical 30-day period. Defense: Breath test machine malfunction, improper calibration, medical condition affecting BAC reading.', 'Medium', 'Plea_Negotiation', 'Open', '', 'Motion to suppress breath test filed based on calibration issues. DMV hearing won - license suspension stayed. Client enrolled in alcohol treatment program voluntarily. Prosecutor willing to discuss reduced charges.', 0.72, 'Plea_Negotiation', DATE_SUB(NOW(), INTERVAL 2 DAY), 'High BAC concerning but breath test machine calibration records missing for 30-day period including arrest date, client proactive with treatment shows good faith, prior conviction limits options but not recent', 0, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), @admin_id, @admin_id, 'associate-david-004', 'prosecutor-la-da-004', 0),

('case-active-robert-003', 'People v. Kim - Possession with Intent to Distribute', 2024003, 'Drug Possession', 'Robert Kim found with 2oz methamphetamine during traffic stop. Intent to distribute charges added based on packaging (multiple small bags) and digital scale found in vehicle. Traffic stop for broken taillight - pretext questionable. Client is college student, no prior record. Defense: Illegal traffic stop (no broken taillight), personal use amount, student with no dealing history.', 'High', 'Investigation', 'Open', '', 'Traffic stop video obtained - no visible taillight damage. Client statement taken - admits to personal use only, scale was for cooking (culinary student). Character witnesses lined up from school. Expert witness needed for packaging analysis.', 0.58, 'Pre_Trial', DATE_SUB(NOW(), INTERVAL 5 DAY), 'Traffic stop pretext questionable as no actual equipment violation visible on video, packaging suggests personal use not distribution based on amount and client profile, client cooperative and seeking treatment, college student status supports credibility', 1, DATE_SUB(NOW(), INTERVAL 60 DAY), NOW(), @admin_id, @admin_id, 'associate-jennifer-005', 'prosecutor-la-da-004', 0),

('case-active-maria-004', 'People v. Santos - Domestic Violence', 2024004, 'Domestic Violence', 'Maria Santos charged with DV against estranged husband Carlos Santos. Mutual combat situation - both parties injured. Neighbors called 911 hearing fight. Carlos has history of abuse, restraining order violations. Photos show Maria more severely injured. Defense: Self-defense, long history of abuse by Carlos, fear for safety.', 'High', 'Pre_Trial', 'Open', '', 'Medical records obtained showing pattern of prior injuries. Neighbors willing to testify about Carlos being aggressor in past. DV expert lined up to testify about battered woman syndrome. Carlos has new assault charges pending from different victim.', 0.49, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 7 DAY), 'Self-defense claim viable based on injury patterns and witness statements, photos show mutual injuries but Maria more severely hurt, 911 calls support client story with neighbors confirming Carlos as historical aggressor, husband history of violence well-documented', 1, DATE_SUB(NOW(), INTERVAL 75 DAY), NOW(), @admin_id, @admin_id, 'associate-michael-006', 'prosecutor-la-da-004', 0),

('case-active-david-005', 'People v. D. Johnson - Assault with Deadly Weapon', 2024005, 'Assault', 'David Johnson charged with ADW after bar fight at The Blue Moon. Victim claims David attacked with pool cue. Video surveillance available but poor quality due to lighting. Multiple witnesses present. David claims self-defense - victim started fight and grabbed bottle first. Defense: Self-defense, victim was aggressor, witnesses support client version.', 'Medium', 'Trial_Pending', 'Open', '', 'Video enhanced by expert - shows victim making first aggressive move. Five witnesses identified, three support self-defense claim. Victim has history of bar fights and assault convictions. Character witnesses from film industry confirm David non-violent.', 0.61, 'Trial_Pending', DATE_SUB(NOW(), INTERVAL 4 DAY), 'Video evidence inconclusive but enhanced version shows victim aggression first, multiple witnesses support self-defense claim, victim had reputation for violence and initiated confrontation, client work history shows stable non-violent character', 0, DATE_SUB(NOW(), INTERVAL 50 DAY), NOW(), @admin_id, @admin_id, 'associate-lisa-007', 'prosecutor-la-da-004', 0),

-- CLOSED CASES (Former clients - critical for conflict detection)
('case-closed-angela-006', 'People v. Davis - Embezzlement RESOLVED', 2023015, 'White Collar Crime', 'Angela Davis charged with embezzling $75,000 from employer over 18-month period. Client was accounting manager with access to accounts. Initially denied charges but evidence strong. Negotiated plea agreement with full restitution and probation. Case RESOLVED successfully - client avoided prison time.', 'High', 'Closed_Won', 'Closed', 'Plea agreement: 3 years probation, full restitution paid, 200 hours community service. Client retained job with different employer. No jail time served. Excellent outcome considering evidence strength.', 'Client cooperation excellent throughout case. Full restitution paid within 6 months of plea. Employer satisfied with resolution. Client completed financial management counseling. Case closed with all terms satisfied.', 0.85, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 180 DAY), 'Strong plea negotiation resulted in probation and full restitution avoiding prison time, client cooperation excellent and restitution completed early, employer satisfied with resolution and did not oppose plea agreement', 0, DATE_SUB(NOW(), INTERVAL 200 DAY), DATE_SUB(NOW(), INTERVAL 180 DAY), @admin_id, @admin_id, 'partner-sarah-001', 'prosecutor-la-da-004', 0),

('case-closed-michael-007', 'People v. M. Brown - Tax Evasion RESOLVED', 2022008, 'Tax Evasion', 'Michael Brown federal tax evasion case - failed to report $200K income over 3 years. IRS investigation led to criminal referral. Complex business structure with multiple entities. Negotiated civil settlement with IRS that avoided criminal prosecution. Criminal charges DISMISSED.', 'High', 'Dismissed', 'Closed', 'Civil settlement: $85K in back taxes and penalties paid. Criminal charges dismissed. Client business restructured for compliance. Full cooperation with IRS audit. Case dismissed after civil resolution.', 'Civil settlement with IRS completed successfully. All back taxes and penalties paid. Business practices reformed with new accounting systems. Client now fully compliant with all tax obligations. Criminal exposure eliminated.', 0.90, 'Dismissed', DATE_SUB(NOW(), INTERVAL 365 DAY), 'Civil settlement with IRS avoided criminal prosecution, criminal charges dropped after full cooperation and payment, client fully compliant now with reformed business practices, excellent outcome given exposure', 0, DATE_SUB(NOW(), INTERVAL 400 DAY), DATE_SUB(NOW(), INTERVAL 365 DAY), @admin_id, @admin_id, 'partner-robert-002', 'prosecutor-la-da-004', 0),

('case-closed-lisa-008', 'People v. L. Rodriguez - DUI RESOLVED', 2023022, 'DUI/DWI', 'Lisa Rodriguez DUI case - arrested after minor fender-bender. BAC 0.09, just over limit. Breath test administered but procedural violations discovered in officer training records. Motion to suppress granted - breath test excluded. Charges reduced to reckless driving. EXCELLENT outcome.', 'Low', 'Closed_Won', 'Closed', 'Motion to suppress granted due to procedural violations. Breath test excluded from evidence. Charges reduced to reckless driving with fine only. No license suspension. Client retained nursing license.', 'Procedural violations discovered in officer training - breath test operator not properly certified during relevant period. Motion practice successful. Client very satisfied with outcome. Professional license protected.', 0.88, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 90 DAY), 'Procedural violations led to evidence suppression, excellent outcome for client with charges reduced significantly, officer training issues discovered through diligent investigation, client professional license protected', 0, DATE_SUB(NOW(), INTERVAL 120 DAY), DATE_SUB(NOW(), INTERVAL 90 DAY), @admin_id, @admin_id, 'associate-david-004', 'prosecutor-la-da-004', 0),

-- CONFLICT CASES (Cases involving opposing parties - demonstrates conflict detection)
('case-conflict-thomas-009', 'Anderson v. Pacific Legal Group - Malpractice RESOLVED', 2023005, 'Civil Litigation', 'Thomas Anderson sued Pacific Legal Group for legal malpractice in business litigation. Firm failed to file critical motion, missed statute of limitations, caused $500K damages. We represented Anderson against Pacific Legal. WON substantial settlement. NOW PACIFIC LEGAL WANTS TO HIRE US - MAJOR CONFLICT!', 'Medium', 'Closed_Won', 'Closed', 'Settlement: $350K paid by Pacific Legal Group malpractice insurance. Anderson satisfied with resolution. Confidential settlement with non-disclosure agreement. Case resolved successfully.', 'Strong malpractice case - clear attorney error, substantial damages proven. Pacific Legal admitted fault in settlement negotiations. Client received fair compensation for damages. Professional relationship with Pacific Legal terminated due to litigation.', 0.92, 'Closed_Won', DATE_SUB(NOW(), INTERVAL 120 DAY), 'Successful malpractice claim with substantial settlement achieved, Pacific Legal Group admitted fault through settlement, client received fair compensation for attorney errors, clear conflict if Pacific Legal seeks our representation', 0, DATE_SUB(NOW(), INTERVAL 150 DAY), DATE_SUB(NOW(), INTERVAL 120 DAY), @admin_id, @admin_id, 'partner-maria-003', 'law-firm-pacific-007', 0),

('case-conflict-patricia-010', 'People v. White - Insurance Fraud RESOLVED', 2022018, 'Fraud', 'Patricia White charged with insurance fraud - allegedly staged auto accident for $25K claim. Investigation by insurance company led to criminal charges. We successfully defended - charges DISMISSED due to flawed investigation. NOW her ex-husband wants representation against her in civil matter - CONFLICT!', 'Medium', 'Dismissed', 'Closed', 'Charges dismissed after motion hearing. Insurance company investigation found to be inadequate and biased. No evidence of fraud. Client reputation restored. Insurance company paid claim.', 'Insurance company investigation severely flawed with investigator bias. Motion to dismiss granted based on lack of evidence. Client fully vindicated. Professional relationship excellent. Clear conflict with any adverse representation.', 0.89, 'Dismissed', DATE_SUB(NOW(), INTERVAL 200 DAY), 'Insurance company investigation flawed with investigator bias, charges dismissed and client reputation restored, successful defense with full vindication, clear conflict if representing adverse parties against former client', 0, DATE_SUB(NOW(), INTERVAL 230 DAY), DATE_SUB(NOW(), INTERVAL 200 DAY), @admin_id, @admin_id, 'associate-lisa-007', 'prosecutor-la-da-004', 0);

-- ====================
-- STEP 5: CREATE CONTACT-CASE RELATIONSHIPS  
-- ====================

INSERT INTO contacts_cases (id, contact_id, case_id, contact_role, date_modified, deleted) VALUES
-- Active client relationships
(UUID(), 'client-active-marcus-001', 'case-active-marcus-001', 'Client', NOW(), 0),
(UUID(), 'client-active-jennifer-002', 'case-active-jennifer-002', 'Client', NOW(), 0),
(UUID(), 'client-active-robert-003', 'case-active-robert-003', 'Client', NOW(), 0),
(UUID(), 'client-active-maria-004', 'case-active-maria-004', 'Client', NOW(), 0),
(UUID(), 'client-active-david-005', 'case-active-david-005', 'Client', NOW(), 0),

-- Former client relationships (CRITICAL for conflict detection)
(UUID(), 'client-former-angela-006', 'case-closed-angela-006', 'Client', DATE_SUB(NOW(), INTERVAL 180 DAY), 0),
(UUID(), 'client-former-michael-007', 'case-closed-michael-007', 'Client', DATE_SUB(NOW(), INTERVAL 365 DAY), 0),
(UUID(), 'client-former-lisa-008', 'case-closed-lisa-008', 'Client', DATE_SUB(NOW(), INTERVAL 90 DAY), 0),

-- Conflict case relationships
(UUID(), 'opposing-thomas-011', 'case-conflict-thomas-009', 'Client', DATE_SUB(NOW(), INTERVAL 120 DAY), 0),
(UUID(), 'opposing-patricia-012', 'case-conflict-patricia-010', 'Client', DATE_SUB(NOW(), INTERVAL 200 DAY), 0),

-- Family member relationships (creates conflict scenarios)
(UUID(), 'family-carlos-013', 'case-active-maria-004', 'Family Member', NOW(), 0),
(UUID(), 'family-michelle-014', 'case-active-david-005', 'Family Member', NOW(), 0),

-- Prosecutor relationships  
(UUID(), 'prosecutor-amanda-015', 'case-active-marcus-001', 'Opposing Counsel', NOW(), 0),
(UUID(), 'prosecutor-amanda-015', 'case-active-jennifer-002', 'Opposing Counsel', NOW(), 0),
(UUID(), 'prosecutor-mark-016', 'case-closed-michael-007', 'Opposing Counsel', DATE_SUB(NOW(), INTERVAL 365 DAY), 0),

-- Expert witness relationships
(UUID(), 'expert-alan-020', 'case-active-marcus-001', 'Expert Witness', NOW(), 0),
(UUID(), 'expert-nancy-021', 'case-active-maria-004', 'Expert Witness', NOW(), 0);

-- ====================
-- STEP 6: CREATE ACCOUNT-CASE RELATIONSHIPS
-- ====================

INSERT INTO accounts_cases (id, account_id, case_id, date_modified, deleted) VALUES
-- Court relationships
(UUID(), 'court-la-superior-001', 'case-active-marcus-001', NOW(), 0),
(UUID(), 'court-la-superior-001', 'case-active-jennifer-002', NOW(), 0),
(UUID(), 'court-la-superior-001', 'case-active-robert-003', NOW(), 0),
(UUID(), 'court-la-superior-001', 'case-active-maria-004', NOW(), 0),
(UUID(), 'court-la-superior-001', 'case-active-david-005', NOW(), 0),

-- Prosecutor office relationships
(UUID(), 'prosecutor-la-da-004', 'case-active-marcus-001', NOW(), 0),
(UUID(), 'prosecutor-la-da-004', 'case-active-jennifer-002', NOW(), 0),
(UUID(), 'prosecutor-la-da-004', 'case-active-robert-003', NOW(), 0),
(UUID(), 'prosecutor-la-da-004', 'case-active-maria-004', NOW(), 0),
(UUID(), 'prosecutor-la-da-004', 'case-active-david-005', NOW(), 0),

-- Expert witness service relationships
(UUID(), 'expert-forensic-009', 'case-active-marcus-001', NOW(), 0),
(UUID(), 'expert-medical-010', 'case-active-maria-004', NOW(), 0),

-- Conflict relationship - Pacific Legal Group
(UUID(), 'law-firm-pacific-007', 'case-conflict-thomas-009', DATE_SUB(NOW(), INTERVAL 120 DAY), 0);

-- ====================
-- STEP 7: CREATE ACCOUNT-CONTACT RELATIONSHIPS
-- ====================

INSERT INTO accounts_contacts (id, account_id, contact_id, role, date_modified, deleted) VALUES
-- Prosecutor office contacts
(UUID(), 'prosecutor-la-da-004', 'prosecutor-amanda-015', 'Employee', NOW(), 0),
(UUID(), 'prosecutor-la-da-004', 'prosecutor-mark-016', 'Employee', NOW(), 0),
(UUID(), 'prosecutor-bh-ca-005', 'prosecutor-diana-017', 'Employee', NOW(), 0),

-- Court contacts  
(UUID(), 'court-la-superior-001', 'judge-william-018', 'Judge', NOW(), 0),
(UUID(), 'court-la-superior-001', 'judge-margaret-019', 'Judge', NOW(), 0),

-- Expert witness service contacts
(UUID(), 'expert-forensic-009', 'expert-alan-020', 'Expert', NOW(), 0),
(UUID(), 'expert-medical-010', 'expert-nancy-021', 'Expert', NOW(), 0),
(UUID(), 'expert-financial-011', 'expert-frank-022', 'Expert', NOW(), 0);

-- Continue with Part 3 for activities and billable hours...