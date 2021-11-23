<?php

require_once "../initTsugi.php";
global $translator;

$currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
$questionId = $_POST["questionId"];
$answerText = $_POST["answerText"];
$questionNum = $_POST["questionNum"];

// In databases doesn't exists answer_language, so we use -1
$answerLanguage = $_POST["answer_language"] ?? -1;

$result = array();

//if the answer is blank
if (!isset($answerText) || trim($answerText) == "") {
    $_SESSION['error'] = $translator->trans('backend-messages.answer.question.failed');
    $result["answer_content"] = false;
} else {
    //Search for the question on the db and map
    $question = \CT\CT_Question::withId($questionId);
    $main = $question->getMain();
    if ($main->getType() == '1') {
        $question1 = new \CT\CT_QuestionCode($question->getQuestionId());
    } else {
        $question1 = \CT\CT_QuestionSQL::withId($question->getQuestionId());
    }

    $array = $question1->createAnswer($USER->id, $answerText, $answerLanguage);
    $answer = $array['answer'];

    $result["answer_content"] = true;
    $result['exists'] = $array['exists'];
    $result['success'] = $answer->getAnswerSuccess();

    $result['answerText'] = $answer->getAnswerTxt();

    // Notify elearning that there is a new answer
    // the message
    $msg = "A new code test was submitted on Learn by " . $USER->displayname . " (" . $USER->email . ").\n
    Question: " . $question->getTitle() . "\n
    Answer: " . $answer->getAnswerTxt();

    // use wordwrap() if lines are longer than 70 characters
    $msg = wordwrap($msg, 70);

    $headers = "From: LEARN < @gmail.com >\n";

    $_SESSION['success'] = $translator->trans('backend-messages.answer.question.saved');
}

$OUTPUT->buffer = true;
$result["flashmessage"] = $OUTPUT->flashMessages();

header('Content-Type: application/json');

echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG);

exit;

