<?php
require_once('initTsugi.php');

$loader = new \Twig_Loader_Filesystem('views');
// TODO eliminar debug 0 true y habilitar cache en tmp
$twig = new \Twig_Environment($loader, [
    'debug' => true
]);

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$questions = $main->getQuestions();

$students = \CT\CT_User::getUsersWithAnswers($_SESSION["ct_id"]);
$studentAndDate = array();
foreach($students as $student) {
    $studentAndDate[$student->getUserId()] = new DateTime($student->getMostRecentAnswerDate($_SESSION["ct_id"]));
}

// Sort students by mostRecentDate desc
arsort($studentAndDate);

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$questions = $main->getQuestions();
$totalQuestions = count($questions);

echo $twig->render('answer/results-student.php', array(
    'OUTPUT' => $OUTPUT,
    'help' => $help(),
    'menu' => $menu,
    'questions' => $questions,
    'totalQuestions' => $totalQuestions,
    'students' => $main->getStudentsOrderedByDate(),
));
