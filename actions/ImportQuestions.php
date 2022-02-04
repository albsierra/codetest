<?php
require_once "../initTsugi.php";

if ($USER->instructor) {

    $questions = isset($_POST["question"]) ? $_POST["question"] : false;

    if (!$questions) {
        $_SESSION["error"] = "Question(s) failed to save. Please try again.";
    } else {
        $main = new \CT\CT_Main($_SESSION["ct_id"]);
        $main->importQuestions($questions);

        $_SESSION['success'] = 'Question(s) Saved.';
    }

    header( 'Location: '.addSession('../instructor-home.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}
