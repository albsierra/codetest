<?php

require_once "../../initTsugi.php";

$user = new \CT\CT_User($USER->id);
$_SESSION['tags'] = ["difficulty" => [], "type" => [], "keywords" => [], "averageGrade" => []];
$page = $_GET['page'];

if ($USER->instructor) {
    $array = \CT\CT_Exercise::findExerciseForImportByPage($page);
    $exercisesForImport = $array['exercises'];
    $totalPages = $array['totalPages'];

    //totalPages // ActualPage
    echo $twig->render('exercise/import/importLtiContextsExercise.php.twig', array(
        'exercisesForImport' => $exercisesForImport,
        'totalPages' => $totalPages,
        'page' => $page,
    ));
} else {
    header('Location: ' . addSession('../../student-home.php'));
}
