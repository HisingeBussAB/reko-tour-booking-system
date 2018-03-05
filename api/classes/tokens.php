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

  public static function createToken($tokentype, $user='blindtoken', $pdo) {
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
    
    if ($tokentype = 'login') {$expire = 1000;}
    if ($tokentype = 'jwt')   {$expire = 604800;}


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

  public static function flushUsersJWTTokens($user, $pdo) {
    
    $tokentype = 'jwt';

    try {
      $sql = "DELETE FROM Tokens WHERE TokenType = :tokentype AND username = :user;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':tokentype', $tokentype, \PDO::PARAM_STR);
      $sth->bindParam(':user', $tokentype, \PDO::PARAM_STR);
      $sth->execute(); 
      
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
    }
    

  }

  public static function createJWTToken($tokentype, $user, $pdo) {

    self::flushUsersJWTTokens($user, $pdo);

    $bytes     = openssl_random_pseudo_bytes(64);
    $hex       = bin2hex($bytes);
    $created   = time();
    $newtoken  = hash('sha512', $hex . microtime() . JWT_SECRET_PEPPER);

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
