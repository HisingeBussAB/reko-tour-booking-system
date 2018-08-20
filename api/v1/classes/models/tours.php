<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

//TODO Add Category!!

class Tours extends Model {

  public function get(array $params) {
    if ($params['id'] > 0 || $params['id'] == -1) {
      try {
        if ($params['id'] == -1) {
          $sql = "SELECT * FROM Tours;";
        } else {
          $sql = "SELECT * FROM Tours WHERE id = :id;";
        }
        $sth = $this->pdo->prepare($sql);
        if ($params['id'] != -1) { $sth->bindParam(':id', $params['id'], \PDO::PARAM_INT); }
        $sth->execute(); 
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      if (count($result) < 1) {
        $this->response->AddResponse('error', 'Resan hittades inte.');
        $this->response->Exit(404);
      } else {
        foreach ($result as $key=>$tour) {
          try {
            $sql = "SELECT * FROM Rooms WHERE tourid = :id;";
            $sth = $this->pdo->prepare($sql);
            $sth->bindParam(':id', $tour['id'], \PDO::PARAM_INT);
            $sth->execute();
            $roomresult = $sth->fetchAll(\PDO::FETCH_ASSOC); 
          } catch(\PDOException $e) {
            $this->response->DBError($e, __CLASS__, $sql);
            $this->response->Exit(500);
          }
          $result[$key]['rooms'] = $roomresult;
        }
        return array('tours' => $result);
      }
    } else {
      $this->response->AddResponse('error', 'Reseid kan bara anges som ett positivt heltal, eller inte anges alls för alla resor.');
      $this->response->AddResponse('response', 'Begäran avbruten felaktigt id.');
      $this->response->Exit(404);
    }
    return false;
  }

  public function post(array $_params) {
    $params = $this->paramsValidationWithExit($_params);
    $sql = "INSERT INTO Tours (label, insuranceprice, reservationfeeprice, departuredate) OUTPUT INSERTED.id VALUES (:lab, :ins, :res, :dep);";
    try {     
      $this->pdo->beginTransaction();
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':lab', $params['label'],               \PDO::PARAM_STR);
      $sth->bindParam(':ins', $params['insuranceprice'],      \PDO::PARAM_INT);
      $sth->bindParam(':res', $params['reservationfeeprice'], \PDO::PARAM_INT);
      $sth->bindParam(':dep', $params['departuredate'],       \PDO::PARAM_STR);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC); 
      foreach ($params['rooms'] as $room) {
        $sql = "INSERT INTO Rooms (tourid, label, price, size, numberavaliable) VALUES (:tid, :lab, :pri, :siz, :num);";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':tid', $result['id'],                  \PDO::PARAM_INT);
        $sth->bindParam(':lab', $room['label'],                 \PDO::PARAM_STR);
        $sth->bindParam(':pri', $room['price'],                 \PDO::PARAM_INT);
        $sth->bindParam(':siz', $room['size'],                  \PDO::PARAM_INT);
        $sth->bindParam(':num', $room['numberavaliable'],       \PDO::PARAM_STR);
        $sth->execute(); 
      }
      $this->pdo->commit();
    } catch(\PDOException $e) {
      $this->response->DBError($e, __CLASS__, $sql);
      $this->pdo->rollBack();
      $this->response->Exit(500);
    }
    return array('updatedid' => $result['id']);   
  }

  public function put(array $_params) {

  }

  public function delete(array $_params) {

  }

  private function paramsValidationWithExit($params) {
    $passed = true;
    $result = array();

    if (isset($params['label'])) {
      $result['label'] = Functions::sanatizeStringUnsafe($params['label']);
    } else {
      $result['label'] = '';
    }
    if (empty($result['label'])) {
      $this->response->AddResponse('error', 'Resan måste ha en benämning.');
      $this->response->AddResponsePushToArray('invalidFields', array('label'));
      $passed = false;
    }

    if (isset($params['insuranceprice'])) {
      $result['insuranceprice'] = Functions::validateInt($params['insuranceprice']);
    } else {
      $result['insuranceprice'] = NULL;
    }
    if (is_null($result['insuranceprice'])) {
      $this->response->AddResponse('error', 'Avbeställningsavgift måste anges med ett heltal.');
      $this->response->AddResponsePushToArray('invalidFields', array('insuranceprice'));
      $passed = false;
    }

    if (isset($params['reservationfeeprice'])) {
      $result['reservationfeeprice'] = Functions::validateInt($params['reservationfeeprice']);
    } else {
      $result['reservationfeeprice'] = NULL;
    }
    if (is_null($result['reservationfeeprice'])) {
      $this->response->AddResponse('error', 'Anmälningsavgift måste anges med ett heltal.');
      $this->response->AddResponsePushToArray('invalidFields', array('reservationfeeprice'));
      $passed = false;
    }

    if (isset($params['departuredate'])) {
      $result['departuredate'] = Functions::validateDate($params['departuredate']);
    } else {
      $result['departuredate'] = NULL;
    }
    if (is_null($result['departuredate'])) {
      $this->response->AddResponse('error', 'Avresedatum måste anges med ett datum. Helst i format ÅÅÅÅ-MM-DD.');
      $this->response->AddResponsePushToArray('invalidFields', array('departuredate'));
      $passed = false;
    }

    foreach($params['rooms'] as $key=>$room) {
      if (isset($room['label'])) {
        $result['rooms'][$key]['label'] = Functions::sanatizeStringUnsafe($room['label']);
      } else {
        $result['rooms'][$key]['label'] = '';
      }
      if (empty($result['rooms'][$key]['label'])) {
        $this->response->AddResponse('error', 'Rumstypen måste ha en benämning.');
        $this->response->AddResponsePushToArray('invalidFields', array('rooms.' . [$key] . '.label'));
        $passed = false;
      }
      
      if (isset($room['price'])) {
        $result['rooms'][$key]['price'] = Functions::validateInt($room['price']);
      } else {
        $result['rooms'][$key]['price'] = NULL;
      }
      if (is_null($result['rooms'][$key])) {
        $this->response->AddResponse('error', 'Rumspris måste anges som ett heltal.');
        $this->response->AddResponsePushToArray('invalidFields', array('rooms.' . [$key] . '.price'));
        $passed = false;
      }
  
      if (isset($room['size'])) {
        $result['rooms'][$key]['size'] = Functions::validateInt($room['size'], -2147483648, 2147483647);
      } else {
        $result['rooms'][$key]['size'] = NULL;
      }
      if (is_null($result['rooms'][$key]['size'])) {
        $this->response->AddResponse('error', 'Antal personer per rum måste anges som ett heltal.');
        $this->response->AddResponsePushToArray('invalidFields', array('rooms.' . [$key] . '.size'));
        $passed = false;
      }

      if (isset($room['numberavaliable'])) {
        $result['rooms'][$key]['numberavaliable'] = Functions::validateInt($room['numberavaliable'], -2147483648, 2147483647);
      } else {
        $result['rooms'][$key]['numberavaliable'] = NULL;
      }
      if (is_null($result['rooms'][$key]['numberavaliable'])) {
        $this->response->AddResponse('error', 'Antal tillgängliga rum måste anges som ett heltal.');
        $this->response->AddResponsePushToArray('invalidFields', array('rooms.' . [$key] . '.numberavaliable'));
        $passed = false;
      }
    }

    $result['id'] = $params['id'];
    if ($passed) {
      return $result;
    } else {
      $this->response->AddResponse('response', 'Ogiltig data skickad. Begäran avbruten.');
      $this->response->Exit(400);
    }
  }

}