<?php

require_once "../initTsugi.php";
global $translator;

$exerciseId = $_POST["exerciseId"];
$understandabilityScore = $_POST['rateUnderstandability'];
$difficultyScore = $_POST['rateDifficulty'];
$timeScore = $_POST['rateTime'];

$user = new \CT\CT_User($USER->id);
$usage = \CT\CT_Usage::constructValues($exerciseId, $user, $understandabilityScore, $difficultyScore, $timeScore);
$code = $usage->save();
$resulta = array();

if ($code == 200) {
    $_SESSION['success'] = $translator->trans('backend-messages.usage.send.success');
} else {
    $_SESSION['error'] = $translator->trans('backend-messages.usage.send.error');
}

$result["flashmessage"] = $OUTPUT->flashMessages();
exit;
