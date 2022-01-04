<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

$SetID = $_SESSION["ct_id"];

$main = new \CT\CT_Main($_SESSION["ct_id"]);

$toolTitle = $main->getTitle() ? $main->getTitle() : "Code Test";

$exercises = $main->getExercises();
$totalExercises = count($exercises);

$currentExerciseNumber = isset($_GET['exerciseNum']) ? $_GET['exerciseNum'] : 1;

$user = new \CT\CT_User($USER->id);

echo $twig->render('pages/student-view.php.twig', array(
    'OUTPUT' => $OUTPUT,
    'help' => $help(),
    'menu' => $menu,
    'user' => $user,
    'exercises' => $exercises,
    'totalExercises' => $totalExercises,
    'currentExerciseNumber' => $currentExerciseNumber,
    'exerciseNum' => $currentExerciseNumber,
    'main' => $main,
    'CFG' => $CFG,
));

