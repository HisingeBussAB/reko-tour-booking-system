<?php 

namespace RekoBooking;

use RekoBooking\classes\LoginCheck;
use RekoBooking\classes\Responder;
use \Moment\Moment;

use \Monolog\Logger;
use \Monolog\Handler\RotatingFileHandler;
use \Monolog\ErrorHandler;


require __DIR__ . '/config/config.php';

if (DEBUG_MODE) {
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
    
    $a = array(
      'saved' => false,
      'response' => 'Du har ett externt IP-nummer och får inte komma åt denna resurs.');
    $headers = ob_get_clean();
    echo $headers;
    echo json_encode($a);
    die();
  }
  
}

if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
  //OPTIONS request. Send CORS headers and die. Preflight handler
  header( $_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
  $headers = ob_get_clean();
  echo $headers;
  die();
}

if ($_SERVER["HTTP_AUTHORIZATION"] != API_TOKEN) {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
  $a = array(
    'login' => false,
    'saved' => false,
    'response' => 'Fel eller ingen API token skickad.');
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

$router->setBasePath('/api');
Moment::setDefaultTimezone('CET');
Moment::setLocale('se_SV');

// create a log channel
$logger = new Logger('main_logger');
$logger->pushHandler(new RotatingFileHandler(__DIR__ . '/log/monolog.log', 10, Logger::WARNING));
if (!DEBUG_MODE) {
  ErrorHandler::register($logger);
}


$response = new Responder;

//response can't be passed as route param, reserved!

$router->addRoutes(array(
  array('POST', '/auth',                                  
    function($response)             {                                          require __DIR__ . '/auth.php';                    }),
  array('POST', '/token/[a:tokentype]',                   
    function($tokentype, $response) {                                          require __DIR__ . '/tokens.php';                  }),
  array('POST', '/tours/tours/[a:operation]',          
    function($operation, $response) { if (LoginCheck::isLoggedin($response)) { require __DIR__ . '/tours/tour.php'; }        }),
  array('POST', '/tours/categories/[a:operation]',      
    function($operation, $response) { if (LoginCheck::isLoggedin($response)) { require __DIR__ . '/tours/category.php'; }        }),
  array('GET', '/timestamp',      
    function() { echo json_encode(array('servertime' => time())); }),
));

$match = $router->match();
$match['params']['response'] = $response; //Pass responder object into the router

if( $match && !empty($match['target']) && is_callable( $match['target'] ) ) {
  call_user_func_array( $match['target'], $match['params'] ); 
} else {
  // no route was matched
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    $a = array(
      'saved' => false,
      'response' => 'Felaktig URL det finns inget innehåll på denna länk.');
    $headers = ob_get_clean();
    echo $headers;
    echo json_encode($a);
    die();
}
header( $_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
$website = ob_get_clean();
echo $website;


//DEBUGGING FUNCTIONS
function Force500() {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error');
  $headers = ob_get_clean();
  echo $headers;
  die();
}