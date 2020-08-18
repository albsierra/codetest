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
    'help' => __('
<h4>Downloading Results</h4>
<p>Click on the link to download an Excel file with all of the results for this Code Test.</p>'),
    'menu' => $menu,
));