<?php
require_once "../../config.php";
require_once('../dao/CT_DAO.php');

use \Tsugi\Core\LTIX;
use \Tsugi\Core\Result;
use \CT\DAO\CT_DAO;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO($PDOX, $p);

$currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
$currentTimeForDB = $currentTime->format("Y-m-d H:i:s");

$studentId = $_POST["student_id"];
$grade = $_POST["grade"];
$ct_id = $_SESSION["ct_id"];

if ($USER->instructor) {
    if (!isset($grade) || !is_numeric($grade)) {
        $_SESSION['error'] = "Invalid Grade.";
    } else {
        $currentGrade = $CT_DAO->getStudentGrade($ct_id, $studentId);
        if (!$currentGrade && $currentGrade !== 0) {
            // No grade yet so create it
            $CT_DAO->createGrade($ct_id, $studentId, $grade, $currentTimeForDB);
        } else {
            // Record exists so update it
            $CT_DAO->updateGrade($ct_id, $studentId, $grade, $currentTimeForDB);
        }

        $_SESSION['success'] = "Grade saved.";

        // Calculate percentage and post
        $percentage = ($grade * 1.0) / $CT_DAO->getPointsPossible($ct_id);

        // Get result record for user
        $resultqry = "SELECT * FROM {$p}lti_result WHERE user_id = :user_id AND link_id = :link_id";
        $arr = array(':user_id' => $studentId, ':link_id' => $LINK->id);
        $row = $PDOX->rowDie($resultqry, $arr);

        Result::gradeSendStatic($percentage, $row);
    }
    header( 'Location: '.addSession('../grade.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}


