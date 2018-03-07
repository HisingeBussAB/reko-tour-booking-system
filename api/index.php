<?php 

namespace RekoBooking;

use RekoBooking\classes\LoginCheck;
use RekoBooking\classes\Responder;
use \Moment\Moment;


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
  header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
  if (ACCESS_CONTROL_ENABLED) {
    header("Access-Control-Allow-Origin:" . FULL_DOMAIN);
  } else {
    header("Access-Control-Allow-Origin: *");
  }




  if (LAN_LOCK) {
    if (!preg_match("/^192\.168\.\d{0,3}\.\d{0,3}$/", $_SERVER["REMOTE_ADDR"]) &&
        $_SERVER["REMOTE_ADDR"] != "127.0.0.1" &&
        $_SERVER["REMOTE_ADDR"] != "::1") {
      header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
      
      $a = array('response' => 'Du har ett externt IP-nummer och får inte komma åt denna resurs.');
      $headers = ob_get_clean();
      echo $headers;
      echo json_encode($a);
      die();
    }
    
  }

  if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    //OPTIONS request. Send CORS headers and die. Preflight handler
    $headers = ob_get_clean();
    echo $headers;
    die();
  }


  $loader = require __DIR__ . '/vendor/autoload.php';
  $loader->add('RekoBooking', __DIR__);
  $loader->addPsr4('RekoBooking\\', __DIR__);


  $router = new \AltoRouter();


  $router->setBasePath('/api');
  Moment::setDefaultTimezone('CET');
  Moment::setLocale('se_SV');

  


  $router->addRoutes(array(
    array('POST', '/auth',                                  
      function()           {$response = new Responder;                                          require __DIR__ . '/auth.php';                    }),
    array('POST', '/token/[a:tokentype]',                   
      function($tokentype) {$response = new Responder;                                          require __DIR__ . '/tokens.php';                  }),
    array('POST', '/tours/savetour/[a:operation]',          
      function($operation) {$response = new Responder; if (LoginCheck::isLoggedin($response)) { require __DIR__ . '/tours/savetour.php'; }        }),
    array('POST', '/tours/savecategory/[a:operation]',      
      function($operation) {$response = new Responder; if (LoginCheck::isLoggedin($response)) { require __DIR__ . '/tours/category.php'; }        }),
  ));


  $match = $router->match();

  if( $match && is_callable( $match['target'] ) ) {
    call_user_func_array( $match['target'], $match['params'] ); 
  } else {
    // no route was matched
      header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
      $a = array('response' => 'Felaktig URL det finns inget innehåll på denna länk.');
      $headers = ob_get_clean();
      echo $headers;
      echo json_encode($a);
      die();
  }
  header( $_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
  $website = ob_get_clean();
  echo $website;


