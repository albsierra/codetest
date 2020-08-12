<?php
require_once "../config.php";

use \Tsugi\Core\LTIX;
use \Tsugi\Core\Result;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new \CT\CT_DAO();

$studentId = $_POST["student_id"];
$grade = $_POST["grade"];
$ct_id = $_SESSION["ct_id"];

if ($USER->instructor) {
    if (!isset($grade) || !is_numeric($grade)) {
        $_SESSION['error'] = "Invalid Grade.";
    } else {
        $student = new \CT\CT_User($studentId);
        $currentGrade = $student->getGrade($ct_id);
        $currentGrade->setCtId($ct_id);
        $currentGrade->setUserId($student->getUserId());
        $currentGrade->setGrade($grade);
        $currentGrade->save();

        $_SESSION['success'] = "Grade saved.";

        // Calculate percentage and post
        $main = new \CT\CT_Main($ct_id);
        $percentage = ($grade * 1.0) / $main->getPoints();

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


