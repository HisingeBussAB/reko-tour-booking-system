<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class DepartureLists extends Model {

  public function get(array $params) {

    if (Functions::validateInt($params['id'],1) == $params['id']) {
      try {
        $sql = "SELECT DISTINCT
                  Tours.id as id
                  ,Tours.id as tourid
                  ,Bookings_Customers.departureLocation as departureLocation
                  ,Bookings_Customers.departureTime as departureTime FROM Bookings_Customers
                INNER JOIN Bookings on Bookings.id = Bookings_Customers.bookingId
                INNER JOIN Tours on Bookings.tourId = Tours.id WHERE Tours.id = :id";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'], \PDO::PARAM_INT);
        $sth->execute(); 
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      if (count($result) < 1) {
        return array('departurelists' => array(['id' => '' . $params['id']]));
      } else {       
        return array('departurelists' => $result);
      }
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