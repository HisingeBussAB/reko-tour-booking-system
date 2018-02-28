<?php 

namespace RekoBooking;

  require __DIR__ . '/config/config.php';

  if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
  }

  ob_start(null, 0);
  header("Content-Type: application/json; charset=UTF-8");
  header("Cache-Control: no-cache, must-revalidate");
  header("Expires: Sat, 26 Jul 2017 05:00:00 GMT");
  if (ACCESS_CONTROL_ENABLED) {
    header("Access-Control-Allow-Origin:" . FULL_DOMAIN);
  } else {
    header("Access-Control-Allow-Origin: *");
  }




  if (LAN_LOCK) {
    if (!preg_match("/^192\.168\.\d{0,3}\.\d{0,3}$/", $_SERVER["REMOTE_ADDR"])) {
      header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
      
      $a = array('response' => '401: Unauthorized');
      $headers = ob_get_clean();
      echo $headers;
      echo json_encode($a);
      die();
    }
    
  }


  $loader = require __DIR__ . '/vendor/autoload.php';
  $loader->add('RekoBooking', __DIR__);
  $loader->addPsr4('RekoBooking\\', __DIR__);


  $router = new \AltoRouter();
  $router->setBasePath('/api');
  $router->map('GET','/', function() {
    require __DIR__ . '/home.php';
  }, 'home');

  $router->map('GET','/auth', function() {
    require __DIR__ . '/auth.php';
  }, 'auth');

  $match = $router->match();

  if( $match && is_callable( $match['target'] ) ) {
    call_user_func_array( $match['target'], $match['params'] ); 
  } else {
    // no route was matched
      header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
      $a = array('response' => '404: Not Found');
      $headers = ob_get_clean();
      echo $headers;
      echo json_encode($a);
      die();
  }

  $website = ob_get_clean();
  echo $website;
