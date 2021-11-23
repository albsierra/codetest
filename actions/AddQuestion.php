<?php

require_once "../initTsugi.php";
global $translator;

if ($USER->instructor) {
    $result = array();
    $type = $_POST["type"];
    $difficulty = $_POST["difficulty"];
    $questionPost = $_POST["question"];

    if (isset($questionPost['title']) && trim($questionPost['title']) != '') {
        $main = new \CT\CT_Main($_SESSION["ct_id"]);
        $question = $main->createQuestion($questionPost, $type, $difficulty);
        $questions = Array();
        array_push($questions, $question);

        //save the question on the repository
        $result = $main->saveQuestions($questions);

        //map the returned question
        $object = json_decode($result);
        if ($main->getType() == '1') {
            $question1 = \CT\CT_Test::mapObjectToCodeQuestion($object);
        } else {
            $question1 = \CT\CT_Test::mapObjectToSQLQuestion($object);
        }
        $question1->setCtId($_SESSION["ct_id"]);

        //Save the returned question on the db
        $question1->save();

        $_SESSION['success'] = $translator->trans('backend-messages.add.question.success');
    } else {

        $_SESSION['error'] = $translator->trans('backend-messages.add.question.failed');
    }
    $OUTPUT->buffer = true;
    header('Content-Type: application/json');

    echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG);

    exit;
} else {
    header('Location: ' . addSession('../student-home.php'));
}

