<?php 
/**
 * Rekå Resor Bokningssystem - API
 * index.php
 * @author    Håkan Arnoldson
 * 
 * Squential front controller
 * 
 * - Imports config.php global constants
 * - Sets up error handling and some basic php.ini configuration
 * - Starts the output cache ob_start
 * - Sets default headers
 * - Handles pre-flight (OPTIONS)
 * - Handles basic 403 request rejection (wrong IP or no API-key)
 * - Initalize Monolog
 * - Initalize Moment to CET and se_SV
 * - Initalize AltoRouter and route request to second controller
 * - Handles 404 response
 * - Logs and reports if the program exits naturally - all requests should lead to a explicit exit with die()
 */

namespace RekoBooking;

use RekoBooking\Controller;

use \Moment\Moment;

mb_internal_encoding("UTF-8");
require __DIR__ . '/config/config.php';

if (ENV_DEBUG_MODE) {
  error_reporting(-1); 
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
} else {
  ini_set('display_errors', 0);
  ini_set('display_startup_errors', 0);
  error_reporting(0);
}

ob_start(null, 0);
header("Content-Type: application/json; charset=UTF-8");
header("Accept-Charset: utf-8");
header("Cache-Control: no-cache, must-revalidate");
header("Content-Language: sv-SE");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-API-Key');

if (ENV_ACCESS_CONTROL_ENABLED) {
  header("Access-Control-Allow-Origin:" . ENV_FULL_DOMAIN);
} else {
  header("Access-Control-Allow-Origin: *");
}

if (ENV_LAN_LOCK) {
  if (!preg_match("/^192\.168\.\d{0,3}\.\d{0,3}$/", $_SERVER["REMOTE_ADDR"]) &&
      $_SERVER["REMOTE_ADDR"] != "127.0.0.1" &&
      $_SERVER["REMOTE_ADDR"] != "::1") {
        http_response_code(403);
        $a = array(
          'login' => false,
          'saved' => false,
          'response' => 'Du har ett externt IP-nummer och får inte komma åt denna resurs.');
          $headers = ob_get_clean();
          echo $headers;
          echo json_encode($a);
          die();
  }
}

/* Pre-flight handler */
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
  http_response_code(200);
  $headers = ob_get_clean();
  echo $headers;
  die();
}

if (!empty($_SERVER["HTTP_X_API_KEY"]) && $_SERVER["HTTP_X_API_KEY"] != AUTH_API_KEY) {
  http_response_code(403);
  $a = array(
    'login' => false,
    'saved' => false,
    'response' => 'Fel eller ingen API-nyckel skickad. Du behöver en API-nyckel i headern "X-API-Key:" för att komma åt denna resurs.');
  $headers = ob_get_clean();
  echo $headers;
  echo json_encode($a);
  die();
}

header("Accept: application/json");

$loader = require __DIR__ . '/vendor/autoload.php';
$loader->add('RekoBooking', __DIR__);
$loader->addPsr4('RekoBooking\\', __DIR__);

$router = new \AltoRouter();

$router->setBasePath('/api/v1');
Moment::setDefaultTimezone('CET');
Moment::setLocale('se_SV');


/* NOTE: response can't be passed as route param. Reserved! */
$router->addRoutes(array(
  array('POST',           '/users/auth[/]?',                function()         { $start = new Controller; echo $start->auth('login');               }),
  array('POST',           '/users/auth/refresh[/]?',        function()         { $start = new Controller; echo $start->auth('refresh');             }),
  array('POST',           '/users/auth/revoke[/]?',         function()         { $start = new Controller; echo $start->auth('revoke');              }),
  array('GET|PUT|DELETE', '/tours/tours/[i:id]?[/]?',       function($id = -1) { $start = new Controller; echo $start->start('tours',       $id);   }),
  array('GET|POST',       '/tours/tours[/]?',               function()         { $start = new Controller; echo $start->start('tours'           );   }),
  array('GET|PUT|DELETE', '/tours/categories/[i:id]?[/]?',  function($id = -1) { $start = new Controller; echo $start->start('categories',  $id);   }),
  array('GET|POST',       '/tours/categories[/]?',          function()         { $start = new Controller; echo $start->start('categories'      );   }),
  array('GET|PUT|DELETE', '/bookings/[i:id]?[/]?',          function($id = -1) { $start = new Controller; echo $start->start('bookings',    $id);   }),
  array('GET|POST',       '/bookings[/]?',                  function()         { $start = new Controller; echo $start->start('bookings'        );   }),
  array('GET|PUT|DELETE', '/customers/[i:id]?[/]?',         function($id = -1) { $start = new Controller; echo $start->start('customers',   $id);   }),
  array('GET|POST',       '/customers[/]?',                 function()         { $start = new Controller; echo $start->start('customers'       );   }),
  array('GET|PUT|DELETE', '/leads/[i:id]?[/]?',             function($id = -1) { $start = new Controller; echo $start->start('leads',       $id);   }),
  array('GET|POST',       '/leads[/]?',                     function()         { $start = new Controller; echo $start->start('leads'           );   }),
  array('GET|PUT|DELETE', '/payments/[i:id]?[/]?',          function($id = -1) { $start = new Controller; echo $start->start('payments',    $id);   }),
  array('GET|POST',       '/payments[/]?',                  function()         { $start = new Controller; echo $start->start('payments'        );   }),
  array('GET|PUT|DELETE', '/budgets/[i:id]?[/]?',           function($id = -1) { $start = new Controller; echo $start->start('budgets',     $id);   }),
  array('GET|POST',       '/budgets[/]?',                   function()         { $start = new Controller; echo $start->start('budgets'         );   }),
  array('GET|PUT|DELETE', '/deadlines/[i:id]?[/]?',         function($id = -1) { $start = new Controller; echo $start->start('deadlines',   $id);   }),
  array('GET|POST',       '/deadlines[/]?',                 function()         { $start = new Controller; echo $start->start('deadlines'       );   }),
  
  array('GET',            '/timestamp[/]?', function() { 
    echo json_encode(array('servertime' => time())); 
    http_response_code(200); 
    echo ob_get_clean(); 
    die();
  }),
));

$match = $router->match();

if( $match && !empty($match['target']) && is_callable( $match['target'] ) ) {
  
  call_user_func_array( $match['target'], $match['params'] ); 
} else {
    http_response_code(404);
    $a = array(
      'success' => false,
      'saved' => false,
      'response' => '',
      'login' => false,
      'error' => 'Felaktig URL, det finns inget innehåll på denna länk.');
    $headers = ob_get_clean();
    echo $headers;
    echo json_encode($a);
    die();
}

$content = ob_get_clean();
echo $content;
