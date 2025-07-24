{*
 * JavaScript for Billable Hours Quick Entry Dashlet
 * Provides timer functionality and AJAX form submission
 *}

{literal}<script type="text/javascript">
// Billable Hours Dashlet JavaScript Class
if (typeof BillableHours === 'undefined') {
    var BillableHours = {};
}

// Timer tracking objects
BillableHours.timers = {};

BillableHours.startTimer = function(dashletId) {
    var timerObj = BillableHours.timers[dashletId];
    
    if (!timerObj) {
        timerObj = BillableHours.timers[dashletId] = {
            startTime: null,
            interval: null,
            running: false
        };
    }
    
    if (!timerObj.running) {
        timerObj.startTime = new Date().getTime();
        timerObj.running = true;
        
        // Update display immediately
        BillableHours.updateTimerDisplay(dashletId);
        
        // Start interval to update every second
        timerObj.interval = setInterval(function() {
            BillableHours.updateTimerDisplay(dashletId);
        }, 1000);
        
        // Update button states
        document.getElementById('start_timer_' + dashletId).style.display = 'none';
        document.getElementById('stop_timer_' + dashletId).style.display = 'inline-block';
        
        BillableHours.showStatus(dashletId, '{/literal}{$strings.LBL_TIMER_STARTED}{literal}', 'info');
    }
};

BillableHours.stopTimer = function(dashletId) {
    var timerObj = BillableHours.timers[dashletId];
    
    if (timerObj && timerObj.running) {
        timerObj.running = false;
        clearInterval(timerObj.interval);
        
        // Calculate elapsed time in hours
        var currentTime = new Date().getTime();
        var elapsedMs = currentTime - timerObj.startTime;
        var elapsedHours = (elapsedMs / (1000 * 60 * 60)).toFixed(2);
        
        // Update duration field
        var durationField = document.getElementById('duration_' + dashletId);
        if (durationField) {
            durationField.value = elapsedHours;
        }
        
        // Update button states
        document.getElementById('start_timer_' + dashletId).style.display = 'inline-block';
        document.getElementById('stop_timer_' + dashletId).style.display = 'none';
        
        // Reset timer display
        document.getElementById('timer_display_' + dashletId).innerHTML = '00:00:00';
        
        BillableHours.showStatus(dashletId, '{/literal}{$strings.LBL_TIMER_STOPPED}{literal}', 'success');
    }
};

BillableHours.updateTimerDisplay = function(dashletId) {
    var timerObj = BillableHours.timers[dashletId];
    var display = document.getElementById('timer_display_' + dashletId);
    
    if (timerObj && timerObj.running && display) {
        var currentTime = new Date().getTime();
        var elapsedMs = currentTime - timerObj.startTime;
        
        var hours = Math.floor(elapsedMs / (1000 * 60 * 60));
        var minutes = Math.floor((elapsedMs % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((elapsedMs % (1000 * 60)) / 1000);
        
        // Format time with leading zeros
        var formattedTime = 
            (hours < 10 ? '0' : '') + hours + ':' +
            (minutes < 10 ? '0' : '') + minutes + ':' +
            (seconds < 10 ? '0' : '') + seconds;
        
        display.innerHTML = formattedTime;
    }
};

BillableHours.logTime = function(dashletId) {
    var form = document.getElementById('billable_hours_form_' + dashletId);
    
    if (!form) {
        BillableHours.showStatus(dashletId, '{/literal}{$strings.LBL_ERROR_FORM_NOT_FOUND}{literal}', 'error');
        return;
    }
    
    // Validate required fields
    var caseId = document.getElementById('case_select_' + dashletId).value;
    var activityType = document.getElementById('activity_type_' + dashletId).value;
    var duration = document.getElementById('duration_' + dashletId).value;
    var description = document.getElementById('description_' + dashletId).value;
    var entryDate = document.getElementById('entry_date_' + dashletId).value;
    var entryTime = document.getElementById('entry_time_' + dashletId).value;
    var hourlyRate = document.getElementById('hourly_rate_' + dashletId).value;
    
    if (!activityType || !duration || !description || !entryDate) {
        BillableHours.showStatus(dashletId, '{/literal}{$strings.LBL_ERROR_REQUIRED_FIELDS}{literal}', 'error');
        return;
    }
    
    if (parseFloat(duration) <= 0) {
        BillableHours.showStatus(dashletId, '{/literal}{$strings.LBL_ERROR_INVALID_DURATION}{literal}', 'error');
        return;
    }
    
    // Show loading state
    var logButton = document.getElementById('log_time_btn_' + dashletId);
    var originalText = logButton.innerHTML;
    logButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> {/literal}{$strings.LBL_SAVING}{literal}';
    logButton.disabled = true;
    
    // Prepare AJAX request
    var params = {
        module: 'Home',
        action: 'CallMethodDashlet',
        method: 'saveTimeEntry',
        dashlet_id: dashletId,
        case_id: caseId,
        activity_type: activityType,
        duration: duration,
        description: description,
        entry_date: entryDate,
        entry_time: entryTime,
        hourly_rate: hourlyRate,
        to_pdf: true
    };
    
    // Make AJAX call
    YAHOO.util.Connect.asyncRequest('POST', 'index.php', {
        success: function(response) {
            try {
                var result = JSON.parse(response.responseText);
                
                if (result.success) {
                    BillableHours.showStatus(dashletId, result.message, 'success');
                    BillableHours.clearForm(dashletId);
                    BillableHours.updateDailySummary(dashletId, result.duration, result.amount);
                } else {
                    BillableHours.showStatus(dashletId, result.message, 'error');
                }
            } catch (e) {
                BillableHours.showStatus(dashletId, '{/literal}{$strings.LBL_ERROR_PARSING_RESPONSE}{literal}', 'error');
            }
            
            // Restore button
            logButton.innerHTML = originalText;
            logButton.disabled = false;
        },
        failure: function() {
            BillableHours.showStatus(dashletId, '{/literal}{$strings.LBL_ERROR_NETWORK}{literal}', 'error');
            
            // Restore button
            logButton.innerHTML = originalText;
            logButton.disabled = false;
        }
    }, BillableHours.buildPostData(params));
};

BillableHours.clearForm = function(dashletId) {
    // Clear form fields but keep defaults
    document.getElementById('case_select_' + dashletId).value = '';
    document.getElementById('duration_' + dashletId).value = '';
    document.getElementById('description_' + dashletId).value = '';
    
    // Reset timer if running
    var timerObj = BillableHours.timers[dashletId];
    if (timerObj && timerObj.running) {
        BillableHours.stopTimer(dashletId);
    }
    
    // Hide status message
    var statusDiv = document.getElementById('status_message_' + dashletId);
    if (statusDiv) {
        statusDiv.style.display = 'none';
    }
};

BillableHours.showStatus = function(dashletId, message, type) {
    var statusDiv = document.getElementById('status_message_' + dashletId);
    var statusText = document.getElementById('status_text_' + dashletId);
    
    if (statusDiv && statusText) {
        statusText.innerHTML = message;
        
        // Update alert class based on type
        var alertDiv = statusDiv.querySelector('.alert');
        if (alertDiv) {
            alertDiv.className = 'alert alert-' + (type === 'error' ? 'danger' : type);
        }
        
        statusDiv.style.display = 'block';
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(function() {
                statusDiv.style.display = 'none';
            }, 5000);
        }
    }
};

BillableHours.updateDailySummary = function(dashletId, addHours, addAmount) {
    var entriesSpan = document.getElementById('daily_entries_' + dashletId);
    var hoursSpan = document.getElementById('daily_hours_' + dashletId);
    var amountSpan = document.getElementById('daily_amount_' + dashletId);
    
    if (entriesSpan && hoursSpan && amountSpan) {
        // Update entries count
        var currentEntries = parseInt(entriesSpan.innerHTML) || 0;
        entriesSpan.innerHTML = currentEntries + 1;
        
        // Update hours
        var currentHours = parseFloat(hoursSpan.innerHTML.replace('h', '')) || 0;
        var newHours = currentHours + parseFloat(addHours);
        hoursSpan.innerHTML = newHours.toFixed(1) + 'h';
        
        // Update amount
        var currentAmount = parseFloat(amountSpan.innerHTML.replace('$', '')) || 0;
        var newAmount = currentAmount + parseFloat(addAmount);
        amountSpan.innerHTML = '$' + newAmount.toFixed(2);
    }
};

BillableHours.buildPostData = function(params) {
    var postData = '';
    for (var key in params) {
        if (postData.length > 0) {
            postData += '&';
        }
        postData += encodeURIComponent(key) + '=' + encodeURIComponent(params[key]);
    }
    return postData;
};

// Auto-calculate total amount when duration or rate changes
BillableHours.updateAmount = function(dashletId) {
    var durationField = document.getElementById('duration_' + dashletId);
    var rateField = document.getElementById('hourly_rate_' + dashletId);
    
    if (durationField && rateField) {
        var duration = parseFloat(durationField.value) || 0;
        var rate = parseFloat(rateField.value) || 0;
        var total = duration * rate;
        
        // You could add a total display field here if desired
        // var totalField = document.getElementById('total_amount_' + dashletId);
        // if (totalField) {
        //     totalField.innerHTML = '$' + total.toFixed(2);
        // }
    }
};

// AI-powered features
BillableHours.AI = {
    // Get smart description suggestions
    getDescriptionSuggestions: function(dashletId, activityType, caseId) {
        if (!activityType) return;
        
        console.log('AI: Getting descriptions for', activityType, 'case:', caseId);
        
        var params = {
            entryPoint: 'aiBillableHours',
            ai_action: 'get_descriptions',
            activity_type: activityType,
            case_id: caseId || '',
            to_pdf: true
        };
        
        YAHOO.util.Connect.asyncRequest('POST', 'index.php', {
            success: function(response) {
                console.log('AI: Raw response:', response.responseText);
                try {
                    var result = JSON.parse(response.responseText);
                    if (result.success) {
                        console.log('AI: Got', result.data.descriptions.length, 'suggestions');
                        BillableHours.AI.showDescriptionSuggestions(dashletId, result.data.descriptions);
                    } else {
                        console.log('AI: Error response:', result.error);
                        BillableHours.showStatus(dashletId, 'AI Error: ' + result.error, 'error');
                    }
                } catch (e) {
                    console.log('AI description suggestions error:', e);
                    BillableHours.showStatus(dashletId, 'AI parsing error', 'error');
                }
            },
            failure: function(response) {
                console.log('AI: AJAX failed:', response);
                BillableHours.showStatus(dashletId, 'AI connection failed', 'error');
            }
        }, BillableHours.buildPostData(params));
    },
    
    // Show description suggestions dropdown
    showDescriptionSuggestions: function(dashletId, descriptions) {
        var descField = document.getElementById('description_' + dashletId);
        if (!descField || descriptions.length === 0) return;
        
        // Remove existing suggestions
        var existingSuggestions = document.getElementById('ai_suggestions_' + dashletId);
        if (existingSuggestions) {
            existingSuggestions.remove();
        }
        
        // Create suggestions dropdown
        var suggestionsDiv = document.createElement('div');
        suggestionsDiv.id = 'ai_suggestions_' + dashletId;
        suggestionsDiv.className = 'ai-suggestions-dropdown';
        suggestionsDiv.style.cssText = 'position: absolute; background: white; border: 1px solid #ccc; border-radius: 4px; max-height: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 2px 4px rgba(0,0,0,0.1);';
        
        descriptions.forEach(function(desc, index) {
            var item = document.createElement('div');
            item.className = 'ai-suggestion-item';
            item.style.cssText = 'padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #eee; font-size: 12px;';
            item.innerHTML = '<i class="fa fa-lightbulb-o" style="color: #f39c12; margin-right: 5px;"></i>' + desc;
            
            item.onmouseover = function() { this.style.background = '#f5f5f5'; };
            item.onmouseout = function() { this.style.background = 'white'; };
            item.onclick = function() {
                descField.value = desc;
                suggestionsDiv.remove();
                descField.focus();
            };
            
            suggestionsDiv.appendChild(item);
        });
        
        // Position and show dropdown
        var rect = descField.getBoundingClientRect();
        suggestionsDiv.style.left = rect.left + 'px';
        suggestionsDiv.style.top = (rect.bottom + window.scrollY) + 'px';
        suggestionsDiv.style.width = rect.width + 'px';
        
        document.body.appendChild(suggestionsDiv);
        
        // Hide suggestions when clicking elsewhere
        setTimeout(function() {
            document.addEventListener('click', function hideHandler(e) {
                if (!suggestionsDiv.contains(e.target) && e.target !== descField) {
                    suggestionsDiv.remove();
                    document.removeEventListener('click', hideHandler);
                }
            });
        }, 100);
    },
    
    // Predict time duration
    predictDuration: function(dashletId, activityType, caseId) {
        if (!activityType) return;
        
        var params = {
            entryPoint: 'aiBillableHours',
            ai_action: 'predict_time',
            activity_type: activityType,
            case_id: caseId || '',
            to_pdf: true
        };
        
        YAHOO.util.Connect.asyncRequest('POST', 'index.php', {
            success: function(response) {
                try {
                    var result = JSON.parse(response.responseText);
                    if (result.success) {
                        BillableHours.AI.showTimePrediction(dashletId, result.data);
                    }
                } catch (e) {
                    console.log('AI time prediction error:', e);
                }
            },
            failure: function() {
                console.log('Failed to get AI time prediction');
            }
        }, BillableHours.buildPostData(params));
    },
    
    // Show time prediction
    showTimePrediction: function(dashletId, prediction) {
        var durationField = document.getElementById('duration_' + dashletId);
        if (!durationField) return;
        
        // Update duration field with prediction
        durationField.value = prediction.predicted_hours;
        
        // Show confidence and reasoning
        var aiIndicator = document.getElementById('ai_indicator_' + dashletId);
        if (!aiIndicator) {
            aiIndicator = document.createElement('div');
            aiIndicator.id = 'ai_indicator_' + dashletId;
            aiIndicator.style.cssText = 'font-size: 10px; color: #666; margin-top: 2px;';
            durationField.parentNode.appendChild(aiIndicator);
        }
        
        var confidencePercent = Math.round(prediction.confidence * 100);
        aiIndicator.innerHTML = '<i class="fa fa-magic" style="color: #9b59b6;"></i> AI: ' + 
                               prediction.predicted_hours + 'h (' + confidencePercent + '% confident)';
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            if (aiIndicator) {
                aiIndicator.style.opacity = '0.5';
            }
        }, 5000);
        
        // Update amount calculation
        BillableHours.updateAmount(dashletId);
    },
    
    // Suggest activity type based on description
    suggestActivityType: function(dashletId, description, caseId) {
        if (!description || description.length < 10) return;
        
        var params = {
            entryPoint: 'aiBillableHours',
            ai_action: 'suggest_activity',
            description: description,
            case_id: caseId || '',
            to_pdf: true
        };
        
        YAHOO.util.Connect.asyncRequest('POST', 'index.php', {
            success: function(response) {
                try {
                    var result = JSON.parse(response.responseText);
                    if (result.success && result.data.suggestions.length > 0) {
                        BillableHours.AI.showActivitySuggestions(dashletId, result.data.suggestions);
                    }
                } catch (e) {
                    console.log('AI activity suggestion error:', e);
                }
            },
            failure: function() {
                console.log('Failed to get AI activity suggestions');
            }
        }, BillableHours.buildPostData(params));
    },
    
    // Show activity type suggestions
    showActivitySuggestions: function(dashletId, suggestions) {
        var activityField = document.getElementById('activity_type_' + dashletId);
        if (!activityField) return;
        
        var bestSuggestion = suggestions[0];
        if (bestSuggestion.confidence > 0.6) {
            // Auto-select if confidence is high
            activityField.value = bestSuggestion.activity_type;
            
            // Show AI indicator
            var indicator = document.createElement('span');
            indicator.style.cssText = 'margin-left: 5px; font-size: 10px; color: #27ae60;';
            indicator.innerHTML = '<i class="fa fa-check-circle"></i> AI suggested';
            activityField.parentNode.appendChild(indicator);
            
            setTimeout(function() {
                if (indicator.parentNode) {
                    indicator.parentNode.removeChild(indicator);
                }
            }, 3000);
            
            // Trigger time prediction
            var caseId = document.getElementById('case_select_' + dashletId).value;
            BillableHours.AI.predictDuration(dashletId, bestSuggestion.activity_type, caseId);
        }
    }
};

// Enhanced event handlers for AI integration
BillableHours.enhanceWithAI = function(dashletId) {
    var activityField = document.getElementById('activity_type_' + dashletId);
    var descField = document.getElementById('description_' + dashletId);
    var caseField = document.getElementById('case_select_' + dashletId);
    var aiButton = document.getElementById('ai_enhance_btn_' + dashletId);
    
    // Immediate feedback when AI Assist is clicked
    if (aiButton) {
        // Change button to show AI is active
        aiButton.innerHTML = '<i class="fa fa-magic"></i> AI Active';
        aiButton.style.backgroundColor = '#27ae60';
        aiButton.disabled = true;
        
        // Show status message
        BillableHours.showStatus(dashletId, 'AI features activated! Select activity type or start typing description.', 'success');
        
        // If activity type is already selected, show suggestions immediately
        if (activityField && activityField.value) {
            var caseId = caseField ? caseField.value : null;
            BillableHours.AI.getDescriptionSuggestions(dashletId, activityField.value, caseId);
            BillableHours.AI.predictDuration(dashletId, activityField.value, caseId);
        }
    }
    
    if (activityField) {
        // Trigger AI suggestions when activity type changes
        activityField.addEventListener('change', function() {
            var caseId = caseField ? caseField.value : null;
            BillableHours.AI.getDescriptionSuggestions(dashletId, this.value, caseId);
            BillableHours.AI.predictDuration(dashletId, this.value, caseId);
        });
    }
    
    if (descField) {
        // Trigger activity suggestions when description changes
        var descriptionTimer;
        descField.addEventListener('input', function() {
            clearTimeout(descriptionTimer);
            descriptionTimer = setTimeout(function() {
                var caseId = caseField ? caseField.value : null;
                BillableHours.AI.suggestActivityType(dashletId, descField.value, caseId);
            }, 1000); // Wait 1 second after user stops typing
        });
        
        // Show suggestions on focus
        descField.addEventListener('focus', function() {
            var activityType = activityField ? activityField.value : null;
            var caseId = caseField ? caseField.value : null;
            if (activityType) {
                BillableHours.AI.getDescriptionSuggestions(dashletId, activityType, caseId);
            }
        });
    }
    
    // Auto-hide status message after 5 seconds
    setTimeout(function() {
        var statusDiv = document.getElementById('status_message_' + dashletId);
        if (statusDiv) {
            statusDiv.style.display = 'none';
        }
    }, 5000);
};

// Initialize event listeners when document is ready
if (document.addEventListener) {
    document.addEventListener('DOMContentLoaded', function() {
        BillableHours.initialized = true;
    });
} else {
    // IE8 compatibility
    document.attachEvent('onreadystatechange', function() {
        if (document.readyState === 'loaded' || document.readyState === 'complete') {
            BillableHours.initialized = true;
        }
    });
}
</script>{/literal}