<?php

$surveyName = !empty($_REQUEST['name']) ? $_REQUEST['name'] : 'Survey';

$surveyThanks = translate('LBL_SURVEY_THANKS', 'Surveys');

// Function to get safe CSS theme path with fallback
function getSurveyThemeCSS() {
    global $sugar_config;
    $currentTheme = $sugar_config['default_theme'] ?? 'SuiteP';
    
    // Check if bootstrap.min.css exists in current theme
    if (file_exists("themes/$currentTheme/css/bootstrap.min.css")) {
        return $currentTheme;
    }
    
    // Fallback to SuiteP if current theme doesn't have Bootstrap CSS
    return 'SuiteP';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $surveyName; ?></title>

    <link href="themes/<?= getSurveyThemeCSS() ?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="custom/include/javascript/rating/rating.min.css" rel="stylesheet">
    <link href="custom/include/javascript/datetimepicker/jquery-ui-timepicker-addon.css" rel="stylesheet">
    <link href="include/javascript/jquery/themes/base/jquery.ui.all.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row well">
        <div class="col-md-offset-2 col-md-8">
            <h1><?= $surveyName; ?></h1>
            <p><?= $surveyThanks; ?></p>
        </div>
    </div>
</div>
<script src="include/javascript/jquery/jquery-min.js"></script>
<script src="include/javascript/jquery/jquery-ui-min.js"></script>
</body>
</html>