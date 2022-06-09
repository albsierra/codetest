<?php
require_once('initTsugi.php');
include('views/dao/menu.php');

if (!$USER->instructor) {
    header('Location: ' . addSession('../student-home.php'));
    exit;
}

$main = new \CT\CT_Main($_SESSION["ct_id"]);
$owner = $_SESSION["lti"]["user_displayname"];
$language = array_keys($_GET, 'language') ? $_GET['language'] : "PHP";
$newExercise = new CT\CT_ExerciseCode();
if (isset($_GET['exerciseId'])) {
    $newExercise = $newExercise->findExerciseForImportId($_GET['exerciseId']);

    if ($newExercise->getCtId() != $_SESSION["ct_id"]) {
        $newExercise = new CT\CT_ExerciseCode();
    }else{
        $libraries = $newExercise->findLibrariesForExerciseId($newExercise->getAkId());
        $librariesNames = array();
        foreach ($libraries as $key => $value) {
            $librariesNames[$key]["pathname"] = $value->pathname;
            $librariesNames[$key]["id"] = $value->id;
        }
        $newExercise->setLibraries($librariesNames);

        $newExercise->setExerciseOutputTest((array) $newExercise->getExerciseOutputTest());
        $newExercise->setExerciseInputTest((array) $newExercise->getExerciseInputTest());
        $exerciseReplace = ($newExercise->getNumberAnswers() == 0) ? $newExercise->getAkId() . ":true": $newExercise->getAkId() . ":false";
    }

}

echo $twig->render('pages/exercise-creation.php.twig', array(
    'main' => $main,
    'type' => $language,
    'owner' => $owner,
    'newExercise' => $newExercise,
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'validatorService' => $validatorService,
    'help' => $help(),
    'exerciseReplace' => isset($exerciseReplace) ? $exerciseReplace : "",
));

