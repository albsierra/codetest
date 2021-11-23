<?php
require_once "../../initTsugi.php";

if ($USER->instructor) {
    $question = \CT\CT_Question::withId($_GET['questionId']);
    $answers = $question->getAnswers();
    $numberResponses = count($answers);
    // Sort by modified date with most recent at the top
    usort($answers, 'response_date_compare');
    $responses = getResponsesArray($answers);
    
    if ($responses) {
        echo $twig->render('answer/getAnswersFromQuestion.php.twig', array(
            'responses' => $responses,
        ));
    } else {
        echo $twig->render('answer/noAnswers.php.twig', array(
        ));
    }
} else {
    header( 'Location: '.addSession('../../student-home.php')) ;
}

function response_date_compare($response1, $response2) {
    $time1 = strtotime($response1->getModified());
    $time2 = strtotime($response2->getModified());
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
