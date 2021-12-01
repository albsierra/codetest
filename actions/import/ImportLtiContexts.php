<?php
require_once "../../initTsugi.php";

$user = new \CT\CT_User($USER->id);
$_SESSION['tags'] = ["difficulty" => [], "type" => [], "keywords" => [], "averageGrade" => []];
$page = $_GET['page'];

if ($USER->instructor) {

    $array = \CT\CT_Test::findTestForImportByPage($page);
    $testForImport = $array['tests'];
    $totalPages = $array['totalPages'];

    echo $twig->render('exercise/import/importLtiContexts.php.twig', array(
        'testForImport' => $testForImport,
        'totalPages' => $totalPages,
        'page' => $page,
    ));
} else {
    header('Location: ' . addSession('../../student-home.php'));
}
