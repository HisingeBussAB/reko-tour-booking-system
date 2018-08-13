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
      $response->AddResponse('error', 'Både användarnamn och lösenord måste anges');
      return false;
    }

    $user = trim(filter_var($_SERVER['PHP_AUTH_USER'], FILTER_SANITIZE_STRING));
    
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
      $response->AddResponse('error', 'Användarnamn eller lösenord felaktigt.');
      return false;
    }

    if (password_verify(trim($_SERVER['PHP_AUTH_PW']) . AUTH_PWD_PEPPER, $result['pwd'])) {

      $userid = $result['authid'];
      $now = time();

      //Generate Access Token
      $token = array(
        "iss"   => ENV_DOMAIN,
        "aud"   => ENV_DOMAIN,
        "sub"   => $userid,
        "iat"   => $now,
        "nbf"   => $now - 10,
        "exp"   => $now + 3700,
        "mark"  => AUTH_JWT_WATERMARK, 
        "jti"   => array(
          "mark"  => AUTH_JWT_WATERMARK,
          "agent" => $_SERVER['HTTP_USER_AGENT'],
          "ip"    => $_SERVER['REMOTE_ADDR'],
          "ent"   => bin2hex(openssl_random_pseudo_bytes(6))
        ),
      );
      $jwtSecret = Tokens::createJWTToken('jwt', $user, $pdo);
      $jwt = JWT::encode($token, $jwtSecret . AUTH_JWT_SECRET_PEPPER, 'HS512');
      $response->AddResponse('login', true);
      $response->AddResponse('saved', false);
      $response->AddResponse('jwt', $jwt);
      $response->AddResponse('user', $user);
      $response->AddResponse('expires', $expires);
      //Generate Refresh Token
    } 
    
    $response->AddResponse('error', 'Användarnamn eller lösenord felaktigt.');
    return false;
    
    
  }

  public static function refresh(Response $response, \PDO $pdo) {
    var_dump($_SERVER['HTTP_AUTHORIZATION']);
    
    preg_match('/Bearer\s((.*)\.(.*)\.(.*))/', $_SERVER['HTTP_AUTHORIZATION'], $matches);
    var_dump($matches);
    
  }

  public static function revoke(Response $response, \PDO $pdo) {
    
  }
}

   