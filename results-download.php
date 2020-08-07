<?php

require_once('../config.php');
require_once('dao/CT_DAO.php');

use \Tsugi\Core\LTIX;
use \CT\DAO\CT_DAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

include("menu.php");

// Start of the output
$OUTPUT->header();

include("tool-header.html");

$OUTPUT->bodyStart();

$OUTPUT->topNav($menu);

echo '<div class="container-fluid">';

$OUTPUT->flashMessages();

$OUTPUT->pageTitle('Download Results', true, false);

?>
        <p class="lead">Click on the link below to download the student results.</p>
        <h4>
            <a href="actions/ExportToFile.php">
                <span class="fa fa-download" aria-hidden="true"></span> CodeTest-<?=$CONTEXT->title?>-Results.xls
            </a>
        </h4>
    </div>
<?php

$OUTPUT->helpModal("Code Test Help", __('
                        <h4>Downloading Results</h4>
                        <p>Click on the link to download an Excel file with all of the results for this Code Test.</p>'));

$OUTPUT->footerStart();

include("tool-footer.html");

$OUTPUT->footerEnd();
