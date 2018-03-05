<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking\tours;

use RekoBooking\classes\DB;
use RekoBooking\classes\tours\SaveTour;
use RekoBooking\classes\Responder;
use RekoBooking\classes\error\NotFound;


$response = new Responder;

$pdo = DB::get();
$jsonData = json_decode(trim(file_get_contents('php://input')), true);


switch (trim($operaton)) {
  case "new":
    if (SaveCategory::New($jsonData, $response, $pdo)) {
      $response->AddResponse('saved', true);
    } else {
      $response->AddResponse('saved', false);
    }
      break;
  case "edit":
    if (SaveCategory::Save($jsonData, $response, $pdo)) {
      $response->AddResponse('saved', true);
    } else {
      $response->AddResponse('saved', false);
    }
      break;
  case "delete":
    if (SaveCategory::Delete($jsonData, $response, $pdo)) {
      $response->AddResponse('saved', true);
    } else {
      $response->AddResponse('saved', false);
    }
      break;
  default:
    NotFound::PrintDie();
  }

  echo $response->GetResponse();



