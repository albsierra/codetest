<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$exercises = $main->getExercises();
$responses = array();

foreach($exercises as $exercise){
    $answers = $exercise->getAnswers();

    $numberResponses = count($answers);
    // Sort by modified date with most recent at the top
    usort($answers, 'response_date_compare');
    foreach ($answers as $answer) {
        $user = new \CT\CT_User($answer->getUserId());
        if (!$user->isInstructor($CONTEXT->id)) {
            $responseDate = new DateTime($answer->getModified());
            $formattedResponseDate = $responseDate->format("m/d/y") . " | " . $responseDate->format("h:i A");
        }
        $response = array(
            'answer' => $answer,
            'user' => $user,
            'formattedResponseDate' => $formattedResponseDate,
        );
        array_push($responses, $response);
    }
}

if ($responses) {
    echo $twig->render('answer/results-exercise.php.twig', array(
        'OUTPUT' => $OUTPUT,
        'CONTEXT' => $CONTEXT,
        'help' => $help(),
        'menu' => $menu,
        'exercises' => $exercises,
        'responses' => $responses
    ));
} else {
    echo $twig->render('answer/noAnswers.php.twig', array(
    ));
}

function response_date_compare($response1, $response2) {
$time1 = strtotime($response1->getModified());
$time2 = strtotime($response2->getModified());
// Most recent at top
return $time2 - $time1;
}


