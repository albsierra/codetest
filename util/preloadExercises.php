<?php
global $translator;

$custom_exercisesListRaw = $LAUNCH->ltiCustomGet("exercises_list", $default = "");
$exercisesToPreloadList = explode(",", $custom_exercisesListRaw);

$hasValidCustomParameter = strlen($custom_exercisesListRaw) > 0 && count($exercisesToPreloadList) > 0;

if ($USER->instructor && $main->getSeenSplash() && $hasValidCustomParameter && !$main->getPreloaded()) {

    $exercises = $exercisesToPreloadList;

    $failedToPreload = [];

    if (!$exercises) {
        $_SESSION["error"] = $translator->trans('backend-messages.error-preload-empty');
    } else {
        foreach ($exercises as $exercise) {
            $origExercise = \CT\CT_Exercise::findExerciseForImportAkId($exercise);
            if(is_null($origExercise)){
                array_push($failedToPreload, $exercise);
                continue;
            }
            if ($origExercise->getExerciseId()) {
                $origExercise->save();
                $_SESSION['success'] = $translator->trans('backend-messages.preload-success');
            } else {
                $_SESSION['error'] = $translator->trans('backend-messages.preload-error-external');
            }
        }
        if(count($failedToPreload) > 0){
            $_SESSION['error'] = $translator->trans('backend-messages.preload-error', ['exercisesList' => "<br>".join("<br>", $failedToPreload)]);
        }else {
            $main->setPreloaded(true);
            $main->save();
        }
    }
}
