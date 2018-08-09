<?php 

namespace RekoBooking;

use RekoBooking\Controller;

use \Moment\Moment;

use \Monolog\Logger;
use \Monolog\Handler\RotatingFileHandler;
use \Monolog\ErrorHandler;

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
    header( $_SERVER["SERVER_PROTOCOL"] . ' 403 Forbidden'); 
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
  header( $_SERVER["SERVER_PROTOCOL"] . ' 200 OK');
  $headers = ob_get_clean();
  echo $headers;
  die();
}

if ($_SERVER["HTTP_X_API_KEY"] != AUTH_API_KEY) {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 403 Forbidden');
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

// create a log channel
$logger = new Logger('main_logger');
$logger->pushHandler(new RotatingFileHandler(ENV_LOG_PATH, 10, Logger::WARNING));
if (!ENV_DEBUG_MODE) {
  ErrorHandler::register($logger);
}

$router = new \AltoRouter();

$router->setBasePath('/api/v1');
Moment::setDefaultTimezone('CET');
Moment::setLocale('se_SV');

/* NOTE: response can't be passed as route param. Reserved! */
$router->addRoutes(array(
  array('GET',            '/token/login',         function()         { $start = new Controller; $start->issueToken('login');    }),
  array('POST',           '/token/refresh',       function()         { $start = new Controller; $start->issueToken('refresh');  }),
  array('POST',           '/login',               function()         { $start = new Controller; $start->doLogin();              }),
  array('GET|PUT|DELETE', '/tours/tours/[i:id]?', function($id = -1) { $start = new Controller; $start->start('tours', $id);    }),
  array('GET|POST',       '/tours/tours[/]?',     function()         { $start = new Controller; $start->start('tours', '');     }),
  
  array('GET', '/timestamp', function() { echo json_encode(array('servertime' => time())); }),
));

$match = $router->match();

if( $match && !empty($match['target']) && is_callable( $match['target'] ) ) {
  
  call_user_func_array( $match['target'], $match['params'] ); 
} else {
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
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

//Uncontrolled exit!
header( $_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error');
http_response_code(500);
$content = ob_get_clean();
echo $content;
echo "Uncontrolled exit! The server behaved unexpectedly, this is a bug please report it to the system administrator!";
$logger->critical('UNCONTROLLED EXIT!', array('site state' => $content));
