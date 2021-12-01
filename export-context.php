<?php
require_once('initTsugi.php');
include('views/dao/menu.php'); // for -> $menu
global $REST_CLIENT_REPO;

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$exercises = $main->getExercises();

// var_dump();die;


// Get exercise subtypes - > > >
$exercise_ids = array_map(function($el){
    return "{$el->getExerciseId()}";
}, $exercises);
if(empty($exercise_ids)){
    echo 'ERROR: No exercises';
    die;
}

$exercisesMetaRequest = $REST_CLIENT_REPO->getClient()->request('POST','api/exercises/getAllExercises', [
    'body' => [
        'exerciseIds' => join(",", $exercise_ids)
    ]
]);
$exercisesMeta = $exercisesMetaRequest->toArray();
$exercisesMetaMap = array_reduce($exercisesMeta,function($acc, $el){
    $acc[$el['id']] = $el;
    return $acc;
},[]);

$in = str_repeat('?,', count($exercise_ids) - 1) . '?';

// --- Code exercises --- //
$iteration = 1;

$queryCodeQ = \CT\CT_DAO::getQuery('main', 'codeExercisesExport');
$queryCodeQ = str_replace(":exercises_in", $in, $queryCodeQ);
$statementCodeQ = $queryCodeQ['PDOX']->prepare($queryCodeQ['sentence']);
foreach($exercise_ids as $exerciseId){
    $statementCodeQ->bindValue($iteration, $exerciseId);
    $iteration++;
}
$statementCodeQ->execute();

$statementResultCodeQ = $statementCodeQ->fetchAll(PDO::FETCH_ASSOC);

$codeExercises = \CT\CT_DAO::createObjectFromArray(\CT\CT_ExerciseCode::class, $statementResultCodeQ);

// --- SQL exercises --- //
$iteration = 1;

$querySqlQ = \CT\CT_DAO::getQuery('main', 'sqlExercisesExport');
$querySqlQ = str_replace(":exercises_in", $in, $querySqlQ);
$statementSqlQ = $querySqlQ['PDOX']->prepare($querySqlQ['sentence']);
foreach($exercise_ids as $exerciseId){
    $statementSqlQ->bindValue($iteration, $exerciseId);
    $iteration++;
}
$statementSqlQ->execute();

$statementResultSqlQ = $statementSqlQ->fetchAll(PDO::FETCH_ASSOC);

$sqlExercises = \CT\CT_DAO::createObjectFromArray(\CT\CT_ExerciseSQL::class, $statementResultSqlQ);
// Get exercise subtypes - < < <

$clone_main = clone $main;
$clone_main->setUserId(null);
$clone_main->setContextId(null);
$clone_main->setLinkId(null);
$clone_main->setCtId(null);

$toArrayWithMeta = function ($data) use ($exercisesMetaMap){
    $arr = json_decode(json_encode($data), true);
    $resultArr = [];

    foreach($arr as $item){
        $auxObj = $exercisesMetaMap[$item['exercise_id']];
        $merge_object = (array) array_merge((array) $item, (array) $auxObj);
        array_push($resultArr, $merge_object);
    }

    $result = json_decode(json_encode($resultArr), true);
    return $result;
};


$clone_exercises = array_map(function($el){
    $el->setCtId(null);
    return $el;
} ,$exercises);
$clone_exercises = json_decode(json_encode($clone_exercises), true);


$clone_qs_code = array_map(function($el){
    $el->setCtId(null);
    return $el;
} ,$codeExercises);
$clone_qs_code = json_decode(json_encode($clone_qs_code), true);


$clone_qs_sql = array_map(function($el){
    $el->setCtId(null);
    return $el;
} ,$sqlExercises);
$clone_qs_sql = json_decode(json_encode($clone_exercises), true);

$exercisesMappedWithMeta = $toArrayWithMeta($clone_exercises);

// ---------------------------------------

$mainFilename = "main.json";
$fileHandler = fopen($mainFilename, 'w');
fwrite($fileHandler, json_encode($clone_main, JSON_PRETTY_PRINT));

$exercisesFilename = "exercises.json";
$fileHandler = fopen($exercisesFilename, 'w');
fwrite($fileHandler, json_encode($exercisesMappedWithMeta, JSON_PRETTY_PRINT));

$codeFilename = "code_exercises.json";
$fileHandler = fopen($codeFilename, 'w');
fwrite($fileHandler, json_encode($clone_qs_code, JSON_PRETTY_PRINT));

$sqlFilename = "sql_exercises.json";
$fileHandler = fopen($sqlFilename, 'w');
fwrite($fileHandler, json_encode($clone_qs_sql, JSON_PRETTY_PRINT));

/// -------------------------------------

$timeFormat = new DateTime('now', new DateTimeZone("Europe/Madrid"));
$timeFormat = $timeFormat->format('Ymd_Hi');

$zip = new ZipArchive();
$zipFinalFilename = "Codetest_export_$timeFormat [{$CONTEXT->id}]-{$CONTEXT->title}.zip";
$openZipFile = $zip->open($zipFinalFilename, ZipArchive::CREATE);
if(!$openZipFile) {
    exit("cannot open <$zipFinalFilename>\n");
}
$zip->addFile($mainFilename,"main.json");
$zip->addFile($exercisesFilename, "exercises/".$exercisesFilename);
$zip->addFile($codeFilename, "exercises/".$codeFilename);
$zip->addFile($sqlFilename, "exercises/".$sqlFilename);

$zip->close();

unlink($mainFilename);
unlink($exercisesFilename);
unlink($codeFilename);
unlink($sqlFilename);


$zipFilename_basename = basename($zipFinalFilename);
$zipFilename_filesize = filesize($zipFinalFilename);


header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="'.$zipFinalFilename.'"');
header('Content-Length: '.$zipFilename_filesize);
header('Expires: 0');
header('Pragma: public');
header('Cache-Control: must-revalidate');
header('Content-Description: File Transfer');

flush();
readfile($zipFinalFilename);
unlink($zipFinalFilename);

