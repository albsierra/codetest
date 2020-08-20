<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$pointsPossible = $main->getPoints();

$questions = $main->getQuestions();
$totalQuestions = count($questions);

echo $twig->render('grade/grade.php', array(
    'maxPoints' => $pointsPossible,
    'totalQuestions' => $totalQuestions,
    'students' => $main->getStudentsOrderedByDate(),
    'OUTPUT' => $OUTPUT,
    'menu' => $menu,
    'help' => $help(),
));
