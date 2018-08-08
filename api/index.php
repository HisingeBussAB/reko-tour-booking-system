<?php 
mb_internal_encoding("UTF-8");

header("Content-Type: application/json; charset=UTF-8");
header("Accept-Charset: utf-8");
header("Cache-Control: no-cache, must-revalidate");
header("Content-Language: sv-SE");

header( $_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
$response = array('status' => 'OK!', 'message' => 'Använd en versionsspecifik URL text /api/v1/', 'servertime' => time());
echo json_encode($response);