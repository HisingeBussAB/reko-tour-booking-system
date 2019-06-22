<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\models\Categories;
use RekoBooking\classes\Functions;

class Bookings extends Model {

  public function get(array $params) {
    if ($params['number'] > 0 || $params['number'] == -1) {
      try {
        $sql = '';
        if ($params['number'] == -1) {
          $sql = "SELECT id, number, tourid, group, cancelled, cancelleddate, paydate1, paydate2, bookingdate FROM Bookings WHERE bookingdate > '" . date('Y-m-d H:i.s', strtotime('-4 years'))->format(Y-m-d) . "' ORDER BY id DESC;";
        } else {
          $sql = "SELECT id, number, tourid, group, cancelled, cancelleddate, paydate1, paydate2, bookingdate FROM Bookings WHERE number = :number ORDER BY id DESC;";
        }
        $sth = $this->pdo->prepare($sql);
        if ($params['number'] != -1) { $sth->bindParam(':number', $params['number'], \PDO::PARAM_INT); }
        $sth->execute(); 
        $result = $sth->fetchAll(\PDO::FETCH_ASSOC); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->response->Exit(500);
      }
      if (count($result) < 1 && $params['number'] != -1) {
        $this->response->AddResponse('error', 'Bokningen hittades inte.');
        $this->response->Exit(404);
      } else {
        $i = 0;
        foreach ($result as $key=>$booking) {
          $result[$key]['cancelled'] = filter_var($result[$key]['cancelled'], FILTER_VALIDATE_BOOLEAN);
          $result[$key]['group']     = filter_var($result[$key]['group'], FILTER_VALIDATE_BOOLEAN);
          try {
            $sql = "SELECT Customers.id as id, firstName, lastName, street, zip, city, phone,	email, personalNumber, date, compare,	isAnonymized,
                              Bookings_Customers.id as BookingsCustomersid, custNumber, bookingId, customerId, roomId, requests, priceAdjustment, departureLocation, departureTime, cancellationInsurance
                              label, price, size, isDeleted
                      FROM Customers 
                      INNER JOIN Bookings_Customers 
                        ON Bookings_Customers.customerid = Customers.id 
                      INNER JOIN Rooms
                        ON Bookings_Customers.roomid = Rooms.id 
                      WHERE Bookings_Customers.bookingid = :id
                      ORDER BY departureTime ASC;";
            $sth = $this->pdo->prepare($sql);
            $sth->bindParam(':id', $booking['id'], \PDO::PARAM_INT);
            $sth->execute();
            $customersresult = $sth->fetchAll(\PDO::FETCH_ASSOC); 
          } catch(\PDOException $e) {
            $this->response->DBError($e, __CLASS__, $sql);
            $this->response->Exit(500);
          }
          $bookingresult['cancellationInsurance'] = filter_var($result[$key]['cancellationInsurance'], FILTER_VALIDATE_BOOLEAN);
          $result[$key]['customers'] = $customersresult;
        }
        return array('tours' => $result);
      }
    } else {
      $this->response->AddResponse('error', 'Bokningsid kan bara anges som ett positivt heltal, eller inte anges alls för alla bokningar.');
      $this->response->AddResponse('response', 'Bokningsid kan bara anges som ett positivt heltal, eller inte anges alls för alla bokningar.');
      $this->response->Exit(404);
    }
    return false;
  }

  public function post(array $_params) {
    $params = $this->paramsValidationWithExit($_params);
    

    $sql = "LOCK TABLES Bookings WRITE, Customers WRITE;";
    try {     
      $this->pdo->beginTransaction();
      $sth = $this->pdo->prepare($sql);
      $sth->execute(); 
      $sql = "INSERT INTO Bookings(tourId, group, payDate1, payDate2)
      VALUES (:tourId,:group,:payDate1,:payDate2)"
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':tourId',    $params['tourId'],   \PDO::PARAM_INT);
      $sth->bindParam(':group',     $params['group'],    \PDO::PARAM_INT);
      $sth->bindParam(':payDate1',  $params['payDate1'], \PDO::PARAM_STR);
      $sth->bindParam(':payDate2',  $params['payDate2'], \PDO::PARAM_STR);
      $sth->execute(); 

      $sql = "SELECT LAST_INSERT_ID() as id;";
      $sth = $this->pdo->prepare($sql);
      $sth->execute(); 

      $bookingid = $sth->fetch(\PDO::FETCH_ASSOC); 
      $nr = ($params['group'] == 1) ? '2' : '1';
      $nr .= str_pad($bookingid['id'], 5, "0", STR_PAD_LEFT);
      $sql = "UPDATE Bookings SET number = :nr WHERE id = :id;"
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':nr',    $nr,                 \PDO::PARAM_INT);
      $sth->bindParam(':id',    $bookingid['id'],    \PDO::PARAM_INT);
      $sth->execute(); 
      $i = 0;
      foreach($params['customer'] as $customer) {
        $comp = Functions::getCompString($customer['firstName'],$customer['lastName'],$customer['zip'],$customer['street']);
        $sql = "INSERT INTO Customers(firstname, lastname, street, zip, city, phone, email, personalnumber, date, compare) 
        VALUES (:firstname, :lastname, :street, :zip, :city, :phone, :email, :personalnumber, date, compare);";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':firstname',         $customer['firstname'],      \PDO::PARAM_STR);
        $sth->bindParam(':lastname',          $customer['lastname'],       \PDO::PARAM_STR);
        $sth->bindParam(':street',            $customer['street'],         \PDO::PARAM_STR);
        $sth->bindParam(':zip',               $customer['zip'],            \PDO::PARAM_INT);
        $sth->bindParam(':city',              $customer['city'],           \PDO::PARAM_STR);
        $sth->bindParam(':phone',             $customer['phone'],          \PDO::PARAM_STR);
        $sth->bindParam(':email',             $customer['email'],          \PDO::PARAM_STR);
        $sth->bindParam(':personalnumber',    $customer['personalnumber'], \PDO::PARAM_STR);
        $sth->bindParam(':date',              $customer['date'],           \PDO::PARAM_STR);
        $sth->bindParam(':compare',           $comp,                     \PDO::PARAM_STR);
        $sth->execute(); 
        $sql = "SELECT LAST_INSERT_ID() as id;";
        $sth = $this->pdo->prepare($sql);
        $sth->execute(); 
        $customerid = $sth->fetch(\PDO::FETCH_ASSOC); 
        $sql = "INSERT INTO Bookings_Customers(bookingid, customerid, roomid, requests, priceadjustment, departurelocation, departuretime, custnumber)
        VALUES (:bookingid, :customerid, :roomid, :requests, :priceadjustment, :departurelocation, :departuretime, :custnumber);";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':bookingid',         $bookingid['id'],              \PDO::PARAM_INT);
        $sth->bindParam(':customerid',        $customerid['id'],             \PDO::PARAM_INT);
        $sth->bindParam(':roomid',            $customer['roomid'],           \PDO::PARAM_INT);
        $sth->bindParam(':requests',          $customer['requests'],         \PDO::PARAM_STR);
        $sth->bindParam(':priceadjustment',   $customer['priceadjustment'],  \PDO::PARAM_INT);
        $sth->bindParam(':departurelocation', $customer['departurelocation'],\PDO::PARAM_STR);
        $sth->bindParam(':departuretime',     $customer['departuretime'],    \PDO::PARAM_STR);
        $sth->bindParam(':custnumber',        $i,                            \PDO::PARAM_INT);
        $sth->execute(); 
        $i++;
      }
      $sql = "UNLOCK TABLES;";
      $sth = $this->pdo->prepare($sql);
      $sth->execute(); 
      $this->pdo->commit();
    } catch(\PDOException $e) {
      $this->response->DBError($e, __CLASS__, $sql);
      $this->pdo->rollBack();
      try {
        $sql = "UNLOCK TABLES;";
        $sth = $this->pdo->prepare($sql);
        $sth->execute();
      } catch(\PDOException $e) {
        $this->response->Exit(500);
      }
      $this->response->Exit(500);
    }
    return array('updatedid' => $result['id']);   
  }

  public function put(array $_params) {
    $params = $this->paramsValidationWithExit($_params, 'put');
    $sql = "UPDATE Tours SET 
        label = :lab, 
        insuranceprice = :ins, 
        reservationfeeprice = :res, 
        departuredate = :dep, 
        isDisabled = :act
        WHERE id=:id;";
    try {     
      $this->pdo->beginTransaction();
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':id',  $params['id'],                  \PDO::PARAM_INT);
      $sth->bindParam(':lab', $params['label'],               \PDO::PARAM_STR);
      $sth->bindParam(':ins', $params['insuranceprice'],      \PDO::PARAM_INT);
      $sth->bindParam(':res', $params['reservationfeeprice'], \PDO::PARAM_INT);
      $sth->bindParam(':dep', $params['departuredate'],       \PDO::PARAM_STR);
      $sth->bindParam(':act', $params['isDisabled'],          \PDO::PARAM_INT);
      $sth->execute(); 
      $sql = "DELETE FROM Categories_Tours WHERE tourId = :id;";
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':id',  $params['id'],                  \PDO::PARAM_INT);        
      $sth->execute();
      $sentRoomIds = '';
      foreach ($params['rooms'] as $room) { 
        if (is_numeric($room['id'])) {
          $sentRoomIds .=  $room['id'] . ',';
        }
      }
      $sentRoomIds = trim($sentRoomIds, ',');
      if (strlen($sentRoomIds) > 0) {
        $sql = "UPDATE Rooms SET isDeleted = 1 WHERE tourId = :id AND id not in (" . $sentRoomIds . ");";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id',  $params['id'],                  \PDO::PARAM_INT);        
        $sth->execute();
      }
      foreach ($params['rooms'] as $room) {
        $sql = "SELECT id FROM Rooms WHERE tourid = :tid AND id = :rid";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':tid',  $params['id'],                  \PDO::PARAM_INT);   
        $sth->bindParam(':rid',  $room['id'],                    \PDO::PARAM_INT);       
        $sth->execute();  
        $result = $sth->fetch(\PDO::FETCH_ASSOC); 
        if (!$result) {
          $sql = "INSERT INTO Rooms (tourid, label, price, size, numberavaliable) VALUES (:tid, :lab, :pri, :siz, :num);";
          $sth = $this->pdo->prepare($sql);
          $sth->bindParam(':tid', $params['id'],                  \PDO::PARAM_INT);
          $sth->bindParam(':lab', $room['label'],                 \PDO::PARAM_STR);
          $sth->bindParam(':pri', $room['price'],                 \PDO::PARAM_INT);
          $sth->bindParam(':siz', $room['size'],                  \PDO::PARAM_INT);
          $sth->bindParam(':num', $room['numberavaliable'],       \PDO::PARAM_INT);
          $sth->execute(); 
        } else {
          $sql = "UPDATE Rooms SET label = :lab, price = :pri, size = :siz, numberavaliable = :num, isDeleted = 0 WHERE id = :rid AND tourid = :tid;";
          $sth = $this->pdo->prepare($sql);
          $sth->bindParam(':tid', $params['id'],                  \PDO::PARAM_INT);
          $sth->bindParam(':rid', $result['id'],                  \PDO::PARAM_INT);
          $sth->bindParam(':lab', $room['label'],                 \PDO::PARAM_STR);
          $sth->bindParam(':pri', $room['price'],                 \PDO::PARAM_INT);
          $sth->bindParam(':siz', $room['size'],                  \PDO::PARAM_INT);
          $sth->bindParam(':num', $room['numberavaliable'],       \PDO::PARAM_STR);
          $sth->execute(); 
        }
      }
      foreach ($params['categories'] as $category) {
        $sql = "INSERT INTO Categories_Tours (tourid, categoryid) VALUES (:tid, :cid);";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':tid', $params['id'],                  \PDO::PARAM_INT);
        $sth->bindParam(':cid', $category['id'],                \PDO::PARAM_INT);
        $sth->execute();
      } 
      $this->pdo->commit();
    } catch(\PDOException $e) {
      $this->response->DBError($e, __CLASS__, $sql);
      $this->pdo->rollBack();
      $this->response->Exit(500);
    }
    return array('updatedid' => $params['id']);   

  }

  public function delete(array $params) {
    if (ENV_DEBUG_MODE && !empty($_GET["forceReal"]) && Functions::validateBoolToBit($_GET["forceReal"])) {
      //Allows true deletes while running tests or after debugging
      //Start debug deleter
      try {
        $this->pdo->beginTransaction();
        $sql = "SELECT * FROM Tours WHERE id = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetch(\PDO::FETCH_ASSOC); 
        if (count($result) < 1) {
          return false;        
        }
        $sql = "DELETE FROM Rooms WHERE tourid = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute();
        $sql = "DELETE FROM Categories_Tours WHERE tourid = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute();
        $sql = "DELETE FROM Tours WHERE id = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $params['id'],     \PDO::PARAM_INT);
        $sth->execute();
        $this->pdo->commit();
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->pdo->rollBack();
        $this->response->Exit(500);
      }
    }
    //End debug deleter

    if ($this->get(array('id' => $params['id'])) !== false) {
      try {
        $sql = "UPDATE Tours SET isDeleted = 1 WHERE id = :id;";
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

  private function paramsValidationWithExit($params, $req = NULL) {
    $passed = true;
    $result = array();

    if (isset($params['payDate2'])) {
      $result['payDate2'] = Functions::validateDate($params['payDate2']);
    } else {
      $result['payDate2'] = NULL;
    }
    if (is_null($result['payDate2'])) {
      $this->response->AddResponse('error', 'Betalningsdatum för slutlikvid måste anges i formatet ÅÅÅÅ-MM-DD.');
      $this->response->AddResponsePushToArray('invalidFields', array('payDate2'));
      $passed = false;
    }

    if (isset($params['payDate1'])) {
      $result['payDate1'] = Functions::validateDate($params['payDate1']);
    } else {
      $result['payDate1'] = NULL;
    }
    
    if (isset($params['group'])) {
      $result['group'] = Functions::validateBoolToBit($params['group']);
    } else {
      $result['group'] = NULL;
    }
    if (is_null($result['group'])) {
      $this->response->AddResponse('error', 'Om bokning avser en gruppbokning eller inte måste anges.');
      $this->response->AddResponsePushToArray('invalidFields', array('group'));
      $passed = false;
    }

    if (isset($params['tourid'])) {
      $result['tourid'] = Functions::validateInt($room['price']);
    } else {
      $result['tourid'] = NULL;
    }
    if (is_null($result['tourid'])) {
      $this->response->AddResponse('error', 'Vilken resa bokingen tillhör måste anges.');
      $this->response->AddResponsePushToArray('invalidFields', array('tourid'));
      $passed = false;
    }

    if (isset($params['customers']) && is_array($params['customers'])) {
      foreach($params['customers'] as $key=>$customer) {
        if (isset($customer['firstname'])) {
          $result['customers'][$key]['firstname'] = Functions::sanatizeStringUnsafe($customer['firstname'], 100);
        } else {
          $result['customers'][$key]['firstname'] = '';
        }
        if (empty($result['customers'][$key]['firstname'])) {
          $this->response->AddResponse('error', 'Förnamn måste anges.');
          $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.fistname'));
          $passed = false;
        }

        if (isset($customer['lastname'])) {
          $result['customers'][$key]['lastname'] = Functions::sanatizeStringUnsafe($customer['lastname'], 100);
        } else {
          $result['customers'][$key]['lastname'] = '';
        }
        if (empty($result['customers'][$key]['lastName'])) {
          $this->response->AddResponse('error', 'Efternamn måste anges.');
          $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.lastname'));
          $passed = false;
        }

        if (isset($customer['street']) && !empty($customer['street'])) {
          $result['customers'][$key]['street'] = Functions::sanatizeStringUnsafe($customer['street'], 100);
          if (is_null($result['customers'][$key]['street'])) {
            $this->response->AddResponse('error', 'Gatunamnet innehåller ogiltiga tecken.');
            $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.street'));
            $passed = false;
          }
        } else {
          $result['customers'][$key]['street'] = '';
        }
      
        if (isset($customer['zip']) && !empty($customer['zip'])) {
          $result['customers'][$key]['zip'] = Functions::validateZIP($customer['zip']);
          if (is_null($result['customers'][$key]['street'])) {
            $this->response->AddResponse('error', 'Gatunamnet innehåller ogiltiga tecken.');
            $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.street'));
            $passed = false;
          }
        } else {
          $result['customers'][$key]['zip'] = '';
        }
        
        if (isset($customer['city']) && !empty($customer['city'])) {
          $result['customers'][$key]['city'] = Functions::sanatizeStringUnsafe($customer['city'], 100);
          if (is_null($result['customers'][$key]['city'])) {
            $this->response->AddResponse('error', 'Stadsnamet innehåller ogiltiga tecken.');
            $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.city'));
            $passed = false;
          }
        } else {
          $result['customers'][$key]['city'] = '';
        }
        
        if (isset($customer['phone']) && !empty($customer['phone'])) {
          $result['customers'][$key]['phone'] = Functions::validatePhone($customer['phone']);
          if (is_null($result['customers'][$key]['phone'])) {
            $this->response->AddResponse('error', 'Telefonnummret innehåller ogiltiga tecken.');
            $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.phone'));
            $passed = false;
          }
        } else {
          $result['customers'][$key]['phone'] = '';
        }

        if (isset($customer['email']) && !empty($customer['email'])) {
          $result['customers'][$key]['email'] = Functions::validateEmail($customer['email']);
          if (is_null($result['customers'][$key]['email'])) {
            $this->response->AddResponse('error', 'E-post addressen måste ha giltigt format.');
            $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.email'));
            $passed = false;
          }
        } else {
          $result['customers'][$key]['email'] = '';
        }

        if (isset($customer['personalnumber']) && !empty($customer['personalnumber'])) {
          $result['customers'][$key]['personalnumber'] = Functions::validatePersonalNumber($customer['personalnumber']);
          if (is_null($result['customers'][$key]['personalnumber'])) {
            $this->response->AddResponse('error', 'Personnummer anges XXXXXX-XXXX och måste ha giltig kontrollsiffra.');
            $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.personalnumber'));
            $passed = false;
          }
        } else {
          $result['customers'][$key]['personalNumber'] = '';
        }

        if (isset($customer['date'])) {
          $result['customers'][$key]['date'] = Functions::validateDate($customer['date']);
        } else {
          $result['customers'][$key]['date'] = Functions::validateDate((string)date_create()->format('Y-m-d'));
        }
        if (is_null($result['customers'][$key]['date'])) {
          $this->response->AddResponse('error', 'Datumet är ogiltigt. Ange i format YYYY-MM-DD.');
          $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.date'));
          $passed = false;
        }

        if (isset($customer['isanonymized'])) {
          $result['customers'][$key]['isanonymized'] = Functions::validateBoolToBit($customer['isanonymized']);
        } else {
          //default to 0
          $result['customers'][$key]['isanonymized'] = 0;
        }

        if (isset($customer['roomid'])) {
          $result['customers'][$key]['roomid'] = Functions::validateInt($customer['roomid']);
        } else {
          $result['customers'][$key]['roomid'] = NULL;
        }
        if (is_null($result['customers'][$key]['roomid'])) {
          $this->response->AddResponse('error', 'En rumstyp måste anges för alla resenärer.');
          $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.roomid'));
          $passed = false;
        }

        if (isset($customer['requests']) && !empty($customer['requests'])) {
          $result['customers'][$key]['requests'] = Functions::sanatizeStringUnsafe($customer['requests'], 100);
          if (is_null($result['customers'][$key]['requests'])) {
            $this->response->AddResponse('error', 'Önskemål innehåller ogiltiga tecken.');
            $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.requests'));
            $passed = false;
          }
        } else {
          $result['customers'][$key]['requests'] = '';
        }

        if (isset($customer['priceadjustment']) && !empty($customer['priceadjustment'])) {
          $result['customers'][$key]['priceadjustment'] = Functions::validateInt($customer['priceadjustment']);
        } else {
          $result['customers'][$key]['priceadjustment'] = 0;
        }
        if (is_null($result['customers'][$key]['priceadjustment'])) {
          $this->response->AddResponse('error', 'Prisjustering kan bara innehålla siffror.');
          $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.priceadjustment'));
          $passed = false;
        }

        if (isset($customer['departurelocation']) && !empty($customer['departurelocation'])) {
          $result['customers'][$key]['departurelocation'] = Functions::sanatizeStringUnsafe($customer['departurelocation'], 100);
          if (is_null($result['customers'][$key]['departurelocation'])) {
            $this->response->AddResponse('error', 'Avreseplats innehåller ogiltiga tecken.');
            $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.departurelocation'));
            $passed = false;
          }
        } else {
          $result['customers'][$key]['departurelocation'] = '';
        }

        if (isset($customer['departuretime']) && !empty($customer['departuretime'])) {
          $result['customers'][$key]['departuretime'] = Functions::validateTime($customer['departuretime'], 100);
          if (is_null($result['customers'][$key]['departuretime'])) {
            $this->response->AddResponse('error', 'Avreseplats innehåller ogiltiga tecken.');
            $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.departuretime'));
            $passed = false;
          }
        } else {
          $result['customers'][$key]['departuretime'] = '';
        }

        if (isset($customer['cancellationinsurance'])) {
          $result['customers'][$key]['cancellationinsurance'] = Functions::validateBoolToBit($customer['cancellationinsurance']);
        } else {
          //default to 0
          $result['customers'][$key]['cancellationinsurance'] = 0;
        }


      }
    } else {
      $this->response->AddResponse('error', 'Minst en kund måste anges i bokningen');
      $this->response->AddResponsePushToArray('invalidFields', array('customers'));
      $passed = false;
    }
    $result['number'] = $params['number'];
    $result['id'] = $params['id'];
    if ($passed) {
      return $result;
    } else {
      $this->response->AddResponse('response', 'Ogiltig data skickad. Begäran avbruten.');
      $this->response->Exit(400);
    }
  }

}