<?php

use CT\CT_ExerciseCode;
use CT\CT_ExerciseSQL;

require_once('initTsugi.php');

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);

$exercises = $main->getExercises();
$typedExercises = array();

if($main->getType() == '1'){
    foreach($exercises as $el){
        array_push($typedExercises, new CT_ExerciseCode($el->getExerciseId()));
    }
}else{
    foreach($exercises as $el){
        array_push($typedExercises, new CT_ExerciseSQL($el->getExerciseId()));
    }
}

// var_dump($exercises);die;
// var_dump($gradesMap);die;

echo $twig->render('pages/exercises-list.php.twig', array(
    'mainType' => $main->getType(),
    'exercises' => $typedExercises,
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));
