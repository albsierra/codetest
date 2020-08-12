<?php
require_once "../initTsugi.php";

$question_id = isset($_POST["question_id"]) ? $_POST["question_id"] : false;
$questionToMove = new \CT\CT_Question($question_id);

if ( $USER->instructor && $question_id ) {
    $main = new \CT\CT_Main($_SESSION["ct_id"]);
    $questions = $main->getQuestions();
    $prevQuestion = false;
    foreach ($questions as $question) {
        if ($question->getQuestionId() == $questionToMove->getQuestionId()) {
            // Move this one up
            if($question->getQuestionNum() == 1) {
                // This was the first so put it at the end
                $questionToMove->setQuestionNum(count($questions) + 1);
                $questionToMove->save();
                \CT\CT_Question::fixUpQuestionNumbers($_SESSION["ct_id"]);
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
