<?php
require_once "../config.php";
require '../vendor/autoload.php';

use \Tsugi\Core\LTIX;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new \CT\CT_DAO();

if ($USER->instructor) {

    $result = array();

    if (isset($_POST["points_possible"]) && is_numeric($_POST["points_possible"])) {
        $currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));

        $main = new \CT\CT_Main($_SESSION["ct_id"]);
        $main->setModified($currentTime->format("Y-m-d H:i:s"));
        $main->setPoints($_POST["points_possible"]);
        $main->save();

        $_SESSION['success'] = "Points Possible updated.";
    } else {
        $_SESSION['error'] = "Points Possible failed to save or you provided an invalid number. Please try again.";
    }

    header( 'Location: '.addSession('../grade.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}

