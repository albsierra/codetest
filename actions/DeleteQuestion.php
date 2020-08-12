<?php
require_once "../initTsugi.php";

$question_id = isset($_POST["question_id"]) ? $_POST["question_id"] : false;

if ( $USER->instructor && $question_id ) {

    $question = new \CT\CT_Question($question_id);
    $question->delete();

    \CT\CT_Question::fixUpQuestionNumbers($_SESSION["ct_id"]);

    $_SESSION['success'] = "Question Deleted.";

    $OUTPUT->buffer=true;
    $result["flashmessage"] = $OUTPUT->flashMessages();

    header('Content-Type: application/json');

    echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG);

    exit;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}

