<?php

require_once "../initTsugi.php";
global $translator;

if ($USER->instructor) {
    $exercises = isset($_POST["exercise"]) ? $_POST["exercise"] : false;

    if (!$exercises) {
        $_SESSION["error"] = $translator->trans('backend-messages.import.exercise.noselect');
    } else {
        $main = new \CT\CT_Main($_SESSION["ct_id"]);
        foreach ($exercises as $exercise) {
            list($exercise_id, $test_id) = explode("/", $exercise);
            $origExercise = \CT\CT_Test::findTestForImportExerciseId($exercise_id, $test_id);
            if ($origExercise->getExerciseId()) {
                $origExercise->save();
            } else {
                $_SESSION['error'] = $translator->trans('backend-messages.import.exercise.failed');
            }
        }
        $_SESSION['success'] = $translator->trans('backend-messages.import.exercise.success', [
            "exercises" => $arr
        ])
    }

    header('Location: ' . addSession('../instructor-home.php'));
} else {
    header('Location: ' . addSession('../student-home.php'));
}
