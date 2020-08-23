<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);

if (!$main->getTitle()) {
    $main->setTitle("Code Test");
    $main->save();
}

$questions = $main->getQuestions();
$class = $main->getTypeProperty('class');
$newQuestion = new $class();

// Clear any preview responses if there are questions
if ($questions) \CT\CT_Answer::deleteInstructorAnswers($questions, $CONTEXT->id);

echo $twig->render('instructor_home.php', array(
    'main' => $main,
    'questions' => $questions,
    'newQuestion' => $newQuestion,
    'newQuestionNumber' => $questions ? count($questions)+1 : 1,
    'questionsForImport' => \CT\CT_Question::findQuestionsForImport($USER->id, $_SESSION["ct_id"]),
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));