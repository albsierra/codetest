<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);

$exercises = $main->getExercises();

// var_dump($gradesMap);die;

echo $twig->render('pages/exercises-list.php.twig', array(
    'exercises' => $exercises,
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));
