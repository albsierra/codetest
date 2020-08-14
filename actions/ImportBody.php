<?php
require_once "../initTsugi.php";

$loader = new \Twig_Loader_Filesystem('../views');
// TODO eliminar debug 0 true y habilitar cache en tmp
$twig = new \Twig_Environment($loader, [
    'debug' => true
]);

if ($USER->instructor) {
    $questionsForImport = \CT\CT_Question::findQuestionsForImport($USER->id, $_SESSION["ct_id"]);
    // Create new question markup
    $questionMap = array();
    foreach ($questionsForImport as $question) {
        if (!array_key_exists($question["sitetitle"], $questionMap)) {
            $questionMap[$question["sitetitle"]] = array();
        }
        if (!array_key_exists($question["tooltitle"], $questionMap[$question["sitetitle"]])) {
            $questionMap[$question["sitetitle"]][$question["tooltitle"]] = array();
        }
        array_push($questionMap[$question["sitetitle"]][$question["tooltitle"]], $question);
    }

    //ob_start();
    //$OUTPUT->buffer=true;
    echo $twig->render('question/importBody.html', array(
        'questionsForImport' => $questionsForImport,
        'questionMap' => $questionMap,

    ));
    //header('Content-Type: application/json');

    //echo json_encode($questionsForImport, JSON_HEX_QUOT | JSON_HEX_TAG);

    //exit;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}
