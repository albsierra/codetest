<?php
require_once('../config.php');
require_once('../dao/CT_DAO.php');
require_once('../dao/CT_Main.php');

use \Tsugi\Core\LTIX;
use \CT\dao\CT_DAO;
use \CT\dao\CT_Main;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$main = new CT_Main($_SESSION["ct_id"]);

if ( $USER->instructor ) {

    $main->setSeenSplash(true);
    $main->save();

    header( 'Location: '.addSession('../instructor-home.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}
