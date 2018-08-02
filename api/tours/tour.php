<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking\tours;

use RekoBooking\classes\DB;
use RekoBooking\classes\tours\Tour;
use RekoBooking\classes\Responder;
use RekoBooking\classes\errors\NotFound;


$pdo = DB::get();
$jsonData = json_decode(trim(file_get_contents('php://input')), true);

if ($operation == 'new' || $operation == 'edit') {
  if (Tour::Save($jsonData, $response, $pdo)) {
    $response->AddResponse('saved', true);
    $response->AddResponse('success', true);
    echo $response->GetResponse();
  } else {
    $response->AddResponse('saved', false);
    $response->AddResponse('success', false);
    echo $response->GetResponse();
  }
} elseif ($operation == 'get') {
  if (Tour::Get($jsonData, $response, $pdo)) {
    $response->AddResponse('saved', false);
    $response->AddResponse('success', true);
    echo $response->GetResponse();
  } else {
    $response->AddResponse('saved', false);
    $response->AddResponse('success', false);
    echo $response->GetResponse();
  }
} else {
  NotFound::PrintDie();
}




