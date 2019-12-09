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

namespace Index;

use \RekoBooking\Controller;
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
header('X-Robots-Tag: noindex, nofollow');
header('Allow: OPTIONS, GET, HEAD, POST, PUT, DELETE');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');

if (ENV_ACCESS_CONTROL_ENABLED) {
  header("Access-Control-Allow-Origin:" . ENV_FULL_DOMAIN);
} else {
  header("Access-Control-Allow-Origin: *");
}

//Prevent special case of using misconfigured mod_remoteip for remote addr spoofing.
if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && preg_match('/\d,\d/', $_SERVER['HTTP_X_FORWARDED_FOR']) == 1) {
  http_response_code(403);
  $a = array(
    'login' => false,
    'saved' => false,
    'response' => 'Begäran nekad av säkerhetsskäl. Kontakta systemadministratör.');
    $headers = ob_get_clean();
    echo $headers;
    echo json_encode($a);
    die();
}

$cloudlfarefile = 'cloudflareips.txt';
if (!file_exists($cloudlfarefile)) {
  file_put_contents($cloudlfarefile, file_get_contents('https://www.cloudflare.com/ips-v4'));
} else {
  if (filemtime($cloudlfarefile) < mktime(0, 0, 0, date("m"), date("d")-2, date("Y")))
  file_put_contents($cloudlfarefile, file_get_contents('https://www.cloudflare.com/ips-v4'));
}

Set_ENV_REMOTE_ADDR($cloudlfarefile);


if (empty(ENV_REMOTE_ADDR)) {
  http_response_code(403);  
        $a = array(
          'login' => false,
          'saved' => false,
          'response' => 'IP validering misslyckades, begäran nekad. Ditt IP: ' . $_SERVER["REMOTE_ADDR"] . '. Ditt validerade IP: ' . ENV_REMOTE_ADDR);
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

//Bypass kill switches for firewall update secret link.
$bypassLocks = false;
if(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == '/v1/updatefirewall/' && parse_url('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], PHP_URL_QUERY) == AUTH_SECRET_LINK) { 
  $bypassLocks = true;
}

//Bypass kill switches for cron jobs
if(in_array(ENV_REMOTE_ADDR, ENV_SERVER_IP) && (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == '/v1/updatefirewall/' || parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == '/v1/maintinance/')) { 
  $bypassLocks = true;
}

if (ENV_IP_ADDRESS_LOCK && !$bypassLocks) {
  $file = 'dynamic_allowed_ips.txt';
  updateDynamicIPBlock($file, false);
  $allowed_ips = array();
  $allowed_ips = explode(",", file_get_contents($file));
  $all_allowed_ips = array_merge($allowed_ips, ENV_IP_ADDRESS_LOCK_ALLOWED_IPS);
    
  if (!in_array(ENV_REMOTE_ADDR, $all_allowed_ips)) {
        http_response_code(403);
        $a = array(
          'login' => false,
          'saved' => false,
          'response' => 'Ditt IP-nummer får inte komma åt denna resurs. Ditt IP:' . ENV_REMOTE_ADDR);
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


if ((empty($_SERVER["HTTP_X_API_KEY"]) || $_SERVER["HTTP_X_API_KEY"] != AUTH_API_KEY) && !$bypassLocks && !($_SERVER['REQUEST_METHOD'] == 'GET' && parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == '/v1/generatehash')) {
  http_response_code(403);
  $a = array(
    'login' => false,
    'saved' => false,
    'response' => 'Fel eller ingen API-nyckel skickad. Du behöver en API-nyckel i headern \'X-API-Key:\' för att komma åt denna resurs.');
  $headers = ob_get_clean();
  echo $headers;
  echo json_encode($a);
  die();
}





header("Accept: application/json");

$loader = require __DIR__ . '/vendor/autoload.php';
//$loader->addPsr4('RekoBooking\\', __DIR__ . "/src/");

$router = new \AltoRouter();

$router->setBasePath('/v1');
Moment::setDefaultTimezone('CET');
Moment::setLocale('sv_SE');


//Final saftey kill setting check for only URLs it should apply to
if ($bypassLocks != false && !(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == '/v1/updatefirewall/' || parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == '/v1/maintinance/' || parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) == '/v1/generatehash'))
{
  http_response_code(403);
  $a = array(
    'login' => false,
    'saved' => false,
    'response' => 'Fel i åtkomstprocedur! Ej behörig.');
  $headers = ob_get_clean();
  echo $headers;
  echo json_encode($a);
  die();
}

$router->addRoutes(array(
  array('POST',           '/users/auth[/]?',                function()         { $start = new Controller; echo $start->auth('login');               }),
  array('POST',           '/users/auth/refresh[/]?',        function()         { $start = new Controller; echo $start->auth('refresh');             }),
  array('POST',           '/users/auth/revoke[/]?',         function()         { $start = new Controller; echo $start->auth('revoke');              }),
  array('POST',           '/users/auth/revokeall[/]?',      function()         { $start = new Controller; echo $start->auth('revokeall');              }),
  array('GET|PUT|DELETE', '/tours/[i:id]?[/]?',             function($id = -1) { $start = new Controller; echo $start->start('Tours',            $id);   }),
  array('GET|POST',       '/tours[/]?',                     function()         { $start = new Controller; echo $start->start('Tours'                );   }),
  array('GET|PUT|DELETE', '/categories/[i:id]?[/]?',        function($id = -1) { $start = new Controller; echo $start->start('Categories',       $id);   }),
  array('GET|POST',       '/categories[/]?',                function()         { $start = new Controller; echo $start->start('Categories'           );   }),
  array('GET|PUT|DELETE', '/bookings/[i:id]?[/]?',          function($id = -1) { $start = new Controller; echo $start->start('Bookings',         $id);   }),
  array('GET|POST',       '/bookings[/]?',                  function()         { $start = new Controller; echo $start->start('Bookings'             );   }),
  array('GET|PUT|DELETE', '/reservations/[i:id]?[/]?',      function($id = -1) { $start = new Controller; echo $start->start('Reservations',     $id);   }),
  array('GET|POST',       '/reservations[/]?',              function()         { $start = new Controller; echo $start->start('Reservations'         );   }),
  array('GET|PUT|DELETE', '/customers/[i:id]?[/]?',         function($id = -1) { $start = new Controller; echo $start->start('Customers',        $id);   }),
  array('GET|POST',       '/customers[/]?',                 function()         { $start = new Controller; echo $start->start('Customers'            );   }),
  array('GET|PUT|DELETE', '/groupcustomers/[i:id]?[/]?',    function($id = -1) { $start = new Controller; echo $start->start('GroupCustomers',   $id);   }),
  array('GET|POST',       '/groupcustomers[/]?',            function()         { $start = new Controller; echo $start->start('GroupCustomers'       );   }),
  array('GET|PUT|DELETE', '/newsletter/[i:id]?[/]?',        function($id = -1) { $start = new Controller; echo $start->start('Newsletter',       $id);   }),
  array('GET|POST',       '/newsletter[/]?',                function()         { $start = new Controller; echo $start->start('Newsletter'           );   }),
  array('GET|PUT|DELETE', '/leads/[i:id]?[/]?',             function($id = -1) { $start = new Controller; echo $start->start('Leads',            $id);   }),
  array('GET|POST',       '/leads[/]?',                     function()         { $start = new Controller; echo $start->start('Leads'                );   }),
  array('GET|PUT|DELETE', '/payments/[i:id]?[/]?',          function($id = -1) { $start = new Controller; echo $start->start('Payments',         $id);   }),
  array('GET|POST',       '/payments[/]?',                  function()         { $start = new Controller; echo $start->start('Payments'             );   }),
  array('GET|PUT|DELETE', '/budgets/[i:id]?[/]?',           function($id = -1) { $start = new Controller; echo $start->start('Budgets',          $id);   }),
  array('GET|POST',       '/budgets[/]?',                   function()         { $start = new Controller; echo $start->start('Budgets'              );   }),
  array('GET|PUT|DELETE', '/budgetgroups/[i:id]?[/]?',      function($id = -1) { $start = new Controller; echo $start->start('BudgetGroups',     $id);   }),
  array('GET|POST',       '/budgetgroups[/]?',              function()         { $start = new Controller; echo $start->start('BudgetGroups'         );   }),
  array('GET|PUT|DELETE', '/deadlines/[i:id]?[/]?',         function($id = -1) { $start = new Controller; echo $start->start('Deadlines',        $id);   }),
  array('GET|POST',       '/deadlines[/]?',                 function()         { $start = new Controller; echo $start->start('Deadlines'            );   }),
  array('GET',            '/departurelists/[i:id]?[/]?',    function($id = -1) { $start = new Controller; echo $start->start('DepartureLists',   $id);   }),
  array('GET',            '/pendingcount[/]?',              function()         { $start = new Controller; echo $start->start('PendingCount'         );   }),
  array('GET|PUT',        '/pendingnewsletter/[i:id]?[/]?', function($id = -1) { $start = new Controller; echo $start->start('PendingNewsletter',$id);   }),
  array('GET',            '/pendingnewsletter[/]?',         function()         { $start = new Controller; echo $start->start('PendingNewsletter'    );   }),
  //UTILITES
  array('GET',            '/generatehash?[**:trailing]?',   function($trailing = false) { die(json_encode(array('pwd' => trim($_SERVER['QUERY_STRING']), 'hash' => password_hash(trim($_SERVER['QUERY_STRING']) . AUTH_PWD_PEPPER, PASSWORD_DEFAULT)))); }),
  //CRON
  array('GET',            '/maintinance[/]?',                 function()       { $start = new Controller; echo $start->Maintinance();               }),
  //FIREWALL
  array('GET|POST',       '/updatefirewall[/]?', function(){
    file_put_contents('cloudflareips.txt', file_get_contents('https://www.cloudflare.com/ips-v4'));
    updateDynamicIPBlock('dynamic_allowed_ips.txt', true);
    $allowed_ips = "";
    foreach(ENV_CLOUDFLARE_ALLOWED_HOSTS as $host) {
      $resolvedhost = gethostbyname($host);
      if ($resolvedhost != $host) {
        $allowed_ips = $allowed_ips . ' ' . $resolvedhost;
      }
    }
    foreach(ENV_IP_ADDRESS_LOCK_ALLOWED_IPS as $ip) {
      $allowed_ips = $allowed_ips . ' ' . $ip;
    }
    foreach(ENV_SERVER_IP as $ip) {
      $allowed_ips = $allowed_ips . ' ' . $ip;
    }
    $allowed_ips = trim($allowed_ips); 

    //Edit IDs in these rules
    $rule1 = array("id" => ENV_CLOUDFLARE_API_FILTER_ID,"expression" => "(ip.src in {" . $allowed_ips . "} and http.request.full_uri contains \"https://api.rekoresor.app\") or (ip.src in {" . $allowed_ips . "} and http.request.full_uri contains \"https://apitest.rekoresor.app\")","paused"=> false,"description"=> "DynamicUpdateAllowedIPs API");
    $rule2 = array("id"=> ENV_CLOUDFLARE_WEB_FILTER_ID,"expression"=>"(ip.src in {" . $allowed_ips . "} and http.request.full_uri contains \"://bokningar.rekoresor.app\") or (ip.src in {" . $allowed_ips . "} and http.request.full_uri contains \"://bokningartest.rekoresor.app\")","paused"=> false,"description"=> "DynamicUpdateAllowedIPs Web");
    $rules = array($rule1, $rule2);
    $reply = array();
    $reply['ips'] = array();
    $reply['response'] = array();
    foreach($rules as $rule) {
      
      $endpoint = 'zones/' . ENV_CLOUDFLARE_ZONE . '/filters/' . $rule['id'];
      $data = $rule;
      $method = 'put';

      $r = http_request( $endpoint, $data, $method );
      if (is_object($r)) {
        if (empty($r->error)) { $r->error = 'none';}
        if (empty($r->success)) { $r->success = 'failed';}
        array_push($reply['response'],array('success' => $r->success, 'error' => $r->error));
      } else {
        array_push($reply['response'],array('success' => 'false', 'error' => 'failed without reason'));
      }
    }
    $user_ips = array("Cloudflare reported IP" => $_SERVER["HTTP_CF_CONNECTING_IP"],"Connection remote IP" => $_SERVER['REMOTE_ADDR']);
    array_push($reply['ips'], $user_ips);
    header("Content-Type: text/html; charset=UTF-8");
    echo '<html><head><META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW"></head><body><div style="margin: 60px;"><h1>Brandväggen har uppdaterats.</h1><h2>';
    echo ENV_DOMAIN == 'apitest.rekoresor.app' ? '<a href="https://bokningartest.rekoresor.app" target="_top">' : '<a href="https://bokningar.rekoresor.app" target="_top">';
    echo 'Tillbaka till bokningssystemet</a></h2><pre style="margin-top: 60px;">';
    print_r($reply);
    echo '</pre></div></body></html>';
    http_response_code(200); 
    echo ob_get_clean(); 
    die();
   
  }),

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

  function updateDynamicIPBlock($file = 'dynamic_allowed_ips.txt', $force = false) {
    $allowed_ips = '';
    if ($force || !file_exists($file) || (file_exists($file) && filemtime($file) < mktime(0, 0, 0, date("m"), date("d")-1, date("Y")))) {

      foreach(ENV_IP_DYNAMIC_LOCK_ALLOWED_IPS as $host) {
        $ip = gethostbyname($host);
        if ($ip != $host) {
          $allowed_ips = $allowed_ips . ' ' . $ip;
        }
      }
      foreach(ENV_SERVER_IP as $ip) {
        $allowed_ips = $allowed_ips . ' ' . $ip;
      }
      $ipsformatted = str_replace(' ', ',', trim($allowed_ips));
      file_put_contents($file, $ipsformatted);
    }    
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


  function http_request( $endpoint, $data, $method ) {
      //setup url
      $url = 'https://api.cloudflare.com/client/v4/' . $endpoint;
      
      //echo $url;exit;
      
      //headers set
      $headers        = array(
        'X-Auth-Key: ' . ENV_CLOUDFLARE_KEY,     
        'X-Auth-Email: ' . ENV_CLOUDFLARE_LOGIN,
        'Content-type: application/json'
     );
      //json encode data
      $json_data = json_encode( $data );
      $ch = curl_init();
      curl_setopt( $ch, CURLOPT_VERBOSE, 0 );
      curl_setopt( $ch, CURLOPT_FORBID_REUSE, true );
      
      curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
      
      curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
      curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
      
      if ( $method === 'post' )
        curl_setopt( $ch, CURLOPT_POST, true );
      
      if ( $method === 'put' )
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
      
      if ( $method === 'delete' )
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'DELETE' );
      
      if ( $method === 'patch' )
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PATCH' );
      
      //get request otherwise pass post data
      if ( !isset( $method ) || $method == 'get' )
        $url .= '?' . http_build_query( $data );
      else
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data );
      //echo $url;
      
      //add headers
      curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
      curl_setopt( $ch, CURLOPT_URL, $url );
      
      
      $http_response = curl_exec( $ch );
      $error         = curl_error( $ch );
      $http_code     = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
      curl_close( $ch );
      
      
      if ( $http_code != 200 ) {
        //hit error will add in error checking but for now will return back to user to handle
        return json_decode($error);
      } 
      return json_decode($http_response);
    }