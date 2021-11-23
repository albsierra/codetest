<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);

$questions = $main->getQuestions();

// var_dump($gradesMap);die;

echo $twig->render('pages/questions-list.php.twig', array(
    'questions' => $questions,
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));
