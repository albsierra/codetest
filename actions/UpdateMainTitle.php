<?php
require_once "../config.php";

use \Tsugi\Core\LTIX;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new \CT\CT_DAO();

if ($USER->instructor) {

    $result = array();

    if (isset($_POST["toolTitle"])) {
        $currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));

        $main = new \CT\CT_Main($_SESSION["ct_id"]);
        $main->setModified($currentTime->format("Y-m-d H:i:s"));
        $main->setTitle($_POST["toolTitle"]);
        $main->setType($_POST["mainType"]);
        $main->save();

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

