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


$formFields = [
    'PHPSESSID' => session_id(),
    'exercise' => DataPart::fromPath($filename),
    'sessionLanguage' =>$TSUGI_LOCALE
];
$formData = new FormDataPart($formFields);

$uploadResponse = $REST_CLIENT_REPO->getClient()->request('POST', 'api/exercises/import-file', [
    'headers' => $formData->getPreparedHeaders()->toArray(),
    'body' => $formData->bodyToIterable(),
    
]);


$uploadResponseCode = $uploadResponse->getStatusCode();
$uploadResponseBody = $uploadResponse->getContent();

unlink($filename);

echo $uploadResponseBody;
