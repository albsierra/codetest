<?php
require_once "../initTsugi.php";

if ($USER->instructor) {

    $result = array();

    $questionPost = $_POST["question"];

    if (isset($questionPost['questionTxt']) && trim($questionPost['questionTxt']) != '') {
        if ($questionPost['questionId'] > -1) {
            // Existing question
            // TODO modificar cada uno de los tipos de Question
            $question = new \CT\CT_Question($questionPost['questionId']);
            $question->setQuestionTxt($questionPost['questionTxt']);
            $question->save();
        } else {
            // New question
            $main = new \CT\CT_Main($_SESSION["ct_id"]);
            $question = $main->createQuestion($questionPost);

            // Create new question markup
            ob_start();

            echo $twig->render('question/instructorQuestion.php', array(
                'question' => $question,
            ));
            $result["new_question"] = ob_get_clean();
        }
        $_SESSION['success'] = 'Question Saved.';
    } else {
        if ($questionPost['questionId'] > -1) {
            // Blank text means delete question
            $question = new \CT\CT_Question($questionPost['questionId']);
            $question->delete();
            // Set question id to false to remove question line
            $questionPost['questionId'] = false;
            $_SESSION['success'] = 'Question Deleted.';
        } else {
            $_SESSION['error'] = 'Unable to save blank question.';
        }
    }

    $OUTPUT->buffer=true;
    $result["flashmessage"] = $OUTPUT->flashMessages();

    header('Content-Type: application/json');

    echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG);

    exit;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}

