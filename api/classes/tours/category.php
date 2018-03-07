<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking\classes\tours;

use RekoBooking\classes\DBError;
use RekoBooking\classes\Tokens;

class Category {

  public static function Save($jsonData, $response, $pdo) {
    $newData = self::VerifyCategoryInput($jsonData, $response);
    if (!$newData) {
      return false;
    } else {

      if (!Tokens::validateToken($jsonData['submittoken'], 'submit', $pdo)) {
        Tokens::validationFailedDie($responder);
      }
/*
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
      }*/
      return true;
    }

  }

  public static function New($jsonData, $response, $pdo) {

    if (!Tokens::validateToken($jsonData['submittoken'], 'submit', $pdo, $jsonData['user'])) {
      Tokens::validationFailedDie($response);
    }
    try {
      $sql = "INSERT INTO Kategori (Kategori, Aktiv) OUTPUT INSERTED.KategoriID VALUES (:category, :active)";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':category', $jsonData['category'], \PDO::PARAM_STR);
      $sth->bindParam(':active',   $jsonData['active'],   \PDO::PARAM_INT);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql, $response);
      return false;
    }
    $response->AddResponse('newid', (int)$result['kategoriid']);
    return true;
  

  }

  public static function VerifyCategoryInput($jsonData, $response) {
    $newData = array();

    if (!empty($jsonData['user'])) {
      $newData['user'] = trim(filter_var($jsonData['user'], FILTER_SANITIZE_STRING));
    } else {
      $response->AddResponse('response', 'Inget användarnamn skickat.');
      return false;
    }

    if (!empty($jsonData['category']) && trim($jsonData['category']) != false) {
      $newData['category'] = mb_strimwidth(filter_var(trim($jsonData['category']), FILTER_SANITIZE_STRING), 0, 60);
    } else {
      $response->AddResponse('response', 'Du måste ange ett namn för kategorin.');
      return false;
    }
    if (!empty($jsonData['categoryid'])) {
      $temp = filter_var(trim($jsonData['categoryid']), FILTER_SANITIZE_NUMBER_INT);
      $temp = filter_var($temp, FILTER_VALIDATE_INT);
      if (!$temp) {
        $response->AddResponse('response', 'Felformaterat kategoriID. Prova ladda om eller kontakta tekniker.');
        return false;
      } else {
        $newData['categoryid'] = $temp;
      }
    } else {
      $newData['categoryid'] = 'new';
    }

    $temp = filter_var($jsonData['active'], FILTER_VALIDATE_BOOLEAN);
    
    $temp ? $newData['active']=1 : $newData['active']=0;  
    
    if (!empty($jsonData['submittoken'])) {
      $newData['submittoken'] = filter_var(trim($jsonData['submittoken']), FILTER_SANITIZE_STRING);
    } else {
      $newData['submittoken'] = 'broken'; //Let if fail on token check
    }
    
    return $newData;

  }


}