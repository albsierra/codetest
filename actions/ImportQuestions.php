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
            list($question_id, $test_id) = explode("/", $question);
            $origQuestion = \CT\CT_Test::findTestForImportQuestionId($question_id, $test_id);
            if ($origQuestion->getQuestionId()) {
                $origQuestion->save();
            } else {
                $_SESSION['error'] = $translator->trans('backend-messages.import.question.failed');
            }
        }
        $_SESSION['success'] = $translator->trans('backend-messages.import.question.success', [
            "questions" => $arr
        ])
    }

    header('Location: ' . addSession('../instructor-home.php'));
} else {
    header('Location: ' . addSession('../student-home.php'));
}
