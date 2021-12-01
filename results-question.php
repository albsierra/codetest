<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$exercises = $main->getExercises();

echo $twig->render('answer/results-exercise.php.twig', array(
    'OUTPUT' => $OUTPUT,
    'CONTEXT' => $CONTEXT,
    'help' => $help(),
    'menu' => $menu,
    'exercises' => $exercises,
));
