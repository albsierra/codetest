<?php
require_once "../initTsugi.php";

if ($USER->instructor) {

    $questions = isset($_POST["question"]) ? $_POST["question"] : false;

    if (!$questions) {
        $_SESSION["error"] = "Question(s) failed to save. Please try again.";
    } else {
        $main = new \CT\CT_Main($_SESSION["ct_id"]);
        foreach($questions as $question) {
            $class = $main->getTypeProperty('class');
            $origQuestion = new $class($question);

            if($origQuestion->getQuestionId()) {
                $origQuestion->setQuestionId(null);
                // $question = $main->createQuestion(\CT\CT_DAO::setObjectPropertiesToArray($origQuestion));
                $question = $main->createQuestion($origQuestion);
            }
        }

        $_SESSION['success'] = 'Question(s) Saved.';
    }

    header( 'Location: '.addSession('../instructor-home.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}
