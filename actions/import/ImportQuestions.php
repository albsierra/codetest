<?php

require_once "../../initTsugi.php";

if ($USER->instructor) {
    $main = new \CT\CT_Main($_GET['questionId']);
    $questionId = $_GET['questionId'];
    $testId = $_GET['testId'];
    $question = \CT\CT_Test::findTestForImportQuestionId($questionId, $testId);

    echo $twig->render('question/import/importQuestions.php.twig', array(
        'question' => $question,
    ));
} else {
    header('Location: ' . addSession('../../student-home.php'));
}
