<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes;

use RekoBooking\classes\DB;
use RekoBooking\classes\DBError;

class Tokens
{


  public static function flushTokens($tokentype, $pdo) {
    
    $expire = 25200; //sets the defult expiration for tokens in seconds
    
    if ($tokentype = 'login') {$expire = 1000;}


    try {
      $sql = "DELETE FROM Tokens WHERE TokenType = :tokentype AND Created < :expired;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tokentype', $tokentype, \PDO::PARAM_STR);
      $sth->bindParam(':expired', $expired, \PDO::PARAM_INT);
      $sth->execute(); 
      
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }
    

  }

  public static function validateToken($token, $tokentype, $pdo) {
  
    self::flushTokens($tokentype, $pdo);
  
    try {
      $sql = "SELECT Token FROM Tokens WHERE TokenType = :tokentype AND Token = :token;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':token', $token, \PDO::PARAM_STR);
      $sth->bindParam(':tokentype', $tokentype, \PDO::PARAM_STR);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      return false;
    }
    
    if ($result) {
      return true;
    } else {
      return false;
    }

  }
}
