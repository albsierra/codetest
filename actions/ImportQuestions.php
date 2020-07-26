<?php
require_once "../../config.php";
require_once('../dao/CT_DAO.php');

use \Tsugi\Core\LTIX;
use \CT\DAO\CT_DAO;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

if ($USER->instructor) {

    $questions = isset($_POST["question"]) ? $_POST["question"] : false;

    if (!$questions) {
        $_SESSION["error"] = "Question(s) failed to save. Please try again.";
    } else {
        $currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
        $currentTime = $currentTime->format("Y-m-d H:i:s");

        foreach($questions as $question) {
            $origQuestion = $CT_DAO->getQuestionById($question);

            if($origQuestion) {
                $CT_DAO->createQuestion($_SESSION["ct_id"], $origQuestion["question_txt"], $currentTime);
            }
        }

        $_SESSION['success'] = 'Question(s) Saved.';
    }

    header( 'Location: '.addSession('../instructor-home.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}
