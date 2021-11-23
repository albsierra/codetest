<?php

require_once "../../initTsugi.php";

if ($USER->instructor) {
    $id = $_GET['test'];
    $test = \CT\CT_Test::findTestForImportId($id);
    $questions = $test->getQuestions();
    echo $twig->render('question/import/importTest.php.twig', array(
        'questions' => $questions,
    ));
} else {
    header('Location: ' . addSession('../../student-home.php'));
}
