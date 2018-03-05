<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking\classes\tours;

use RekoBooking\classes\DBError;
use \Moment\Moment;

class SaveTour {

  public static function Save($jsonData, $response, $pdo) {
    $newData = self::VerifyTourInput($jsonData, $response);
    if (!$newData) {
      return false;
    } else {
      try {
      $sql = "INSERT INTO Resa (Resa, AvbskyddPris, AnmavgPris) VALUES (:tour, :cancel, :reserve)";
      $pdo->beginTransaction();
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tour',    $newData['tourName'],       \PDO::PARAM_STR);
      $sth->bindParam(':cancel',  $newData['insuranceFee'],   \PDO::PARAM_INT);
      $sth->bindParam(':reserve', $newData['reservationFee'], \PDO::PARAM_INT);
      $sth->execute(); 
      $pdo->commit();
      
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql, $response);
        return false;
      }
      return true;
    }

  }

  public static function VerifyTourInput($jsonData, $response) {
    $newData = array();

    if (!empty($jsonData['tourName']) && trim($jsonData['tourName']) != false) {
      $newData['tourName'] = mb_strimwidth(filter_var(trim($jsonData['tourName']), FILTER_SANITIZE_STRING), 0, 100);
      var_dump($newData['tourName']);
    } else {
      $response->AddResponse('response', 'Du måste ange ett namn för resan.');
      return false;
    }

    if (!empty($jsonData['reservationFee']) || trim($jsonData['reservationFee']) == 0) {
      $temp = filter_var(trim($jsonData['reservationFee']), FILTER_SANITIZE_NUMBER_INT);
      $temp = filter_var(trim($temp), FILTER_VALIDATE_INT);
      if (!$temp) {
        $response->AddResponse('response', 'Anmälningsavgift får bara innehålla siffror.');
        return false;
      } else {
        $newData['reservationFee'] = $temp;
      }
    } else {
      $response->AddResponse('response', 'Anmälningsavgift måste anges, skriv 0 för 0kr.');
      return false;
    }

    if (!empty($jsonData['insuranceFee']) || trim($jsonData['insuranceFee']) == 0) {
      $temp = filter_var(trim($jsonData['insuranceFee']), FILTER_SANITIZE_NUMBER_INT);
      $temp = filter_var(trim($temp), FILTER_VALIDATE_INT);
      if (!$temp) {
        $response->AddResponse('response', 'Avbeställningsskydd får bara innehålla siffror.');
        return false;
      } else {
        $newData['insuranceFee'] = $temp;
      }
    } else {
      $response->AddResponse('response', 'Avbeställningsskydd måste anges, skriv 0 för 0kr.');
      return false;
    }

    if (!empty($jsonData['startDate']) && trim($jsonData['startDate']) != false) {
      $moment = new Moment($jsonData['startDate']);
      $newData['startDate'] = $moment->format('Y-m-d');
    } else {
      $response->AddResponse('response', 'Du måste ange ett giltigt datum för avresa YYYY-MM-DD.');
      return false;
    }

    return $newData;

  }


}