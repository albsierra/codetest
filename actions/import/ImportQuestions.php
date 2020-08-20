<?php
require_once "../../initTsugi.php";

if ($USER->instructor) {
    $main = new \CT\CT_Main($_GET['ctId']);
    $questions = $main->getQuestions();
    echo $twig->render('question/import/importQuestions.html', array(
        'questions' => $questions,
    ));

} else {
    header( 'Location: '.addSession('../../student-home.php') ) ;
}
