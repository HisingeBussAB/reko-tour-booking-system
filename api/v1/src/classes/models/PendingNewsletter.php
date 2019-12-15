<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class PendingNewsletter extends Model {

  public function get(array $params) {
    if ($this->pdo == false) {
      $this->response->AddResponse('response',  'Kritiskt fel. Databasanslutning misslyckades.');
      $this->response->Exit(500);
    }
    try {
        $sql = "SELECT id, email, processed, arrived, ip FROM newsletter ORDER BY arrived ASC;";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(); 
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      if ($result !== false && is_array($result)) {
        try {
          $sql = "DELETE FROM newsletter WHERE processed = 1 AND arrived < '" . date("Y-m-d", strtotime("-5 weeks")) . "';";
          $sth = $this->pdo->prepare($sql);
          $sth->execute(); 
        } catch(\PDOException $e) {
          $this->response->DBError($e, __CLASS__, $sql);
          $this->response->Exit(500);
        }
        return array('pendingnewsletter' => $result);
      }
      return array('pendingnewsletter' => array());

  }

  public function post(array $_params) {
    return false;    
  }

  public function put(array $_params) {
    $id = filter_var($_params['id'],FILTER_SANITIZE_NUMBER_INT);
    try {
      $sql = "UPDATE newsletter SET processed = 1 WHERE id = :id;";
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':id', $id, \PDO::PARAM_INT);
      $sth->execute(); 
    } catch(\PDOException $e) {
      $this->response->DBError($e, __CLASS__, $sql);
      $this->response->Exit(500);
    }
    return array('updatedid' => $id);
  }

  public function delete(array $params) {
    return false;    
  }

}