<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class Newsletter extends Model {

  public function get(array $params) {
    if ($params['id'] > 0 || $params['id'] == -1) {
      try {
        $sql = ($params['id'] == -1) 
          ? "SELECT id, email FROM Newsletter ORDER BY email ASC;"
          : "SELECT id, email FROM Newsletter WHERE id = :id ORDER BY email ASC;";
        
        $sth = $this->pdo->prepare($sql);
        if ($params['id'] != -1) { $sth->bindParam(':id', $params['id'], \PDO::PARAM_INT); }
        $sth->execute(); 
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      if (count($result) < 1 && $params['id'] != -1) {
        $this->response->AddResponse('error', 'Nyhetbrev mottagare hittades inte.');
        $this->response->Exit(404);
      } else {
        return array('newsletter' => $result);
      }
    } else {
      $this->response->AddResponse('error', 'Newsletterid kan bara anges som ett positivt heltal, eller inte anges alls för alla resor.');
      $this->response->AddResponse('response', 'Newsletterid kan bara anges som ett positivt heltal, eller inte anges alls för alla resor.');
      $this->response->Exit(404);
    }
    return false;
  }

  public function post(array $_params) {
    $params = $this->paramsValidationWithExit($_params);
    $sql = "INSERT INTO Newsletter (email) VALUES (:email);";
    try {     
      $this->pdo->beginTransaction();
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':email', $params['email'],        \PDO::PARAM_STR);
      $sth->execute(); 
      $sql = "SELECT LAST_INSERT_ID() as id;";
      $sth = $this->pdo->prepare($sql);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC); 
      $this->pdo->commit();
    } catch(\PDOException $e) {
      $this->pdo->rollBack();
      $this->response->DBError($e, __CLASS__, $sql);
      $this->response->Exit(500);
    }
    return array('updatedid' => $result['id']);   
  }

  public function put(array $_params) {
    $params = $this->paramsValidationWithExit($_params);
    if ($this->get(array('id' => $params['id'])) !== false) {
      try {
        $sql = "UPDATE Newsletter SET email = :email WHERE id = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->bindParam(':email', $params['email'],  \PDO::PARAM_STR);
        $sth->execute(); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      return array('updatedid' => $params['id']);
    }
    return false;    

  }

  public function delete(array $params) {
    try {
    $sql = "DELETE FROM Newsletter WHERE id = :id;";
    $sth = $this->pdo->prepare($sql);
    $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
    $sth->execute();
  } catch(\PDOException $e) {
    $this->response->DBError($e, __CLASS__, $sql);
    $this->response->Exit(500);
  }
}
if ($this->get(array('id' => $params['id'])) !== false) {
  try {
    $sql = "UPDATE Categories SET isdeleted = 1 WHERE id = :id;";
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
          $result['rooms'][$key]['label'] = Functions::sanatizeStringUnsafe($room['label']);
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