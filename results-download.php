<?php
require_once('initTsugi.php');

$loader = new \Twig_Loader_Filesystem('views');
// TODO eliminar debug 0 true y habilitar cache en tmp
$twig = new \Twig_Environment($loader, [
    'debug' => true
]);

$twig->addExtension(new Twig_Extensions_Extension_I18n());

include('views/dao/menu.php');

echo $twig->render('answer/results-download.php', array(
    'OUTPUT' => $OUTPUT,
    'CONTEXT' => $CONTEXT,
    'help' => $help(),
    'menu' => $menu,
));