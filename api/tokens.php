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



  
if (!empty($jsonData['user'])) {
  $user = trim(filter_var($jsonData['user'], FILTER_SANITIZE_STRING));
} else {
  $user = 'blindtoken';
}

$pdo = DB::get();
$response->AddResponse('saved', false);
$response->AddResponse('servertime', time());
$response->AddResponseArray(Tokens::createToken($tokentype, $pdo, $user));
echo $response->GetResponse();

