<?php
require_once "../initTsugi.php";

use \Tsugi\Core\Result;

$studentId = $_POST["student_id"];
$grade = $_POST["grade"];
$ct_id = $_SESSION["ct_id"];

if ($USER->instructor) {
    if (!isset($grade) || !is_numeric($grade)) {
        $_SESSION['error'] = "Invalid Grade.";
    } else {
        $main = new \CT\CT_Main($ct_id);
        $main->gradeUser($studentId, $grade);
    }
    header( 'Location: '.addSession('../grade.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}


