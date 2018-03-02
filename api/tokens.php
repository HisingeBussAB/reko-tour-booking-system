<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking;

use RekoBooking\classes\DB;
use RekoBooking\classes\DBError;
use RekoBooking\classes\Tokens;


$jsonData = json_decode(trim(file_get_contents('php://input')), true);



if ($jsonData['apitoken'] === API_TOKEN) {
  
  if (!empty($jsonData['user'])) {
    $user = $jsonData['user'];
  } else {
    $user = 'blindtoken';
  }

  $pdo = DB::get();
  $a = Tokens::createToken($tokentype, $user, $pdo);
  echo json_encode($a);

} else {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
  $headers = ob_get_clean();
  echo $headers;
  $a = array('response' => 'Fel APItoken sänd med begäran. Inte tillåten.');
  echo json_encode($a);
  die();
}
