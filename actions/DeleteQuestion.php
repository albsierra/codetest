<?php

require_once "../initTsugi.php";
global $translator;

$question_id = isset($_POST["question_id"]) ? $_POST["question_id"] : false;
$ct_id = $_SESSION['ct_id'];
if ($USER->instructor && $question_id) {

    //look for the question in the db and delete it
    $question = CT\CT_Question::withId($question_id);
    $question->delete();
    \CT\CT_Question::fixUpQuestionNumbers($_SESSION["ct_id"]);

    $_SESSION['success'] = $translator->trans('backend-messages.question.deleted.success');
    $OUTPUT->buffer = true;
    $result["flashmessage"] = $OUTPUT->flashMessages();

    header('Content-Type: application/json');

    echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG);

    exit;
} else {
    header('Location: ' . addSession('../student-home.php'));
}

