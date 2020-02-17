<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class DepartureLists extends Model {

  public function get(array $params) {

    if (Functions::validateInt($params['id'],1) == $params['id']) {
      try {
        $sql = "SELECT DISTINCT 
          Bookings_Customers.id as id
          ,Tours.id as tourid
          ,Bookings_Customers.departureLocation as departurelocation
          ,TIME_FORMAT(Bookings_Customers.departureTime, '%H:%i') as departuretime FROM Bookings_Customers
           INNER JOIN Bookings on Bookings.id = Bookings_Customers.bookingId
           INNER JOIN Tours on Bookings.tourId = Tours.id WHERE Tours.id = :id
           ORDER BY Bookings_Customers.departureTime,Bookings_Customers.departureLocation,Bookings_Customers.id,Tours.id ";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'], \PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      //MySQL 5.7, no row_number() or cte so just make distinct here
      $place = '';
      $time = '';
      $departureresult = array();
      foreach($result as $key => $item) {
        if ($place != $item["departurelocation"] || $time != $item["departuretime"]) {
          array_push($departureresult, $item);
        }
        $place = $item["departurelocation"];
        $time = $item["departuretime"];
      }
      return array('departurelists' => $departureresult);
    } else {
      $this->response->AddResponse('error', 'Rese id kan bara anges som ett positivt heltal, resa måste anges för påstigningsplatslista.');
      $this->response->AddResponse('response', 'Begäran avbruten felaktigt id.');
      $this->response->Exit(404);
    }
    return false;
  }

  public function post(array $_params) {
    
    $this->response->AddResponse('error', 'Åtgården finns inte för denna metod.');
    $this->response->AddResponse('response', 'Begäran avbruten.');
    $this->response->Exit(405);
  }

  public function put(array $_params) {
    $this->response->AddResponse('error', 'Åtgården finns inte för denna metod.');
    $this->response->AddResponse('response', 'Begäran avbruten.');
    $this->response->Exit(405);
  }

  public function delete(array $params) {
    $this->response->AddResponse('error', 'Åtgården finns inte för denna metod.');
    $this->response->AddResponse('response', 'Begäran avbruten.');
    $this->response->Exit(405);   
  }

  private function paramsValidationWithExit($params) {
    $passed = true;
    $result = array();
   
    $result['id'] = $params['id'];

    if ($passed) {
      return $result;
    } else {
      $this->response->AddResponse('response', 'Ogiltig data skickad. Begäran avbruten.');
      $this->response->Exit(400);
    }
  }
}