<?php

require_once "../../initTsugi.php";

$user = new \CT\CT_User($USER->id);
$_SESSION['tags'] = ["difficulty" => [], "type" => [], "keywords" => [], "averageGrade" => []];
$page = $_GET['page'];

if ($USER->instructor) {
    $array = \CT\CT_Question::findQuestionForImportByPage($page);
    $questionsForImport = $array['questions'];
    $totalPages = $array['totalPages'];

    //totalPages // ActualPage
    echo $twig->render('question/import/importLtiContextsQuestion.php.twig', array(
        'questionsForImport' => $questionsForImport,
        'totalPages' => $totalPages,
        'page' => $page,
    ));
} else {
    header('Location: ' . addSession('../../student-home.php'));
}
