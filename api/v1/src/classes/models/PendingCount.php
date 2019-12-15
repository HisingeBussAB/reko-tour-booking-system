<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class PendingCount extends Model {

  public function get(array $params) {
    if ($this->pdo == false) {
      $this->response->AddResponse('response',  'Kritiskt fel. Databasanslutning misslyckades.');
      $this->response->Exit(500);
    }
    $pending = array('bookings' => 0, 'leads' => 0, 'newsletter' => 0);
    try {
        $sql = "SELECT id FROM bokningar WHERE processed = 0;";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(); 
        $resultBookings = $sth->fetchAll(\PDO::FETCH_ASSOC); 
        $sql = "SELECT id FROM leads WHERE processed = 0;";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(); 
        $resultLeads = $sth->fetchAll(\PDO::FETCH_ASSOC); 
        $sql = "SELECT id FROM newsletter WHERE processed = 0;";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(); 
        $resultNewsletter = $sth->fetchAll(\PDO::FETCH_ASSOC); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      if (count($resultBookings) > 0) {
        $pending['bookings'] = count($resultBookings);
      }
      if (count($resultLeads) > 0) {
        $pending['leads'] = count($resultLeads);
      }
      if (count($resultNewsletter) > 0) {
        $pending['newsletter'] = count($resultNewsletter);
      }
      
    return array('pendingcount' => $pending);
  }

  public function post(array $_params) {
    return false;    
  }

  public function put(array $_params) {
    return false;    
  }

  public function delete(array $params) {
    return false;    
  }

}