<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch Subpanel Definition
 * 
 * Defines the layout and fields for ConflictSearch subpanels
 * that appear in Contacts, Accounts, and Cases modules.
 */

$subpanel_layout = array(
    'top_buttons' => array(
        array(
            'widget_class' => 'SubPanelTopCreateButton',
            'title' => 'LBL_NEW_CONFLICT_SEARCH',
            'width' => '10%'
        ),
        array(
            'widget_class' => 'SubPanelTopButton',
            'title' => 'LBL_QUICK_CONFLICT_CHECK',
            'width' => '10%'
        )
    ),
    
    'list_fields' => array(
        'name' => array(
            'vname' => 'LBL_NAME',
            'widget_class' => 'SubPanelDetailViewLink',
            'width' => '20%',
            'default' => true
        ),
        'search_term' => array(
            'vname' => 'LBL_SEARCH_TERM',
            'width' => '25%',
            'default' => true
        ),
        'search_type' => array(
            'vname' => 'LBL_SEARCH_TYPE',
            'width' => '12%',
            'default' => true
        ),
        'total_matches_found' => array(
            'vname' => 'LBL_TOTAL_MATCHES',
            'width' => '10%',
            'default' => true,
            'align' => 'center'
        ),
        'confidence_summary' => array(
            'vname' => 'LBL_CONFIDENCE_SUMMARY',
            'width' => '15%',
            'default' => true,
            'function' => array(
                'name' => 'formatConfidenceSummary',
                'returns' => 'html',
                'include' => 'custom/modules/ConflictSearch/utils/subpanel_utils.php'
            )
        ),
        'search_status' => array(
            'vname' => 'LBL_STATUS',
            'width' => '10%',
            'default' => true
        ),
        'date_entered' => array(
            'vname' => 'LBL_DATE_ENTERED',
            'width' => '8%',
            'default' => true
        )
    )
);

/**
 * Format confidence summary for display in subpanel
 * 
 * @param array $fields Field data from the record
 * @return string HTML formatted confidence summary
 */
function formatConfidenceSummary($fields)
{
    $high = intval($fields['high_confidence_matches']['value'] ?? 0);
    $medium = intval($fields['medium_confidence_matches']['value'] ?? 0);
    $low = intval($fields['low_confidence_matches']['value'] ?? 0);
    
    $html = '<div style="font-size: 11px;">';
    
    if ($high > 0) {
        $html .= '<span style="color: #dc3545; font-weight: bold;">🔴 ' . $high . '</span> ';
    }
    if ($medium > 0) {
        $html .= '<span style="color: #ffc107; font-weight: bold;">🟡 ' . $medium . '</span> ';
    }
    if ($low > 0) {
        $html .= '<span style="color: #28a745;">🟢 ' . $low . '</span>';
    }
    
    if ($high == 0 && $medium == 0 && $low == 0) {
        $html .= '<span style="color: #6c757d;">No matches</span>';
    }
    
    $html .= '</div>';
    
    return $html;
}
?>