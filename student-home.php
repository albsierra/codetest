<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

$SetID = $_SESSION["ct_id"];

$main = new \CT\CT_Main($_SESSION["ct_id"]);

$toolTitle = $main->getTitle() ? $main->getTitle() : "Code Test";

$exercises = $main->getExercises();
$totalExercises = count($exercises);

$currentExerciseNumber = isset($_GET['exerciseNumber']) ? $_GET['exerciseNumber'] : 0;

$user = new \CT\CT_User($USER->id);

echo $twig->render('student-home.php.twig', array(
    'OUTPUT' => $OUTPUT,
    'help' => $help(),
    'menu' => $menu,
    'user' => $user,
    'exercises' => $exercises,
    'totalExercises' => $totalExercises,
    'currentExerciseNumber' => $currentExerciseNumber,
    'main' => $main,
    'CFG' => $CFG,
));

