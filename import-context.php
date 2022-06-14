<?php
require_once('initTsugi.php');
include('views/dao/menu.php'); // for -> $menu

$main = new \CT\CT_Main($_SESSION["ct_id"]);

$importFile = $_FILES['import-file'];
$zip = new ZipArchive;

if ($zip->open($importFile['tmp_name']) !== TRUE) {
    exit("Invalid zip");
}

$mainContent = $zip->getFromName('main.json');
$mainContentArr = json_decode($mainContent, true);

$exercisesContent = $zip->getFromName('exercises/exercises.json');
$exercisesContentArr = json_decode($exercisesContent, true);

$currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
$currentTime = $currentTime->format("Y-m-d H:i:s");
$main = \CT\CT_Main::getMainFromContext($CONTEXT->id, $LINK->id, $USER->id, $currentTime);

// Main update >>

$main->setTitle($mainContentArr['title']);
$main->setSeenSplash($mainContentArr['seen_splash']);
$main->setShuffle($mainContentArr['shuffle']);
$main->setPoints($mainContentArr['points']);
$main->save();

// Main update <<

foreach($exercisesContentArr as $exercise) {

    $exerciseCls = new \CT\CT_ExerciseCode();
    \CT\CT_DAO::setObjectPropertiesFromArray($exerciseCls, $exercise);
    $exerciseCls->setCtId($_SESSION["ct_id"]);
    $exerciseCls->save();
}

$_SESSION['success'] = "Main actualizado";
header( 'Location: '.addSession('index.php')) ;

