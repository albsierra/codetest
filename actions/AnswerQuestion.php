<?php
require_once "../initTsugi.php";

$currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));

$questionId = $_POST["questionId"];
$answerText = $_POST["answerText"];

$result = array();

if (!isset($answerText) || trim($answerText) == "") {
    $_SESSION['error'] = "Your answer cannot be blank.";
    $result["answer_content"] = false;
} else {
    $question = new \CT\CT_Question($questionId);
    $main = $question->getMain();
    $class = $main->getTypeProperty('class');
    $question = new $class($questionId);

    $answer = $question->createAnswer($USER->id, $answerText);

    ob_start();
    echo $twig->render('question/studentQuestion.php', array(
        'question' => $question,
        'answer' => $answer,
        'main' => $main,
        'CFG' => $CFG,
    ));

    $result["answer_content"] = ob_get_clean();

    $_SESSION['success'] = "Answer saved.";

    // Notify elearning that there is a new answer
    // the message
    $msg = "A new code test was submitted on Learn by ".$USER->displayname." (".$USER->email.").\n
    Question: ".$question->getQuestionTxt()."\n
    Answer: ".$answer->getAnswerTxt();

    // use wordwrap() if lines are longer than 70 characters
    $msg = wordwrap($msg,70);

    $headers  = "From: LEARN < no-reply@learn.udayton.edu >\n";

    // send email
    //mail("elearning@udayton.edu", "A new codetest has been submitted on Learn", $msg, $headers);
}

$OUTPUT->buffer=true;
$result["flashmessage"] = $OUTPUT->flashMessages();

header('Content-Type: application/json');

echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG);

exit;

