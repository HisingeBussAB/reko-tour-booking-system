<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class GroupCustomers extends Model {

  public function get(array $params) {
    if ($params['id'] > 0 || $params['id'] == -1) {
      try {
        $sql = "SELECT id
                    ,organisation
                    ,firstname
                    ,lastname
                    ,street
                    ,zip
                    ,city
                    ,phone
                    ,email
                    ,personalNumber	
                    ,date
                    ,compare 
                    ,isAnonymized
                    FROM GroupCustomers";
        $sql .= ($params['id'] != -1) 
                    ? " WHERE id = :id AND isAnonymized = 0"
                    : " WHERE isAnonymized = 0";
        $sql .= " ORDER BY organisation, lastname, firstname, date ASC;";
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
          $result[$key]['zip'] = ($client['zip'] == null OR $client['zip'] < 1) ? '' : $client['zip'];
        }
        return array('groupcustomers' => $result);
      }
    } else {
      $this->response->AddResponse('error', 'Groupcustomerid kan bara anges som ett positivt heltal, eller inte anges alls för alla gruppkunder.');
      $this->response->AddResponse('response', 'Groupcustomerid kan bara anges som ett positivt heltal, eller inte anges alls för alla gruppkunder.');
      $this->response->Exit(404);
    }
    return false;
  }

  public function post(array $_params) {
    $params = $this->paramsValidationWithExit($_params, true);
    $comp = Functions::getCompString($params['firstname'],$params['lastname'],$params['zip'],$params['street']);
    $sql = "INSERT INTO GroupCustomers (
      organisation
      ,firstname
      ,lastname
      ,street
      ,zip
      ,city
      ,phone
      ,email
      ,personalNumber	
      ,date
      ,compare
      ,isAnonymized) 
    VALUES (
      :organisation
      ,:firstname
      ,:lastname
      ,:street
      ,:zip
      ,:city
      ,:phone
      ,:email
      ,:personalNumber
      ,:date
      ,:compare
      ,0);";
    try {     
      $this->pdo->beginTransaction();
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':organisation',      $params['organisation'],   \PDO::PARAM_STR);
      $sth->bindParam(':firstname',         $params['firstname'],      \PDO::PARAM_STR);
      $sth->bindParam(':lastname',          $params['lastname'],       \PDO::PARAM_STR);
      $sth->bindParam(':street',            $params['street'],         \PDO::PARAM_STR);
      $sth->bindParam(':zip',               $params['zip'],            \PDO::PARAM_INT);
      $sth->bindParam(':city',              $params['city'],           \PDO::PARAM_STR);
      $sth->bindParam(':phone',             $params['phone'],          \PDO::PARAM_STR);
      $sth->bindParam(':email',             $params['email'],          \PDO::PARAM_STR);
      $sth->bindParam(':personalNumber',    $params['personalNumber'], \PDO::PARAM_STR);
      $sth->bindParam(':date',              $params['date'],           \PDO::PARAM_STR);
      $sth->bindParam(':compare',           $comp,                     \PDO::PARAM_STR);
      $sth->execute(); 
      $sql = "SELECT LAST_INSERT_ID() as id;";
      $sth = $this->pdo->prepare($sql);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC); 
      foreach ($params['categories'] as $category) {
        $sql = "INSERT INTO Categories_GroupCustomers (groupId, categoryId) VALUES (:gid, :cid);";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':gid', $result['id'],                  \PDO::PARAM_INT);
        $sth->bindParam(':cid', $category['id'],                \PDO::PARAM_INT);
        $sth->execute();
      } 
      $this->pdo->commit();
    } catch(\PDOException $e) {
      $this->pdo->rollBack();
      $this->response->DBError($e, __CLASS__, $sql);
      $this->response->Exit(500);
    }
    return array('updatedid' => $result['id']);   
  }

  public function put(array $_params) {
    $params = $this->paramsValidationWithExit($_params, true);
    $comp = Functions::getCompString($params['firstname'],$params['lastname'],$params['zip'],$params['street']);
    if ($this->get(array('id' => $params['id'])) !== false) {
      try {
        $sql = "UPDATE GroupCustomers SET 
        organisation  = :organisation
        ,firstname    = :firstname
        ,lastname     = :lastname
        ,street       = :street
        ,zip          = :zip 
        ,city         = :city
        ,phone        = :phone
        ,email        = :email
        ,personalNumber	 = :personalNumber
        ,date	        = :date
        ,compare	    = :compare
        ,isAnonymized = 0
        WHERE id = :id AND isAnonymized = 0;";
        
        $this->pdo->beginTransaction();
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id',                $params['id'],             \PDO::PARAM_INT);
        $sth->bindParam(':organisation',      $params['organisation'],   \PDO::PARAM_STR);
        $sth->bindParam(':firstname',         $params['firstname'],      \PDO::PARAM_STR);
        $sth->bindParam(':lastname',          $params['lastname'],       \PDO::PARAM_STR);
        $sth->bindParam(':street',            $params['street'],         \PDO::PARAM_STR);
        $sth->bindParam(':zip',               $params['zip'],            \PDO::PARAM_INT);
        $sth->bindParam(':city',              $params['city'],           \PDO::PARAM_STR);
        $sth->bindParam(':phone',             $params['phone'],          \PDO::PARAM_STR);
        $sth->bindParam(':email',             $params['email'],          \PDO::PARAM_STR);
        $sth->bindParam(':personalNumber',    $params['personalNumber'], \PDO::PARAM_STR);
        $sth->bindParam(':date',              $params['date'],           \PDO::PARAM_STR);
        $sth->bindParam(':compare',           $comp,                     \PDO::PARAM_STR);
        $sth->execute(); 
        $sql = "DELETE FROM Categories_GroupCustomers WHERE groupId = :gid;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':gid',               $params['id'],             \PDO::PARAM_INT);
        $sth->execute(); 
        foreach ($params['categories'] as $category) {
          $sql = "INSERT INTO Categories_GroupCustomers (groupId, categoryId) VALUES (:gid, :cid);";
          $sth = $this->pdo->prepare($sql);
          $sth->bindParam(':gid', $params['id'],                  \PDO::PARAM_INT);
          $sth->bindParam(':cid', $category['id'],                \PDO::PARAM_INT);
          $sth->execute();
        } 
        $this->pdo->commit();
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
        $sql = "DELETE FROM GroupCustomers WHERE id = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute();
        $sql = "DELETE FROM Categories_GroupCustomers WHERE groupid = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute();
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      return array('updatedid' => $params['id']);
    }
    if ($this->get(array('id' => $params['id'])) !== false) {
      try {
        $params['organisation'] = substr(md5(mt_rand()),0,4);
        $params['firstname'] = substr(md5(mt_rand()),0,4);
        $params['lastname'] = substr(md5(mt_rand()),0,4);
        $params['street'] = substr(md5(mt_rand()),0,2);
        $params['zip'] = '0';
        $params['city'] = substr(md5(mt_rand()),0,2);
        $params['phone'] = '0';
        $params['email'] = substr(md5(mt_rand()),0,2);
        $params['personalNumber'] = '0';
        $comp = Functions::getCompString($params['firstname'],$params['lastname'],$params['zip'],$params['street']);

        $sql = "UPDATE GroupCustomers SET 
        organisation  = :organisation
        ,firstname    = :firstname
        ,lastname     = :lastname
        ,street       = :street
        ,zip          = :zip 
        ,city         = :city
        ,phone        = :phone
        ,email        = :email
        ,personalNumber	 = :personalNumber
        ,date	        = :date
        ,compare	    = :compare
        ,isAnonymized = 1
        WHERE id = :id;";
        
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id',                $params['id'],             \PDO::PARAM_INT);
        $sth->bindParam(':organisation',      $params['organisation'],   \PDO::PARAM_STR);
        $sth->bindParam(':firstname',         $params['firstname'],      \PDO::PARAM_STR);
        $sth->bindParam(':lastname',          $params['lastname'],       \PDO::PARAM_STR);
        $sth->bindParam(':street',            $params['street'],         \PDO::PARAM_STR);
        $sth->bindParam(':zip',               $params['zip'],            \PDO::PARAM_INT);
        $sth->bindParam(':city',              $params['city'],           \PDO::PARAM_STR);
        $sth->bindParam(':phone',             $params['phone'],          \PDO::PARAM_STR);
        $sth->bindParam(':email',             $params['email'],          \PDO::PARAM_STR);
        $sth->bindParam(':personalNumber',    $params['personalNumber'], \PDO::PARAM_STR);
        $sth->bindParam(':date',              $params['date'],           \PDO::PARAM_STR);
        $sth->bindParam(':compare',           $comp,                     \PDO::PARAM_STR);
        $sth->execute();
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      return array('updatedid' => $params['id']);
    }
    return false;
  }

  

  private function paramsValidationWithExit($params, $validateCategories = false) {
    $passed = true;
    $result = array();
    if (isset($params['organisation'])) {
      $result['organisation'] = Functions::sanatizeStringUnsafe($params['organisation'], 200);
    } else {
      $result['organisation'] = '';
    }
    if (empty($result['organisation'])) {
      $result['organisation'] = 'Privat grupp';
    }
    if (isset($params['firstname']) && !empty($params['firstname'])) {
      $result['firstname'] = Functions::sanatizeStringUnsafe($params['firstname'], 100);
      if (is_null($result['firstname'])) {
        $this->response->AddResponse('error', 'Förnamn innehåller ogiltiga tecken.');
        $this->response->AddResponsePushToArray('invalidFields', array('firstname'));
        $passed = false;
    } 
    } else {
      $result['firstname'] = '';
    }
    if (isset($params['lastname']) && !empty($params['lastname'])) {
      $result['lastname'] = Functions::sanatizeStringUnsafe($params['lastname'], 100);
      if (is_null($result['lastname'])) {
        $this->response->AddResponse('error', 'Efternamn innehåller ogiltiga tecken.');
        $this->response->AddResponsePushToArray('invalidFields', array('lastname'));
        $passed = false;
      } 
    } else {
      $result['lastname'] = '';
    }

    if (empty($result['lastname']) && empty($result['firstname']) && $result['organisation'] == 'Privat grupp') {
      $this->response->AddResponse('error', 'Något av förnamn, efternamn eller organisation måste anges.');
      $this->response->AddResponsePushToArray('invalidFields', array('firstname','lastname','organisation'));
      $passed = false;
    }

    if (isset($params['street']) && !empty($params['street'])) {
      $result['street'] = Functions::sanatizeStringUnsafe($params['street'], 100);
      if (is_null($result['street'])) {
        $this->response->AddResponse('error', 'Gatunamnet innehåller ogiltiga tecken.');
        $this->response->AddResponsePushToArray('invalidFields', array('street'));
        $passed = false;
      }
    } else {
      $result['street'] = '';
    }
   
    if (isset($params['zip']) && !empty($params['zip'])) {
      $result['zip'] = Functions::validateZIP($params['zip']);
      if (is_null($result['street'])) {
        $this->response->AddResponse('error', 'Postnummret innehåller ogiltiga tecken.');
        $this->response->AddResponsePushToArray('invalidFields', array('zip'));
        $passed = false;
      }
    } else {
      $result['zip'] = '';
    }
    
    if (isset($params['city']) && !empty($params['city'])) {
      $result['city'] = Functions::sanatizeStringUnsafe($params['city'], 100);
      if (is_null($result['city'])) {
        $this->response->AddResponse('error', 'Stadsnamet innehåller ogiltiga tecken.');
        $this->response->AddResponsePushToArray('invalidFields', array('city'));
        $passed = false;
      }
    } else {
      $result['city'] = '';
    }
    
    if (isset($params['phone']) && !empty($params['phone'])) {
      $result['phone'] = Functions::validatePhone($params['phone']);
      if (is_null($result['phone'])) {
        $this->response->AddResponse('error', 'Telefonnummret innehåller ogiltiga tecken.');
        $this->response->AddResponsePushToArray('invalidFields', array('phone'));
        $passed = false;
      }
    } else {
      $result['phone'] = '';
    }

    if (isset($params['email']) && !empty($params['email'])) {
      $result['email'] = Functions::validateEmail($params['email']);
      if (is_null($result['email'])) {
        $this->response->AddResponse('error', 'E-post addressen måste ha giltigt format.');
        $this->response->AddResponsePushToArray('invalidFields', array('email'));
        $passed = false;
      }
    } else {
      $result['email'] = '';
    }

    if (isset($params['personalnumber']) && !empty($params['personalnumber'])) {
      $result['personalNumber'] = Functions::validatePersonalNumber($params['personalnumber']);
      if (is_null($result['personalNumber'])) {
        $this->response->AddResponse('error', 'Personnummer anges XXXXXX-XXXX och måste ha giltig kontrollsiffra.');
        $this->response->AddResponsePushToArray('invalidFields', array('personalNumber'));
        $passed = false;
      }
    } else {
      $result['personalNumber'] = '';
    }

    if (isset($params['date'])) {
      $result['date'] = Functions::validateDate($params['date']);
    } else {
      $result['date'] = Functions::validateDate((string)date_create()->format('Y-m-d'));
    }
    if (is_null($result['date'])) {
      $this->response->AddResponse('error', 'Datumet är ogiltigt. Ange i format YYYY-MM-DD.');
      $this->response->AddResponsePushToArray('invalidFields', array('date'));
      $passed = false;
    }
    

    $result['categories'] = array();
    if (isset($params['categories']) && is_array($params['categories'])) {
      foreach($params['categories'] as $key=>$category) {
        if (isset($category['id'])) {
          $result['categories'][$key]['id'] = Functions::sanatizeStringUnsafe($category['id']);
        } else {
          $result['categories'][$key]['id'] = '';
        }

        if ($validateCategories) {
          $flag = true;
          if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            try {
              $sql = "SELECT DISTINCT categoryid as id
                        FROM Categories_GroupCustomers
                        WHERE groupid = :id;";
              $sth = $this->pdo->prepare($sql);
              $sth->bindParam(':id', $params['id'], \PDO::PARAM_INT);
              $sth->execute();
              $categoryids = $sth->fetchAll(\PDO::FETCH_ASSOC); 
            } catch(\PDOException $e) {
              $this->response->DBError($e, __CLASS__, $sql);
              $this->response->Exit(500);
            }
            $org = array();
            $in = array();
            foreach($categoryids as $c) {
              array_push($org, $c['id']);
            }
            foreach($params['categories'] as $c) {
              array_push($in, $c['id']);
            }
            if (sizeof(array_diff($org,$in)) == 0) {
              $flag = false;
            }
          }
          if ($flag) {
            $Categories = new Categories($this->response, $this->pdo);
            if (empty($result['categories'][$key]['id']) || $Categories->get(array('id' => $category['id'])) == false) {
              $this->response->AddResponse('error', 'Kategori id: ' . $category['id'] . ' är borttagen eller ogiltig.');
              $this->response->AddResponsePushToArray('invalidFields', array('categories.' . $key . '.id'));
              $passed = false;
              }
            }
          }
        }
      }

    if (isset($params['isanonymized'])) {
      $result['isAnonymized'] = Functions::validateBoolToBit($params['isanonymized']);
    } else {
      $result['isAnonymized'] = -1;
    }
    if (is_null($result['isAnonymized'])) {
      $this->response->AddResponse('error', 'Anonymizerad måste anges som true eller false.');
      $this->response->AddResponsePushToArray('invalidFields', array('isAnonymized'));
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