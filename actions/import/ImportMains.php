<?php
require_once "../../initTsugi.php";

$loader = new \Twig_Loader_Filesystem('../../views');
// TODO eliminar debug 0 true y habilitar cache en tmp
$twig = new \Twig_Environment($loader, [
    'debug' => true
]);

if ($USER->instructor) {
    $mains = \CT\CT_Main::getMainsFromContext($_GET['contextId']);

    echo $twig->render('question/import/importMains.html', array(
        'mains' => $mains,
    ));

} else {
    header( 'Location: '.addSession('../../student-home.php') ) ;
}
