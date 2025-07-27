<?php
/**
 * AI Template Suggestion API
 */
if (!defined('sugarEntry')) {
    define('sugarEntry', true);
}
require_once('include/entryPoint.php');

header('Content-Type: application/json');

try {
    global $db;
    
    // Get request data
    $input = json_decode(file_get_contents('php://input'), true);
    $context = $input['context'] ?? [];
    
    $recipient = $context['recipient'] ?? '';
    $subject = $context['subject'] ?? '';
    $caseId = $context['caseId'] ?? '';
    $emailContent = $context['emailContent'] ?? '';
    
    // Get case details if case ID provided
    $caseDetails = null;
    if ($caseId) {
        $caseQuery = "SELECT * FROM cases WHERE id = '" . addslashes($caseId) . "' AND deleted = 0";
        $caseResult = $db->query($caseQuery);
        if ($caseResult && ($case = $db->fetchByAssoc($caseResult))) {
            $caseDetails = $case;
        }
    }
    
    // AI-powered template suggestion logic
    $suggestions = [];
    
    // Keywords that suggest different template types
    $templateKeywords = [
        'client-update' => ['update', 'progress', 'status', 'information', 'development'],
        'settlement-offer' => ['settle', 'resolution', 'offer', 'negotiate', 'agreement'],
        'discovery-request' => ['discovery', 'documents', 'request', 'production', 'interrogatory'],
        'court-scheduling' => ['hearing', 'court', 'schedule', 'calendar', 'appearance'],
        'opposing-counsel' => ['counsel', 'attorney', 'lawyer', 'representation']
    ];
    
    // Analyze subject and content for keywords
    $searchText = strtolower($subject . ' ' . $emailContent);
    
    foreach ($templateKeywords as $templateType => $keywords) {
        $score = 0;
        foreach ($keywords as $keyword) {
            if (strpos($searchText, $keyword) !== false) {
                $score++;
            }
        }
        
        if ($score > 0) {
            $suggestions[] = [
                'template' => $templateType,
                'score' => $score,
                'reason' => "Detected keywords suggesting " . str_replace('-', ' ', $templateType)
            ];
        }
    }
    
    // Domain-based suggestions (if recipient email indicates type)
    if (strpos($recipient, 'court') !== false || strpos($recipient, '.gov') !== false) {
        $suggestions[] = [
            'template' => 'court-scheduling',
            'score' => 3,
            'reason' => 'Recipient appears to be court-related'
        ];
    }
    
    // Case type-based suggestions
    if ($caseDetails) {
        $caseType = strtolower($caseDetails['type'] ?? '');
        
        if (strpos($caseType, 'personal injury') !== false) {
            $suggestions[] = [
                'template' => 'settlement-offer',
                'score' => 2,
                'reason' => 'Personal injury cases often involve settlement negotiations'
            ];
        }
        
        if (strpos($caseType, 'contract') !== false) {
            $suggestions[] = [
                'template' => 'discovery-request',
                'score' => 2,
                'reason' => 'Contract disputes typically require document discovery'
            ];
        }
    }
    
    // Time-based suggestions
    $currentHour = (int)date('H');
    if ($currentHour >= 17 || $currentHour <= 9) {
        // After hours - suggest client update (less urgent)
        $suggestions[] = [
            'template' => 'client-update',
            'score' => 1,
            'reason' => 'After-hours communication suggests client update'
        ];
    }
    
    // Sort suggestions by score
    usort($suggestions, function($a, $b) {
        return $b['score'] - $a['score'];
    });
    
    // Generate response
    $response = [
        'success' => true,
        'suggestions' => array_slice($suggestions, 0, 3), // Top 3 suggestions
        'context_analysis' => [
            'case_type' => $caseDetails['type'] ?? 'Unknown',
            'case_status' => $caseDetails['status'] ?? 'Unknown',
            'recipient_domain' => substr(strrchr($recipient, "@"), 1),
            'time_of_day' => date('H:i'),
            'detected_keywords' => array_filter($templateKeywords, function($keywords) use ($searchText) {
                foreach ($keywords as $keyword) {
                    if (strpos($searchText, $keyword) !== false) return true;
                }
                return false;
            }, ARRAY_FILTER_USE_KEY)
        ],
        'recommendations' => []
    ];
    
    // Add specific recommendations based on analysis
    if (empty($suggestions)) {
        $response['recommendations'][] = 'No specific template suggested. Consider using a general professional template.';
    } else {
        $topSuggestion = $suggestions[0];
        $response['recommendations'][] = "Recommended template: " . str_replace('-', ' ', ucwords($topSuggestion['template']));
        $response['recommendations'][] = "Reason: " . $topSuggestion['reason'];
    }
    
    // Add case-specific variables if case selected
    if ($caseDetails) {
        $response['variables'] = [
            'case_number' => $caseDetails['case_number'] ?? $caseDetails['id'],
            'case_name' => $caseDetails['name'],
            'case_type' => $caseDetails['type'],
            'case_status' => $caseDetails['status'],
            'case_priority' => $caseDetails['priority']
        ];
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>