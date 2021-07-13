<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
* License, v. 2.0. If a copy of the MPL was not distributed with this
* file, You can obtain one at http://mozilla.org/MPL/2.0/. */

// Include required functions file
require_once(realpath(__DIR__ . '/../includes/functions.php'));
require_once(realpath(__DIR__ . '/../includes/authenticate.php'));
require_once(realpath(__DIR__ . '/../includes/display.php'));
require_once(realpath(__DIR__ . '/../includes/alerts.php'));
require_once(realpath(__DIR__ . '/../includes/permissions.php'));
require_once(realpath(__DIR__ . '/../includes/governance.php'));
require_once(realpath(__DIR__ . '/../includes/compliance.php'));
require_once(realpath(__DIR__ . '/../vendor/autoload.php'));

// Include Laminas Escaper for HTML Output Encoding
$escaper = new Laminas\Escaper\Escaper('utf-8');

// Add various security headers
add_security_headers();

// Add the session
$permissions = array(
        "check_access" => true,
        "check_compliance" => true,
);
add_session_check($permissions);

// Include the CSRF Magic library
include_csrf_magic();

// Include the SimpleRisk language file
require_once(language_file());

// If team separation is enabled
if (team_separation_extra()) {
    //Include the team separation extra
    require_once(realpath(__DIR__ . '/../extras/separation/index.php'));
    
    $test_audit_id  = (int)$_GET['id'];
    
    if (!is_user_allowed_to_access($_SESSION['uid'], $test_audit_id, 'audit')) {
        set_alert(true, "bad", $escaper->escapeHtml($lang['NoPermissionForThisAudit']));
        refresh($_SESSION['base_url']."/compliance/active_audits.php");
    }
}

// Check if a framework was updated
if (isset($_POST['submit_test_result']))
{
    // check permission
    if(!isset($_SESSION["modify_audits"]) || $_SESSION["modify_audits"] != 1){
        set_alert(true, "bad", $lang['NoPermissionForThisAction']);
        refresh();
    }
    // Process submitting test result
    if(submit_test_result()){
        $closed_audit_status = get_setting("closed_audit_status");
        if($_POST['status'] == $closed_audit_status)
        {
            refresh($_SESSION['base_url']."/compliance/active_audits.php");
        }
        else
        {
            refresh();
        }
    }
}

?>
<!doctype html>
<html>

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=10,9,7,8">
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery.easyui.min.js"></script>
    <script src="../js/jquery-ui.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/bootstrap-multiselect.js"></script>
    <script src="../js/common.js"></script>

    <title>SimpleRisk: Enterprise Risk Management Simplified</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">

    <link rel="stylesheet" href="../css/easyui.css">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/bootstrap-responsive.css">
    <link rel="stylesheet" href="../css/bootstrap-multiselect.css">
    
    <link rel="stylesheet" href="../vendor/fortawesome/font-awesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../css/theme.css">
    <link rel="stylesheet" href="../css/side-navigation.css">
    <?php
        setup_favicon("..");
        setup_alert_requirements("..");
    ?>    
    
</head>

<body>

    <?php
        view_top_menu("Compliance");

        // Get any alert messages
        get_alert();
    ?>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span3">
                <?php view_compliance_menu("ActiveAudits"); ?>
            </div>
            <div class="span9 compliance-content-container content-margin-height">
                <div class="row-fluid">
                    <div class="span12">
                        <?php display_testing(); ?>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>

    <?php display_set_default_date_format_script(); ?>
    </body>
</html>
