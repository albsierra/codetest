<?php

require_once('../../initTsugi.php');

if ($USER->instructor) {
    $main = new \CT\CT_Main($_SESSION["ct_id"]);
    $questions = $main->getQuestionsForImport();
    $newQuestion = new CT\CT_Question();

    echo $twig->render('question/newQuestionForm.php.twig', array(
        'main' => $main,
        'newQuestion' => $newQuestion,
        'newQuestionNumber' => $questions ? count($questions) + 1 : 1,
        'CFG' => $CFG,
    ));
} else {
    header('Location: ' . addSession('../student-home.php'));
}

