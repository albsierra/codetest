<?php
require_once('initTsugi.php');

$currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
$currentTime = $currentTime->format("Y-m-d H:i:s");

if ( $USER->instructor ) {
    $main = \CT\CT_Main::getMainFromContext($CONTEXT->id, $LINK->id, $USER->id, $currentTime);
    $_SESSION["ct_id"] = $main->getCtId();

    if ($main->getSeenSplash()) {
        // Instructor has already setup this instance
        header( 'Location: '.addSession('instructor-home.php') ) ;
    } else {
        header('Location: '.addSession('splash.php'));
    }
} else { // student
    $main = \CT\CT_Main::getMainFromContext($CONTEXT->id, $LINK->id);

    if (!$main) {
        header('Location: '.addSession('splash.php'));
    } else {
        $_SESSION["ct_id"] = $main->getCtId();
        $exercises = $main->getExercises();

        if (!$exercises || count($exercises) == 0) {
            header('Location: '.addSession('splash.php'));
        } else {
            header( 'Location: '.addSession('student-home.php')) ;
        }
    }
}
