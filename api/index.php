<?php 

namespace RekoBooking;

require __DIR__ . '/config/config.php';
if (DEBUG_MODE) {
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
}

if (LAN_LOCK) {
  if (!preg_match("/^192\.168\.\d{0,3}\.\d{0,3}$/", $_SERVER["REMOTE_ADDR"])) {
    header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin:" . FULL_DOMAIN);
    $a = array('response' => '401: Unauthorized');
    echo json_encode($a);
    die();
  }
  
}


$loader = require __DIR__ . '/vendor/autoload.php';
$loader->add('RekoBooking', __DIR__);
$loader->addPsr4('RekoBooking\\', __DIR__);


$router = new \AltoRouter();
$router->setBasePath('/beta/api');
$router->map('GET','/', function() {
	require __DIR__ . '/home.php';
}, 'home');

$match = $router->match();

if( $match && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin:" . FULL_DOMAIN);
    $a = array('response' => '404: Not Found');
    echo json_encode($a);
    die();
}