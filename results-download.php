<?php
require_once('initTsugi.php');

include('views/dao/menu.php');

echo $twig->render('answer/results-download.php.twig', array(
    'OUTPUT' => $OUTPUT,
    'CONTEXT' => $CONTEXT,
    'help' => $help(),
    'menu' => $menu,
));