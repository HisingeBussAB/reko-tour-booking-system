<?php 
mb_internal_encoding("UTF-8");
date_default_timezone_set ('Europe/Stockholm');

header("Content-Type: application/json; charset=UTF-8");
header("Accept-Charset: utf-8");
header("Cache-Control: no-cache, must-revalidate");
header("Content-Language: sv-SE");
header('Access-Control-Allow-Methods: GET');
header('X-Robots-Tag: noindex, nofollow');
header( $_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
$response = array('status' => 'OK!', 'message' => 'Använd en versionsspecifik URL', 'servertime' => time());
echo json_encode($response);



