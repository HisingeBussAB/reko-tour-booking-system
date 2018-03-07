<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking\tours;

use RekoBooking\classes\DB;
use RekoBooking\classes\tours\Category;
use RekoBooking\classes\Responder;
use RekoBooking\classes\error\NotFound;



$pdo = DB::get();

$newData = true;
$operation = trim($operation);
if ($operation == 'new' || $operation == 'edit') {
  $jsonData = json_decode(trim(file_get_contents('php://input')), true);
  $newData = array();
  $newData = Category::VerifyCategoryInput($jsonData, $response);
} 

if ($operation == 'get' || $operation == 'delete') {
  $newData = array();
  if (!empty($jsonData['categoryid']) && trim($jsonData['categoryid']) != 'all') {
    $temp = filter_var(trim($jsonData['categoryid']), FILTER_SANITIZE_NUMBER_INT);
    $temp = filter_var($temp, FILTER_VALIDATE_INT);
    if (!$temp) {
      $response->AddResponse('response', 'Felformaterat kategoriID. Prova ladda om eller kontakta tekniker.');
      $newData = false;
    } else {
      $newData['categoryid'] = $temp;
    }
  } else {
    $newData['categoryid'] = 'all';
  }
}


if (!$newData) {
  $operation = 'void';
}

switch ($operation) {
  case "new":
    if (Category::New($newData, $response, $pdo)) {
      $response->AddResponse('saved', true);
    } else {
      $response->AddResponse('saved', false);
    }
    break;
  case "edit":
    if (Category::Save($newData, $response, $pdo)) {
      $response->AddResponse('saved', true);
    } else {
      $response->AddResponse('saved', false);
    }
    break;
  case "delete":
    if (Category::Delete($newData, $response, $pdo)) {
      $response->AddResponse('saved', true);
    } else {
      $response->AddResponse('saved', false);
    }
    break;
  case "get":
    if (Category::Get($newData, $response, $pdo)) {
      $response->AddResponse('saved', false);
    } else {
      $response->AddResponse('saved', false);
    }
    break;
  case "void":
    $response->AddResponse('saved', false);
    break;
  default:
    NotFound::PrintDie();
  }

  echo $response->GetResponse();

  


