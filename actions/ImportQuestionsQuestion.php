<?php

require_once "../initTsugi.php";
global $translator;

if ($USER->instructor) {
    $questions = isset($_POST["question"]) ? $_POST["question"] : false;

    if (!$questions) {
        $_SESSION["error"] = $translator->trans('backend-messages.import.question.noselect');
    } else {
        $main = new \CT\CT_Main($_SESSION["ct_id"]);
        foreach ($questions as $question) {
            $origQuestion = \CT\CT_Question::findQuestionForImportId($question);
            if ($origQuestion->getQuestionId()) {
                $origQuestion->save();
                $_SESSION['success'] = $translator->trans('backend-messages.import.question.imported');
            } else {
                $_SESSION['error'] = $translator->trans('backend-messages.import.question.failed');
            }
        }
    }

    header('Location: ' . addSession('../instructor-home.php'));
} else {
    header('Location: ' . addSession('../student-home.php'));
}
