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
            echo $twig->render('exercise/import/importLtiContexts.php.twig', array(
                'testForImport' => $testForImport,
                'tags' => $tags,
                'totalPages' => $totalPages,
                'page' => $page
            ));
        } else if ($_GET['object'] == "exercise") {
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                $array = \CT\CT_Exercise::findExerciseForImportByValue($_GET['value'], $page);
            } else {
                $array = \CT\CT_Exercise::findExerciseForImportByValue($_GET['value']);
                $page = 0;
            }
            $exercisesForImport = $array['exercises'];
            $totalPages = $array['totalPages'];
            $tags = $_SESSION['tags'];
            echo $twig->render('exercise/import/importLtiContextsExercise.php.twig', array(
                'exercisesForImport' => $exercisesForImport,
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
            echo $twig->render('exercise/import/importLtiContexts.php.twig', array(
                'testForImport' => $testForImport,
                'tags' => $tags,
                'totalPages' => $totalPages,
                'page' => 0
            ));
        } else if ($_GET['object'] == "exercise") {
            $array = CT\CT_Exercise::findExercisesForImportByDeleteValue($_GET['value']);
            $exercisesForImport = $array['exercises'];
            $totalPages = $array['totalPages'];
            $tags = $_SESSION['tags'];
            echo $twig->render('exercise/import/importLtiContextsExercise.php.twig', array(
                //'coursesForImport' => $coursesForImport
                'exercisesForImport' => $exercisesForImport,
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
            echo $twig->render('exercise/import/importLtiContexts.php.twig', array(
                'testForImport' => $testForImport,
                'tags' => $tags,
                'totalPages' => $totalPages,
                'page' => $page
            ));
        } else if ($_GET['object'] == "exercise") {
            $page = $_GET['page'];
            $array = \CT\CT_Exercise::findExerciseForImportByValue(null, $page);

            $exercisesForImport = $array['exercises'];
            $totalPages = $array['totalPages'];
            $tags = $_SESSION['tags'];
            echo $twig->render('exercise/import/importLtiContextsExercise.php.twig', array(
                'exercisesForImport' => $exercisesForImport,
                'tags' => $tags,
                'totalPages' => $totalPages,
                'page' => $page
            ));
        }
    }
} else {
    header('Location: ' . addSession('../../student-home.php'));
}
