<?php
require_once "../../initTsugi.php";

$loader = new \Twig_Loader_Filesystem('../../views');
// TODO eliminar debug 0 true y habilitar cache en tmp
$twig = new \Twig_Environment($loader, [
    'debug' => true
]);

if ($USER->instructor) {
    $main = new \CT\CT_Main($_GET['ctId']);
    $questions = $main->getQuestions();
    echo $twig->render('question/import/importQuestions.html', array(
        'questions' => $questions,
    ));

} else {
    header( 'Location: '.addSession('../../student-home.php') ) ;
}
