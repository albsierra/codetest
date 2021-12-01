<?php

require_once "../../initTsugi.php";

if ($USER->instructor) {
    $id = $_GET['test'];
    $test = \CT\CT_Test::findTestForImportId($id);
    $exercises = $test->getExercises();
    echo $twig->render('exercise/import/importTest.php.twig', array(
        'exercises' => $exercises,
    ));
} else {
    header('Location: ' . addSession('../../student-home.php'));
}
