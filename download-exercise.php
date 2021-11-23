<?php
require_once('initTsugi.php');
include('views/dao/menu.php'); // for -> $menu
include('util/Functions.php');

global $REST_CLIENT_AUTHOR;

$exerciseId = $_GET['exerciseId'];

$exerciseFileResponse = $REST_CLIENT_AUTHOR->getClient()->request('GET', "exercises/$exerciseId/export?format=zip", [
    'buffer' => false,
]);

if (200 !== $exerciseFileResponse->getStatusCode()) {
    throw new \Exception('Failed to create a request');
}
$headers = $exerciseFileResponse->getHeaders();
$filename = getFilenameFromDisposition($headers['content-disposition'][0]);


$fileHandler = fopen($filename, 'w');
foreach ($REST_CLIENT_AUTHOR->getClient()->stream($exerciseFileResponse) as $chunk) {
    fwrite($fileHandler, $chunk->getContent());
}

// header("Cache-Control: public");
// header("Content-Description: File Transfer");
// header("Content-Disposition: attachment; filename=$filename");
// header("Content-Type: application/zip");
// header("Content-Transfer-Encoding: binary");
// read the file from disk
// readfile($file);

// header('Content-Description: File Transfer');
// header('Content-Type: application/octet-stream');
// header('Content-Disposition: attachment; filename="'.basename($filename).'"');
// header('Expires: 0');
// header('Cache-Control: must-revalidate');
// header('Pragma: public');
// header('Content-Length: ' . filesize($filename));
// flush(); // Flush system output buffer
// readfile($filepath);


$_SESSION['success'] = "File: $filename - Downloaded successfully";

$response = $REST_CLIENT_AUTHOR->getClient()->request('GET', 'projects');
$projects = $response->toArray();


echo $twig->render('pages/questions-management.php.twig', array(
    'projects' => $projects,
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));
