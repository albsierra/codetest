<?php
require_once('../config.php');
require_once('dao/CT_DAO.php');

use \Tsugi\Core\LTIX;
use \CT\DAO\CT_DAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

$currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
$currentTime = $currentTime->format("Y-m-d H:i:s");

if ( $USER->instructor ) {

    $_SESSION["ct_id"] = $CT_DAO->getOrCreateMain($USER->id, $CONTEXT->id, $LINK->id, $currentTime);

    $seenSplash = $CT_DAO->hasSeenSplash($_SESSION["ct_id"]);

    if ($seenSplash) {
        // Instructor has already setup this instance
        header( 'Location: '.addSession('instructor-home.php') ) ;
    } else {
        header('Location: '.addSession('splash.php'));
    }
} else { // student

    $mainId = $CT_DAO->getMainID($CONTEXT->id, $LINK->id);

    if (!$mainId) {
        header('Location: '.addSession('splash.php'));
    } else {
        $_SESSION["ct_id"] = $mainId;

        $questions = $CT_DAO->getQuestions($_SESSION["ct_id"]);

        if (!$questions || count($questions) == 0) {
            header('Location: '.addSession('splash.php'));
        } else {
            header( 'Location: '.addSession('student-home.php') ) ;
        }
    }
}
