<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
* License, v. 2.0. If a copy of the MPL was not distributed with this
* file, You can obtain one at http://mozilla.org/MPL/2.0/. */

// Include required functions file
require_once(realpath(__DIR__ . '/../includes/functions.php'));
require_once(realpath(__DIR__ . '/../includes/authenticate.php'));
require_once(realpath(__DIR__ . '/../includes/display.php'));
require_once(realpath(__DIR__ . '/../includes/reporting.php'));
require_once(realpath(__DIR__ . '/../vendor/autoload.php'));

// Include Laminas Escaper for HTML Output Encoding
$escaper = new Laminas\Escaper\Escaper('utf-8');

// Add various security headers
add_security_headers();

// Add the session
add_session_check();

// Include the CSRF Magic library
include_csrf_magic();

// Include the SimpleRisk language file
require_once(language_file());

// Record the page the workflow started from as a session variable
$_SESSION["workflow_start"] = $_SERVER['SCRIPT_NAME'];

// If the select_report form was posted
if (isset($_REQUEST['report']))
{
  $report = (int)$_REQUEST['report'];
}
else $report = 0;

$sortby = isset($_REQUEST['sortby']) ? (int)$_REQUEST['sortby'] : 0;
$asset_tags = isset($_REQUEST['asset_tags']) ? $_REQUEST['asset_tags'] : [];
if(!isset($_REQUEST['report'])) $asset_tags = "all";
?>

<!doctype html>
<html lang="<?php echo $escaper->escapehtml($_SESSION['lang']); ?>" xml:lang="<?php echo $escaper->escapeHtml($_SESSION['lang']); ?>">

<head>
  <script src="../js/jquery.min.js"></script>
  <script src="../js/bootstrap.min.js"></script>
  <script src="../js/bootstrap-multiselect.js"></script>
  <script src="../js/sorttable.js"></script>
  <script src="../js/obsolete.js"></script>
  <script src="../js/dynamic.js"></script>
  <title>SimpleRisk: Enterprise Risk Management Simplified</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
  <link rel="stylesheet" href="../css/bootstrap.css">
  <link rel="stylesheet" href="../css/bootstrap-responsive.css">

  <link rel="stylesheet" href="../vendor/fortawesome/font-awesome/css/fontawesome.min.css">
  <link rel="stylesheet" href="../css/theme.css">
  <link rel="stylesheet" href="../css/side-navigation.css">
  <?php
    setup_favicon("..");
    setup_alert_requirements("..");
  ?>
    <style>
        .group-name-row {
            cursor: pointer;
        }
        td.group-name > i {
            margin-right: 10px;
            width: 10px;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('.group-name-row').click(function() {
                var id = $(this).data('group-name-row-id');
                $("[data-group-id='" + id + "']").toggle();
                $(this).find('i').toggleClass('fa-caret-right fa-caret-down');
            });
        });
    </script>
</head>

<body>

  <?php
    view_top_menu("Reporting");
    // Get any alert messages
    get_alert();
  ?>

  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span3">
        <?php view_reporting_menu("RisksAndAssets"); ?>
      </div>
      <div class="span9">
        <div class="row-fluid">
          <div id="selections" class="span12">
            <div class="well">
              <?php view_risks_and_assets_selections($report, $sortby, $asset_tags); ?>
            </div>
          </div>
        </div>
        <div class="row-fluid">
          <div class="span12">
            <?php risks_and_assets_table($report, $sortby, $asset_tags); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>
