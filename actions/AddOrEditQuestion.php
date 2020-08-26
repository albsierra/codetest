<?php
require_once "../initTsugi.php";

if ($USER->instructor) {

    $result = array();

    $questionPost = $_POST["question"];

    if (isset($questionPost['question_txt']) && trim($questionPost['question_txt']) != '') {
        $main = new \CT\CT_Main($_SESSION["ct_id"]);
        if ($questionPost['question_id'] > -1) {
            // Existing question
            $class = $main->getTypeProperty('class');
            $question = new $class($questionPost['question_id']);
            \CT\CT_DAO::setObjectPropertiesFromArray($question, $questionPost);
            $question->save();
        } else {
            // New question
            $question = $main->createQuestion($questionPost);

            // Create new question markup
            ob_start();

            echo $twig->render('question/instructorQuestion.php', array(
                'CFG' => $CFG,
                'question' => $question,
                'main' => $main,
            ));
            $result["new_question"] = ob_get_clean();
        }
        $_SESSION['success'] = 'Question Saved.';
    } else {
        if ($questionPost['question_id'] > -1) {
            // Blank text means delete question
            $question = new \CT\CT_Question($questionPost['question_id']);
            $question->delete();
            // Set question id to false to remove question line
            $questionPost['question_id'] = false;
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

