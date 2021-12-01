<?php
require_once('initTsugi.php');
include('views/dao/menu.php'); // for -> $menu

global $REST_CLIENT_AUTHOR;


$response = $REST_CLIENT_AUTHOR->getClient()->request('GET', 'projects');

$projects = $response->toArray();


echo $twig->render('pages/exercises-management.php.twig', array(
    'projects' => $projects,
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));

