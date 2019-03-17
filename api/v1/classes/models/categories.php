<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class Categories extends Model {

  public function get(array $params) {

    if ($params['id'] > 0 || $params['id'] == -1) {
      try {
        if ($params['id'] == -1) {
          $sql = "SELECT id, label, isdisabled, isdeleted FROM Categories WHERE isDeleted = 0 ORDER BY label ASC;";
        } else {
          $sql = "SELECT id, label, isdisabled, isdeleted FROM Categories WHERE id = :id AND isDeleted = 0;";
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
        $this->response->AddResponse('error', 'Kategorin hittades inte.');
        $this->response->Exit(404);
      } else {
        $i = 0;
        foreach ($result as $item) {
          $result[$i]['isdisabled'] = filter_var($result[$i]['isdisabled'], FILTER_VALIDATE_BOOLEAN);
          $i++;
        }
        return array('categories' => $result);
      }
    } else {
      $this->response->AddResponse('error', 'Kategori id kan bara anges som ett positivt heltal, eller inte anges alls för alla kategorier.');
      $this->response->AddResponse('response', 'Begäran avbruten felaktigt id.');
      $this->response->Exit(404);
    }
    return false;
  }

  public function post(array $_params) {
    
    $params = $this->paramsValidationWithExit($_params);
    if ($params['isdisabled'] == -1) {
      $params['isdisabled'] = 0;
    } 
    $sql = "INSERT INTO Categories (label, isdisabled, isdeleted) VALUES (:cat, :act, 0);";

    try {     
      $this->pdo->beginTransaction();
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':cat', $params['label'],          \PDO::PARAM_STR);
      $sth->bindParam(':act', $params['isdisabled'],     \PDO::PARAM_INT);
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
        if ($params['isdisabled'] == -1) {
          $sql = "UPDATE Categories SET label = :cat WHERE id = :id AND isDeleted = 0;";
        } else {
          $sql = "UPDATE Categories SET label = :cat, isDisabled = :act WHERE id = :id AND isDeleted = 0;";
        }
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->bindParam(':cat', $params['label'],  \PDO::PARAM_STR);
        if ($params['isdisabled'] != -1) { $sth->bindParam(':act', $params['isdisabled'],     \PDO::PARAM_INT); }
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
    if (ENV_DEBUG_MODE && !empty($_GET["forceReal"]) && Functions::validateBoolToBit($_GET["forceReal"])) {
      //Allows true deletes while running tests or after debugging, does not validate exiting ID
      try {
        $sql = "DELETE FROM Categories WHERE id = :id;";
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

  private function paramsValidationWithExit($params) {
    $passed = true;
    $result = array();
    if (isset($params['label'])) {
      $result['label'] = Functions::sanatizeStringUnsafe($params['label']);
    } else {
      $result['label'] = '';
    }
    if (empty($result['label'])) {
      $this->response->AddResponse('error', 'Kategorin måste ha en benämning.');
      $this->response->AddResponsePushToArray('invalidFields', array('label'));
      $passed = false;
    }

    if (isset($params['isdisabled'])) {
      $result['isdisabled'] = Functions::validateBoolToBit($params['isdisabled']);
    } else {
      $result['isdisabled'] = -1;
    }
    if (is_null($result['isdisabled'])) {
      $this->response->AddResponse('error', 'Avaktiverad måste anges som true eller false.');
      $this->response->AddResponsePushToArray('invalidFields', array('isDisabled'));
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

