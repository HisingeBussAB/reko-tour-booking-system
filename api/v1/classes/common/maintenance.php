<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

final class Maintenance {

  public static function refreshSecrets(Responder $response, \PDO $pdo): bool {
    try {
      $sql = "SELECT token, created FROM Tokens WHERE tokentype = 'jwtsecret' ORDER BY created DESC;";
      $sth = $pdo->prepare($sql);
      $sth->execute(); 
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
      var_dump($result);
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      return false;
    }

    if (count($result) < 2) {
      self::insertNewSecret($response, $pdo);
    } else {
      // Check if newest token is older then 12 hours. If so issue one new token 
      // and keep only latest from earlier result
      $limit = time() - 43200; //12 hours ago
      if ($result[0]['created'] < $limit) {
        self::insertNewSecret($response, $pdo);
        $i = 0;
        foreach ($result as $item) {
          if ($i > 0) {
            $token = $item['token'];
            try {
              $sql = "DELETE FROM Tokens WHERE token = :token;";
              $sth = $pdo->prepare($sql);
              $sth->bindParam(':token', $token, \PDO::PARAM_STR);
              $sth->execute(); 
            } catch(\PDOException $e) {
              $response->DBError($e, __CLASS__, $sql);
              return false;
            }
          }
          $i++;
        }
      }
      
     
    }
    return true;
  }

  private static function insertNewSecret(Responder $response, \PDO $pdo) {
    $now = time();
    $token = bin2hex(random_bytes(16));
    try {
      $sql = "INSERT INTO Tokens (token, tokentype, created) 
        VALUES ('$token', 'jwtsecret', $now);";
      $sth = $pdo->prepare($sql);
      $sth->execute(); 
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
    }
  }
}

   