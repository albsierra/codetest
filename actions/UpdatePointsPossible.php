<?php
require_once "../../config.php";
require_once('../dao/CT_DAO.php');

use \Tsugi\Core\LTIX;
use \CT\DAO\CT_DAO;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO($PDOX, $p);

if ($USER->instructor) {

    $result = array();

    if (isset($_POST["points_possible"]) && is_numeric($_POST["points_possible"])) {
        $currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
        $currentTime = $currentTime->format("Y-m-d H:i:s");

        $CT_DAO->updatePointsPossible($_SESSION["ct_id"], $_POST["points_possible"], $currentTime);

        $_SESSION['success'] = "Points Possible updated.";
    } else {
        $_SESSION['error'] = "Points Possible failed to save or you provided an invalid number. Please try again.";
    }

    header( 'Location: '.addSession('../grade.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}

