<?php
require_once "../../initTsugi.php";

$loader = new \Twig_Loader_Filesystem('../../views');
// TODO eliminar debug 0 true y habilitar cache en tmp
$twig = new \Twig_Environment($loader, [
    'debug' => true
]);

$user = new \CT\CT_User($USER->id);

if ($USER->instructor) {
    $coursesForImport = $user->getLtiContexts();

    echo $twig->render('question/import/importLtiContexts.html', array(
        'coursesForImport' => $coursesForImport
    ));

} else {
    header( 'Location: '.addSession('../../student-home.php') ) ;
}
