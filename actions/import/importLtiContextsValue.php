<?php

require_once "../../initTsugi.php";

$user = new \CT\CT_User($USER->id);

if ($USER->instructor) {
    if ($_GET['action'] == "add") {
        if ($_GET['object'] == "test") {

            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                $array = CT\CT_Test::findTestForImportByValue($_GET['value'], $page);
            } else {

                $array = CT\CT_Test::findTestForImportByValue($_GET['value']);
                $page = 0;
            }
            $testForImport = $array['tests'];
            $totalPages = $array['totalPages'];
            $tags = $_SESSION['tags'];
            echo $twig->render('question/import/importLtiContexts.php.twig', array(
                'testForImport' => $testForImport,
                'tags' => $tags,
                'totalPages' => $totalPages,
                'page' => $page
            ));
        } else if ($_GET['object'] == "question") {
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                $array = \CT\CT_Question::findQuestionForImportByValue($_GET['value'], $page);
            } else {
                $array = \CT\CT_Question::findQuestionForImportByValue($_GET['value']);
                $page = 0;
            }
            $questionsForImport = $array['questions'];
            $totalPages = $array['totalPages'];
            $tags = $_SESSION['tags'];
            echo $twig->render('question/import/importLtiContextsQuestion.php.twig', array(
                'questionsForImport' => $questionsForImport,
                'tags' => $tags,
                'totalPages' => $totalPages,
                'page' => $page
            ));
        }
    } else if ($_GET['action'] == "delete") {
        if ($_GET['object'] == "test") {
            $array = CT\CT_Test::findTestForImportByDeleteValue($_GET['value']);
            $testForImport = $array['tests'];
            $totalPages = $array['totalPages'];
            $tags = $_SESSION['tags'];
            echo $twig->render('question/import/importLtiContexts.php.twig', array(
                'testForImport' => $testForImport,
                'tags' => $tags,
                'totalPages' => $totalPages,
                'page' => 0
            ));
        } else if ($_GET['object'] == "question") {
            $array = CT\CT_Question::findQuestionsForImportByDeleteValue($_GET['value']);
            $questionsForImport = $array['questions'];
            $totalPages = $array['totalPages'];
            $tags = $_SESSION['tags'];
            echo $twig->render('question/import/importLtiContextsQuestion.php.twig', array(
                //'coursesForImport' => $coursesForImport
                'questionsForImport' => $questionsForImport,
                'tags' => $tags,
                'totalPages' => $totalPages,
                'page' => 0
            ));
        }
    } else if ($_GET['action'] == "page") {
        if ($_GET['object'] == "test") {
            $page = $_GET['page'];
            $array = CT\CT_Test::findTestForImportByValue(null, $page);
            $testForImport = $array['tests'];
            $totalPages = $array['totalPages'];
            $tags = $_SESSION['tags'];
            echo $twig->render('question/import/importLtiContexts.php.twig', array(
                'testForImport' => $testForImport,
                'tags' => $tags,
                'totalPages' => $totalPages,
                'page' => $page
            ));
        } else if ($_GET['object'] == "question") {
            $page = $_GET['page'];
            $array = \CT\CT_Question::findQuestionForImportByValue(null, $page);

            $questionsForImport = $array['questions'];
            $totalPages = $array['totalPages'];
            $tags = $_SESSION['tags'];
            echo $twig->render('question/import/importLtiContextsQuestion.php.twig', array(
                'questionsForImport' => $questionsForImport,
                'tags' => $tags,
                'totalPages' => $totalPages,
                'page' => $page
            ));
        }
    }
} else {
    header('Location: ' . addSession('../../student-home.php'));
}
