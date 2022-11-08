<?php
require_once('initTsugi.php');
include('views/dao/menu.php');

$SetID = $_SESSION["ct_id"];
$main = new \CT\CT_Main($_SESSION["ct_id"]);
$toolTitle = $main->getTitle() ? $main->getTitle() : "Code Test";

$exercises = $main->getExercises();
$totalExercises = count($exercises);
$currentExerciseNumber = isset($_GET['exerciseNum']) ? $_GET['exerciseNum'] : 1;
$student_language = $_SESSION["lti"]["user_locale"];
$user_id = $_SESSION["lti"]["user_id"];
$user = new \CT\CT_User($user_id);

if(empty($student_language)){
    $student_language = "en";
}

if($totalExercises > 0){
    
    $firstExerciseAkId = $exercises[$currentExerciseNumber - 1]->getAkId();
    $firsExerciseId = $exercises[$currentExerciseNumber - 1]->getExerciseId();
    $exerciseTestsResponse = $REST_CLIENT_REPO->getClient()->request('GET', "api/exercises/$firstExerciseAkId/tests");
    $exerciseStatementsResponse = $REST_CLIENT_REPO->getClient()->request('GET', "api/exercises/$firstExerciseAkId/statements/$student_language");
    $statements_list = $exerciseStatementsResponse->toArray();
    $testsList = $exerciseTestsResponse->toArray();
}

if(($statements_list[0] != null)){
    $statement_value = $statements_list[0]["statementValue"];
}else{
    $exerciseStatementsResponse = $REST_CLIENT_REPO->getClient()->request('GET', "api/exercises/$firstExerciseAkId/statements/en");
    $statements_list = $exerciseStatementsResponse->toArray();
    $statement_value = $statements_list[0]["statementValue"];
    if($statements_list[0] == null){
        $statement_value = getStatemets($student_language,$REST_CLIENT_REPO,$firstExerciseAkId);
    }
}

echo $twig->render('pages/student-view.php.twig', array(
    'OUTPUT' => $OUTPUT,
    'help' => $help(),
    'menu' => $menu,
    'user' => $user,
    //return true when have a correct usage if not returns false
    'correctUsage' => $user->getHaveCorrectUsage($firsExerciseId,$user_id),
    'exercises' => $exercises,
    'statementValue' =>$statement_value,
    'testsList' => $testsList,
    'totalExercises' => $totalExercises,
    'currentExerciseNumber' => $currentExerciseNumber,
    'exerciseNum' => $currentExerciseNumber,
    'main' => $main,
    'validatorService' => $validatorService,
    'CFG' => $CFG,
));

function getStatemets($student_language,$REST_CLIENT_REPO,$firstExerciseAkId){

    $exerciseStatementsResponse = $REST_CLIENT_REPO->getClient()->request('GET', "api/exercises/$firstExerciseAkId/statements");
    $statements_list = $exerciseStatementsResponse->toArray();
    $statement_value;
    $is_english = false;

    foreach($statements_list as $statement_Array => $statement_array_value){

        if($student_language == $statement_array_value["nat_lang"]){

            $statement_value = $statement_array_value["statementValue"];
            $is_english = false;
            break;

        }else if($student_language != $statement_array_value["nat_lang"]){

            if($statement_array_value["nat_lang"] == "en"){

                $statement_value_en = $statement_array_value["statementValue"];
                $is_english = true;

            }else{

                $statement_value = $statement_array_value["statementValue"];
            }
        }
    }

    if($is_english){

        $statement_value =  $statement_value_en;
    }
    
    return $statement_value;
}