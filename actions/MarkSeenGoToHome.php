<?php
require_once('../config.php');
require '../vendor/autoload.php';

use \Tsugi\Core\LTIX;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$main = new \CT\CT_Main($_SESSION["ct_id"]);

if ( $USER->instructor ) {

    $main->setSeenSplash(true);
    $main->save();

    header( 'Location: '.addSession('../instructor-home.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}
