<?php
//https://localhost/tsugi/mod/codetest/panic-restart.php

$token = $_GET['token'] ?? null;
$now = date('ymdhis');

$base_token_path = '/tmp';

//clean old token files
$handle = opendir($base_token_path);
while (false !== ($entry = readdir($handle))) {
	$query = 'token-';
	if(substr($entry, 0, strlen($query)) === $query){
		$date = file_get_contents("$base_token_path/$entry");
		if($now >= $date){
			unlink("$base_token_path/$entry");
		}
	}
}
closedir($handle);

if($token == null){
	//---------------------------- BASIC AUTH ----------------------------------

	$AUTH_USER = 'admin';
	$AUTH_PASS = (getenv('TSUGI_PANIC_RESTART_PASSWORD') ?: 'panic');
	header('Cache-Control: no-cache, must-revalidate, max-age=0');
	$has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
	$is_not_authenticated = (
		!$has_supplied_credentials ||
		$_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
		$_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
	);
	if ($is_not_authenticated) {
		header('HTTP/1.1 401 Authorization Required');
		header('WWW-Authenticate: Basic realm="Access denied"');
		exit;
	}

	//--------------------------------------------------------------------------
	
	$token = substr(base64_encode(mt_rand()), 0, 15);
	$min5 = date('ymdhis', strtotime("+5 minutes"));
	
	$token_path = "$base_token_path/token-$token";
	file_put_contents($token_path, $min5);
	
	$proto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['REQUEST_SCHEME'];
	$msg = "{$proto}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}?token=$token";
	//echo "MSG: <a href='$msg'>$msg</a><br/>";
	//echo "TO: ".getenv('TSUGI_PANIC_RESTART_EMAIL')."<br/>";

	// send email
	mail(getenv('TSUGI_PANIC_RESTART_EMAIL'), "Reset password: {$_SERVER['HTTP_HOST']}", $msg);
	
	echo "Enviado";
} else {

	$token_path = "$base_token_path/token-$token";
	
	if(file_exists($token_path)){
		$date = file_get_contents($token_path);
		unlink($token_path);
		if($now < $date){
			echo "OK";
			file_put_contents("/var/pipe/hostpipe", 'restart');
		}
	}
}