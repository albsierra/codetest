<?php
require_once "../../config.php";
require_once('../dao/CT_DAO.php');
require_once('../dao/CT_Main.php');
require_once('../dao/CT_Question.php');

use \Tsugi\Core\LTIX;
use \CT\dao\CT_DAO;
use \CT\dao\CT_Main;
use \CT\dao\CT_Question;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

$question_id = isset($_POST["question_id"]) ? $_POST["question_id"] : false;
$questionToMove = new CT_Question($question_id);

if ( $USER->instructor && $question_id ) {
    $main = new CT_Main($_SESSION["ct_id"]);
    $questions = $main->getQuestions();
    $prevQuestion = false;
    foreach ($questions as $question) {
        if ($question->getQuestionId() == $questionToMove->getQuestionId()) {
            // Move this one up
            if($question->getQuestionNum() == 1) {
                // This was the first so put it at the end
                $questionToMove->setQuestionNum(count($questions) + 1);
                $questionToMove->save();
                CT_Question::fixUpQuestionNumbers($_SESSION["ct_id"]);
                break;
            } else {
                // This was one of the other questions so swap with previous
                $number = $questionToMove->getQuestionNum();
                $questionToMove->setQuestionNum($prevQuestion->getQuestionNum());
                $questionToMove->save();
                $prevQuestion->setQuestionNum($number);
                $prevQuestion->save();
                break;
            }
        }
        $prevQuestion = $question;
    }

    $_SESSION["success"] = "Question Order Saved.";

    $result = array();

    $OUTPUT->buffer=true;
    $result["flashmessage"] = $OUTPUT->flashMessages();

    header('Content-Type: application/json');

    echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG);

    exit;
} else if ($USER->instructor) {
    exit;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}
