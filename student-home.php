<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

$SetID = $_SESSION["ct_id"];

$main = new \CT\CT_Main($_SESSION["ct_id"]);

$toolTitle = $main->getTitle() ? $main->getTitle() : "Code Test";

$questions = $main->getQuestions();
$totalQuestions = count($questions);

$user = new \CT\CT_User($USER->id);

echo $twig->render('student-home.php', array(
    'OUTPUT' => $OUTPUT,
    'help' => $help(),
    'menu' => $menu,
    'user' => $user,
    'questions' => $questions,
));
