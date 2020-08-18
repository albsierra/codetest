<?php
require_once('initTsugi.php');

$loader = new \Twig_Loader_Filesystem('views');
// TODO eliminar debug 0 true y habilitar cache en tmp
$twig = new \Twig_Environment($loader, [
    'debug' => true
]);

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);

if (!$main->getTitle()) {
    $main->setTitle("Code Test");
    $main->save();
}

$questions = $main->getQuestions();

// Clear any preview responses if there are questions
if ($questions) \CT\CT_Answer::deleteInstructorAnswers($questions, $CONTEXT->id);

echo $twig->render('instructor_home.php', array(
    'phpsessid' => $_GET["PHPSESSID"],
    'main' => $main,
    'questions' => $questions,
    'newQuestionNumber' => $questions ? count($questions)+1 : 1,
    'questionsForImport' => \CT\CT_Question::findQuestionsForImport($USER->id, $_SESSION["ct_id"]),
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));