<?php

ini_set("max_execution_time",0);
// Mailform Pro _HASH() produces a 22-character, URL-safe MD5 digest.
$key = isset($_GET['key']) ? $_GET['key'] : null;
if (!is_string($key) || !preg_match('/\A[A-Za-z0-9_-]{22}\z/', $key)) {
    http_response_code(400);
    exit('Invalid request.');
}
$tempdir = realpath(__DIR__ . '/data/temp');
$candidate = $tempdir !== false ? $tempdir . DIRECTORY_SEPARATOR . $key . '.php' : '';
$tmpinclude = $candidate !== '' ? realpath($candidate) : false;
if ($tmpinclude === false || dirname($tmpinclude) !== $tempdir ||
    is_link($candidate) || !is_file($tmpinclude) || !is_readable($tmpinclude)) {
    http_response_code(404);
    exit('Request not found.');
}
include $tmpinclude;
error_reporting(E_ALL);

require_once "../../../../vendor/autoload.php";
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;

$app = new DropboxApp($AppKey, $SecretKey, $AccessToken);
$dropbox = new Dropbox($app);

for($i=0;$i<count($UploadFiles);$i++){
	if(file_exists("." . $UploadFiles[$i])){
		$dropboxFile = new DropboxFile(__DIR__ . $UploadFiles[$i]);
		$file = $dropbox->simpleUpload($dropboxFile, $UploadFileNames[$i], ['autorename' => true]);
		if($file_clear){
			unlink("." . $UploadFiles[$i]);
		}
	}
}
unlink($tmpinclude);
header("Location: $thanks_url");
