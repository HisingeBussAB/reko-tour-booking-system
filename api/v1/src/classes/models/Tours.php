<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\models\Categories;
use RekoBooking\classes\Functions;

class Tours extends Model {

  public function get(array $params) {
    if ($params['id'] > 0 || $params['id'] == -1) {
      try {
        $sql = '';
        if ($params['id'] == -1) {
          $sql = "SELECT id, label, insuranceprice, reservationfeeprice, departuredate, isDisabled FROM Tours WHERE isDeleted = 0 ORDER BY departuredate DESC;";
        } else {
          $sql = "SELECT id, label, insuranceprice, reservationfeeprice, departuredate, isDisabled FROM Tours WHERE id = :id AND isDeleted = 0 ORDER BY departuredate DESC;";
        }
        $sth = $this->pdo->prepare($sql);
        if ($params['id'] != -1) { $sth->bindParam(':id', $params['id'], \PDO::PARAM_INT); }
        $sth->execute(); 
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      if (count($result) < 1 && $params['id'] != -1) {
        $this->response->AddResponse('error', 'Resan hittades inte.');
        $this->response->Exit(404);
      } else {
        $i = 0;
        foreach ($result as $key=>$tour) {
          $result[$key]['isdisabled'] = filter_var($result[$key]['isdisabled'], FILTER_VALIDATE_BOOLEAN);
          try {
            $sql = "SELECT id, label, price, size, numberavaliable FROM Rooms WHERE tourid = :id ORDER BY size ASC;";
            $sth = $this->pdo->prepare($sql);
            $sth->bindParam(':id', $tour['id'], \PDO::PARAM_INT);
            $sth->execute();
            $roomresult = $sth->fetchAll(\PDO::FETCH_ASSOC); 
          } catch(\PDOException $e) {
            $this->response->DBError($e, __CLASS__, $sql);
            $this->response->Exit(500);
          }
          try {
            $sql = "SELECT Categories.id as id, label 
                      FROM Categories 
                      INNER JOIN Categories_Tours 
                        ON Categories_Tours.categoryid = Categories.id 
                      WHERE Categories_Tours.tourid = :id
                      GROUP BY Categories.id, label 
                      ORDER BY label ASC;";
            $sth = $this->pdo->prepare($sql);
            $sth->bindParam(':id', $tour['id'], \PDO::PARAM_INT);
            $sth->execute();
            $categoryresult = $sth->fetchAll(\PDO::FETCH_ASSOC); 
          } catch(\PDOException $e) {
            $this->response->DBError($e, __CLASS__, $sql);
            $this->response->Exit(500);
          }
          $result[$key]['rooms'] = $roomresult;
          $result[$key]['categories'] = $categoryresult;
        }
        return array('tours' => $result);
      }
    } else {
      $this->response->AddResponse('error', 'Reseid kan bara anges som ett positivt heltal, eller inte anges alls för alla resor.');
      $this->response->AddResponse('response', 'Reseid kan bara anges som ett positivt heltal, eller inte anges alls för alla resor.');
      $this->response->Exit(404);
    }
    return false;
  }

  public function post(array $_params) {
    $params = $this->paramsValidationWithExit($_params);
    

    $sql = "INSERT INTO Tours (label, insuranceprice, reservationfeeprice, departuredate, isDisabled, isDeleted) VALUES (:lab, :ins, :res, :dep, 0, 0);";
    try {     
      $this->pdo->beginTransaction();
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':lab', $params['label'],               \PDO::PARAM_STR);
      $sth->bindParam(':ins', $params['insuranceprice'],      \PDO::PARAM_INT);
      $sth->bindParam(':res', $params['reservationfeeprice'], \PDO::PARAM_INT);
      $sth->bindParam(':dep', $params['departuredate'],       \PDO::PARAM_STR);
      $sth->execute(); 
      $sql = "SELECT LAST_INSERT_ID() as id;";
      $sth = $this->pdo->prepare($sql);
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
      foreach ($params['categories'] as $category) {
        $sql = "INSERT INTO Categories_Tours (tourid, categoryid) VALUES (:tid, :cid);";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':tid', $result['id'],                  \PDO::PARAM_INT);
        $sth->bindParam(':cid', $category['id'],                \PDO::PARAM_INT);
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
    $params = $this->paramsValidationWithExit($_params, 'put');
    $sql = "UPDATE Tours SET 
        label = :lab, 
        insuranceprice = :ins, 
        reservationfeeprice = :res, 
        departuredate = :dep, 
        isDisabled = :act
        WHERE id=:id;";
    try {     
      $this->pdo->beginTransaction();
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':id',  $params['id'],                  \PDO::PARAM_INT);
      $sth->bindParam(':lab', $params['label'],               \PDO::PARAM_STR);
      $sth->bindParam(':ins', $params['insuranceprice'],      \PDO::PARAM_INT);
      $sth->bindParam(':res', $params['reservationfeeprice'], \PDO::PARAM_INT);
      $sth->bindParam(':dep', $params['departuredate'],       \PDO::PARAM_STR);
      $sth->bindParam(':act', $params['isDisabled'],          \PDO::PARAM_INT);
      $sth->execute(); 
      $this->pdo->commit();
    } catch(\PDOException $e) {
      $this->response->DBError($e, __CLASS__, $sql);
      $this->pdo->rollBack();
      $this->response->Exit(500);
    }
    return array('updatedid' => $params['id']);   

  }

  public function delete(array $params) {
    if (ENV_DEBUG_MODE && !empty($_GET["forceReal"]) && Functions::validateBoolToBit($_GET["forceReal"])) {
      //Allows true deletes while running tests or after debugging
      //Start debug deleter
      try {
        $this->pdo->beginTransaction();
        $sql = "SELECT * FROM Tours WHERE id = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetch(\PDO::FETCH_ASSOC); 
        if (count($result) < 1) {
          return false;        
        }
        $sql = "DELETE FROM Rooms WHERE tourid = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute();
        $sql = "DELETE FROM Categories_Tours WHERE tourid = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute();
        $sql = "DELETE FROM Tours WHERE id = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute();
        $this->pdo->commit();
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->pdo->rollBack();
        $this->response->Exit(500);
      }
    }
    //End debug deleter

    if ($this->get(array('id' => $params['id'])) !== false) {
      try {
        $sql = "UPDATE Tours SET isDeleted = 1 WHERE id = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute();
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      return array('updatedid' => $params['id']);
    }
    
    return false;    
  }

  private function paramsValidationWithExit($params, $req = NULL) {
    $passed = true;
    $result = array();

    if (isset($params['label'])) {
      $result['label'] = Functions::sanatizeStringUnsafe($params['label'], 100);
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

    if (isset($params['isDisabled'])) {
      $result['isDisabled'] = Functions::validateBoolToBit($params['isDisabled']);
    } else {
      //default to 0
      $result['isDisabled'] = 0;
    }
    
    if (isset($params['categories']) && is_array($params['categories'])) {
      foreach($params['categories'] as $key=>$category) {
        if (isset($category['id'])) {
          $result['categories'][$key]['id'] = Functions::sanatizeStringUnsafe($category['id']);
        } else {
          $result['categories'][$key]['id'] = '';
        }
        $Categories = new Categories($this->response, $this->pdo);
        if (empty($result['categories'][$key]['id']) || $Categories->get(array('id' => $category['id'])) == false) {
          $this->response->AddResponse('error', 'Kategori id är ogiltigt.');
          $this->response->AddResponsePushToArray('invalidFields', array('categories.' . $key . '.id'));
          $passed = false;
        }
      }
    } elseif ($req != 'put') {
      $this->response->AddResponse('error', 'Minst en kategori måste anges för resan.');
      $this->response->AddResponsePushToArray('invalidFields', array('categories'));
      $passed = false;
    }
    
    
    if (isset($params['rooms']) && is_array($params['rooms'])) {
      foreach($params['rooms'] as $key=>$room) {
        if (isset($room['label'])) {
          $result['rooms'][$key]['label'] = Functions::sanatizeStringUnsafe($room['label'], 100);
        } else {
          $result['rooms'][$key]['label'] = '';
        }
        if (empty($result['rooms'][$key]['label'])) {
          $this->response->AddResponse('error', 'Rumstypen måste ha en benämning.');
          $this->response->AddResponsePushToArray('invalidFields', array('rooms.' . $key . '.label'));
          $passed = false;
        }
        
        if (isset($room['price'])) {
          $result['rooms'][$key]['price'] = Functions::validateInt($room['price']);
        } else {
          $result['rooms'][$key]['price'] = NULL;
        }
        if (is_null($result['rooms'][$key])) {
          $this->response->AddResponse('error', 'Rumspris måste anges som ett heltal.');
          $this->response->AddResponsePushToArray('invalidFields', array('rooms.' . $key . '.price'));
          $passed = false;
        }
    
        if (isset($room['size'])) {
          $result['rooms'][$key]['size'] = Functions::validateInt($room['size'], -2147483648, 2147483647);
        } else {
          $result['rooms'][$key]['size'] = NULL;
        }
        if (is_null($result['rooms'][$key]['size'])) {
          $this->response->AddResponse('error', 'Antal personer per rum måste anges som ett heltal.');
          $this->response->AddResponsePushToArray('invalidFields', array('rooms.' . $key . '.size'));
          $passed = false;
        }

        if (isset($room['numberavaliable'])) {
          $result['rooms'][$key]['numberavaliable'] = Functions::validateInt($room['numberavaliable'], -2147483648, 2147483647);
        } else {
          $result['rooms'][$key]['numberavaliable'] = NULL;
        }
        if (is_null($result['rooms'][$key]['numberavaliable'])) {
          $this->response->AddResponse('error', 'Antal tillgängliga rum måste anges som ett heltal.');
          $this->response->AddResponsePushToArray('invalidFields', array('rooms.' . $key . '.numberavaliable'));
          $passed = false;
        }
      }
    } elseif ($req != 'put') {
      $this->response->AddResponse('error', 'Minst en rumstyp måste anges för resan. För dagsresa lägg till ett rum av typen Dagsresa');
      $this->response->AddResponsePushToArray('invalidFields', array('rooms'));
      $passed = false;
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