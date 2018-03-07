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



if (!empty($jsonData['apitoken']) && $jsonData['apitoken'] === API_TOKEN) {
  
  if (!empty($jsonData['user'])) {
    $user = trim(filter_var($jsonData['user'], FILTER_SANITIZE_STRING));
  } else {
    $user = 'blindtoken';
  }

  $pdo = DB::get();
  $response->AddResponse('saved', false);
  $response->AddResponseArray(Tokens::createToken($tokentype, $pdo, $user));
  echo $response->GetResponse();

} else {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
  $headers = ob_get_clean();
  echo $headers;
  $response->AddResponse('saved', false);
  $response->AddResponse('response', 'Fel APItoken sänd med begäran. Inte tillåten.');
  echo $response->GetResponse();
  die();
}
