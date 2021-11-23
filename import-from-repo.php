<?php
require_once('initTsugi.php');
include('views/dao/menu.php'); // for -> $menu

global $REST_CLIENT_AUTHOR;

$projectId = $_GET['projectId'];

$projectResponse = $REST_CLIENT_AUTHOR->getClient()->request('GET', "projects/$projectId");
$project = $projectResponse->toArray();


$exercisesResponse = $REST_CLIENT_AUTHOR->getClient()->request('GET', 'exercises', [
    'headers' => [
        'project' => $projectId
    ]
]);

$exercises = $exercisesResponse->toArray();


echo $twig->render('pages/exercises-management.php.twig', array(
    "project" => $project,
    "exercises" => $exercises,
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));

