<?php
require_once "../../initTsugi.php";

if ($USER->instructor) {
    $mains = \CT\CT_Main::getMainsFromContext($_GET['contextId']);

    echo $twig->render('question/import/importMains.html', array(
        'mains' => $mains,
    ));

} else {
    header( 'Location: '.addSession('../../student-home.php') ) ;
}
