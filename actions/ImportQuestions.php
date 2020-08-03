<?php
require_once "../../config.php";
require_once('../dao/CT_DAO.php');
require_once('../dao/CT_Main.php');
require_once('../dao/CT_Question.php');

use \Tsugi\Core\LTIX;
use \CT\DAO\CT_DAO;
use \CT\DAO\CT_Main;
use \CT\DAO\CT_Question;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

if ($USER->instructor) {

    $questions = isset($_POST["question"]) ? $_POST["question"] : false;

    if (!$questions) {
        $_SESSION["error"] = "Question(s) failed to save. Please try again.";
    } else {
        foreach($questions as $question) {
            $origQuestion = new CT_Question($question);

            if($origQuestion->getQuestionId()) {
                $main = new CT_Main($_SESSION["ct_id"]);
                $question = $main->createQuestion($origQuestion->getQuestionTxt());
            }
        }

        $_SESSION['success'] = 'Question(s) Saved.';
    }

    header( 'Location: '.addSession('../instructor-home.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}
