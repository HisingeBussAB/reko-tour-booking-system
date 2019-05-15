<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class GroupCustomers extends Model {

  public function get(array $params) {
    if ($params['id'] > 0 || $params['id'] == -1) {
      try {
        $sql = "SELECT  id
                    ,organisation
                    ,firstName
                    ,lastName
                    ,street
                    ,zip
                    ,city
                    ,phone
                    ,email
                    ,personalNumber	
                    ,date
                    ,compare FROM GroupCustomers";
        $sql .= ($params['id'] != -1) 
                    ? " WHERE id = :id"
                    : "";
        $sql .= " ORDER BY organisation, lastName, firstName, date ASC;";
        $sth = $this->pdo->prepare($sql);
        if ($params['id'] != -1) { $sth->bindParam(':id', $params['id'], \PDO::PARAM_INT); }
        $sth->execute(); 
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      if (count($result) < 1 && $params['id'] != -1) {
        $this->response->AddResponse('error', 'Gruppkunden hittades inte.');
        $this->response->Exit(404);
      } else {
        foreach ($result as $key=>$client) {
          try {
            $sql = "SELECT DISTINCT Categories.id as id, label 
                      FROM Categories 
                      INNER JOIN Categories_GroupCustomers
                        ON Categories_GroupCustomers.categoryid = Categories.id 
                      WHERE Categories_GroupCustomers.groupid = :id
                      ORDER BY label ASC;";
            $sth = $this->pdo->prepare($sql);
            $sth->bindParam(':id', $client['id'], \PDO::PARAM_INT);
            $sth->execute();
            $categoryresult = $sth->fetchAll(\PDO::FETCH_ASSOC); 
          } catch(\PDOException $e) {
            $this->response->DBError($e, __CLASS__, $sql);
            $this->response->Exit(500);
          }
          $result[$key]['categories'] = $categoryresult;
        }
        return array('newsletter' => $result);
      }
    } else {
      $this->response->AddResponse('error', 'GroupCuustomerid kan bara anges som ett positivt heltal, eller inte anges alls för alla gruppkunder.');
      $this->response->AddResponse('response', 'GroupCuustomerid kan bara anges som ett positivt heltal, eller inte anges alls för alla gruppkunder.');
      $this->response->Exit(404);
    }
    return false;
  }

  public function post(array $_params) {
    $params = $this->paramsValidationWithExit($_params);
    $sql = "SELECT id FROM Newsletter WHERE email = :email;";
    try {     
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':email', $params['email'],        \PDO::PARAM_STR);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC); 
    } catch(\PDOException $e) {
      $this->response->DBError($e, __CLASS__, $sql);
      $this->response->Exit(500);
    }
    if ($result != false) {
      $this->response->AddResponse('error', 'Adressen ' . $params['email'] . ' finns redan i systemet.');
      $this->response->AddResponse('response', 'Adressen ' . $params['email'] . ' finns redan i systemet.');
      $this->response->Exit(404);
    }
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
    if ($this->get(array('id' => $params['id'])) !== false) {
      try {
        $sql = "DELETE FROM Newsletter WHERE id = :id;";
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

    $result['email'] = (isset($params['email'])) 
      ? Functions::validateEmail($params['email']) : '';
    if (empty($result['email'])) {
      $this->response->AddResponse('error', 'En giltig e-postadress måste anges.');
      $this->response->AddResponsePushToArray('invalidFields', array('email'));
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