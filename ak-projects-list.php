<?php
require_once('initTsugi.php');
include('views/dao/menu.php'); // for -> $menu

global $REST_CLIENT_AUTHOR;


$response = $REST_CLIENT_AUTHOR->getClient()->request('GET', 'projects');

$projects = $response->toArray();

// var_dump($response);
// var_dump($projects);die;


echo $twig->render('pages/ak-projects-list.php.twig', array(
    'projects' => $projects,
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));

