<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking\classes\tours;

use RekoBooking\classes\DBError;
use \Moment\Moment;

class Tour {

  public static function Get($jsonData, $response, $pdo) {
    $validID = false;
    if ($jsonData['tourid'] == 'all') {
      $validID = 'all';
    } else {
      if (!empty($jsonData['tourid']) || trim($jsonData['tourid']) == 0) {
        $temp = filter_var(trim($jsonData['tourid']), FILTER_SANITIZE_NUMBER_INT);
        $temp = filter_var(trim($temp), FILTER_VALIDATE_INT);
        if (!$temp) {
          $response->AddResponse('response', 'Resan hittades inte.');
          return false;
          } 
        $validID = $temp;
      } else {
        $response->AddResponse('response', 'Resan hittades inte.');
        return false;
      }
    }
    $response->AddResponse('requestedid', $validID);
    try {
      $sql = "SELECT Resa.ResaID as ResaID, Resa, AvbskyddPris, AnmavgPris, Avresa, Resa.Aktiv as Aktiv, BoendeID, BoendeNamn, Pris, Personer, AntalTillg, Kategori.KategoriID as KategoriID, Kategori FROM Resa INNER JOIN Kategori_Resa ON Resa.ResaID = Kategori_Resa.ResaID 
        INNER JOIN Kategori ON Kategori_Resa.KategoriID = Kategori.KategoriID RIGHT JOIN Boende ON Boende.ResaID = Resa.ResaID ";
      if ($validID != 'all') {
        $sql .= "WHERE Resa.ResaID = :id ";
      }
      $sql .= "ORDER BY Avresa ASC, ResaID DESC, Personer ASC;";


      $sth = $pdo->prepare($sql);
      if ($validID != 'all') {
        $sth->bindParam(':id', $validID, \PDO::PARAM_INT);
      }
      $sth->execute(); 
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);     
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql, $response);
        return false;
      }
      if (count($result) > 0) {
        $tourid = '';
        $roomOpts = [];
        $responseArray = [];
        foreach ($result as $tour) {
          if ($tourid != $tour['resaid'] && $tourid != '') {
            $output = $responseArray;
            $output['roomOpts'] = $roomOpts;
            $roomOpts = [];
            $responseArray = [];
            $response->AddToArrayOnKey('tours', $output);
          }   
          $tourid = $tour['resaid'];
          $active = $tour['aktiv'] ? true : false;
          array_push($roomOpts, array('roomid' => $tour['boendeid'], 'roomtype' => $tour['boendenamn'], 'roomprice' => $tour['pris'], 'roomsize' => $tour['personer'], 'roomcount' => $tour['antaltillg']));
          if (empty($responseArray)) {
          $responseArray = array('id' => (int)$tour['resaid'], 'tour' => $tour['resa'], 'active' => $active, 'insurancefee' => $tour['avbskyddpris'], 'reservefee' => $tour['anmavgpris'], 
              'departure' => date('Y-m-d',strtotime($tour['avresa'])), 'categoryid' => $tour['kategoriid'], 'category' => $tour['kategori'], 'active' => $active,
              'roomOpts' => []);
          }
          
        }
        if ($tourid != '') {
          $output = $responseArray;
          $output['roomOpts'] = $roomOpts;
          $response->AddToArrayOnKey('tours', $output);
          unset($responseArray);
          unset($roomOpts);
          unset($output);
          unset($tourid);
        } else {
          $response->AddResponse('response', 'Inga resor hittades');
          $response->AddResponse('tours', []);
        }
        return true;
      } else {
          $response->AddResponse('response', 'Inga resor hittades');
          $response->AddResponse('tours', []);
        return false;
      }
  }

  public static function Save($jsonData, $response, $pdo) {
    $newData = self::VerifyTourInput($jsonData, $response, $pdo);
    if (!$newData) {
      return false;
    } else {
      try {
      $pdo->beginTransaction();
      $sql = "INSERT INTO Resa (Resa, AvbskyddPris, AnmavgPris, Avresa, Aktiv) OUTPUT INSERTED.ResaID VALUES (:tour, :cancel, :reserve, :departure, 1)";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tour',      $newData['tourName'],        \PDO::PARAM_STR);
      $sth->bindParam(':cancel',    $newData['tourInsurance'],   \PDO::PARAM_INT);
      $sth->bindParam(':reserve',   $newData['tourReservation'], \PDO::PARAM_INT);
      $sth->bindParam(':reserve',   $newData['tourReservation'], \PDO::PARAM_INT);
      $sth->bindParam(':departure', $newData['tourDate'],      \PDO::PARAM_STR);
      $sth->execute(); 
      $temp = $sth->fetch(\PDO::FETCH_ASSOC);
      $newTourID = intval($temp['resaid']);
      $sql = "INSERT INTO Kategori_Resa (ResaID, KategoriID) VALUES (:tid, :catid)";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tid',    $newTourID,               \PDO::PARAM_INT);
      $sth->bindParam(':catid',  $newData['tourCategory'], \PDO::PARAM_INT);
      $sth->execute(); 
      foreach ($newData['tourRoomOpt'] as $item) {
        $sql = "INSERT INTO Boende (ResaID, BoendeNamn, Pris, Personer, AntalTillg) VALUES (:tourid, :rname, :rprice, :rsize, :rcount)";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':tourid', $newTourID,         \PDO::PARAM_INT);
        $sth->bindParam(':rname',  $item['roomType'],  \PDO::PARAM_STR);
        $sth->bindParam(':rprice', $item['roomPrice'], \PDO::PARAM_INT);
        $sth->bindParam(':rsize',  $item['roomSize'],  \PDO::PARAM_INT);
        $sth->bindParam(':rcount', $item['roomCount'], \PDO::PARAM_INT);
        $sth->execute(); 
      }
      $pdo->commit();
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__, $sql, $response);
        $pdo->rollBack();
        return false;
      }
      $response->AddResponse('modifiedid', $newTourID);
      return true;
    }

  }

  public static function VerifyTourInput($jsonData, $response, $pdo) {
    $newData = array();

    if (!empty($jsonData['tourRoomOpt']) && is_array($jsonData['tourRoomOpt'])) {
      $newData['tourRoomOpt'] = array();
      $i = -1;
      foreach ($jsonData['tourRoomOpt'] as $item) {
        $i++;
        if (!empty($item) && is_array($item)) {
          if (!empty($item['roomType']) && trim($item['roomType']) != false) {
            $newData['tourRoomOpt'][$i]['roomType'] = mb_strimwidth(filter_var(trim($item['roomType']), FILTER_SANITIZE_STRING), 0, 100);
          } else {
            $response->AddResponse('response', 'Felformaterad data i rumsnamn.');
            return false;
          }
          if (!empty($item['roomSize']) || trim($item['roomSize']) == 0) {
            $temp = filter_var(trim($item['roomSize']), FILTER_SANITIZE_NUMBER_INT);
            $temp = filter_var(trim($temp), FILTER_VALIDATE_INT);
            if (!$temp) {$temp = 0;} //assume 0 on invalid
            $newData['tourRoomOpt'][$i]['roomSize'] = $temp;
          } else {
            $response->AddResponse('response', 'Felformaterad data i personer per rum.');
            return false;
          }

          if (!empty($item['roomPrice']) || trim($item['roomPrice']) == 0) {
            $temp = filter_var(trim($item['roomPrice']), FILTER_SANITIZE_NUMBER_INT);
            $temp = filter_var(trim($temp), FILTER_VALIDATE_INT);
            if (!$temp) {$temp = 0;} //assume 0 on invalid
            $newData['tourRoomOpt'][$i]['roomPrice'] = $temp;
          } else {
            $response->AddResponse('response', 'Felformaterad data i pris per rum.');
            return false;
          }

          if (!empty($item['roomCount']) || trim($item['roomCount']) == 0) {
            $temp = filter_var(trim($item['roomCount']), FILTER_SANITIZE_NUMBER_INT);
            $temp = filter_var(trim($temp), FILTER_VALIDATE_INT);
            if (!$temp) {$temp = 0;} //assume 0 on invalid
            $newData['tourRoomOpt'][$i]['roomCount'] = $temp;
          } else {
            $response->AddResponse('response', 'Felformaterad data i antal bokade rum.');
            return false;
          }
        } else {
          $response->AddResponse('response', 'Felformaterad data i rumsalternativ.');
          return false;
        }
      }
    } else {
      $response->AddResponse('response', 'Minst ett rumsalternativ eller dagsresa och pris måste anges.');
      return false;
    }

    if (!empty($jsonData['tourCategory']) || trim($jsonData['tourCategory']) == 0) {
      $temp = filter_var(trim($jsonData['tourCategory']), FILTER_SANITIZE_NUMBER_INT);
      $temp = filter_var(trim($temp), FILTER_VALIDATE_INT);
      if (!$temp) {
        $response->AddResponse('response', 'Ogiltig kategori för resan. Välj en kategori i listan.');
        return false;
      } else {
        try {
          $sql = "SELECT KategoriID FROM Kategori WHERE KategoriID = :id";
          $sth = $pdo->prepare($sql);
          $sth->bindParam(':id', $temp, \PDO::PARAM_INT);
          $sth->execute(); 
          $result = $sth->fetchAll(\PDO::FETCH_ASSOC);     
          } catch(\PDOException $e) {
            DBError::showError($e, __CLASS__, $sql, $response);
            return false;
          }
          if (count($result) < 1) { 
            $response->AddResponse('response', 'Ogiltig kategori för resan. Välj en kategori i listan.');
            return false;
          }
        $newData['tourCategory'] = $temp;
      }
    } else {
      $response->AddResponse('response', 'Ogiltig kategori för resan. Välj en kategori i listan.');
      return false;
    }

    if (!empty($jsonData['tourName']) && trim($jsonData['tourName']) != false) {
      $newData['tourName'] = mb_strimwidth(filter_var(trim($jsonData['tourName']), FILTER_SANITIZE_STRING), 0, 100);
    } else {
      $response->AddResponse('response', 'Du måste ange ett namn för resan.');
      return false;
    }

    if (!empty($jsonData['tourReservation']) || trim($jsonData['tourReservation']) == 0) {
      $temp = filter_var(trim($jsonData['tourReservation']), FILTER_SANITIZE_NUMBER_INT);
      $temp = filter_var(trim($temp), FILTER_VALIDATE_INT);
      if (!$temp) {
        $response->AddResponse('response', 'Anmälningsavgift får bara innehålla siffror.');
        return false;
      } else {
        $newData['tourReservation'] = $temp;
      }
    } else {
      $response->AddResponse('response', 'Anmälningsavgift måste anges, skriv 0 för 0kr.');
      return false;
    }

    if (!empty($jsonData['tourInsurance']) || trim($jsonData['tourInsurance']) == 0) {
      $temp = filter_var(trim($jsonData['tourInsurance']), FILTER_SANITIZE_NUMBER_INT);
      $temp = filter_var(trim($temp), FILTER_VALIDATE_INT);
      if (!$temp) {
        $response->AddResponse('response', 'Avbeställningsskydd får bara innehålla siffror.');
        return false;
      } else {
        $newData['tourInsurance'] = $temp;
      }
    } else {
      $response->AddResponse('response', 'Avbeställningsskydd måste anges, skriv 0 för 0kr.');
      return false;
    }

    if (!empty($jsonData['tourDate']) && trim($jsonData['tourDate']) != false) {
      try {
      $moment = new Moment($jsonData['tourDate']);
      $newData['tourDate'] = $moment->format('Y-m-d');
      } catch (Exception $e) {
        $response->AddResponse('response', 'Kunde inte förstå datumet för avresa. Använd format: YYYY-MM-DD.');
        return false;
      }
    } else {
      $response->AddResponse('response', 'Du måste ange ett giltigt datum för avresa YYYY-MM-DD.');
      return false;
    }

    return $newData;

  }


}