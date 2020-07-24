<?php
require_once "../../config.php";
require_once "../dao/CT_DAO.php";

use \Tsugi\Core\LTIX;
use \CT\DAO\CT_DAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new CT_DAO($PDOX, $p);

if ( $USER->instructor ) {

    $CT_DAO->markAsSeen($_SESSION["ct_id"]);

    header( 'Location: '.addSession('../instructor-home.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}
