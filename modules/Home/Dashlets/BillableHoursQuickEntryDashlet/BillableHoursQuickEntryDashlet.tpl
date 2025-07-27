{*
 * Billable Hours Quick Entry Dashlet Template
 * Provides a quick form for attorneys to log billable time
 *}

<div id="billable_hours_dashlet_{$id}" class="billable-hours-dashlet">
    <!-- Quick Entry Form -->
    <form id="billable_hours_form_{$id}" class="billable-hours-form" style="margin: 0; padding: 10px;">
        <div class="row" style="margin-bottom: 8px;">
            <div class="col-xs-12 col-sm-6">
                <label for="case_select_{$id}" style="font-weight: bold; margin-bottom: 3px; display: block;">
                    {$strings.LBL_CASE}:
                </label>
                <select id="case_select_{$id}" name="case_id" class="form-control" style="width: 100%;">
                    <option value="">{$strings.LBL_SELECT_CASE}</option>
                    {foreach from=$activeCases key=case_id item=case_name}
                        <option value="{$case_id}">{$case_name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col-xs-12 col-sm-6">
                <label for="activity_type_{$id}" style="font-weight: bold; margin-bottom: 3px; display: block;">
                    {$strings.LBL_ACTIVITY_TYPE}:
                </label>
                <select id="activity_type_{$id}" name="activity_type" class="form-control" style="width: 100%;" required>
                    {foreach from=$activityTypes key=type_key item=type_label}
                        <option value="{$type_key}" {if $type_key == $defaultActivityType}selected{/if}>
                            {$type_label}
                        </option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div class="row" style="margin-bottom: 8px;">
            <div class="col-xs-12 col-sm-3">
                <label for="duration_{$id}" style="font-weight: bold; margin-bottom: 3px; display: block;">
                    {$strings.LBL_DURATION} ({$strings.LBL_HOURS}):
                </label>
                <input type="number" id="duration_{$id}" name="duration" class="form-control"
                       step="0.25" min="0.25" max="24" placeholder="1.5" style="width: 100%;" required
                       onchange="BillableHours.updateAmount('{$id}')">
            </div>
            <div class="col-xs-12 col-sm-3">
                <label for="hourly_rate_{$id}" style="font-weight: bold; margin-bottom: 3px; display: block;">
                    {$strings.LBL_RATE} ($):
                </label>
                <input type="number" id="hourly_rate_{$id}" name="hourly_rate" class="form-control"
                       step="0.01" min="0" value="{$defaultRate}" style="width: 100%;"
                       onchange="BillableHours.updateAmount('{$id}')">
            </div>
            <div class="col-xs-12 col-sm-3">
                <label for="entry_date_{$id}" style="font-weight: bold; margin-bottom: 3px; display: block;">
                    {$strings.LBL_DATE}:
                </label>
                <input type="date" id="entry_date_{$id}" name="entry_date" class="form-control"
                       value="{$currentDate}" style="width: 100%;" required>
            </div>
            <div class="col-xs-12 col-sm-3">
                <label for="entry_time_{$id}" style="font-weight: bold; margin-bottom: 3px; display: block;">
                    {$strings.LBL_TIME}:
                </label>
                <input type="time" id="entry_time_{$id}" name="entry_time" class="form-control"
                       value="{$currentTime}" style="width: 100%;">
            </div>
        </div>

        <div class="row" style="margin-bottom: 8px;">
            <div class="col-xs-12">
                <label for="description_{$id}" style="font-weight: bold; margin-bottom: 3px; display: block;">
                    {$strings.LBL_DESCRIPTION}:
                    <button type="button" id="ai_narrative_btn_{$id}" class="btn btn-info btn-xs" 
                            onclick="BillableHours.AI.generateBillingNarrative('{$id}', document.getElementById('description_{$id}').value, document.getElementById('activity_type_{$id}').value, document.getElementById('case_select_{$id}').value)"
                            style="float: right; margin-top: -2px;">
                        <i class="fa fa-magic"></i> Enhance
                    </button>
                </label>
                <textarea id="description_{$id}" name="description" class="form-control"
                          rows="2" placeholder="{$strings.LBL_DESCRIPTION_PLACEHOLDER}"
                          style="width: 100%; resize: vertical;" required></textarea>
                <small class="help-text" style="color: #666; font-size: 10px; margin-top: 2px; display: block;">
                    Type a casual description, then click "Enhance" to convert to professional legal billing language
                </small>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row">
            <div class="col-xs-12">
                <div class="button-group" style="text-align: right;">
                    <!-- Timer Controls -->
                    <div class="timer-controls" style="float: left; margin-top: 5px;">
                        <button type="button" id="start_timer_{$id}" class="btn btn-success btn-sm"
                                onclick="BillableHours.startTimer('{$id}')" style="margin-right: 5px;">
                            <i class="fa fa-play"></i> {$strings.LBL_START_TIMER}
                        </button>
                        <button type="button" id="stop_timer_{$id}" class="btn btn-warning btn-sm"
                                onclick="BillableHours.stopTimer('{$id}')" style="display: none; margin-right: 5px;">
                            <i class="fa fa-stop"></i> {$strings.LBL_STOP_TIMER}
                        </button>
                        <span id="timer_display_{$id}" class="timer-display"
                              style="font-weight: bold; margin-left: 10px; color: #333;">00:00:00</span>
                    </div>

                    <!-- AI Enhancement Button -->
                    <button type="button" id="ai_enhance_btn_{$id}" class="btn btn-info btn-sm"
                            onclick="BillableHours.enhanceWithAI('{$id}')" style="margin-right: 5px;">
                        <i class="fa fa-magic"></i> AI Assist
                    </button>
                    
                    <!-- PDF Export Button -->
                    <button type="button" id="pdf_export_btn_{$id}" class="btn btn-success btn-sm"
                            onclick="BillableHours.exportToPDF('{$id}')" style="margin-right: 5px;">
                        <i class="fa fa-file-pdf-o"></i> {$strings.LBL_EXPORT_PDF}
                    </button>
                    
                    <!-- Main Action Buttons -->
                    <button type="button" id="log_time_btn_{$id}" class="btn btn-primary"
                            onclick="BillableHours.logTime('{$id}')" style="margin-left: 10px;">
                        <i class="fa fa-clock-o"></i> {$strings.LBL_LOG_TIME}
                    </button>
                    <button type="button" id="clear_form_btn_{$id}" class="btn btn-default"
                            onclick="BillableHours.clearForm('{$id}')" style="margin-left: 5px;">
                        <i class="fa fa-eraser"></i> {$strings.LBL_CLEAR}
                    </button>
                </div>
            </div>
        </div>

        <!-- Status Messages -->
        <div id="status_message_{$id}" class="status-message" style="margin-top: 10px; display: none;">
            <div class="alert alert-info" role="alert">
                <span id="status_text_{$id}"></span>
            </div>
        </div>

        <!-- Today's Summary -->
        <div class="daily-summary" style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #ddd;">
            <div style="font-weight: bold; margin-bottom: 5px;">{$strings.LBL_TODAY_SUMMARY}:</div>
            <div class="row">
                <div class="col-xs-4">
                    <small>{$strings.LBL_ENTRIES}: <span id="daily_entries_{$id}" style="font-weight: bold;">0</span></small>
                </div>
                <div class="col-xs-4">
                    <small>{$strings.LBL_TOTAL_TIME}: <span id="daily_hours_{$id}" style="font-weight: bold;">0.0h</span></small>
                </div>
                <div class="col-xs-4">
                    <small>{$strings.LBL_TOTAL_AMOUNT}: <span id="daily_amount_{$id}" style="font-weight: bold;">$0.00</span></small>
                </div>
            </div>
        </div>
    </form>
</div>

{literal}
<style>
.billable-hours-dashlet {
    font-size: 12px;
}

.billable-hours-dashlet .form-control {
    font-size: 12px;
    padding: 3px 6px;
    height: auto;
}

.billable-hours-dashlet .btn {
    font-size: 11px;
    padding: 4px 8px;
}

.billable-hours-dashlet .btn-sm {
    font-size: 10px;
    padding: 3px 6px;
}

.billable-hours-dashlet label {
    font-size: 11px;
}

.timer-display {
    font-family: 'Courier New', monospace;
    background: #f5f5f5;
    padding: 3px 8px;
    border-radius: 3px;
    border: 1px solid #ddd;
}

.daily-summary {
    background: #f9f9f9;
    padding: 8px;
    border-radius: 3px;
}

.daily-summary small {
    color: #666;
}

.status-message .alert {
    margin: 0;
    padding: 8px 12px;
    font-size: 12px;
}

.help-text {
    font-style: italic;
}

.btn-xs {
    font-size: 9px;
    padding: 2px 5px;
    line-height: 1.2;
}

.row {
    margin-left: -5px;
    margin-right: -5px;
}

.row > div {
    padding-left: 5px;
    padding-right: 5px;
}

@media (max-width: 768px) {
    .billable-hours-dashlet .col-xs-12 {
        margin-bottom: 8px;
    }

    .timer-controls {
        float: none !important;
        text-align: center;
        margin-bottom: 10px;
    }

    .button-group {
        text-align: center !important;
    }
}
</style>
{/literal}