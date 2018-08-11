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
  
  function __construct(Responder $_response, \PDO $_pdo) {
    $this->response = $_response;
    $this->pdo = $_pdo;
  }

  /**
   * Issues a new token of type
   * @param string $tokenType Type of the token
   */
  public function issueToken(string $tokenType, string $user = "anonymous"): bool {
    if ($tokenType != "login" && $tokenType != "refresh") {
      http_response_code(404);
      return false;
    }  
    if ($tokenType == "refresh" && $user == "anonymous") {
      $this->response->AddResponse("error", "Denna token kan inte ges till en anonym användare.");
      http_response_code(403);
      return false;
    }

    $bytes     = openssl_random_pseudo_bytes(16);
    $hex       = bin2hex($bytes);
    $created   = time();
    $newtoken  = hash('sha256', $hex . $created);
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
   

  public function validateToken() {

  }

}