<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\Functions;

class BookingSearchList extends Model {

  public function get(array $params) {
      try {
        $sql = "SELECT DISTINCT Bookings.id as bookingid
            ,Tours.label as tour
            ,Tours.departureDate
            ,Bookings.number as bookingnr
            ,Customers.firstName
            ,Customers.lastName
            ,Customers.phone
            ,Customers.email
            ,concat(Customers.firstName,' ',Customers.lastName) as FullName
            FROM `Customers` 
            INNER JOIN Bookings_Customers on Customers.id = Bookings_Customers.customerId
            INNER JOIN Bookings on Bookings_Customers.bookingId = Bookings.id
            INNER JOIN Tours on Tours.id = Bookings.tourId
            WHERE Tours.departureDate < NOW()
           ORDER BY Tours.departureDate DESC";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'], \PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }    
      return array('bookingssearchlist' => $result);
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