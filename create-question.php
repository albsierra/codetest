<?php
require_once('initTsugi.php');
include('views/dao/menu.php');

if (!$USER->instructor) {
    header('Location: ' . addSession('../student-home.php'));
    exit;
}

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$language = array_keys($_GET, 'language') ? $_GET['language'] : "PHP";
$newQuestion = new CT\CT_Question();

echo $twig->render('pages/question-creation.php.twig', array(
    'main' => $main,
    'type' => $language,
    'newQuestion' => $newQuestion,
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));

