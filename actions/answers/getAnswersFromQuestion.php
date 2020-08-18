<?php
require_once "../../initTsugi.php";

$loader = new \Twig_Loader_Filesystem('../../views');
// TODO eliminar debug 0 true y habilitar cache en tmp
$twig = new \Twig_Environment($loader, [
    'debug' => true
]);

if ($USER->instructor) {
    $question = new \CT\CT_Question($_GET['questionId']);

    $answers = $question->getAnswers();
    $numberResponses = count($answers);
    // Sort by modified date with most recent at the top
    usort($answers, 'response_date_compare');
    $responses = getResponsesArray($answers);

    echo $twig->render('answer/getAnswersFromQuestion.html', array(
        'responses' => $responses,
    ));

} else {
    header( 'Location: '.addSession('../../student-home.php') ) ;
}

function response_date_compare($response1, $response2) {
    $time1 = strtotime($response1['modified']);
    $time2 = strtotime($response2['modified']);
    // Most recent at top
    return $time2 - $time1;
}

function getResponsesArray($answers)
{
    global $CONTEXT;
    $responses = array();
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
    return $responses;
}