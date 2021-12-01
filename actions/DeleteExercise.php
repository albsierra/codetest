<?php

require_once "../initTsugi.php";
global $translator;

$exercise_id = isset($_POST["exercise_id"]) ? $_POST["exercise_id"] : false;
$ct_id = $_SESSION['ct_id'];
if ($USER->instructor && $exercise_id) {

    //look for the exercise in the db and delete it
    $exercise = CT\CT_Exercise::withId($exercise_id);
    $exercise->delete();
    \CT\CT_Exercise::fixUpExerciseNumbers($_SESSION["ct_id"]);

    $_SESSION['success'] = $translator->trans('backend-messages.exercise.deleted.success');
    $OUTPUT->buffer = true;
    $result["flashmessage"] = $OUTPUT->flashMessages();

    header('Content-Type: application/json');

    echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG);

    exit;
} else {
    header('Location: ' . addSession('../student-home.php'));
}

