<?php
require_once('initTsugi.php');

$loader = new \Twig_Loader_Filesystem('views');
// TODO eliminar debug 0 true y habilitar cache en tmp
$twig = new \Twig_Environment($loader, [
    'debug' => true
]);

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$pointsPossible = $main->getPoints();

$questions = $main->getQuestions();
$totalQuestions = count($questions);

echo $twig->render('grade/grade.php', array(
    'maxPoints' => $pointsPossible,
    'totalQuestions' => $totalQuestions,
    'students' => getStudents($main),
    'OUTPUT' => $OUTPUT,
    'menu' => $menu,
));

function getStudents($main) {
    $studentsUnordered = \CT\CT_User::getUsersWithAnswers($main->getCtId());
    $studentAndDate = array();
    foreach($studentsUnordered as $student) {
        $studentAndDate[$student->getMostRecentAnswerDate($main->getCtId())] = $student;
    }
// Sort students by mostRecentDate desc
    krsort($studentAndDate);
    $students = array();
    $index = 0;
    foreach ($studentAndDate as $date => $user) {
        $mostRecentDate = new DateTime($date);
        $students[$index]['user'] = $user;
        $students[$index]['isInstructor'] = $user->isInstructor($main->getContextId());
        if (!$students[$index]['isInstructor']) {
            $students[$index]['formattedMostRecentDate'] = $mostRecentDate->format("m/d/y") . " | " . $mostRecentDate->format("h:i A");
            $students[$index]['numberAnswered'] = $user->getNumberQuestionsAnswered($main->getCtId());
            $students[$index]['grade'] = $user->getGrade($main->getCtId())->getGrade();
        }
        $index++;
    }
    return $students;
}