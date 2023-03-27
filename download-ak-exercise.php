<?php
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

// include('views/dao/menu.php'); // for -> $menu
include('util/Functions.php');

$exerciseId = $_GET['exerciseId'];

echo downloadAkExercise($exerciseId);
