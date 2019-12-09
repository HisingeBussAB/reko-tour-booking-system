<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class Budgets extends Model {

  public function get(array $params) {

    if ($params['id'] > 0 || $params['id'] == -1) {
      try {
        $sql = "SELECT Budgets.id as id
                  ,budgetgroupid
                  ,Budgets.label as label
                  ,Tours.label as tourLabelCalc
                  ,tourid
                  ,estimatedpax
                  ,actualpax
                  ,estimatedsurplus
                  ,cast(createdDate as date) as createdDate
                  ,departureDate
                  ,cast(COALESCE(departureDate, createdDate) as date) as sortdateCalc
                  ,isLocked
                  ,Budgets.isDisabled as isDisabled
                  ,Budgets.isDeleted as isDeleted
                  FROM Budgets
                  LEFT JOIN Tours ON Budgets.tourid = Tours.id";
        if ($params['id'] == -1) {
          $sql .= " WHERE Budgets.isDeleted = 0 ORDER BY COALESCE(departureDate, createdDate) DESC, Tours.label ASC;";
        } else {
          $sql .= " WHERE Budgets.id = :id AND Budgets.isDeleted = 0;";
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
        $this->response->AddResponse('error', 'Kalkylen hittades inte.');
        return false;
      } else {
        foreach ($result as $key=>$item) {
          $result[$key]['isdisabled'] = filter_var($result[$key]['isdisabled'], FILTER_VALIDATE_BOOLEAN);
          try {
            $sql = "SELECT `id`, `budgetId`, `label`, estimatedamount, actualamount, `isfixed` FROM `Budgets_Costs` WHERE budgetId = :id;";
            $sth = $this->pdo->prepare($sql);
            $sth->bindParam(':id', $item['id'], \PDO::PARAM_INT);
            $sth->execute();
            $costsresult = $sth->fetchAll(\PDO::FETCH_ASSOC); 
          } catch(\PDOException $e) {
            $this->response->DBError($e, __CLASS__, $sql);
            $this->response->Exit(500);
          }
          try {
            $sql = "SELECT `id`, `budgetid`, paymentid, `label`, price, amount FROM `Budgets_Sales` WHERE budgetId = :id;";
            $sth = $this->pdo->prepare($sql);
            $sth->bindParam(':id', $item['id'], \PDO::PARAM_INT);
            $sth->execute();
            $salesresult = $sth->fetchAll(\PDO::FETCH_ASSOC); 
          } catch(\PDOException $e) {
            $this->response->DBError($e, __CLASS__, $sql);
            $this->response->Exit(500);
          }
          foreach ($costsresult as $key=>$cost) {
            $costsresult[$key]['isfixed'] = filter_var($cost['isfixed'], FILTER_VALIDATE_BOOLEAN);
          }
          $result[$key]['costs'] = $costsresult;
          $result[$key]['sales'] = $salesresult;
        }
        return array('budgets' => $result);
      }
    } else {
      $this->response->AddResponse('error', 'Kalkyl id kan bara anges som ett positivt heltal, eller inte anges alls för alla kalkyler.');
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

    $sql = "INSERT INTO Budgets_Group (label, isDisabled, isdeleted) VALUES (:cat, :act, 0);";
    try {     
      $this->pdo->beginTransaction();
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':cat', $params['label'],          \PDO::PARAM_STR);
      $sth->bindParam(':act', $params['isDisabled'],     \PDO::PARAM_INT);
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
  
    return false;    
  }

  public function put(array $_params) {
    return false;    
  }

  public function delete(array $params) {
 
    
    return false;    
    
  }

  private function paramsValidationWithExit($params) {
    $passed = true;
    $result = array();
    if (isset($params['label'])) {
      $result['label'] = Functions::sanatizeStringUnsafe($params['label'], 60);
    } else {
      $result['label'] = '';
    }
    if (empty($result['label'])) {
      $this->response->AddResponse('error', 'Kalkylen måste ha en benämning.');
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

