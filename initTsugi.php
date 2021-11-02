<?php
require_once('config.php');

use \Tsugi\Core\LTIX;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$CT_DAO = new \CT\CT_DAO();

$help = function() {
    if (file_exists('views/help/' . basename($_SERVER['PHP_SELF']))) {
        $filename = basename($_SERVER['PHP_SELF']);
    } else {
        $filename = 'no-help.php';
    }
    return $filename;
};

$loader = new \Twig\Loader\FilesystemLoader($CFG->twig['viewsPath']);

$twig = new \Twig\Environment($loader, [
    'debug' => false, // $CFG->twig['debug'],
    'cache' => $CFG->twig['cachePath'],
]);
