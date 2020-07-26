<?php
require_once "../../config.php";
require_once('../dao/CT_DAO.php');

use \Tsugi\Core\LTIX;
use \CT\DAO\CT_DAO;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO();

if ($USER->instructor) {

    $result = array();

    if (isset($_POST["toolTitle"])) {
        $currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
        $currentTime = $currentTime->format("Y-m-d H:i:s");

        $CT_DAO->updateMainTitle($_SESSION["ct_id"], $_POST["toolTitle"], $currentTime);

        $_SESSION['success'] = "Title saved.";
    } else {
        $_SESSION['error'] = "Title failed to save. Please try again.";
    }

    $OUTPUT->buffer=true;
    $result["flashmessage"] = $OUTPUT->flashMessages();

    header('Content-Type: application/json');

    echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG);

    exit;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}

