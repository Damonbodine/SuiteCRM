<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch DetailView Metadata
 * 
 * Defines the layout and fields for the ConflictSearch detail view
 */

$viewdefs['ConflictSearch']['DetailView'] = array(
    'templateMeta' => array(
        'form' => array(
            'buttons' => array(
                'EDIT',
                array(
                    'customCode' => '<input type="button" name="rerun_button" class="button" title="{$MOD.LBL_RERUN_SEARCH}" onclick="document.location.href=\'index.php?module=ConflictSearch&action=rerun&record={$fields.id.value}\';" value="{$MOD.LBL_RERUN_SEARCH}">',
                ),
                array(
                    'customCode' => '<input type="button" name="export_button" class="button" title="{$MOD.LBL_EXPORT_RESULTS}" onclick="showExportOptions();" value="{$MOD.LBL_EXPORT_RESULTS}">',
                ),
                'DUPLICATE',
                'DELETE',
                'FIND_DUPLICATES'
            )
        ),
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),
        'includes' => array(
            array(
                'file' => 'modules/ConflictSearch/js/ConflictSearchDetail.js'
            )
        )
    ),
    'panels' => array(
        'default' => array(
            array(
                array(
                    'name' => 'name',
                    'label' => 'LBL_NAME'
                ),
                array(
                    'name' => 'search_status',
                    'label' => 'LBL_SEARCH_STATUS'
                )
            ),
            array(
                array(
                    'name' => 'search_term',
                    'label' => 'LBL_SEARCH_TERM'
                ),
                array(
                    'name' => 'search_type',
                    'label' => 'LBL_SEARCH_TYPE'
                )
            ),
            array(
                array(
                    'name' => 'modules_searched',
                    'label' => 'LBL_MODULES_SEARCHED'
                ),
                array(
                    'name' => 'confidence_threshold',
                    'label' => 'LBL_CONFIDENCE_THRESHOLD'
                )
            ),
            array(
                array(
                    'name' => 'total_matches_found',
                    'label' => 'LBL_TOTAL_MATCHES_FOUND'
                ),
                array(
                    'name' => 'execution_time',
                    'label' => 'LBL_EXECUTION_TIME'
                )
            ),
            array(
                array(
                    'name' => 'high_confidence_matches',
                    'label' => 'LBL_HIGH_CONFIDENCE_MATCHES'
                ),
                array(
                    'name' => 'medium_confidence_matches',
                    'label' => 'LBL_MEDIUM_CONFIDENCE_MATCHES'
                )
            ),
            array(
                array(
                    'name' => 'low_confidence_matches',
                    'label' => 'LBL_LOW_CONFIDENCE_MATCHES'
                ),
                array(
                    'name' => 'performed_by_name',
                    'label' => 'LBL_PERFORMED_BY'
                )
            ),
            array(
                array(
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME'
                ),
                array(
                    'name' => 'date_entered',
                    'label' => 'LBL_DATE_ENTERED'
                )
            ),
            array(
                array(
                    'name' => 'search_criteria',
                    'label' => 'LBL_SEARCH_CRITERIA'
                ),
                ''
            ),
            array(
                array(
                    'name' => 'description',
                    'label' => 'LBL_DESCRIPTION'
                ),
                ''
            )
        ),
        'LBL_RESULTS_SUMMARY' => array(
            array(
                array(
                    'name' => 'search_results_display',
                    'label' => 'LBL_SEARCH_RESULTS',
                    'type' => 'function',
                    'function' => array(
                        'name' => 'displaySearchResults',
                        'returns' => 'html',
                        'include' => 'modules/ConflictSearch/ConflictSearchResults.php'
                    )
                ),
                ''
            )
        )
    )
);