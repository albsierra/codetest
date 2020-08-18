<?php
require_once('initTsugi.php');

$loader = new \Twig_Loader_Filesystem('views');
// TODO eliminar debug 0 true y habilitar cache en tmp
$twig = new \Twig_Environment($loader, [
    'debug' => true
]);

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$questions = $main->getQuestions();

echo $twig->render('answer/results-question.php', array(
    'OUTPUT' => $OUTPUT,
    'CONTEXT' => $CONTEXT,
    'help' => $help(),
    'menu' => $menu,
    'questions' => $questions,
));
