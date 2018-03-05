<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking\tours;

use RekoBooking\classes\DB;
use RekoBooking\classes\tours\SaveTour;
use RekoBooking\classes\Responder;


$response = new Responder;

$pdo = DB::get();
$jsonData = json_decode(trim(file_get_contents('php://input')), true);

var_dump($jsonData);

if (SaveTour::Save($jsonData, $response, $pdo)) {
  $response->AddResponse('saved', true);
  echo $response->GetResponse();
} else {
  $response->AddResponse('saved', false);
  echo $response->GetResponse();
}
