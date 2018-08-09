<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

use \Firebase\JWT\JWT;

class Tokens {

  private $response;
  private $pdo;
  
  function __construct(Responder $_response, $_pdo) {
    $this->response = $_response;
    $this->pdo = $_pdo;
  }

  /**
   * Issues a new token of type
   * @param string $tokenType Type of the token
   */
  public function issueToken(string $tokenType) {
    if ($tokenType == "login") {
      $bytes     = openssl_random_pseudo_bytes(24);
      $hex       = bin2hex($bytes);
      $created   = time();
      $newtoken  = hash('sha256', $hex . $created);
      $user      = "anonymous";
      try {
        $sql = "INSERT INTO Tokens (Token, TokenType, Created, username) VALUES (:token, :tokentype, :created, :user);";
        $sth = $this->pdo->prepare($sql);
        $sth->bindParam(':token', $newtoken, \PDO::PARAM_STR);
        $sth->bindParam(':tokentype', $tokenType, \PDO::PARAM_STR);
        $sth->bindParam(':created', $created, \PDO::PARAM_INT);
        $sth->bindParam(':user', $user, \PDO::PARAM_STR);
        $sth->execute(); 
      } catch(\PDOException $e) {
        $this->response->DBError($e, __CLASS__, $sql);
        return false;
      }
      $this->response->AddResponse("token", $newtoken);
      return true;
    }
   
  }

  public function validateToken() {

  }

}