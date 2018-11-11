<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class Categories extends Model {

  public function get(array $params) {
    if ($params['id'] > 0 || $params['id'] == -1) {
      try {
        if ($params['id'] == -1) {
          $sql = "SELECT id, label, isDisabled FROM Categories WHERE isDeleted = 0 ORDER BY label ASC;";
        } else {
          $sql = "SELECT id, label, isDisabled FROM Categories WHERE id = :id;";
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
          $result[$i]['isDisabled'] = filter_var($result[$i]['isDisabled'], FILTER_VALIDATE_BOOLEAN);
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
    if ($params['isDisabled'] == -1) {
      $params['isDisabled'] = 0;
    } 
    $sql = "INSERT INTO Categories (label, isDisabled, isDeleted) OUTPUT INSERTED.id VALUES (:cat, :act, 0);";

    try {     
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':cat', $params['label'],          \PDO::PARAM_STR);
      $sth->bindParam(':act', $params['isDisabled'],     \PDO::PARAM_INT);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC); 
    } catch(\PDOException $e) {
      $this->response->DBError($e, __CLASS__, $sql);
      $this->response->Exit(500);
    }
    return array('updatedid' => $result['id']);   
  }

  public function put(array $_params) {
    $params = $this->paramsValidationWithExit($_params);

    if ($this->get(array('id' => $params['id'])) !== false) {
      try {
        if ($params['isDisabled'] == -1) {
          $sql = "UPDATE Categories SET label = :cat WHERE id = :id;";
        } else {
          $sql = "UPDATE Categories SET label = :cat, isDisabled = :act WHERE id = :id;";
        }
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->bindParam(':cat', $params['label'],  \PDO::PARAM_STR);
        if ($params['isDisabled'] != -1) { $sth->bindParam(':act', $params['isDisabled'],     \PDO::PARAM_INT); }
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
    if ($this->get(array('id' => $params['id'])) !== false) {
      try {
        $sql = "UPDATE Categories SET isDeleted = 1 WHERE id = :id;";
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

    if (isset($params['isDisabled'])) {
      $result['isDisabled'] = Functions::validateBoolToBit($params['isDisabled']);
    } else {
      $result['isDisabled'] = -1;
    }
    if (is_null($result['isDisabled'])) {
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

