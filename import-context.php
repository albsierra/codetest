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

$codeExercisesContent = $zip->getFromName('exercises/code_exercises.json');
$codeExercisesContentArr = json_decode($codeExercisesContent, true);

$sqlExercisesContent = $zip->getFromName('exercises/sql_exercises.json');
$sqlExercisesContentArr = json_decode($sqlExercisesContent, true);

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

$getTypeProperty = function ($property, $language) {
    global $CFG;
    if (in_array($language, $CFG->programmingLanguajes)) {
        return $CFG->CT_Types['types']['programming'][$property];
    } else {
        return $CFG->CT_Types['types'][$language][$property];
    }
};

$setCtIdFromMain = function($el) use ($main) {
    $el->setCtId($main->getCtId());
    return $el;
};

foreach($exercisesContentArr as $exercise) {
    $oldId = $exercise['exercise_id'];
    $exercise['exercise_id'] = null;


    $type = $exercise['type'];
    $difficulty = $exercise['difficulty'];
    if($main->getType() == '1'){
        $class = \CT\CT_ExerciseCode::class;
    }else{
        $class = \CT\CT_ExerciseSQL::class;
    }


    $exerciseCls = new $class();
    \CT\CT_DAO::setObjectPropertiesFromArray($exerciseCls, $exercise);
    if (in_array($type, $CFG->programmingLanguajes)) {
        $array = $getTypeProperty('codeLanguages', $type);
        foreach ( $array as $k => $v){
            if($v['name'] == $type){
                $exerciseCls->setExerciseLanguage($k);
            }
        }
    }
    $exerciseCls->setType($type);
    $exerciseCls->setDifficulty($difficulty);

    $result = $main->saveExercises([$exerciseCls]);

    $object = json_decode($result);
    if ($main->getType() == '1') {
        $exercise1 = \CT\CT_Test::mapObjectToCodeExercise($object);
    } else {
        $exercise1 = \CT\CT_Test::mapObjectToSQLExercise($object);
    }
    $exercise1->setCtId($_SESSION["ct_id"]);

    $exercise1->save();
}

$_SESSION['success'] = "Main actualizado";
header( 'Location: '.addSession('index.php')) ;

