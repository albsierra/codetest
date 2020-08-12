<?php
require_once "../config.php";
require '../vendor/autoload.php';

use \Tsugi\Core\LTIX;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new \CT\CT_DAO();

if ($USER->instructor) {

    $questions = isset($_POST["question"]) ? $_POST["question"] : false;

    if (!$questions) {
        $_SESSION["error"] = "Question(s) failed to save. Please try again.";
    } else {
        foreach($questions as $question) {
            $origQuestion = new \CT\CT_Question($question);

            if($origQuestion->getQuestionId()) {
                $main = new \CT\CT_Main($_SESSION["ct_id"]);
                $question = $main->createQuestion($origQuestion->getQuestionTxt());
            }
        }

        $_SESSION['success'] = 'Question(s) Saved.';
    }

    header( 'Location: '.addSession('../instructor-home.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}
