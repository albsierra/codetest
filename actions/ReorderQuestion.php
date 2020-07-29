<?php
require_once "../../config.php";
require_once('../dao/CT_DAO.php');
require_once('../dao/CT_Main');

use \Tsugi\Core\LTIX;
use \CT\DAO\CT_DAO;
use \CT\DAO\CT_Main;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

$question_id = isset($_POST["question_id"]) ? $_POST["question_id"] : false;

if ( $USER->instructor && $question_id ) {
    $main = new CT_Main($_SESSION["ct_id"]);
    $questions = $main->getQuestions();
    $prevQuestion = false;
    foreach ($questions as $question) {
        if ($question["question_id"] == $question_id) {
            // Move this one up
            if($question["question_num"] == 1) {
                // This was the first so put it at the end
                $CT_DAO->updateQuestionNumber($question_id, count($questions) + 1);
                $CT_DAO->fixUpQuestionNumbers($_SESSION["ct_id"]);
                break;
            } else {
                // This was one of the other questions so swap with previous
                $CT_DAO->updateQuestionNumber($question_id, $prevQuestion["question_num"]);
                $CT_DAO->updateQuestionNumber($prevQuestion["question_id"], $question["question_num"]);
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
