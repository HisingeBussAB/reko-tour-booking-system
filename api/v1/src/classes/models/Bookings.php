<?php
namespace RekoBooking\classes\models;

use RekoBooking\classes\models\Model;
use RekoBooking\classes\models\Tours;
use RekoBooking\classes\Functions;

class Bookings extends Model {

  public function get(array $params) {
    $params['number'] = $params['id'];
    if ($params['number'] > 0 || $params['number'] == -1) {
      try {
        $sql = '';
        if ($params['number'] == -1) {
          $sql = "SELECT id, number, tourid, `group`, cancelled, cancelleddate, paydate1, paydate2, bookingdate FROM Bookings WHERE bookingdate > '" . date('Y-m-d H:i.s', strtotime('-5 years')) . "' ORDER BY bookingdate DESC;";
        } else {
          $sql = "SELECT id, number, tourid, `group`, cancelled, cancelleddate, paydate1, paydate2, bookingdate FROM Bookings WHERE number = :number ORDER BY bookingdate DESC;";
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
                              Bookings_Customers.id as BookingsCustomersid, custNumber, bookingId, customerId, roomId, requests, priceAdjustment, cancelledCust, departureLocation, departureTime, cancellationinsurance,
                              label, price, size, Rooms.isDeleted as roomDeleted
                      FROM Customers 
                      INNER JOIN Bookings_Customers 
                        ON Bookings_Customers.customerid = Customers.id AND Customers.isAnonymized = 0
                      INNER JOIN Rooms
                        ON Bookings_Customers.roomid = Rooms.id 
                      WHERE Bookings_Customers.bookingid = :id
                      ORDER BY custNumber ASC;";
            $sth = $this->pdo->prepare($sql);
            $sth->bindParam(':id', $booking['id'], \PDO::PARAM_INT);
            $sth->execute();
            $customersresult = $sth->fetchAll(\PDO::FETCH_ASSOC); 
          } catch(\PDOException $e) {
            $this->response->DBError($e, __CLASS__, $sql);
            $this->response->Exit(500);
          }
          foreach ($customersresult as $k=>$r) {
            $customersresult[$k]['custnumber']              = str_pad($customersresult[$k]['custnumber'], 2, '0', STR_PAD_LEFT);
            $customersresult[$k]['cancellationinsurance']   = filter_var($customersresult[$k]['cancellationinsurance'], FILTER_VALIDATE_BOOLEAN);
            $customersresult[$k]['isanonymized']            = filter_var($customersresult[$k]['isanonymized'], FILTER_VALIDATE_BOOLEAN);
            $customersresult[$k]['cancelledcust']           = filter_var($customersresult[$k]['cancelledcust'], FILTER_VALIDATE_BOOLEAN);
            $customersresult[$k]['roomdeleted']             = filter_var($customersresult[$k]['roomdeleted'], FILTER_VALIDATE_BOOLEAN);
            $customersresult[$k]['zip']                     = wordwrap($customersresult[$k]['zip'], 3, ' ', true );
          }
          $result[$key]['customers'] = $customersresult;
        }
        return array('bookings' => $result);
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
    try {     
      $this->pdo->beginTransaction();
      $this->pdo->exec("LOCK TABLES Bookings WRITE, Customers WRITE, Bookings_Customers WRITE;"); 
      $sql = "INSERT INTO Bookings(tourid, `group`, paydate1, paydate2, bookingdate) VALUES (:tourid, :group, :paydate1, :paydate2, :bookingdate);";
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':tourid',      $params['tourid'],      \PDO::PARAM_INT);
      $sth->bindParam(':group',       $params['group'],       \PDO::PARAM_INT);
      $sth->bindParam(':paydate1',    $params['paydate1'],    \PDO::PARAM_STR);
      $sth->bindParam(':paydate2',    $params['paydate2'],    \PDO::PARAM_STR);
      $sth->bindParam(':bookingdate', $params['bookingdate'], \PDO::PARAM_STR);
      $sth->execute(); 

      $sql = "SELECT LAST_INSERT_ID() as id;";
      $sth = $this->pdo->prepare($sql);
      $sth->execute(); 

      $bookingid = $sth->fetch(\PDO::FETCH_ASSOC); 
      $nr = ($params['group'] == 1) ? '2' : '1';
      $nr .= str_pad($bookingid['id'], 6, "0", STR_PAD_LEFT);
      $sql = "UPDATE Bookings SET number = :nr WHERE id = :id;";
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':nr',    $nr,                 \PDO::PARAM_INT);
      $sth->bindParam(':id',    $bookingid['id'],    \PDO::PARAM_INT);
      $sth->execute(); 
      $i = 0;
      foreach($params['customers'] as $customer) {
        $customerid = 0;
        if (is_numeric($customer['id']) && $customer['id'] >= 0) {
          $customerid = $customer['id'];
        } else {
          $comp = Functions::getCompString($customer['firstname'],$customer['lastname'],$customer['zip'],$customer['street']);
          $sql = "INSERT INTO Customers(firstname, lastname, street, zip, city, phone, email, personalnumber, compare) 
          VALUES (:firstname, :lastname, :street, :zip, :city, :phone, :email, :personalnumber, :compare);";
          $sth = $this->pdo->prepare($sql);
          $sth->bindParam(':firstname',         $customer['firstname'],      \PDO::PARAM_STR);
          $sth->bindParam(':lastname',          $customer['lastname'],       \PDO::PARAM_STR);
          $sth->bindParam(':street',            $customer['street'],         \PDO::PARAM_STR);
          $sth->bindParam(':zip',               $customer['zip'],            \PDO::PARAM_INT);
          $sth->bindParam(':city',              $customer['city'],           \PDO::PARAM_STR);
          $sth->bindParam(':phone',             $customer['phone'],          \PDO::PARAM_STR);
          $sth->bindParam(':email',             $customer['email'],          \PDO::PARAM_STR);
          $sth->bindParam(':personalnumber',    $customer['personalnumber'], \PDO::PARAM_STR);
          $sth->bindParam(':compare',           $comp,                       \PDO::PARAM_STR);
          $sth->execute(); 
          $sql = "SELECT LAST_INSERT_ID() as id;";
          $sth = $this->pdo->prepare($sql);
          $sth->execute(); 
          $customerid = $sth->fetch(\PDO::FETCH_ASSOC); 
        }
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
      $this->pdo->exec("UNLOCK TABLES;");
      $this->pdo->commit();
    } catch(\PDOException $e) {
      $this->response->DBError($e, __CLASS__, $sql);
      $this->pdo->rollBack();
      try {
        $this->pdo->exec("UNLOCK TABLES;");
      } catch(\PDOException $e) {
        $this->response->Exit(500);
      }
      $this->response->Exit(500);
    }
    return array('updatedid' => $nr);
  }

  public function put(array $_params) {
    $_params['number'] = $_params['id'];
    $params = $this->paramsValidationWithExit($_params, 'put');
    try {     
      $this->pdo->beginTransaction();
      $this->pdo->exec("LOCK TABLES Bookings WRITE, Customers WRITE, Bookings_Customers WRITE;"); 
      $sql = "UPDATE Bookings SET tourid = :tourid, `group` = :group, paydate1 = :paydate1, paydate2 = :paydate2, bookingdate = :bookingdate, cancelled = :cancelled, cancelleddate = :cancelleddate
      WHERE number = :number;";
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':tourid',        $params['tourid'],        \PDO::PARAM_INT);
      $sth->bindParam(':group',         $params['group'],         \PDO::PARAM_INT);
      $sth->bindParam(':paydate1',      $params['paydate1'],      \PDO::PARAM_STR);
      $sth->bindParam(':paydate2',      $params['paydate2'],      \PDO::PARAM_STR);
      $sth->bindParam(':bookingdate',   $params['bookingdate'],   \PDO::PARAM_STR);
      $sth->bindParam(':cancelled',     $params['cancelled'],     \PDO::PARAM_INT);
      $sth->bindParam(':cancelleddate', $params['cancelleddate'], \PDO::PARAM_STR);
      $sth->bindParam(':number',        $params['number'],        \PDO::PARAM_INT);
      $sth->execute();
      $sql = "SELECT id from Bookings where number = :number;";
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':number',        $params['number'],        \PDO::PARAM_INT);
      $sth->execute(); 
      $bookingsr = $sth->fetch(\PDO::FETCH_ASSOC);
      $bookingsid = $bookingsr['id'];
      $idsonly = array_column($params['customers'], 'id');
      $customerids = '';
      foreach ($idsonly as $id) {
        $customerids .= filter_var($id, FILTER_SANITIZE_NUMBER_INT) . ','; 
      }
      $customerids = trim($customerids, ',');
      $sql = "UPDATE Bookings_Customers SET cancelledCust = 1 WHERE customerid not in (" . $customerids . ") AND bookingid = :id;"; 
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':id', $bookingsid, \PDO::PARAM_INT);
      $sth->execute(); 
      $sql = "SELECT customerid as id FROM Bookings_Customers WHERE bookingid = :id;"; 
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':id', $bookingsid, \PDO::PARAM_INT);
      $sth->execute(); 
      $existingcustomerids = $sth->fetchAll(\PDO::FETCH_ASSOC);
      $sql = "SELECT max(custNumber) as nr FROM Bookings_Customers WHERE bookingid = :id;"; 
      $sth = $this->pdo->prepare($sql);
      $sth->bindParam(':id', $bookingsid, \PDO::PARAM_INT);
      $sth->execute(); 
      $maxcustnr = $sth->fetch(\PDO::FETCH_ASSOC);
      $i = is_numeric($maxcustnr) ? $maxcustnr + 1 : 0;
      foreach ($params['customers'] as $customer) {
        $exists = in_array($customer['id'], array_column($existingcustomerids, 'id'));
        if ($customer['id'] == -1) {
          $comp = Functions::getCompString($customer['firstname'],$customer['lastname'],$customer['zip'],$customer['street']);
          $sql = "INSERT INTO Customers(firstname, lastname, street, zip, city, phone, email, personalnumber, compare) 
          VALUES (:firstname, :lastname, :street, :zip, :city, :phone, :email, :personalnumber, :compare);";
          $sth = $this->pdo->prepare($sql);
          $sth->bindParam(':firstname',         $customer['firstname'],      \PDO::PARAM_STR);
          $sth->bindParam(':lastname',          $customer['lastname'],       \PDO::PARAM_STR);
          $sth->bindParam(':street',            $customer['street'],         \PDO::PARAM_STR);
          $sth->bindParam(':zip',               $customer['zip'],            \PDO::PARAM_INT);
          $sth->bindParam(':city',              $customer['city'],           \PDO::PARAM_STR);
          $sth->bindParam(':phone',             $customer['phone'],          \PDO::PARAM_STR);
          $sth->bindParam(':email',             $customer['email'],          \PDO::PARAM_STR);
          $sth->bindParam(':personalnumber',    $customer['personalnumber'], \PDO::PARAM_STR);
          $sth->bindParam(':compare',           $comp,                       \PDO::PARAM_STR);
          $sth->execute(); 
          $sql = "SELECT LAST_INSERT_ID() as id;";
          $sth = $this->pdo->prepare($sql);
          $sth->execute(); 
          $newcustomerid = $sth->fetch(\PDO::FETCH_ASSOC); 
          $sql = "INSERT INTO Bookings_Customers(bookingid, customerid, roomid, requests, priceadjustment, departurelocation, departuretime, custnumber)
          VALUES (:bookingid, :customerid, :roomid, :requests, :priceadjustment, :departurelocation, :departuretime, :custnumber);";
          $sth = $this->pdo->prepare($sql);
          $sth->bindParam(':bookingid',         $bookingsid,                   \PDO::PARAM_INT);
          $sth->bindParam(':customerid',        $newcustomerid['id'],          \PDO::PARAM_INT);
          $sth->bindParam(':roomid',            $customer['roomid'],           \PDO::PARAM_INT);
          $sth->bindParam(':requests',          $customer['requests'],         \PDO::PARAM_STR);
          $sth->bindParam(':priceadjustment',   $customer['priceadjustment'],  \PDO::PARAM_INT);
          $sth->bindParam(':departurelocation', $customer['departurelocation'],\PDO::PARAM_STR);
          $sth->bindParam(':departuretime',     $customer['departuretime'],    \PDO::PARAM_STR);
          $sth->bindParam(':custnumber',        $i,                            \PDO::PARAM_INT);
          $sth->execute(); 
        } else {
          if ($exists) {
            $sql = "UPDATE Bookings_Customers SET roomid = :roomid, requests = :requests, priceadjustment = :priceadjustment, departurelocation = :departurelocation, 
            departuretime = :departuretime, cancellationinsurance = :cancellationinsurance, cancelledcust = :cancelledcust
            WHERE bookingid = :bookingid AND customerid = :customerid;";
            $sth = $this->pdo->prepare($sql);
            $sth->bindParam(':bookingid',               $bookingsid,                        \PDO::PARAM_INT);
            $sth->bindParam(':customerid',              $customer['id'],                    \PDO::PARAM_INT);
            $sth->bindParam(':roomid',                  $customer['roomid'],                \PDO::PARAM_INT);
            $sth->bindParam(':requests',                $customer['requests'],              \PDO::PARAM_STR);
            $sth->bindParam(':priceadjustment',         $customer['priceadjustment'],       \PDO::PARAM_INT);
            $sth->bindParam(':departurelocation',       $customer['departurelocation'],     \PDO::PARAM_STR);
            $sth->bindParam(':departuretime',           $customer['departuretime'],         \PDO::PARAM_STR);
            $sth->bindParam(':cancellationinsurance',   $customer['cancellationinsurance'], \PDO::PARAM_INT);
            $sth->bindParam(':cancelledcust',           $customer['cancelledcust'],         \PDO::PARAM_INT);
            $sth->execute(); 
          } else {
            $sql = "INSERT INTO Bookings_Customers(bookingid, customerid, roomid, requests, priceadjustment, departurelocation, departuretime, custnumber)
            VALUES (:bookingid, :customerid, :roomid, :requests, :priceadjustment, :departurelocation, :departuretime, :custnumber);";
            $sth = $this->pdo->prepare($sql);
            $sth->bindParam(':bookingid',         $bookingsid,                   \PDO::PARAM_INT);
            $sth->bindParam(':customerid',        $customer['id'],               \PDO::PARAM_INT);
            $sth->bindParam(':roomid',            $customer['roomid'],           \PDO::PARAM_INT);
            $sth->bindParam(':requests',          $customer['requests'],         \PDO::PARAM_STR);
            $sth->bindParam(':priceadjustment',   $customer['priceadjustment'],  \PDO::PARAM_INT);
            $sth->bindParam(':departurelocation', $customer['departurelocation'],\PDO::PARAM_STR);
            $sth->bindParam(':departuretime',     $customer['departuretime'],    \PDO::PARAM_STR);
            $sth->bindParam(':custnumber',        $i,                            \PDO::PARAM_INT);
            $sth->execute(); 
            $i++;
          }
        }
      }
      $this->pdo->exec("UNLOCK TABLES;");
      $this->pdo->commit();
    } catch(\PDOException $e) {
      $this->response->DBError($e, __CLASS__, $sql);
      $this->pdo->rollBack();
      try {
        $this->pdo->exec("UNLOCK TABLES;");
      } catch(\PDOException $e) {
        $this->response->Exit(500);
      }
      $this->response->Exit(500);
    }
    return array('updatedid' => $params['number']);   

  }

  public function delete(array $_params) {
    $_params['number'] = $_params['id'];
    $params['number'] = Functions::validateInt($_params['number']);
    if (is_null($params['number'])) {
      $this->response->AddResponse('error', 'Bokningsnummret är ogiltigt.');
      $this->response->AddResponsePushToArray('invalidFields', array('number'));
      return false;
    }
    if (ENV_DEBUG_MODE && !empty($_GET["forceReal"]) && Functions::validateBoolToBit($_GET["forceReal"])) {
      //Allows true deletes while running tests or after debugging
      //Start debug deleter, WARNING! Hard deletes all associed customers and all associated data and connections to other trips and payments
      //ONLY FOR AUTOMATED TESTING
      try {
        $this->pdo->beginTransaction();
        $sql = "SELECT id FROM Bookings WHERE number = :number;"; 
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':number', $params['number'], \PDO::PARAM_INT);
        $sth->execute(); 
        $bookingid = $sth->fetch(\PDO::FETCH_ASSOC);
        $sql = "SELECT customerid FROM Bookings_Customers WHERE bookingid = :id;"; 
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $bookingid['id'], \PDO::PARAM_INT);
        $sth->execute(); 
        $customerids = $sth->fetchAll(\PDO::FETCH_ASSOC);
        foreach($customerids as $id) {
          $this->pdo->exec("DELETE FROM Payments WHERE customerid = " . $id['customerid'] . ";"); 
          $this->pdo->exec("DELETE FROM Bookings_Customers WHERE customerid = " . $id['customerid'] . ";"); 
          $this->pdo->exec("DELETE FROM Customers WHERE id = " . $id['customerid'] . ";"); 
        }
        $this->pdo->exec("DELETE FROM Bookings WHERE id = " . $bookingid['id'] . ";"); 
        $this->pdo->commit();
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->pdo->rollBack();
        $this->response->Exit(500);
      }
      return array('updatedid' => $params['number']);
    }
    //End debug deleter

    if ($this->get(array('id' => $params['number'])) !== false) {
      try {
        $this->pdo->beginTransaction();
        $sql = "SELECT id FROM Bookings WHERE number = :number;"; 
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':number', $params['number'], \PDO::PARAM_INT);
        $sth->execute(); 
        $r = $sth->fetch(\PDO::FETCH_ASSOC);
        $bid = $r['id'];
        $sql = "UPDATE Bookings SET cancelled = 1, cancelleddate = '" . date("Y-m-d H:i:s") . "' WHERE id = :id;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':id', $bid,     \PDO::PARAM_INT);
        $sth->execute();
        $sql = "UPDATE Bookings_Customers SET cancelledcust = 1 WHERE bookingid = :bookingid;";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':bookingid', $bid,     \PDO::PARAM_INT);
        $sth->execute();
        $this->pdo->commit();
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        $this->pdo->rollBack();
        $this->response->Exit(500);
      }
      return array('updatedid' => $params['number']);
    }
    
    return false;    
  }

  private function paramsValidationWithExit($params, $req = NULL) {
    $passed = true;
    $result = array();

    if (isset($params['cancelled'])) {
      $result['cancelled'] = Functions::validateBoolToBit($params['cancelled']);
    } else {
      $result['cancelled'] = NULL;
    }
    if (is_null($result['cancelled'])) {
      //default to not cancelled
      $result['cancelled'] = 0;
    }

    if (isset($params['cancelleddate'])) {
      $result['cancelleddate'] = Functions::validateDate($params['cancelleddate']);
    } else {
      $result['cancelleddate'] = NULL;
    }
    if (is_null($result['cancelleddate']) && $req = 'put' && $result['cancelled'] == 1) {
      $this->response->AddResponse('error', 'Datum för avbokning måste anges.');
      $this->response->AddResponsePushToArray('invalidFields', array('cancelleddate'));
      $passed = false;
    }

    if (isset($params['paydate2'])) {
      $result['paydate2'] = Functions::validateDate($params['paydate2']);
    } else {
      $result['paydate2'] = NULL;
    }
    if (is_null($result['paydate2'])) {
      $this->response->AddResponse('error', 'Betalningsdatum för slutlikvid måste anges i formatet ÅÅÅÅ-MM-DD.');
      $this->response->AddResponsePushToArray('invalidFields', array('paydate2'));
      $passed = false;
    }

    if (isset($params['paydate1'])) {
      $result['paydate1'] = Functions::validateDate($params['paydate1']);
    } else {
      $result['paydate1'] = NULL;
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
      $result['tourid'] = Functions::validateInt($params['tourid']);
      $Tours = new Tours($this->response, $this->pdo);
      if ($Tours->get(array('id' => $result['tourid'])) == false) { 
        $this->response->AddResponse('error', 'Kan inte hitta resan bokningen skall tillhöra, är den borttagen?');
        $this->response->AddResponsePushToArray('invalidFields', array('tourid'));
        $passed = false;
      }
    } else {
      $result['tourid'] = NULL;
    }
    if (is_null($result['tourid'])) {
      $this->response->AddResponse('error', 'Vilken resa bokingen tillhör måste anges.');
      $this->response->AddResponsePushToArray('invalidFields', array('tourid'));
      $passed = false;
    }

    if (isset($params['bookingdate'])) {
      $result['bookingdate'] = Functions::validateDateTime($params['bookingdate']);
    } else {
      $result['bookingdate'] = Functions::validateDateTime((string)date_create()->format('Y-m-d H:i:s'));
    }
    if (is_null($result['bookingdate'])) {
      //default to today
      $result['bookingdate'] = Functions::validateDateTime((string)date_create()->format('Y-m-d H:i:s'));
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
        if (empty($result['customers'][$key]['lastname'])) {
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
          //default to today
          $result['customers'][$key]['date'] = Functions::validateDate((string)date_create()->format('Y-m-d'));
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

        if (isset($customer['id'])) {
          $result['customers'][$key]['id'] = Functions::validateInt($customer['id']);
        } else {
          $result['customers'][$key]['id'] = -1;
        }
        if (is_null($result['customers'][$key]['id']) && $req == 'put') {
          $this->response->AddResponse('error', 'Kund id är inte ett heltal för en eller flera kunder.');
          $this->response->AddResponsePushToArray('invalidFields', array('customers.' . $key . '.id'));
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

        if (isset($customer['cancelledcust'])) {
          $result['customers'][$key]['cancelledcust'] = Functions::validateBoolToBit($customer['cancelledcust']);
        } else {
          //default to 0
          $result['customers'][$key]['cancelledcust'] = 0;
        }


      }
    } else {
      $this->response->AddResponse('error', 'Minst en kund måste anges i bokningen');
      $this->response->AddResponsePushToArray('invalidFields', array('customers'));
      $passed = false;
    }

    if (isset($params['number'])) {
      $result['number'] = Functions::validateInt($params['number']);
      if (empty($result['number'])) {
        $result['number'] = -1;
      }
    } else {
      $result['number'] = -1;
    }

    if (isset($params['id'])) {
      $result['id'] = Functions::validateInt($params['id']);
      if (empty($result['id'])) {
        $result['id'] = -1;
      }
    } else {
      $result['id'] = -1;
    }

    if ($passed) {
      return $result;
    } else {
      $this->response->AddResponse('response', 'Ogiltig data skickad. Begäran avbruten.');
      $this->response->Exit(400);
    }
  }

}