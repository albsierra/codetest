<?php
require_once('initTsugi.php');
include('views/dao/menu.php'); // for -> $menu
global $REST_CLIENT_REPO;

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$questions = $main->getQuestions();

// var_dump();die;


// Get question subtypes - > > >
$question_ids = array_map(function($el){
    return "{$el->getQuestionId()}";
}, $questions);
if(empty($question_ids)){
    echo 'ERROR: No questions';
    die;
}

$questionsMetaRequest = $REST_CLIENT_REPO->getClient()->request('POST','api/questions/getAllQuestions', [
    'body' => [
        'questionIds' => join(",", $question_ids)
    ]
]);
$questionsMeta = $questionsMetaRequest->toArray();
$questionsMetaMap = array_reduce($questionsMeta,function($acc, $el){
    $acc[$el['id']] = $el;
    return $acc;
},[]);

$in = str_repeat('?,', count($question_ids) - 1) . '?';

// --- Code questions --- //
$iteration = 1;

$queryCodeQ = \CT\CT_DAO::getQuery('main', 'codeQuestionsExport');
$queryCodeQ = str_replace(":questions_in", $in, $queryCodeQ);
$statementCodeQ = $queryCodeQ['PDOX']->prepare($queryCodeQ['sentence']);
foreach($question_ids as $questionId){
    $statementCodeQ->bindValue($iteration, $questionId);
    $iteration++;
}
$statementCodeQ->execute();

$statementResultCodeQ = $statementCodeQ->fetchAll(PDO::FETCH_ASSOC);

$codeQuestions = \CT\CT_DAO::createObjectFromArray(\CT\CT_QuestionCode::class, $statementResultCodeQ);

// --- SQL questions --- //
$iteration = 1;

$querySqlQ = \CT\CT_DAO::getQuery('main', 'sqlQuestionsExport');
$querySqlQ = str_replace(":questions_in", $in, $querySqlQ);
$statementSqlQ = $querySqlQ['PDOX']->prepare($querySqlQ['sentence']);
foreach($question_ids as $questionId){
    $statementSqlQ->bindValue($iteration, $questionId);
    $iteration++;
}
$statementSqlQ->execute();

$statementResultSqlQ = $statementSqlQ->fetchAll(PDO::FETCH_ASSOC);

$sqlQuestions = \CT\CT_DAO::createObjectFromArray(\CT\CT_QuestionSQL::class, $statementResultSqlQ);
// Get question subtypes - < < <

$clone_main = clone $main;
$clone_main->setUserId(null);
$clone_main->setContextId(null);
$clone_main->setLinkId(null);
$clone_main->setCtId(null);

$toArrayWithMeta = function ($data) use ($questionsMetaMap){
    $arr = json_decode(json_encode($data), true);
    $resultArr = [];

    foreach($arr as $item){
        $auxObj = $questionsMetaMap[$item['question_id']];
        $merge_object = (array) array_merge((array) $item, (array) $auxObj);
        array_push($resultArr, $merge_object);
    }

    $result = json_decode(json_encode($resultArr), true);
    return $result;
};


$clone_questions = array_map(function($el){
    $el->setCtId(null);
    return $el;
} ,$questions);
$clone_questions = json_decode(json_encode($clone_questions), true);


$clone_qs_code = array_map(function($el){
    $el->setCtId(null);
    return $el;
} ,$codeQuestions);
$clone_qs_code = json_decode(json_encode($clone_qs_code), true);


$clone_qs_sql = array_map(function($el){
    $el->setCtId(null);
    return $el;
} ,$sqlQuestions);
$clone_qs_sql = json_decode(json_encode($clone_questions), true);

$questionsMappedWithMeta = $toArrayWithMeta($clone_questions);

// ---------------------------------------

$mainFilename = "main.json";
$fileHandler = fopen($mainFilename, 'w');
fwrite($fileHandler, json_encode($clone_main, JSON_PRETTY_PRINT));

$questionsFilename = "questions.json";
$fileHandler = fopen($questionsFilename, 'w');
fwrite($fileHandler, json_encode($questionsMappedWithMeta, JSON_PRETTY_PRINT));

$codeFilename = "code_questions.json";
$fileHandler = fopen($codeFilename, 'w');
fwrite($fileHandler, json_encode($clone_qs_code, JSON_PRETTY_PRINT));

$sqlFilename = "sql_questions.json";
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
$zip->addFile($questionsFilename, "questions/".$questionsFilename);
$zip->addFile($codeFilename, "questions/".$codeFilename);
$zip->addFile($sqlFilename, "questions/".$sqlFilename);

$zip->close();

unlink($mainFilename);
unlink($questionsFilename);
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

