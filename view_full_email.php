<?php
/**
 * View Full Email - Display complete email content with AI analysis
 */

if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

require_once('include/entryPoint.php');

// Set headers
header('Content-Type: text/html; charset=UTF-8');

try {
    global $db, $current_user;
    
    // Get email ID from request
    $emailId = $_GET['email_id'] ?? '';
    if (empty($emailId)) {
        throw new Exception('Email ID is required');
    }
    
    // Get the email analysis from database
    $query = "SELECT * FROM ai_email_analysis 
              WHERE email_message_id = ? 
              AND user_id = ? 
              AND deleted = 0 
              LIMIT 1";
    
    $userId = $current_user->id ?? '1';
    $result = $db->pQuery($query, array($emailId, $userId));
    
    if (!$result || !($email = $db->fetchByAssoc($result))) {
        throw new Exception('Email not found or access denied');
    }
    
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Full Email View - <?php echo htmlspecialchars($email['subject'] ?? 'No Subject'); ?></title>
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
            .email-container {
                max-width: 800px;
                margin: 0 auto;
                background: white;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                overflow: hidden;
            }
            .email-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px;
            }
            .email-title {
                font-size: 24px;
                font-weight: 600;
                margin: 0 0 10px 0;
            }
            .email-meta {
                opacity: 0.9;
                font-size: 14px;
            }
            .email-content {
                padding: 30px;
            }
            .section {
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 1px solid #eee;
            }
            .section:last-child {
                border-bottom: none;
                margin-bottom: 0;
            }
            .section-title {
                font-size: 18px;
                font-weight: 600;
                color: #333;
                margin-bottom: 15px;
                display: flex;
                align-items: center;
            }
            .section-title .icon {
                margin-right: 10px;
                font-size: 20px;
            }
            .metadata-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 15px;
                margin-bottom: 20px;
            }
            .metadata-item {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 8px;
                border-left: 4px solid #667eea;
            }
            .metadata-label {
                font-weight: 600;
                color: #555;
                font-size: 12px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 5px;
            }
            .metadata-value {
                color: #333;
                font-size: 14px;
            }
            .priority-badge {
                display: inline-block;
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            .priority-high { background: #fee; color: #d63384; }
            .priority-medium { background: #fff3cd; color: #f57c00; }
            .priority-low { background: #d1ecf1; color: #0c5460; }
            .category-badge {
                display: inline-block;
                padding: 6px 12px;
                background: #e7f3ff;
                color: #0066cc;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 500;
            }
            .email-body {
                background: #fafbfc;
                border: 1px solid #e1e8ed;
                border-radius: 8px;
                padding: 20px;
                white-space: pre-wrap;
                font-family: Georgia, serif;
                line-height: 1.7;
                color: #333;
                max-height: 400px;
                overflow-y: auto;
            }
            .ai-summary {
                background: linear-gradient(135deg, #667eea20, #764ba220);
                border-left: 4px solid #667eea;
                padding: 20px;
                border-radius: 8px;
                font-style: italic;
                color: #444;
            }
            .back-button {
                display: inline-flex;
                align-items: center;
                background: #667eea;
                color: white;
                padding: 12px 20px;
                text-decoration: none;
                border-radius: 6px;
                font-weight: 500;
                transition: background 0.3s;
                margin-bottom: 20px;
            }
            .back-button:hover {
                background: #5a67d8;
                color: white;
                text-decoration: none;
            }
            .back-button .icon {
                margin-right: 8px;
            }
            @media (max-width: 600px) {
                body { padding: 10px; }
                .email-header, .email-content { padding: 20px; }
                .metadata-grid { grid-template-columns: 1fr; }
            }
        </style>
    </head>
    <body>
        <a href="index.php?module=Emails&action=index" class="back-button">
            <span class="icon">←</span> Back to Email List
        </a>
        
        <div class="email-container">
            <div class="email-header">
                <h1 class="email-title"><?php echo htmlspecialchars($email['subject'] ?: 'No Subject'); ?></h1>
                <div class="email-meta">
                    Email ID: <?php echo htmlspecialchars(substr($email['email_message_id'], 0, 12)); ?>... | 
                    Analyzed: <?php echo date('M j, Y g:i A', strtotime($email['analysis_date'])); ?>
                </div>
            </div>
            
            <div class="email-content">
                <!-- Email Metadata -->
                <div class="section">
                    <h2 class="section-title">
                        <span class="icon">📧</span> Email Details
                    </h2>
                    <div class="metadata-grid">
                        <div class="metadata-item">
                            <div class="metadata-label">From</div>
                            <div class="metadata-value">
                                <?php 
                                if (!empty($email['sender_name'])) {
                                    echo htmlspecialchars($email['sender_name']);
                                    if (!empty($email['sender_email'])) {
                                        echo ' &lt;' . htmlspecialchars($email['sender_email']) . '&gt;';
                                    }
                                } else {
                                    echo htmlspecialchars($email['sender_email'] ?: 'Unknown Sender');
                                }
                                ?>
                            </div>
                        </div>
                        <?php if (!empty($email['email_date'])): ?>
                        <div class="metadata-item">
                            <div class="metadata-label">Date</div>
                            <div class="metadata-value"><?php echo date('M j, Y g:i A', strtotime($email['email_date'])); ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="metadata-item">
                            <div class="metadata-label">Category</div>
                            <div class="metadata-value">
                                <span class="category-badge"><?php echo htmlspecialchars(ucfirst($email['category'] ?: 'General')); ?></span>
                            </div>
                        </div>
                        <div class="metadata-item">
                            <div class="metadata-label">Priority</div>
                            <div class="metadata-value">
                                <?php 
                                $priority = intval($email['priority_score'] ?: 5);
                                $priorityClass = $priority >= 8 ? 'priority-high' : ($priority >= 6 ? 'priority-medium' : 'priority-low');
                                $priorityText = $priority >= 8 ? 'High' : ($priority >= 6 ? 'Medium' : 'Low');
                                ?>
                                <span class="priority-badge <?php echo $priorityClass; ?>"><?php echo $priorityText; ?> (<?php echo $priority; ?>/10)</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- AI Analysis -->
                <?php if (!empty($email['ai_summary'])): ?>
                <div class="section">
                    <h2 class="section-title">
                        <span class="icon">🤖</span> AI Analysis Summary
                    </h2>
                    <div class="ai-summary">
                        <?php echo htmlspecialchars($email['ai_summary']); ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Email Body -->
                <?php if (!empty($email['email_body'])): ?>
                <div class="section">
                    <h2 class="section-title">
                        <span class="icon">📄</span> Email Content
                    </h2>
                    <div class="email-body">
                        <?php echo htmlspecialchars($email['email_body']); ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="section">
                    <h2 class="section-title">
                        <span class="icon">📄</span> Email Content
                    </h2>
                    <div class="email-body">
                        <em>Email content not available - this may be a test record or the content was not fetched from Gmail API.</em>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </body>
    </html>
    <?php
    
} catch (Exception $e) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Email View Error</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 40px; }
            .error { background: #fee; border: 1px solid #fcc; padding: 20px; border-radius: 8px; color: #c33; }
            .back-link { margin-top: 20px; }
            .back-link a { color: #0066cc; text-decoration: none; }
        </style>
    </head>
    <body>
        <div class="error">
            <h2>Error Loading Email</h2>
            <p><?php echo htmlspecialchars($e->getMessage()); ?></p>
        </div>
        <div class="back-link">
            <a href="index.php?module=Emails&action=index">← Back to Email List</a>
        </div>
    </body>
    </html>
    <?php
}
?>