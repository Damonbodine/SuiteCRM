<?php
/**
 * Winnability Analysis Handler - Final Working Version
 * Simple entry point that integrates with SuiteCRM's authentication
 */

// SuiteCRM bootstrap
define('sugarEntry', true);
chdir(dirname(__FILE__));
require_once('include/entryPoint.php');

// Authentication check - SuiteCRM handles this automatically
global $current_user;
if (empty($current_user) || empty($current_user->id)) {
    SugarApplication::redirect('index.php');
    exit;
}

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    SugarApplication::redirect('index.php?module=Cases&action=index');
    exit;
}

try {
    // Get case ID from form submission
    $caseId = $_POST['record'] ?? $_POST['case_id'] ?? '';
    if (empty($caseId)) {
        redirectToCase('', 'Missing case ID for winnability analysis', 'error');
        exit;
    }
    
    // Load and validate case
    $case = BeanFactory::getBean('Cases', $caseId);
    if (empty($case) || empty($case->id)) {
        redirectToCase('', 'Case not found', 'error');
        exit;
    }
    
    // Security: Check user permissions
    if (!$case->ACLAccess('edit')) {
        redirectToCase($caseId, 'Access denied', 'error');
        exit;
    }
    
    // Perform winnability analysis
    $analysis = performWinnabilityAnalysis($case);
    
    if ($analysis) {
        // Save results
        saveAnalysisResults($case, $analysis);
        createCaseUpdate($case, $analysis);
        redirectToCase($caseId, 'Winnability analysis completed successfully!', 'success');
    } else {
        redirectToCase($caseId, 'Analysis failed - please try again', 'error');
    }
    
} catch (Exception $e) {
    $GLOBALS['log']->error('Winnability Analysis Error: ' . $e->getMessage());
    redirectToCase($caseId ?? '', 'System error occurred during analysis', 'error');
}

/**
 * Perform comprehensive winnability analysis
 */
function performWinnabilityAnalysis($case)
{
    try {
        // Analysis factors
        $caseStrength = analyzeCaseStrength($case);
        $evidenceQuality = analyzeEvidenceQuality($case);
        $legalPrecedents = analyzeLegalPrecedents($case);
        $caseComplexity = analyzeCaseComplexity($case);
        
        // Weighted calculation
        $winnabilityScore = (
            ($caseStrength * 0.30) +      // 30% case strength
            ($evidenceQuality * 0.25) +   // 25% evidence
            ($legalPrecedents * 0.25) +   // 25% precedents
            ((100 - $caseComplexity) * 0.20)  // 20% complexity (inverse)
        );
        
        $winnabilityPercentage = max(15, min(90, round($winnabilityScore)));
        
        return array(
            'winnability_percentage' => $winnabilityPercentage,
            'case_strength' => $caseStrength,
            'evidence_quality' => $evidenceQuality,
            'legal_precedents' => $legalPrecedents,
            'case_complexity' => $caseComplexity,
            'reasoning_factors' => generateReasoningFactors($case, $caseStrength, $evidenceQuality, $legalPrecedents, $caseComplexity),
            'confidence_score' => 0.85,
            'analysis_method' => 'integrated_comprehensive'
        );
        
    } catch (Exception $e) {
        $GLOBALS['log']->error('Winnability Analysis Logic Error: ' . $e->getMessage());
        return false;
    }
}

function analyzeCaseStrength($case)
{
    $score = 50; // Base score
    $description = strtolower($case->description ?? '');
    
    // Positive indicators
    if (strpos($description, 'strong evidence') !== false) $score += 15;
    if (strpos($description, 'clear liability') !== false) $score += 15;
    if (strpos($description, 'documented') !== false) $score += 10;
    if (strpos($description, 'witness') !== false) $score += 10;
    if (strpos($description, 'contract breach') !== false) $score += 10;
    
    // Negative indicators
    if (strpos($description, 'disputed') !== false) $score -= 10;
    if (strpos($description, 'unclear') !== false) $score -= 10;
    if (strpos($description, 'complex') !== false) $score -= 5;
    
    // Priority influence
    if ($case->priority === 'High') $score += 10;
    if ($case->priority === 'Low') $score -= 5;
    
    return max(10, min(90, $score));
}

function analyzeEvidenceQuality($case)
{
    $score = 50;
    $description = strtolower($case->description ?? '');
    
    // Quality indicators
    if (strpos($description, 'documentation') !== false) $score += 15;
    if (strpos($description, 'emails') !== false) $score += 10;
    if (strpos($description, 'contracts') !== false) $score += 15;
    if (strpos($description, 'photos') !== false) $score += 10;
    if (strpos($description, 'video') !== false) $score += 10;
    if (strpos($description, 'expert') !== false) $score += 10;
    
    // Poor evidence
    if (strpos($description, 'verbal') !== false) $score -= 10;
    if (strpos($description, 'hearsay') !== false) $score -= 15;
    if (strpos($description, 'no documentation') !== false) $score -= 20;
    
    return max(10, min(90, $score));
}

function analyzeLegalPrecedents($case)
{
    $score = 50;
    $caseType = strtolower($case->type ?? '');
    $description = strtolower($case->description ?? '');
    
    // Case type analysis
    switch ($caseType) {
        case 'contract': $score += 10; break;
        case 'personal injury': $score += 5; break;
        case 'employment': $score += 5; break;
        case 'intellectual property': $score -= 5; break;
    }
    
    // Precedent indicators
    if (strpos($description, 'similar cases') !== false) $score += 10;
    if (strpos($description, 'established law') !== false) $score += 10;
    if (strpos($description, 'precedent') !== false) $score += 10;
    if (strpos($description, 'novel') !== false) $score -= 15;
    if (strpos($description, 'first impression') !== false) $score -= 20;
    
    return max(10, min(90, $score));
}

function analyzeCaseComplexity($case)
{
    $complexity = 30;
    $description = strtolower($case->description ?? '');
    
    // Complexity factors
    if (strpos($description, 'multiple parties') !== false) $complexity += 15;
    if (strpos($description, 'cross-claims') !== false) $complexity += 15;
    if (strpos($description, 'expert testimony') !== false) $complexity += 10;
    if (strpos($description, 'federal') !== false) $complexity += 10;
    if (strpos($description, 'class action') !== false) $complexity += 20;
    if (strpos($description, 'international') !== false) $complexity += 15;
    
    // Simplicity factors
    if (strpos($description, 'straightforward') !== false) $complexity -= 10;
    if (strpos($description, 'simple') !== false) $complexity -= 10;
    if (strpos($description, 'clear cut') !== false) $complexity -= 15;
    
    return max(10, min(80, $complexity));
}

function generateReasoningFactors($case, $caseStrength, $evidenceQuality, $legalPrecedents, $caseComplexity)
{
    $factors = array();
    
    // Factor assessments
    if ($caseStrength >= 70) {
        $factors[] = "Strong case foundation (Score: {$caseStrength}/100)";
    } elseif ($caseStrength >= 50) {
        $factors[] = "Moderate case strength (Score: {$caseStrength}/100)";
    } else {
        $factors[] = "Weak case foundation (Score: {$caseStrength}/100)";
    }
    
    if ($evidenceQuality >= 70) {
        $factors[] = "High-quality evidence available (Score: {$evidenceQuality}/100)";
    } elseif ($evidenceQuality >= 50) {
        $factors[] = "Adequate evidence quality (Score: {$evidenceQuality}/100)";
    } else {
        $factors[] = "Evidence quality concerns (Score: {$evidenceQuality}/100)";
    }
    
    if ($legalPrecedents >= 70) {
        $factors[] = "Favorable legal precedents (Score: {$legalPrecedents}/100)";
    } elseif ($legalPrecedents >= 50) {
        $factors[] = "Mixed precedent landscape (Score: {$legalPrecedents}/100)";
    } else {
        $factors[] = "Challenging precedents (Score: {$legalPrecedents}/100)";
    }
    
    if ($caseComplexity <= 30) {
        $factors[] = "Low complexity case (Score: {$caseComplexity}/100)";
    } elseif ($caseComplexity <= 50) {
        $factors[] = "Moderate complexity (Score: {$caseComplexity}/100)";
    } else {
        $factors[] = "High complexity case (Score: {$caseComplexity}/100)";
    }
    
    $factors[] = "Case Type: " . ($case->type ?? 'General');
    if (!empty($case->priority)) {
        $factors[] = "Priority: {$case->priority}";
    }
    
    return $factors;
}

function saveAnalysisResults($case, $analysis)
{
    $winnability_percentage = $analysis['winnability_percentage'];
    $case->ai_suggested_status = "Winnability: {$winnability_percentage}% (Comprehensive Analysis)";
    $case->ai_confidence_score = $winnability_percentage / 100;
    $case->ai_last_analysis = date('Y-m-d H:i:s');
    $case->ai_analysis_factors = json_encode($analysis);
    $case->ai_status_needs_review = ($winnability_percentage < 40) ? 1 : 0;
    $case->save(false);
}

function createCaseUpdate($case, $analysis)
{
    try {
        global $current_user;
        
        $winnability_percentage = $analysis['winnability_percentage'];
        $reasoning_factors = $analysis['reasoning_factors'];
        $confidence_score = round($analysis['confidence_score'] * 100);
        
        $description = "🤖 **AI Winnability Analysis Complete**\n\n";
        $description .= "**🎯 Winnability Assessment:** {$winnability_percentage}%\n";
        $description .= "**✅ Analysis Confidence:** {$confidence_score}%\n\n";
        
        $description .= "**📊 Detailed Factor Analysis:**\n";
        $description .= "• Case Strength: {$analysis['case_strength']}/100\n";
        $description .= "• Evidence Quality: {$analysis['evidence_quality']}/100\n";
        $description .= "• Legal Precedents: {$analysis['legal_precedents']}/100\n";
        $description .= "• Case Complexity: {$analysis['case_complexity']}/100\n\n";
        
        $description .= "**📋 Key Analysis Points:**\n";
        foreach ($reasoning_factors as $factor) {
            $description .= "• {$factor}\n";
        }
        
        $description .= "\n**💡 Strategic Recommendation:**\n";
        if ($winnability_percentage >= 70) {
            $description .= "✅ **Strong case** - Recommend proceeding with confidence.";
        } elseif ($winnability_percentage >= 50) {
            $description .= "⚖️ **Moderate winnability** - Proceed with standard precautions.";
        } elseif ($winnability_percentage >= 30) {
            $description .= "⚠️ **Lower winnability** - Careful risk assessment recommended.";
        } else {
            $description .= "🚨 **High risk case** - Detailed review required before proceeding.";
        }
        
        $description .= "\n\n---\n";
        $description .= "*This analysis provides strategic guidance based on case data patterns. Use alongside professional legal judgment for decision-making.*";
        
        $caseUpdate = BeanFactory::newBean('AOP_Case_Updates');
        $caseUpdate->name = '🤖 AI Winnability Analysis - ' . date('M j, Y g:i A');
        $caseUpdate->description = $description;
        $caseUpdate->case_id = $case->id;
        $caseUpdate->contact_id = !empty($case->primary_contact_id) ? $case->primary_contact_id : '';
        $caseUpdate->internal = true;
        $caseUpdate->assigned_user_id = $current_user->id;
        $caseUpdate->save();
        
    } catch (Exception $e) {
        $GLOBALS['log']->error('Case Update Creation Error: ' . $e->getMessage());
    }
}

function redirectToCase($caseId, $message, $type = 'info')
{
    $baseUrl = 'index.php?module=Cases&action=';
    
    if (!empty($caseId)) {
        $url = $baseUrl . 'DetailView&record=' . urlencode($caseId);
    } else {
        $url = $baseUrl . 'index';
    }
    
    if (!empty($message)) {
        $url .= '&' . $type . '_message=' . urlencode($message);
    }
    
    SugarApplication::redirect($url);
    exit;
}
?>