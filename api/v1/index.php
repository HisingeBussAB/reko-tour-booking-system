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
header('Allow: OPTIONS, GET, HEAD, POST, PUT, DELETE');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, X-API-Key');

if (ENV_ACCESS_CONTROL_ENABLED) {
  header("Access-Control-Allow-Origin:" . ENV_FULL_DOMAIN);
} else {
  header("Access-Control-Allow-Origin: *");
}

$cloudlfarefile = 'cloudflareips.txt';
if (!file_exists($cloudlfarefile)) {
  file_put_contents($cloudlfarefile, file_get_contents('https://www.cloudflare.com/ips-v4'));
} else {
  if (filemtime($cloudlfarefile) < mktime(0, 0, 0, date("m"), date("d")-7, date("Y")))
  file_put_contents($cloudlfarefile, file_get_contents('https://www.cloudflare.com/ips-v4'));
}

Set_ENV_REMOTE_ADDR($cloudlfarefile);


if (empty(ENV_REMOTE_ADDR)) {
  http_response_code(403);  
        $a = array(
          'login' => false,
          'saved' => false,
          'response' => 'IP validering misslyckades, begäran nekad. . Ditt IP: ' . $_SERVER["REMOTE_ADDR"] . '. Ditt validerade IP: ' . ENV_REMOTE_ADDR);
          $headers = ob_get_clean();
          echo $headers;
          echo json_encode($a);
          die();
}

if (ENV_LAN_LOCK) {
  if (!preg_match("/^192\.168\.\d{0,3}\.\d{0,3}$/", ENV_REMOTE_ADDR) &&
        ENV_REMOTE_ADDR != "127.0.0.1" &&
        ENV_REMOTE_ADDR != "::1") {
        http_response_code(403);
        $a = array(
          'login' => false,
          'saved' => false,
          'response' => 'Du har ett externt IP-nummer och får inte komma åt denna resurs. Ditt IP:' . ENV_REMOTE_ADDR);
          $headers = ob_get_clean();
          echo $headers;
          echo json_encode($a);
          die();
  }
}

if (ENV_IP_ADDRESS_LOCK) {
  if (!in_array(ENV_REMOTE_ADDR, ENV_IP_ADDRESS_LOCK_ALLOWED_IPS)) {
        http_response_code(403);
        
        $a = array(
          'login' => false,
          'saved' => false,
          'response' => 'Ditt IP-nummer och får inte komma åt denna resurs. Ditt IP:' . ENV_REMOTE_ADDR);
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
//$loader->addPsr4('RekoBooking\\', __DIR__ . "/src/");

$router = new \AltoRouter();

$router->setBasePath('/api/v1');
Moment::setDefaultTimezone('CET');
Moment::setLocale('se_SV');


$router->addRoutes(array(
  array('POST',           '/users/auth[/]?',                function()         { $start = new Controller; echo $start->auth('login');               }),
  array('POST',           '/users/auth/refresh[/]?',        function()         { $start = new Controller; echo $start->auth('refresh');             }),
  array('POST',           '/users/auth/revoke[/]?',         function()         { $start = new Controller; echo $start->auth('revoke');              }),
  array('GET|PUT|DELETE', '/tours/[i:id]?[/]?',             function($id = -1) { $start = new Controller; echo $start->start('Tours',       $id);   }),
  array('GET|POST',       '/tours[/]?',                     function()         { $start = new Controller; echo $start->start('Tours'           );   }),
  array('GET|PUT|DELETE', '/categories/[i:id]?[/]?',        function($id = -1) { $start = new Controller; echo $start->start('Categories',  $id);   }),
  array('GET|POST',       '/categories[/]?',                function()         { $start = new Controller; echo $start->start('Categories'      );   }),
  array('GET|PUT|DELETE', '/bookings/[i:id]?[/]?',          function($id = -1) { $start = new Controller; echo $start->start('Bookings',    $id);   }),
  array('GET|POST',       '/bookings[/]?',                  function()         { $start = new Controller; echo $start->start('Bookings'        );   }),
  array('GET|PUT|DELETE', '/reservations/[i:id]?[/]?',      function($id = -1) { $start = new Controller; echo $start->start('Reservations',$id);   }),
  array('GET|POST',       '/reservations[/]?',              function()         { $start = new Controller; echo $start->start('Reservations'    );   }),
  array('GET|PUT|DELETE', '/customers/[i:id]?[/]?',         function($id = -1) { $start = new Controller; echo $start->start('Customers',   $id);   }),
  array('GET|POST',       '/customers[/]?',                 function()         { $start = new Controller; echo $start->start('Customers'       );   }),
  array('GET|PUT|DELETE', '/leads/[i:id]?[/]?',             function($id = -1) { $start = new Controller; echo $start->start('Leads',       $id);   }),
  array('GET|POST',       '/leads[/]?',                     function()         { $start = new Controller; echo $start->start('Leads'           );   }),
  array('GET|PUT|DELETE', '/payments/[i:id]?[/]?',          function($id = -1) { $start = new Controller; echo $start->start('Payments',    $id);   }),
  array('GET|POST',       '/payments[/]?',                  function()         { $start = new Controller; echo $start->start('Payments'        );   }),
  array('GET|PUT|DELETE', '/budgets/[i:id]?[/]?',           function($id = -1) { $start = new Controller; echo $start->start('Budgets',     $id);   }),
  array('GET|POST',       '/budgets[/]?',                   function()         { $start = new Controller; echo $start->start('Budgets'         );   }),
  array('GET|PUT|DELETE', '/deadlines/[i:id]?[/]?',         function($id = -1) { $start = new Controller; echo $start->start('Deadlines',   $id);   }),
  array('GET|POST',       '/deadlines[/]?',                 function()         { $start = new Controller; echo $start->start('Deadlines'       );   }),
  
  array('GET',            '/timestamp[/]?', function() { 
    echo json_encode(array('servertime' => time())); 
    http_response_code(200); 
    echo ob_get_clean(); 
    die();
  }),

  //Temporary route for manual user creation
  array('POST',            '/hashpwd[/]?', function() { 
    $data = json_decode(trim(file_get_contents('php://input')), true);
    echo json_encode(array('pwd' => password_hash($data['pwd'] . AUTH_PWD_PEPPER, PASSWORD_DEFAULT))); 
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




function Set_ENV_REMOTE_ADDR($cloudlfarefile) {
  //http://thisinterestsme.com/php-ip-address-cloudflare/
  //NA by default.
  
  $cloudflareIPRanges = preg_split("/\r\n|\n|\r/", file_get_contents($cloudlfarefile));
  
  $ipAddress = 'NA';
   
  //Check to see if the CF-Connecting-IP header exists.
  if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
      
      //Assume that the request is invalid unless proven otherwise.
      $validCFRequest = false;
      
      //Make sure that the request came via Cloudflare.
      foreach($cloudflareIPRanges as $range){
          //Use the ip_in_range function from Joomla.
          if(ip_in_range($_SERVER['REMOTE_ADDR'], $range)) {
              //IP is valid. Belongs to Cloudflare.
              $validCFRequest = true;
              break;
          }
      }
      
      //If it's a valid Cloudflare request
      if($validCFRequest){
          //Use the CF-Connecting-IP header.
          $ipAddress = $_SERVER["HTTP_CF_CONNECTING_IP"];
      } else{
          //If it isn't valid, then use REMOTE_ADDR. 
          $ipAddress = $_SERVER['REMOTE_ADDR'];
      }
      
  } else{
      //Otherwise, use REMOTE_ADDR.
      $ipAddress = $_SERVER['REMOTE_ADDR'];
    }
   
  define('ENV_REMOTE_ADDR', $ipAddress);
  }
  
    /*
   * ip_in_range.php - Function to determine if an IP is located in a
   *                   specific range as specified via several alternative
   *                   formats.
   *
   * Network ranges can be specified as:
   * 1. Wildcard format:     1.2.3.*
   * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
   * 3. Start-End IP format: 1.2.3.0-1.2.3.255
   *
   * Return value BOOLEAN : ip_in_range($ip, $range);
   *
   * Copyright 2008: Paul Gregg <pgregg@pgregg.com>
   * 10 January 2008
   * Version: 1.2
   *
   * Source website: http://www.pgregg.com/projects/php/ip_in_range/
   * Version 1.2
   *
   * This software is Donationware - if you feel you have benefited from
   * the use of this tool then please consider a donation. The value of
   * which is entirely left up to your discretion.
   * http://www.pgregg.com/donate/
   *
   * Please do not remove this header, or source attibution from this file.
   */
  function ip_in_range($ip, $range) {
      if (strpos($range, '/') !== false) {
          // $range is in IP/NETMASK format
          list($range, $netmask) = explode('/', $range, 2);
          if (strpos($netmask, '.') !== false) {
              // $netmask is a 255.255.0.0 format
              $netmask = str_replace('*', '0', $netmask);
              $netmask_dec = ip2long($netmask);
              return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
          } else {
              // $netmask is a CIDR size block
              // fix the range argument
              $x = explode('.', $range);
              while(count($x)<4) $x[] = '0';
              list($a,$b,$c,$d) = $x;
              $range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
              $range_dec = ip2long($range);
              $ip_dec = ip2long($ip);
              
              # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
              #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));
              
              # Strategy 2 - Use math to create it
              $wildcard_dec = pow(2, (32-$netmask)) - 1;
              $netmask_dec = ~ $wildcard_dec;
              
              return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
          }
      } else {
          // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
          if (strpos($range, '*') !==false) { // a.b.*.* format
              // Just convert to A-B format by setting * to 0 for A and 255 for B
              $lower = str_replace('*', '0', $range);
              $upper = str_replace('*', '255', $range);
              $range = "$lower-$upper";
          }
          
          if (strpos($range, '-')!==false) { // A-B format
              list($lower, $upper) = explode('-', $range, 2);
              $lower_dec = (float)sprintf("%u",ip2long($lower));
              $upper_dec = (float)sprintf("%u",ip2long($upper));
              $ip_dec = (float)sprintf("%u",ip2long($ip));
              return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
          }
          return false;
      } 
  }
  
    //https://github.com/cloudflarearchive/Cloudflare-Tools/blob/master/cf-joomla/plgCloudFlare/ip_in_range.php
   function decbin32 ($dec) {
      return str_pad(decbin($dec), 32, '0', STR_PAD_LEFT);
    }