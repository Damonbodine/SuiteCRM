<?php

if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}

/**
 * ConflictSearch EditView Metadata
 * 
 * Defines the layout and fields for the ConflictSearch edit view
 */

$viewdefs['ConflictSearch']['EditView'] = array(
    'templateMeta' => array(
        'maxColumns' => '2',
        'widths' => array(
            array('label' => '10', 'field' => '30'),
            array('label' => '10', 'field' => '30')
        ),
        'javascript' => '{$PROBABILITY_SCRIPT}',
        'form' => array(
            'buttons' => array(
                array(
                    'customCode' => '<input type="submit" name="button" class="button primary" title="{$APP.LBL_SAVE_BUTTON_TITLE}" onclick="this.form.action.value=\'Save\'; return check_form(\'EditView\');" value="{$APP.LBL_SAVE_BUTTON_LABEL}">',
                ),
                array(
                    'customCode' => '<input type="button" name="search_button" class="button" title="{$MOD.LBL_SEARCH_BUTTON}" onclick="this.form.action.value=\'search\'; this.form.submit();" value="{$MOD.LBL_SEARCH_BUTTON}">',
                ),
                'CANCEL'
            ),
            'headerTpl' => 'modules/ConflictSearch/tpls/EditViewHeader.tpl',
            'footerTpl' => 'modules/ConflictSearch/tpls/EditViewFooter.tpl',
        ),
        'includes' => array(
            array(
                'file' => 'modules/ConflictSearch/js/ConflictSearch.js'
            )
        )
    ),
    'panels' => array(
        'default' => array(
            array(
                array(
                    'name' => 'name',
                    'label' => 'LBL_NAME',
                    'displayParams' => array(
                        'required' => true
                    )
                ),
                array(
                    'name' => 'assigned_user_name',
                    'label' => 'LBL_ASSIGNED_TO_NAME'
                )
            ),
            array(
                array(
                    'name' => 'search_term',
                    'label' => 'LBL_SEARCH_TERM',
                    'displayParams' => array(
                        'required' => true,
                        'help' => 'LBL_SEARCH_TERM_HELP'
                    )
                ),
                array(
                    'name' => 'search_type',
                    'label' => 'LBL_SEARCH_TYPE',
                    'displayParams' => array(
                        'help' => 'LBL_SEARCH_TYPE_HELP'
                    )
                )
            ),
            array(
                array(
                    'name' => 'modules_searched',
                    'label' => 'LBL_MODULES_SEARCHED',
                    'displayParams' => array(
                        'help' => 'LBL_MODULES_SEARCHED_HELP'
                    )
                ),
                array(
                    'name' => 'confidence_threshold',
                    'label' => 'LBL_CONFIDENCE_THRESHOLD',
                    'displayParams' => array(
                        'help' => 'LBL_CONFIDENCE_THRESHOLD_HELP'
                    )
                )
            ),
            array(
                array(
                    'name' => 'search_criteria',
                    'label' => 'LBL_SEARCH_CRITERIA',
                    'displayParams' => array(
                        'rows' => 4,
                        'cols' => 80
                    )
                ),
                ''
            ),
            array(
                array(
                    'name' => 'description',
                    'label' => 'LBL_DESCRIPTION',
                    'displayParams' => array(
                        'rows' => 6,
                        'cols' => 80
                    )
                ),
                ''
            )
        )
    )
);