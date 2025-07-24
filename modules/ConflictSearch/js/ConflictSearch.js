/**
 * ConflictSearch JavaScript functionality
 * 
 * Handles client-side interactions for the Attorney Conflict of Interest Search
 */

/**
 * Perform conflict search via AJAX
 */
function performConflictSearch() {
    var searchTerm = document.getElementById('search_term').value;
    var searchType = document.getElementById('search_type').value;
    var confidenceThreshold = document.getElementById('confidence_threshold').value;
    var modulesSearched = getSelectedModules();
    
    // Validation
    if (!searchTerm || searchTerm.trim() === '') {
        alert('Please enter a search term');
        return false;
    }
    
    if (searchTerm.length < 2) {
        alert('Search term must be at least 2 characters long');
        return false;
    }
    
    // Show loading indicator
    showSearchLoading();
    
    // Prepare AJAX request
    var postData = {
        module: 'ConflictSearch',
        action: 'performSearch',
        search_term: searchTerm,
        search_type: searchType || 'comprehensive',
        confidence_threshold: confidenceThreshold || '50',
        modules_searched: modulesSearched.join(','),
        to_pdf: false,
        sugar_body_only: true
    };
    
    // Perform AJAX request
    YAHOO.util.Connect.asyncRequest('POST', 'index.php', {
        success: function(response) {
            handleSearchResponse(response.responseText);
        },
        failure: function(response) {
            handleSearchError(response);
        }
    }, buildPostData(postData));
    
    return false;
}

/**
 * Perform quick search for immediate results
 */
function performQuickSearch() {
    var searchTerm = document.getElementById('search_term').value;
    
    if (!searchTerm || searchTerm.length < 3) {
        clearQuickResults();
        return;
    }
    
    var postData = {
        module: 'ConflictSearch',
        action: 'quickSearch',
        search_term: searchTerm,
        sugar_body_only: true
    };
    
    YAHOO.util.Connect.asyncRequest('POST', 'index.php', {
        success: function(response) {
            displayQuickResults(response.responseText);
        },
        failure: function(response) {
            // Silently fail for quick search
            clearQuickResults();
        }
    }, buildPostData(postData));
}

/**
 * Handle successful search response
 */
function handleSearchResponse(responseText) {
    hideSearchLoading();
    
    try {
        var response = JSON.parse(responseText);
        
        if (response.success) {
            displaySearchResults(response.data);
            updateSearchStatus('completed');
        } else {
            displaySearchError(response.error || 'Search failed');
            updateSearchStatus('failed');
        }
    } catch (e) {
        displaySearchError('Invalid response from server');
        updateSearchStatus('failed');
    }
}

/**
 * Handle search error
 */
function handleSearchError(response) {
    hideSearchLoading();
    var errorMsg = 'Search request failed. Please try again.';
    
    if (response.status === 403) {
        errorMsg = 'Access denied. You do not have permission to perform conflict searches.';
    } else if (response.status === 500) {
        errorMsg = 'Server error. Please contact your administrator.';
    }
    
    displaySearchError(errorMsg);
    updateSearchStatus('failed');
}

/**
 * Display search results
 */
function displaySearchResults(results) {
    var resultsContainer = document.getElementById('search_results_container');
    if (!resultsContainer) {
        createResultsContainer();
        resultsContainer = document.getElementById('search_results_container');
    }
    
    var html = buildResultsHTML(results);
    resultsContainer.innerHTML = html;
    
    // Update summary fields
    updateResultsSummary(results);
    
    // Show results container
    resultsContainer.style.display = 'block';
}

/**
 * Build HTML for search results
 */
function buildResultsHTML(results) {
    if (!results || results.length === 0) {
        return '<div class="conflict-no-results">No potential conflicts found.</div>';
    }
    
    var html = '<div class="conflict-results-header">';
    html += '<h3>Potential Conflicts Found (' + results.length + ')</h3>';
    html += '</div>';
    
    html += '<div class="conflict-results-list">';
    
    for (var i = 0; i < results.length; i++) {
        var result = results[i];
        html += buildResultItemHTML(result, i);
    }
    
    html += '</div>';
    
    // Add export options
    html += '<div class="conflict-results-actions">';
    html += '<button type="button" onclick="exportResults(\'pdf\')" class="button">Export to PDF</button>';
    html += '<button type="button" onclick="exportResults(\'csv\')" class="button">Export to CSV</button>';
    html += '</div>';
    
    return html;
}

/**
 * Build HTML for individual result item
 */
function buildResultItemHTML(result, index) {
    var confidenceClass = getConfidenceClass(result.confidence);
    var riskClass = getRiskClass(result.confidence);
    
    var html = '<div class="conflict-result-item ' + confidenceClass + '" id="result_' + index + '">';
    
    // Header with confidence and risk indicators
    html += '<div class="conflict-result-header">';
    html += '<div class="conflict-result-name">';
    html += '<strong>' + escapeHtml(result.name || 'Unknown') + '</strong>';
    html += '<span class="conflict-module-badge">' + (result.source_module || 'Unknown') + '</span>';
    html += '</div>';
    html += '<div class="conflict-result-metrics">';
    html += '<span class="confidence-score ' + confidenceClass + '">' + (result.confidence || 0) + '% Confidence</span>';
    html += '<span class="risk-level ' + riskClass + '">' + (result.risk_level || 'Unknown Risk') + '</span>';
    html += '</div>';
    html += '</div>';
    
    // Details
    html += '<div class="conflict-result-details">';
    if (result.details) {
        html += '<p>' + escapeHtml(result.details) + '</p>';
    }
    
    // Match information
    html += '<div class="conflict-match-info">';
    html += '<small>';
    html += 'Match Type: ' + (result.match_type || 'Unknown') + ' | ';
    html += 'Search Type: ' + (result.search_type || 'Unknown');
    if (result.relationship_path) {
        html += ' | Relationship: ' + escapeHtml(result.relationship_path);
    }
    html += '</small>';
    html += '</div>';
    
    html += '</div>';
    
    // Actions
    html += '<div class="conflict-result-actions">';
    html += '<button type="button" onclick="viewRecord(\'' + (result.source_module || '') + '\', \'' + (result.id || '') + '\')" class="button">View Record</button>';
    html += '<button type="button" onclick="addToExclusions(\'' + (result.id || '') + '\')" class="button">Add to Exclusions</button>';
    html += '</div>';
    
    html += '</div>';
    
    return html;
}

/**
 * Get CSS class for confidence level
 */
function getConfidenceClass(confidence) {
    if (confidence >= 90) return 'confidence-high';
    if (confidence >= 70) return 'confidence-medium';
    if (confidence >= 50) return 'confidence-low';
    return 'confidence-minimal';
}

/**
 * Get CSS class for risk level
 */
function getRiskClass(confidence) {
    if (confidence >= 90) return 'risk-high';
    if (confidence >= 70) return 'risk-medium';
    if (confidence >= 50) return 'risk-low';
    return 'risk-minimal';
}

/**
 * Update results summary fields
 */
function updateResultsSummary(results) {
    var total = results.length;
    var high = results.filter(function(r) { return r.confidence >= 90; }).length;
    var medium = results.filter(function(r) { return r.confidence >= 70 && r.confidence < 90; }).length;
    var low = results.filter(function(r) { return r.confidence >= 50 && r.confidence < 70; }).length;
    
    // Update form fields if they exist
    updateFieldValue('total_matches_found', total);
    updateFieldValue('high_confidence_matches', high);
    updateFieldValue('medium_confidence_matches', medium);
    updateFieldValue('low_confidence_matches', low);
}

/**
 * Show search loading indicator
 */
function showSearchLoading() {
    var loadingDiv = document.getElementById('search_loading');
    if (!loadingDiv) {
        loadingDiv = document.createElement('div');
        loadingDiv.id = 'search_loading';
        loadingDiv.className = 'conflict-search-loading';
        loadingDiv.innerHTML = '<div class="loading-spinner"></div><p>Searching for potential conflicts...</p>';
        
        var searchButton = document.getElementById('search_button') || document.querySelector('input[name="search_button"]');
        if (searchButton && searchButton.parentNode) {
            searchButton.parentNode.insertBefore(loadingDiv, searchButton.nextSibling);
        }
    }
    
    loadingDiv.style.display = 'block';
    
    // Disable search button
    var searchButton = document.getElementById('search_button') || document.querySelector('input[name="search_button"]');
    if (searchButton) {
        searchButton.disabled = true;
    }
}

/**
 * Hide search loading indicator
 */
function hideSearchLoading() {
    var loadingDiv = document.getElementById('search_loading');
    if (loadingDiv) {
        loadingDiv.style.display = 'none';
    }
    
    // Re-enable search button
    var searchButton = document.getElementById('search_button') || document.querySelector('input[name="search_button"]');
    if (searchButton) {
        searchButton.disabled = false;
    }
}

/**
 * Display search error
 */
function displaySearchError(message) {
    var errorDiv = document.getElementById('search_error');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'search_error';
        errorDiv.className = 'conflict-search-error';
        
        var resultsContainer = document.getElementById('search_results_container');
        if (resultsContainer) {
            resultsContainer.parentNode.insertBefore(errorDiv, resultsContainer);
        }
    }
    
    errorDiv.innerHTML = '<p><strong>Error:</strong> ' + escapeHtml(message) + '</p>';
    errorDiv.style.display = 'block';
    
    // Hide after 10 seconds
    setTimeout(function() {
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }, 10000);
}

/**
 * Create results container if it doesn't exist
 */
function createResultsContainer() {
    var container = document.createElement('div');
    container.id = 'search_results_container';
    container.className = 'conflict-search-results';
    container.style.display = 'none';
    
    // Insert after the form
    var form = document.getElementById('EditView');
    if (form && form.parentNode) {
        form.parentNode.insertBefore(container, form.nextSibling);
    }
}

/**
 * Get selected modules from form
 */
function getSelectedModules() {
    var modulesField = document.getElementById('modules_searched');
    if (modulesField) {
        if (modulesField.multiple) {
            var selected = [];
            for (var i = 0; i < modulesField.options.length; i++) {
                if (modulesField.options[i].selected) {
                    selected.push(modulesField.options[i].value);
                }
            }
            return selected;
        } else {
            return modulesField.value.split(',');
        }
    }
    
    // Default modules
    return ['Contacts', 'Accounts', 'Cases'];
}

/**
 * Update search status field
 */
function updateSearchStatus(status) {
    updateFieldValue('search_status', status);
}

/**
 * Update field value helper
 */
function updateFieldValue(fieldName, value) {
    var field = document.getElementById(fieldName);
    if (field) {
        field.value = value;
    }
}

/**
 * Build POST data string
 */
function buildPostData(data) {
    var pairs = [];
    for (var key in data) {
        if (data.hasOwnProperty(key)) {
            pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
        }
    }
    return pairs.join('&');
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    if (typeof text !== 'string') {
        return text;
    }
    
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * View a record in its module
 */
function viewRecord(module, id) {
    if (module && id) {
        window.open('index.php?module=' + module + '&action=DetailView&record=' + id, '_blank');
    }
}

/**
 * Export search results
 */
function exportResults(format) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = 'index.php';
    form.target = '_blank';
    
    // Add hidden fields
    var fields = {
        module: 'ConflictSearch',
        action: 'exportResults',
        format: format,
        search_id: document.getElementById('record') ? document.getElementById('record').value : ''
    };
    
    for (var key in fields) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

/**
 * Add event listeners when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Add quick search functionality
    var searchTermField = document.getElementById('search_term');
    if (searchTermField) {
        var quickSearchTimeout;
        searchTermField.addEventListener('input', function() {
            clearTimeout(quickSearchTimeout);
            quickSearchTimeout = setTimeout(performQuickSearch, 500);
        });
    }
    
    // Add form validation
    var form = document.getElementById('EditView');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });
    }
});

/**
 * Validate form before submission
 */
function validateForm() {
    var searchTerm = document.getElementById('search_term').value;
    
    if (!searchTerm || searchTerm.trim() === '') {
        alert('Please enter a search term');
        document.getElementById('search_term').focus();
        return false;
    }
    
    if (searchTerm.length < 2) {
        alert('Search term must be at least 2 characters long');
        document.getElementById('search_term').focus();
        return false;
    }
    
    return true;
}