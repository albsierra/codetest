<?php

require_once "../../initTsugi.php";

if ($USER->instructor) {
    $main = new \CT\CT_Main($_GET['exerciseId']);
    $exerciseId = $_GET['exerciseId'];
    $testId = $_GET['testId'];
    $exercise = \CT\CT_Test::findTestForImportExerciseId($exerciseId, $testId);

    echo $twig->render('exercise/import/importExercises.php.twig', array(
        'exercise' => $exercise,
    ));
} else {
    header('Location: ' . addSession('../../student-home.php'));
}
