<?php

require_once "../initTsugi.php";
global $translator;

$questionId = $_POST["questionId"];
$understandabilityScore = $_POST['rateUnderstandability'];
$difficultyScore = $_POST['rateDifficulty'];
$timeScore = $_POST['rateTime'];

$user = new \CT\CT_User($USER->id);
$feedback = \CT\CT_Feedback::constructValues($questionId, $user, $understandabilityScore, $difficultyScore, $timeScore);
$code = $feedback->save();
$resulta = array();

if ($code == 200) {
    $_SESSION['success'] = $translator->trans('backend-messages.feedback.send.success');
} else {
    $_SESSION['error'] = $translator->trans('backend-messages.feedback.send.error');
}

$result["flashmessage"] = $OUTPUT->flashMessages();
exit;
