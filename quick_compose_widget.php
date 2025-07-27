<?php
/**
 * Quick Compose Widget - Can be embedded in case detail pages
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}

// Get context from URL parameters
$caseId = $_GET['case_id'] ?? '';
$contactId = $_GET['contact_id'] ?? '';
$module = $_GET['module'] ?? '';
$record = $_GET['record'] ?? '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Quick Compose</title>
    <style>
        .quick-compose-widget {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .widget-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .quick-btn {
            padding: 10px 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .quick-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        .quick-btn.settlement {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .quick-btn.discovery {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            color: #333;
        }
        .quick-btn.client {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        }
    </style>
</head>
<body>
    <div class="quick-compose-widget">
        <div class="widget-title">
            ⚡ Quick Compose
        </div>
        
        <div class="quick-actions">
            <a href="javascript:void(0)" class="quick-btn client" 
               onclick="openSmartCompose('client-update', '<?php echo htmlspecialchars($caseId); ?>')">
                📧 Client Update
            </a>
            
            <a href="javascript:void(0)" class="quick-btn settlement" 
               onclick="openSmartCompose('settlement-offer', '<?php echo htmlspecialchars($caseId); ?>')">
                🤝 Settlement Offer
            </a>
            
            <a href="javascript:void(0)" class="quick-btn discovery" 
               onclick="openSmartCompose('discovery-request', '<?php echo htmlspecialchars($caseId); ?>')">
                📋 Discovery Request
            </a>
            
            <a href="javascript:void(0)" class="quick-btn" 
               onclick="openSmartCompose('court-scheduling', '<?php echo htmlspecialchars($caseId); ?>')">
                ⚖️ Court Communication
            </a>
            
            <a href="javascript:void(0)" class="quick-btn" 
               onclick="openSmartCompose('', '<?php echo htmlspecialchars($caseId); ?>')">
                ✍️ Custom Email
            </a>
        </div>
    </div>

    <script>
        function openSmartCompose(templateType, caseId, contactId) {
            let url = 'smart_compose.php';
            let params = [];
            
            if (caseId) params.push('case_id=' + encodeURIComponent(caseId));
            if (contactId) params.push('contact_id=' + encodeURIComponent(contactId));
            if (templateType) params.push('template=' + encodeURIComponent(templateType));
            
            if (params.length > 0) {
                url += '?' + params.join('&');
            }
            
            window.open(url, '_blank', 'width=1000,height=700,scrollbars=yes,resizable=yes');
        }
    </script>
</body>
</html>