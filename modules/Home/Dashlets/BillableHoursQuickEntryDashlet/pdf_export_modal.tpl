{*
 * PDF Export Modal Template for Billable Hours
 *}

<!-- PDF Export Modal -->
<div id="pdf_export_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <i class="fa fa-file-pdf-o"></i> Export Billable Hours to PDF
                </h4>
            </div>
            <div class="modal-body">
                <form id="pdf_export_form" class="form-horizontal">
                    <!-- Date Range Selection -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Date Range:</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <div class="col-sm-6">
                                    <input type="date" id="export_start_date" name="start_date" 
                                           class="form-control" value="{$default_start_date}" required>
                                </div>
                                <div class="col-sm-6">
                                    <input type="date" id="export_end_date" name="end_date" 
                                           class="form-control" value="{$default_end_date}" required>
                                </div>
                            </div>
                            <small class="help-block">Select the date range for billable hours to include</small>
                        </div>
                    </div>

                    <!-- Report Type Selection -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Report Type:</label>
                        <div class="col-sm-9">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="report_type" value="summary" checked>
                                    <strong>Summary Report</strong> - Overview of billable hours by case and activity
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="report_type" value="timesheet">
                                    <strong>Attorney Timesheet</strong> - Detailed time entries for the selected period
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="report_type" value="case">
                                    <strong>Case Billing Report</strong> - Billable hours grouped by case
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Case Filter (for case report) -->
                    <div class="form-group" id="case_filter_group" style="display: none;">
                        <label class="col-sm-3 control-label">Select Case:</label>
                        <div class="col-sm-9">
                            <select id="export_case_filter" name="case_filter" class="form-control">
                                <option value="">-- All Cases --</option>
                                {foreach from=$activeCases key=case_id item=case_name}
                                    <option value="{$case_id}">{$case_name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                    <!-- Attorney Filter -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Attorney:</label>
                        <div class="col-sm-9">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="attorney_filter" value="current" checked>
                                    Current User Only
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="attorney_filter" value="all">
                                    All Attorneys
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Format Options -->
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Options:</label>
                        <div class="col-sm-9">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="include_descriptions" checked>
                                    Include task descriptions
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="group_by_case" checked>
                                    Group entries by case
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="show_totals" checked>
                                    Show summary totals
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Cancel
                </button>
                <button type="button" id="generate_pdf_btn" class="btn btn-primary" onclick="BillableHours.generatePDF()">
                    <i class="fa fa-file-pdf-o"></i> Generate PDF
                </button>
            </div>
        </div>
    </div>
</div>

{literal}
<script type="text/javascript">
// Show/hide case filter based on report type
$(document).ready(function() {
    $('input[name="report_type"]').change(function() {
        if ($(this).val() === 'case') {
            $('#case_filter_group').show();
        } else {
            $('#case_filter_group').hide();
        }
    });
});
</script>
{/literal}