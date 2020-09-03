<?php
require_once('../../initTsugi.php');

if ($USER->instructor) {

    $main = new \CT\CT_Main($_SESSION["ct_id"]);

    $questions = $main->getQuestions();
    $class = $main->getTypeProperty('class');
    $newQuestion = new $class();

    echo $twig->render('question/newQuestionForm.php', array(
        'main' => $main,
        'newQuestion' => $newQuestion,
        'newQuestionNumber' => $questions ? count($questions)+1 : 1,
        'CFG' => $CFG,
        ));

} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}

