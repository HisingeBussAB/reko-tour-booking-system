<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

final class Maintenance {

  public static function refreshSecrets(Responder $response, \PDO $pdo): bool {
    $now = time();
    //Check and update last run
    try {
      $sql = "SELECT created FROM Tokens WHERE tokentype = 'LastMaintenanceRun' ORDER BY created DESC LIMIT 1;";
      $sth = $pdo->prepare($sql);
      $sth->execute(); 
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
    }
    if (!$result) {
      try {
        $sql = "INSERT INTO Tokens (token, tokentype, created, session) VALUES ('Maintenance', 'LastMaintenanceRun', $now, 'Maintenance');";
        $sth = $pdo->prepare($sql);
        $sth->execute(); 
      } catch(\PDOException $e) {
        $response->DBError($e, __CLASS__, $sql);
      }
    } else {
      try {
        $sql = "UPDATE Tokens SET created = $now WHERE tokentype = 'LastMaintenanceRun' AND token = 'Maintenance';";
        $sth = $pdo->prepare($sql);
        $sth->execute(); 
      } catch(\PDOException $e) {
        $response->DBError($e, __CLASS__, $sql);
      }
      if ($result[0]['created'] > ($now - 7100)) {
        //Was run less then 2 hours ago, dont do it now
        return true;
      }
    }


    //Fluses old secrets, creates new ones if too few.

    //Remove all tokens older then 12 hours. These should never be needed. The login will have expired.
    $limit = time() - 43200;
    try {
      $sql = "DELETE FROM Tokens WHERE tokentype = 'jwtsecret' AND created < $limit;";
      $sth = $pdo->prepare($sql);
      $sth->execute(); 
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
    }
    //Get all tokens per user agent
    try {
      $sql = "SELECT token, created, session FROM Tokens WHERE tokentype = 'jwtsecret' ORDER BY session ASC, created DESC;";
      $sth = $pdo->prepare($sql);
      $sth->execute(); 
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
    }
    $i = 0;
    $lastsession = '';
    $tokenstodelete = '';
    //Cycle, delete tokens older then 2 hours if more then 3 tokens
    if($result != false) {
      foreach ($result as $row) {
        if($lastsession == '' || $lastsession == $row['session']) {
          $i++;
          if ($i > 3 && $row['created'] < (time() - 7200)) {
            $tokenstodelete .= "'" . $row['token'] . "', ";
          }
        } else {
          $i = 0;
          $lastsession = $row['session'];
        }
      }

      $tokenstodelete = trim($tokenstodelete, ', ');
      if (!empty($tokenstodelete)) {
        try {
          $sql = "DELETE FROM Tokens WHERE tokentype = 'jwtsecret' AND token IN ($tokenstodelete);";
          $sth = $pdo->prepare($sql);
          $sth->execute(); 
        } catch(\PDOException $e) {
          $response->DBError($e, __CLASS__, $sql);
        }
      }
    }

    return true;
  }

  public static function insertNewSecret(Responder $response, \PDO $pdo, $session = false): bool {
    $now = time();
    $token = bin2hex(random_bytes(28));
    $usedsession = empty($session) ? md5($_SERVER['HTTP_USER_AGENT']) : $session;
    try {
      $sql = "INSERT INTO Tokens (token, tokentype, created, session) VALUES ('$token', 'jwtsecret', $now, '$usedsession');";
      $sth = $pdo->prepare($sql);
      $sth->execute(); 
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      return false;
    }
    return true;
  }
}

   