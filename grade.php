<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$pointsPossible = $main->getPoints();
$exercises = $main->getExercises();
$totalExercises = count($exercises);

echo $twig->render('grade/grade.php.twig', array(
    'maxPoints' => $pointsPossible,
    'totalExercises' => $totalExercises,
    'main' => $main,
    'OUTPUT' => $OUTPUT,
    'menu' => $menu,
    'help' => $help(),
));
