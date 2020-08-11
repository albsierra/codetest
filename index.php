<?php
require_once('config.php');
require_once('dao/CT_DAO.php');
require_once('dao/CT_Main.php');
require_once('dao/CT_Question.php');

use \Tsugi\Core\LTIX;
use \CT\dao\CT_DAO;
use \CT\dao\CT_Main;
use \CT\dao\CT_Question;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

$currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
$currentTime = $currentTime->format("Y-m-d H:i:s");

if ( $USER->instructor ) {

    $main = CT_Main::getMainFromContext($CONTEXT->id, $LINK->id, $USER->id, $currentTime);

    $_SESSION["ct_id"] = $main->getCtId();

    if ($main->getSeenSplash()) {
        // Instructor has already setup this instance
        header( 'Location: '.addSession('instructor-home.php') ) ;
    } else {
        header('Location: '.addSession('splash.php'));
    }
} else { // student

    $main = CT_Main::getMainFromContext($CONTEXT->id, $LINK->id);

    if (!$main) {
        header('Location: '.addSession('splash.php'));
    } else {
        $_SESSION["ct_id"] = $main->getCtId();

        $questions = $main->getQuestions();

        if (!$questions || count($questions) == 0) {
            header('Location: '.addSession('splash.php'));
        } else {
            header( 'Location: '.addSession('student-home.php') ) ;
        }
    }
}
