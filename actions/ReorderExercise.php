<?php

require_once "../initTsugi.php";

$exerciseId = isset($_POST["exerciseId"]) ? $_POST["exerciseId"] : false;
$oldIndex = isset($_POST["oldIndex"]) ? $_POST["oldIndex"] + 1 : false;
$newIndex = isset($_POST["newIndex"]) ? $_POST["newIndex"] + 1 : false;

if ($USER->instructor) {
    $main = new \CT\CT_Main($_SESSION["ct_id"]);
    $exercises = $main->getExercises();
    $prevExercise = false;
    $find = false;

    foreach ($exercises as $exercise) {
        $exerciseNum = $exercise->getExerciseNum();
        
        //Set the new position to the moved exercise
        if ($exercise->getExerciseId() == $exerciseId) {
            $exercise->setExerciseNum($newIndex);
            
            // -1 position to the exercise with a lower index than the new
        } else if (($oldIndex < $newIndex ) && ($exerciseNum <= $newIndex) && ($exerciseNum > $oldIndex)) {
            $exercise->setExerciseNum($exerciseNum - 1);

            // +1 position to the exercise with a higher index than the new
        } else if (($oldIndex > $newIndex ) && ($exerciseNum >= $newIndex) && ($exerciseNum < $oldIndex)) {

            $exercise->setExerciseNum($exerciseNum + 1);
        }
        $exercise->update();
    }
    $_SESSION["success"] = "Exercise Order Saved.";

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
