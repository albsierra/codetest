<?php
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

require_once('initTsugi.php');
include('views/dao/menu.php'); // for -> $menu
include('util/Functions.php');



global $REST_CLIENT_AUTHOR, $REST_CLIENT_REPO;

$exerciseId = $_GET['exerciseId'];

$exerciseFileResponse = $REST_CLIENT_AUTHOR->getClient()->request('GET', "exercises/$exerciseId/export?format=zip", [
    'buffer' => false,
]);

if (200 !== $exerciseFileResponse->getStatusCode()) {
    throw new \Exception('Request to AK failed');
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


$formFields = [
    'exercise' => DataPart::fromPath($filename),
];
$formData = new FormDataPart($formFields);


$uploadResponse = $REST_CLIENT_REPO->getClient()->request('POST', 'api/exercises/import-file', [
    'headers' => $formData->getPreparedHeaders()->toArray(),
    'body' => $formData->bodyToIterable(),
]);


$uploadResponseCode = $uploadResponse->getStatusCode();
unlink($filename);


if($uploadResponseCode == 200){
    $_SESSION['success'] = "File: $filename - Imported to repo successfully";
}else{
    $_SESSION['error'] = "Failed to import";
}

// Load project list again

$response = $REST_CLIENT_AUTHOR->getClient()->request('GET', 'projects');
$projects = $response->toArray();


echo $twig->render('pages/ak-projects-list.php.twig', array(
    'projects' => $projects,
    'OUTPUT' => $OUTPUT,
    'CFG' => $CFG,
    'menu' => $menu,
    'help' => $help(),
));
