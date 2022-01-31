<?php

require_once "../initTsugi.php";
global $translator;

if ($USER->instructor) {
    $exercises = isset($_POST["exercise"]) ? $_POST["exercise"] : false;

    $exercises = [$exercises];

    if (!$exercises) {
        $_SESSION["error"] = $translator->trans('backend-messages.import.exercise.noselect');
    } else {
        $main = new \CT\CT_Main($_SESSION["ct_id"]);
        foreach ($exercises as $exercise) {
            $origExercise = \CT\CT_Exercise::findExerciseForImportId($exercise);
            if ($origExercise->getExerciseId()) {
                $origExercise->save();
                $_SESSION['success'] = $translator->trans('backend-messages.import.exercise.imported');
            } else {
                $_SESSION['error'] = $translator->trans('backend-messages.import.exercise.failed');
            }
        }
    }
    echo "Success";
}
