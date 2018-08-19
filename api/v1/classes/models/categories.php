<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class Categories extends Model {

  public function get(array $params) {
    if ($params['id'] > 0 || $params['id'] == -1) {
      try {
        if ($params['id'] == -1) {
          $sql = "SELECT * FROM Categories;";
        } else {
          $sql = "SELECT * FROM Categories WHERE id = :id;";
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
        $this->response->AddResponse('error', 'Kategorin hittades inte.');
        $this->response->Exit(404);
      } else {
        $i = 0;
        foreach ($result as $item) {
          $result[$i]['active'] = filter_var($result[$i]['active'], FILTER_VALIDATE_BOOLEAN);
          $i++;
        }
        return array('categories' => $result);
      }
    } else {
      $this->response->AddResponse('error', 'Kategori id kan bara anges som ett positivt heltal, eller inte anges alls för alla kategorier.');
      $this->response->AddResponse('response', 'Begäran avbruten felaktigt id.');
      $this->response->Exit(400);
    }
    return false;
  }

  public function post(array $_params, bool $trash = false) {
    
    if (!$trash) {
      $params = $this->paramsValidationWithExit($_params);
      if ($params['active'] == -1) {
        $params['active'] = 1;
      } 
      $sql = "INSERT INTO Categories (label, active) OUTPUT INSERTED.id VALUES (:cat, :act);";
    } else {
      $params = $_params;
      $sql = "INSERT INTO [trashCategories] (id, label, active) VALUES (:id, :cat, :act);";
    }
    //SET IDENTITY_INSERT [trashCategories] ON
    //TODO Transaction for trash archiving;
    var_dump($params);
    try {     
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':cat', $params['label'],   \PDO::PARAM_STR);
      $sth->bindParam(':act', $params['active'],     \PDO::PARAM_INT);
      if ($trash) { $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT); }
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
        if ($params['active'] == -1) {
          $sql = "UPDATE Categories SET label = :cat WHERE id = :id;";
        } else {
          $sql = "UPDATE Categories SET label = :cat, active = :act WHERE id = :id;";
        }
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->bindParam(':cat', $params['label'],  \PDO::PARAM_STR);
        if ($params['active'] != -1) { $sth->bindParam(':act', $params['active'],     \PDO::PARAM_INT); }
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
    $originalData = $this->get(array('id' => $params['id']));
    if ($originalData !== false) {
      try {
        $sql = "DELETE FROM Categories WHERE id = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute(); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      $this->post($originalData['categories'], true);
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

    if (isset($params['active'])) {
      $result['active'] = Functions::validateBoolToBit($params['active']);
    } else {
      $result['active'] = -1;
    }
    if (is_null($result['active'])) {
      $this->response->AddResponse('error', 'Aktiv måste anges som true eller false.');
      $this->response->AddResponsePushToArray('invalidFields', array('active'));
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

