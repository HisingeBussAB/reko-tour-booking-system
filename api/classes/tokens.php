<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes;

use RekoBooking\classes\DBError;
use \Firebase\JWT\JWT;

class Tokens
{

  public static function Test($responder, $groda) {
    var_dump($responder);

    var_dump($groda);
  }

  public static function createToken($tokentype, $pdo, $user='blindtoken') {
    $bytes     = openssl_random_pseudo_bytes(24);
    $hex       = bin2hex($bytes);
    $created   = time();
    $newtoken  = hash('sha256', $hex . $created);
    try {
      $sql = "INSERT INTO Tokens (Token, TokenType, Created, username) VALUES (:token, :tokentype, :created, :user);";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':token', $newtoken, \PDO::PARAM_STR);
      $sth->bindParam(':tokentype', $tokentype, \PDO::PARAM_STR);
      $sth->bindParam(':created', $created, \PDO::PARAM_INT);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->execute(); 
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      die();
    }
    return array($tokentype . 'token' => $newtoken);
  }


  public static function flushTokens($tokentype, $pdo) {
    
    $expire = 25200; //sets the defult expiration for tokens in seconds
    
    if ($tokentype = 'login')     {$expire = 1000;}
    if ($tokentype = 'jwt')       {$expire = 604800;}
    if ($tokentype = 'submit')    {$expire = 1600;}

    $expired = time() - $expire;

    try {
      $sql = "DELETE FROM Tokens WHERE TokenType = :tokentype AND Created < :expired;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tokentype', $tokentype, \PDO::PARAM_STR);
      $sth->bindParam(':expired', $expired, \PDO::PARAM_INT);
      $sth->execute(); 
      
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      die();
    }
    

  }

  public static function validateToken($token, $tokentype, $pdo, $user='blindtoken') {
  
    self::flushTokens($tokentype, $pdo);

    if (($tokentype == 'jwt' || $tokentype == 'submit') && $user == 'blindtoken') {
      return false;
    }
  
    try {
      $sql = "SELECT Token FROM Tokens WHERE TokenType = :tokentype AND Token = :token AND username = :user;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':token', $token, \PDO::PARAM_STR);
      $sth->bindParam(':tokentype', $tokentype, \PDO::PARAM_STR);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
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

  public static function validationFailedDie($response) {
    header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
    $headers = ob_get_clean();
    echo $headers;
    $response->AddResponse('saved', false);
    $response->AddResponse('response', 'En tillfällig token som behövs för den här operationen har troligen gått ut. Prova ladda om sidan (F5).');
    echo $response->GetResponse();
    die();
  }

  public static function flushUsersJWTTokens($user, $pdo) {
    
    $tokentype = 'jwt';

    try {
      $sql = "DELETE FROM Tokens WHERE TokenType = :tokentype AND username = :user;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tokentype', $tokentype, \PDO::PARAM_STR);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->execute(); 
      
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }
    

  }

  public static function createJWTToken($tokentype, $user, $pdo) {

    self::flushUsersJWTTokens($user, $pdo);

    $bytes     = openssl_random_pseudo_bytes(48);
    $hex       = bin2hex($bytes);
    $created   = time();
    $newtoken  = hash('sha512', $hex . microtime());

    try {
      $sql = "INSERT INTO Tokens (Token, TokenType, Created, username) VALUES (:token, :tokentype, :created, :user);";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':token', $newtoken, \PDO::PARAM_STR);
      $sth->bindParam(':tokentype', $tokentype, \PDO::PARAM_STR);
      $sth->bindParam(':created', $created, \PDO::PARAM_INT);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->execute(); 
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      die();
    }
    return $newtoken;
  }


}
