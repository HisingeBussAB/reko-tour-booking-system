<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

use \Firebase\JWT\JWT;

final class Auth {

  public static function login(Responder $response, \PDO $pdo): bool {
    if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
      $response->AddResponse('error', 'Både användarnamn och lösenord måste anges.');
      return false;
    }

    $user = trim(filter_var($_SERVER['PHP_AUTH_USER'], FILTER_SANITIZE_STRING));

    if (!self::HammerGuard($response, $pdo, false)) {
      $response->AddResponse('error', 'För många inloggningsförsök. Prova igen lite senare.');
      return false;
    }
    
    try {
      $sql = "SELECT TOP 1 * FROM Auth WHERE username = :user ORDER BY AuthID;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }

    if (!$result) {
      $response->AddResponse('error', 'Användarnamnet eller lösenordet är felaktigt.');
      return false;
    }

    if (password_verify(trim($_SERVER['PHP_AUTH_PW']) . AUTH_PWD_PEPPER, $result['pwd'])) {

      $userid = $result['authid'];
      $now = time();
      $accessExp = $now + 3700; //+1 hour
      $refreshExp = $now + 7776000;//+90 days
      $accessToken = '';
      $refreshToken = '';
      //Generate Access Token
      $token = array(
        "iss"   => ENV_DOMAIN,
        "aud"   => ENV_DOMAIN,
        "sub"   => $userid,
        "iat"   => $now,
        "nbf"   => $now - 10,
        "exp"   => $accessExp,
        "client" => array(
          "agent" => $_SERVER['HTTP_USER_AGENT'],
          "ip"    => $_SERVER['REMOTE_ADDR'],
        ),
        "jti"   => bin2hex(random_bytes(6)) //Not used, only adds some entropy
      );
      $secrets = self::getSecrets($response, $pdo);
      try {
        $accessToken = JWT::encode($token, $secrets[0]['token'] . AUTH_JWT_SECRET_PEPPER, 'HS512');
      } catch (\Exception $e) {
        $response->AddResponse('error', 'Kunde inte kryptera accesstoken.');
        $response->LogError($e->getMessage(), __CLASS__);
        $response->Exit(500);
      }

      //Generate Refresh Token
      $token = array(
        "iss"   => ENV_DOMAIN,
        "aud"   => ENV_DOMAIN,
        "sub"   => $userid,
        "iat"   => $now,
        "nbf"   => $now - 10,
        "exp"   => $refreshExp,
        "client" => array(
          "agent" => $_SERVER['HTTP_USER_AGENT'],
          "ip"    => $_SERVER['REMOTE_ADDR']
        ),
        "jti"   => bin2hex(random_bytes(6)) //Not used, only adds some entropy
      );

      $refreshSecret = bin2hex(random_bytes(24));
      //Clear all users saved refresh secrets
      try {
        $sql = "DELETE FROM Tokens WHERE tokentype = 'refreshsecret' AND username = :user;";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':user', $user, \PDO::PARAM_STR);
        $sth->execute(); 
      } catch(\PDOException $e) {
        $response->DBError($e, __CLASS__, $sql);
        $response->Exit(500);
      }
      //Save refresh secret
      try {
        $sql = "INSERT INTO Tokens (Token, TokenType, Created, username) VALUES (:token, 'refreshsecret', :created, :user);";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':token', $refreshSecret, \PDO::PARAM_STR);
        $sth->bindParam(':created', $now, \PDO::PARAM_INT);
        $sth->bindParam(':user', $user, \PDO::PARAM_STR);
        $sth->execute(); 
      } catch(\PDOException $e) {
        $response->DBError($e, __CLASS__, $sql);
        $response->Exit(500);
      }
      try {
        $refreshToken = JWT::encode($token, $refreshSecret . AUTH_JWT_SECRET_PEPPER, 'HS512');
      } catch (\Exception $e) {
        $response->AddResponse('error', 'Kunde inte kryptera refreshtoken.');
        $response->LogError($e->getMessage(), __CLASS__);
        $response->Exit(500);
      }

      //Clear HammerGuard for IP
      self::HammerGuard($response, $pdo, true);

      //Write login status and tokens to response and return true
      $response->AddResponse('login', true);
      $response->AddResponse('response', 'Tokens skapade och skickade. Inloggning lyckad!');
      $response->AddResponse('access', array('token' => $accessToken, 'expires' => $accessExp));
      $response->AddResponse('refresh', array('token' => $refreshToken, 'expires' => $refreshExp));
      
      return true;
    } 
    
    $response->AddResponse('error', 'Användarnamn eller lösenord felaktigt.');
    return false;
    
    
  }

  public static function refresh(Responder $response, \PDO $pdo) {
    var_dump($_SERVER['HTTP_AUTHORIZATION']);
    
    preg_match('/Bearer\s((.*)\.(.*)\.(.*))/', $_SERVER['HTTP_AUTHORIZATION'], $matches);
    var_dump($matches);
    
  }

  public static function revoke(Responder $response, \PDO $pdo) {
    
  }

  private static function getSecrets(Responder $response, \PDO $pdo) {
    try {
      $sql = "SELECT token FROM Tokens WHERE tokentype = 'jwtsecret' ORDER BY created DESC;";
      $sth = $pdo->prepare($sql);
      $sth->execute(); 
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }
    if (count($result) < 1) {
      $response->AddResponse('error', 'Databasen är korruperad, det hittades ingen nyckel att kyptera din token med.');
      $response->Exit(500);
    }
    return $result;
  } 
}

   