<?php
require_once('initTsugi.php');

$currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
$currentTime = $currentTime->format("Y-m-d H:i:s");

if ( $USER->instructor ) {
    $main = \CT\CT_Main::getMainFromContext($CONTEXT->id, $LINK->id, $USER->id, $currentTime);

    $_SESSION["ct_id"] = $main->getCtId();

	// If lti_link is a copy, we will try to import questions.
    // TODO dealing with more than one link.id.history.
    $post = $_SESSION['lti_post'];
    if(array_key_exists('custom_link_id_history',$post) && $linkOriginal = \CT\CT_Link::withLinkKey($post['custom_link_id_history'])) {
        $linkCopy = new \CT\CT_Link($LINK->id);
        $linkCopy->import($linkOriginal, $main);
    }

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

        $questions = $main->getQuestions();

        if (!$questions || count($questions) == 0) {
            header('Location: '.addSession('splash.php'));
        } else {
            header( 'Location: '.addSession('student-home.php') ) ;
        }
    }
}

