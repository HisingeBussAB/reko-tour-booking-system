<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class Categories extends Model {

  public function get($_param) {
   
  }

  public function post($_params) {
    $params = $this->paramsValidationWithExit($_params);
    if ($params['active'] == -1) {
      $params['active'] = 1;
    } 
    try {
      $sql = "INSERT INTO Kategori (Kategori, Aktiv) VALUES (:cat, :act);";
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':cat', $params['category'],   \PDO::PARAM_STR);
      $sth->bindParam(':act', $params['active'],     \PDO::PARAM_INT);
      $sth->execute(); 
    } catch(\PDOException $e) {
      $this->response->DBError($e, __CLASS__, $sql);
      $this->response->Exit(500);
    }
    $this->response->AddResponse('success', true);
    return true;    
  }

  public function put($param) {
    $id = $param['id'];
    if ($id > 0) {
      try {
        $sql = "SELECT kategoriid FROM Kategori WHERE kategoriid = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $id,     \PDO::PARAM_INT);
        $sth->execute(); 
        $result = $sth->fetch(\PDO::FETCH_ASSOC);
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      if (!$result) {
        $this->response->AddResponse('error', 'Kategorin som skall uppdateras hittades inte.');
        $this->response->Exit(404);
      }
    } else {
      $this->response->AddResponse('error', 'Kategori id måste anges med positivt heltal.');
      $this->response->AddResponse('response', 'Begäran avbruten felaktigt id.');
      $this->response->Exit(400);
    }
    $kategori = $this->validateCategory($param['category']);
    $aktiv = 1;
    if (isset($param['active'])) {
      $aktiv = $this->validateActive($param['active']);
    }
    if (is_null($aktiv) || $kategori == false) { 
      $this->response->AddResponse('response', 'Ogiltig data skickad. Begäran avbruten.');
      $this->response->Exit(400);
    }
    try {
      $sql = "UPDATE Kategori SET kategori = :cat, aktiv = :act WHERE kategoriid = :id;";
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':id', $id,     \PDO::PARAM_INT);
      $sth->bindParam(':cat', $kategori,  \PDO::PARAM_STR);
      $sth->bindParam(':act', $aktiv,     \PDO::PARAM_INT);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      $this->response->DBError($e, __CLASS__, $sql);
      $this->response->Exit(500);
    }
        
  }

  public function delete($param) {
    
  }

  private function paramsValidationWithExit($params) {
    $passed = true;
    $result = array();
    if (isset($params['category'])) {
      $result['category'] = Functions::sanatizeStringUnsafe($params['category']);
    } else {
      $result['category'] = '';
    }
    if (empty($result['category'])) {
      $this->response->AddResponse('error', 'Kategorin måste ha en benämning.');
      $this->response->AddResponsePushToArray('invalidFields', array('category'));
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

