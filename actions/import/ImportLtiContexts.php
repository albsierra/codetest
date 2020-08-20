<?php
require_once "../../initTsugi.php";

$user = new \CT\CT_User($USER->id);

if ($USER->instructor) {
    $coursesForImport = $user->getLtiContexts();

    echo $twig->render('question/import/importLtiContexts.html', array(
        'coursesForImport' => $coursesForImport
    ));

} else {
    header( 'Location: '.addSession('../../student-home.php') ) ;
}
