<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);

if (!$main->getTitle()) {
    $main->setTitle("Code Test");
    $main->save();
}

$questions = $main->getQuestions();

$typeNames = array_keys($CFG->CT_Types['types']);
$type = $typeNames[$main->getType()];
$typeName = $CFG->CT_Types['types'][$type]['name'];

// Clear any preview responses if there are questions
if ($questions) \CT\CT_Answer::deleteInstructorAnswers($questions, $CONTEXT->id);

$usageCount = 0;
if($REST_CLIENT_REPO->getIsOnline()){
    try {
        $usagesCountRequest = $REST_CLIENT_REPO->
                                    getClient()->
                                    request('GET','api/usage/usagesCount', [
                                        'query' => [
                                            'ctid' => $main->getCtId()
                                        ]
                                    ]);
        $usageCount = $usagesCountRequest->getContent();
    } catch (Exception $ex) {
        $errorMessage = "Couldn't fetch usages";
        logg($ex->getMessage());
        logg($errorMessage);
        $_SESSION["error"] = $errorMessage;
    }
}



$grades = $main->getGradesCtId();


$gradesCount = 0;
try {
    $grades = $main->getGradesCtId();
    $gradesCount = count($grades);
} catch (Exception $ex) {
    $errorMessage = "Couldn't fetch grades";
    logg($ex->getMessage());
    logg($errorMessage);
    $_SESSION["error"] = $errorMessage;
}


$gradesMap = array_reduce($grades,function($acc, $el){
    $acc['min'] = min(
            $el->getGrade(),
            array_key_exists("min", $acc) ? $acc['min'] : $el->getGrade()
    );
    $acc['max'] = max(
        $el->getGrade(),
        array_key_exists("max", $acc) ? $acc['max'] : $el->getGrade()
    );
    $acc['avg'] = (array_key_exists("avg", $acc) ? $acc['avg'] : 0) + $el->getGrade();
    return $acc;
},[]);
if(array_key_exists('avg',$gradesMap)){
    $gradesMap['avg'] = $gradesMap['avg'] / $gradesCount;
}

// var_dump($gradesMap);die;

echo $twig->render('instructor-home.php.twig', array(
    'main' => $main,
    'type' => $typeName,
    'questions' => $questions,
    'usagesCount' => $usageCount,
    'gradesCount' => $gradesCount,
    'gradesMap' => $gradesMap,
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));

