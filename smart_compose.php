<?php
/**
 * Smart Legal Compose - AI-Powered Email Templates for Attorneys
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}
require_once('include/entryPoint.php');

// Check if this is a reply or has pre-selected context
$replyToEmailId = $_GET['reply_to'] ?? '';
$selectedCaseId = $_GET['case_id'] ?? '';
$selectedContactId = $_GET['contact_id'] ?? '';
$preSelectedTemplate = $_GET['template'] ?? '';
$replyData = null;

global $db;

// Get reply data if applicable
if ($replyToEmailId) {
    $emailIdEscaped = "'" . addslashes($replyToEmailId) . "'";
    $query = "SELECT * FROM ai_email_analysis WHERE email_message_id = $emailIdEscaped AND deleted = 0 LIMIT 1";
    $result = $db->query($query);
    
    if ($result && ($email = $db->fetchByAssoc($result))) {
        $replyData = $email;
    }
}

// Get active cases for context
$casesQuery = "SELECT id, name, case_number, type, status, priority 
               FROM cases 
               WHERE deleted = 0 AND status IN ('Open_Assigned', 'Open_New', 'Open_Pending')
               ORDER BY date_entered DESC 
               LIMIT 20";
$casesResult = $db->query($casesQuery);
$activeCases = [];
while ($casesResult && ($case = $db->fetchByAssoc($casesResult))) {
    $activeCases[] = $case;
}

// Get contacts with emails
$contactsQuery = "SELECT id, first_name, last_name, email1, account_name, title 
                  FROM contacts 
                  WHERE deleted = 0 AND (email1 IS NOT NULL AND email1 != '')
                  ORDER BY last_name, first_name 
                  LIMIT 50";
$contactsResult = $db->query($contactsQuery);
$contacts = [];
while ($contactsResult && ($contact = $db->fetchByAssoc($contactsResult))) {
    $contacts[] = $contact;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>🧠 Smart Legal Compose</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f5f7fa;
            line-height: 1.6;
        }
        .smart-compose-container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .compose-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .compose-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        .close-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        .close-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .compose-layout {
            display: flex;
            height: 600px;
        }
        .sidebar {
            width: 300px;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
            padding: 20px;
            overflow-y: auto;
        }
        .main-compose {
            flex: 1;
            padding: 30px;
        }
        .sidebar-section {
            margin-bottom: 25px;
        }
        .sidebar-title {
            font-weight: 600;
            margin-bottom: 12px;
            color: #495057;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .template-list {
            max-height: 200px;
            overflow-y: auto;
        }
        .template-item {
            padding: 10px;
            margin-bottom: 8px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 13px;
        }
        .template-item:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        .template-item.active {
            border-color: #667eea;
            background: #e3f2fd;
        }
        .template-name {
            font-weight: 600;
            color: #333;
        }
        .template-desc {
            color: #6c757d;
            font-size: 12px;
            margin-top: 4px;
        }
        .smart-select {
            width: 100%;
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 10px;
        }
        .context-info {
            background: #e8f5e8;
            padding: 12px;
            border-radius: 6px;
            font-size: 13px;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }
        .form-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        .form-input:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-textarea {
            min-height: 250px;
            resize: vertical;
            font-family: inherit;
        }
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5a6fd8;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-template {
            background: #17a2b8;
            color: white;
            margin-bottom: 10px;
            width: 100%;
            font-size: 12px;
            padding: 8px 12px;
        }
        .btn-template:hover {
            background: #138496;
        }
        .variable-hint {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-top: 8px;
        }
        .recipient-suggestion {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px;
            margin-bottom: 4px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        .recipient-suggestion:hover {
            background: #f0f4ff;
        }
        .recipient-name {
            font-weight: 600;
        }
        .recipient-email {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="smart-compose-container">
        <div class="compose-header">
            <h1 class="compose-title">
                🧠 Smart Legal Compose
                <?php echo $replyData ? ' - Reply' : ''; ?>
            </h1>
            <button class="close-btn" onclick="window.close()">✕ Close</button>
        </div>
        
        <div class="compose-layout">
            <!-- Sidebar with templates and context -->
            <div class="sidebar">
                <div class="sidebar-section">
                    <div class="sidebar-title">📁 Case Context</div>
                    <select id="caseSelector" class="smart-select" onchange="loadCaseContext(this.value)">
                        <option value="">Select a case...</option>
                        <?php foreach ($activeCases as $case): ?>
                            <option value="<?php echo htmlspecialchars($case['id']); ?>" 
                                    <?php echo $selectedCaseId == $case['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($case['case_number'] ?? $case['id']); ?> - 
                                <?php echo htmlspecialchars(substr($case['name'], 0, 30)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <div id="caseInfo" class="context-info" style="display: none;">
                        <strong>Case Info:</strong><br>
                        <span id="caseDetails"></span>
                    </div>
                </div>
                
                <div class="sidebar-section">
                    <div class="sidebar-title">📧 Smart Recipients</div>
                    <div class="template-list">
                        <?php foreach ($contacts as $contact): ?>
                            <div class="recipient-suggestion" onclick="addRecipient('<?php echo htmlspecialchars($contact['email1']); ?>', '<?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?>')">
                                <div>
                                    <div class="recipient-name"><?php echo htmlspecialchars($contact['first_name'] . ' ' . $contact['last_name']); ?></div>
                                    <div class="recipient-email"><?php echo htmlspecialchars($contact['email1']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="sidebar-section">
                    <div class="sidebar-title">📝 Legal Templates</div>
                    <div class="template-list" id="templateList">
                        <!-- Templates will be loaded here -->
                    </div>
                </div>
            </div>
            
            <!-- Main compose area -->
            <div class="main-compose">
                <?php if ($replyData): ?>
                <div style="background: #f8f9fa; border-left: 4px solid #667eea; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <h4 style="margin: 0 0 8px 0; color: #495057; font-size: 14px;">Replying to:</h4>
                    <p style="margin: 0; color: #6c757d; font-size: 13px;">
                        <strong>From:</strong> <?php echo htmlspecialchars($replyData['sender_name'] ?? 'Unknown'); ?> 
                        &lt;<?php echo htmlspecialchars($replyData['sender_email'] ?? ''); ?>&gt;<br>
                        <strong>Subject:</strong> <?php echo htmlspecialchars($replyData['subject'] ?? 'No Subject'); ?>
                    </p>
                </div>
                <?php endif; ?>
                
                <form id="smartComposeForm" onsubmit="sendSmartEmail(event)">
                    <div class="form-group">
                        <label class="form-label" for="to">To:</label>
                        <input type="email" id="to" name="to" class="form-input" 
                               value="<?php echo $replyData ? htmlspecialchars($replyData['sender_email'] ?? '') : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="cc">CC: (optional)</label>
                        <input type="text" id="cc" name="cc" class="form-input" 
                               placeholder="Multiple emails separated by commas">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" class="form-input" 
                               value="<?php echo $replyData ? 'Re: ' . htmlspecialchars($replyData['subject'] ?? '') : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="body">Message:</label>
                        <textarea id="body" name="body" class="form-input form-textarea" 
                                  required placeholder="Type your message or select a template..."></textarea>
                        <div class="variable-hint">
                            💡 <strong>Available variables:</strong> {{case_number}}, {{client_name}}, {{attorney_name}}, {{date}}, {{court_name}}
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-primary" onclick="generateAIResponse()">
                            🤖 AI Assist
                        </button>
                        <button type="submit" class="btn btn-success">
                            📧 Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Legal email templates
        const legalTemplates = {
            'client-update': {
                name: 'Client Case Update',
                description: 'Standard case progress update for clients',
                content: `Dear {{client_name}},

I hope this email finds you well. I wanted to provide you with an update on the progress of your case ({{case_number}}).

{{case_update_details}}

Next Steps:
- {{next_step_1}}
- {{next_step_2}}

Please don't hesitate to reach out if you have any questions or concerns.

Best regards,
{{attorney_name}}
{{law_firm_name}}`
            },
            'settlement-offer': {
                name: 'Settlement Offer',
                description: 'Professional settlement negotiation email',
                content: `Dear Counsel,

I represent {{client_name}} in the matter of {{case_number}}. After careful consideration of the facts and circumstances surrounding this case, my client is prepared to discuss resolution.

We propose the following settlement terms:
{{settlement_terms}}

This offer is made in good faith and is subject to the approval of my client. Please let me know if you would like to discuss this matter further.

Sincerely,
{{attorney_name}}`
            },
            'discovery-request': {
                name: 'Discovery Request',
                description: 'Formal discovery request template',
                content: `Dear Counsel,

Pursuant to the applicable rules of civil procedure, please find attached our formal discovery requests in the matter of {{case_number}}.

We request responses within the time frame prescribed by law. If you anticipate any issues with meeting this deadline, please contact me immediately.

The requested discovery includes:
{{discovery_items}}

Thank you for your anticipated cooperation.

Respectfully,
{{attorney_name}}`
            },
            'court-scheduling': {
                name: 'Court Scheduling',
                description: 'Communication with court clerk or opposing counsel for scheduling',
                content: `Dear {{recipient_title}},

I am writing regarding the scheduling of proceedings in {{case_number}}.

{{scheduling_request}}

I am available on the following dates:
{{available_dates}}

Please let me know what dates work best for the court's calendar.

Thank you for your assistance.

Respectfully submitted,
{{attorney_name}}`
            },
            'opposing-counsel': {
                name: 'Opposing Counsel Communication',
                description: 'Professional communication with opposing counsel',
                content: `Dear {{opposing_counsel_name}},

I hope this email finds you well. I am writing regarding {{case_number}} to discuss {{matter_topic}}.

{{main_message}}

I look forward to your response and to working together on this matter in a professional manner.

Best regards,
{{attorney_name}}`
            }
        };

        // Load templates on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadTemplates();
            
            // Auto-select template if pre-selected
            const preSelectedTemplate = '<?php echo htmlspecialchars($preSelectedTemplate); ?>';
            if (preSelectedTemplate) {
                setTimeout(() => applyTemplate(preSelectedTemplate), 500);
            }
            
            // Auto-load case context if case is pre-selected
            const preSelectedCase = '<?php echo htmlspecialchars($selectedCaseId); ?>';
            if (preSelectedCase) {
                document.getElementById('caseSelector').value = preSelectedCase;
                loadCaseContext(preSelectedCase);
            }
        });

        function loadTemplates() {
            const templateList = document.getElementById('templateList');
            templateList.innerHTML = '';
            
            Object.keys(legalTemplates).forEach(key => {
                const template = legalTemplates[key];
                const templateDiv = document.createElement('div');
                templateDiv.className = 'template-item';
                templateDiv.onclick = () => applyTemplate(key);
                
                templateDiv.innerHTML = `
                    <div class="template-name">${template.name}</div>
                    <div class="template-desc">${template.description}</div>
                `;
                
                templateList.appendChild(templateDiv);
            });
        }

        function applyTemplate(templateKey) {
            const template = legalTemplates[templateKey];
            const bodyField = document.getElementById('body');
            
            // Fill in the template content
            bodyField.value = template.content;
            
            // Highlight active template
            document.querySelectorAll('.template-item').forEach(item => {
                item.classList.remove('active');
            });
            event.target.closest('.template-item').classList.add('active');
            
            // Auto-fill variables if case is selected
            const selectedCase = document.getElementById('caseSelector').value;
            if (selectedCase) {
                fillTemplateVariables();
            }
        }

        function addRecipient(email, name) {
            const toField = document.getElementById('to');
            if (toField.value) {
                // Add to CC if To field is already filled
                const ccField = document.getElementById('cc');
                ccField.value = ccField.value ? ccField.value + ', ' + email : email;
            } else {
                toField.value = email;
            }
        }

        function loadCaseContext(caseId) {
            if (caseId) {
                // Show case info
                document.getElementById('caseInfo').style.display = 'block';
                document.getElementById('caseDetails').textContent = 'Loading case details...';
                
                // Here you would fetch case details via AJAX
                // For now, we'll use the data we have
                const caseSelector = document.getElementById('caseSelector');
                const selectedText = caseSelector.options[caseSelector.selectedIndex].text;
                document.getElementById('caseDetails').textContent = selectedText;
                
                // Fill template variables if template is active
                fillTemplateVariables();
            } else {
                document.getElementById('caseInfo').style.display = 'none';
            }
        }

        function fillTemplateVariables() {
            const bodyField = document.getElementById('body');
            let content = bodyField.value;
            
            // Replace common variables
            content = content.replace(/\{\{attorney_name\}\}/g, 'Attorney Name');
            content = content.replace(/\{\{law_firm_name\}\}/g, 'Law Firm Name');
            content = content.replace(/\{\{date\}\}/g, new Date().toLocaleDateString());
            content = content.replace(/\{\{case_number\}\}/g, document.getElementById('caseSelector').selectedOptions[0]?.text?.split(' - ')[0] || 'CASE-NUMBER');
            content = content.replace(/\{\{client_name\}\}/g, 'Client Name');
            
            bodyField.value = content;
        }

        async function generateAIResponse() {
            const context = {
                recipient: document.getElementById('to').value,
                subject: document.getElementById('subject').value,
                emailContent: document.getElementById('body').value,
                caseId: document.getElementById('caseSelector').value
            };
            
            try {
                const response = await fetch('ai_template_suggest.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ context: context })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show AI suggestions
                    let message = '🤖 AI Analysis:\n\n';
                    
                    if (result.suggestions.length > 0) {
                        message += 'Recommended Templates:\n';
                        result.suggestions.forEach((suggestion, index) => {
                            message += `${index + 1}. ${suggestion.template.replace('-', ' ').toUpperCase()}\n`;
                            message += `   Reason: ${suggestion.reason}\n\n`;
                        });
                    }
                    
                    if (result.recommendations.length > 0) {
                        message += 'Recommendations:\n';
                        result.recommendations.forEach(rec => {
                            message += `• ${rec}\n`;
                        });
                    }
                    
                    if (result.context_analysis) {
                        message += `\nContext Analysis:\n`;
                        message += `• Case Type: ${result.context_analysis.case_type}\n`;
                        message += `• Case Status: ${result.context_analysis.case_status}\n`;
                        message += `• Time: ${result.context_analysis.time_of_day}\n`;
                    }
                    
                    alert(message);
                    
                    // Auto-apply top suggestion if available
                    if (result.suggestions.length > 0 && confirm('Apply the top recommended template?')) {
                        applyTemplate(result.suggestions[0].template);
                    }
                } else {
                    alert('AI analysis failed: ' + result.error);
                }
            } catch (error) {
                alert('AI Assist error: ' + error.message);
            }
        }

        async function sendSmartEmail(event) {
            event.preventDefault();
            
            // Use the existing send_email.php functionality
            const form = document.getElementById('smartComposeForm');
            const formData = new FormData(form);
            formData.append('action', 'send');
            
            try {
                const response = await fetch('send_email.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('✅ Email sent successfully!');
                    setTimeout(() => {
                        window.close();
                    }, 1000);
                } else {
                    alert('❌ Failed to send email: ' + result.error);
                }
            } catch (error) {
                alert('❌ Network error: ' + error.message);
            }
        }

        // Auto-resize textarea
        document.getElementById('body').addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    </script>
</body>
</html>