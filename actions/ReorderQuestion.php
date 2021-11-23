<?php

require_once "../initTsugi.php";

$questionId = isset($_POST["questionId"]) ? $_POST["questionId"] : false;
$oldIndex = isset($_POST["oldIndex"]) ? $_POST["oldIndex"] + 1 : false;
$newIndex = isset($_POST["newIndex"]) ? $_POST["newIndex"] + 1 : false;

if ($USER->instructor) {
    $main = new \CT\CT_Main($_SESSION["ct_id"]);
    $questions = $main->getQuestions();
    $prevQuestion = false;
    $find = false;

    foreach ($questions as $question) {
        $questionNum = $question->getQuestionNum();
        
        //Set the new position to the moved question
        if ($question->getQuestionId() == $questionId) {
            $question->setQuestionNum($newIndex);
            
            // -1 position to the question with a lower index than the new
        } else if (($oldIndex < $newIndex ) && ($questionNum <= $newIndex) && ($questionNum > $oldIndex)) {
            $question->setQuestionNum($questionNum - 1);

            // +1 position to the question with a higher index than the new
        } else if (($oldIndex > $newIndex ) && ($questionNum >= $newIndex) && ($questionNum < $oldIndex)) {

            $question->setQuestionNum($questionNum + 1);
        }
        $question->update();
    }
    $_SESSION["success"] = "Question Order Saved.";

    $result = array();

    $OUTPUT->buffer = true;
    $result["flashmessage"] = $OUTPUT->flashMessages();

    header('Content-Type: application/json');

    echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG);

    exit;
} else if ($USER->instructor) {
    exit;
} else {
    header('Location: ' . addSession('../student-home.php'));
}
