<?php
require_once('../initTsugi.php');

$main = new \CT\CT_Main($_SESSION["ct_id"]);

if ( $USER->instructor ) {

    $main->setSeenSplash(true);
    $main->save();

    header( 'Location: '.addSession('../instructor-home.php') ) ;
} else {
    header( 'Location: '.addSession('../student-home.php') ) ;
}
