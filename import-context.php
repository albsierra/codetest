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

$questionsContent = $zip->getFromName('questions/questions.json');
$questionsContentArr = json_decode($questionsContent, true);

$codeQuestionsContent = $zip->getFromName('questions/code_questions.json');
$codeQuestionsContentArr = json_decode($codeQuestionsContent, true);

$sqlQuestionsContent = $zip->getFromName('questions/sql_questions.json');
$sqlQuestionsContentArr = json_decode($sqlQuestionsContent, true);

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

foreach($questionsContentArr as $question) {
    $oldId = $question['question_id'];
    $question['question_id'] = null;


    $type = $question['type'];
    $difficulty = $question['difficulty'];
    if($main->getType() == '1'){
        $class = \CT\CT_QuestionCode::class;
    }else{
        $class = \CT\CT_QuestionSQL::class;
    }


    $questionCls = new $class();
    \CT\CT_DAO::setObjectPropertiesFromArray($questionCls, $question);
    if (in_array($type, $CFG->programmingLanguajes)) {
        $array = $getTypeProperty('codeLanguages', $type);
        foreach ( $array as $k => $v){
            if($v['name'] == $type){
                $questionCls->setQuestionLanguage($k);
            }
        }
    }
    $questionCls->setType($type);
    $questionCls->setDifficulty($difficulty);

    $result = $main->saveQuestions([$questionCls]);

    $object = json_decode($result);
    if ($main->getType() == '1') {
        $question1 = \CT\CT_Test::mapObjectToCodeQuestion($object);
    } else {
        $question1 = \CT\CT_Test::mapObjectToSQLQuestion($object);
    }
    $question1->setCtId($_SESSION["ct_id"]);

    $question1->save();
}

$_SESSION['success'] = "Main actualizado";
header( 'Location: '.addSession('index.php')) ;

