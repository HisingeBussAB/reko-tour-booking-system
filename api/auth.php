<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking;

use \Firebase\JWT\JWT;
use RekoBooking\classes\DB;
use RekoBooking\classes\DBError;
use RekoBooking\classes\Tokens;

$jsonData = json_decode(trim(file_get_contents('php://input')), true);

if (!empty($jsonData['user'])) {
  $user = trim(filter_var($jsonData['user'], FILTER_SANITIZE_STRING));
} else {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
  $headers = ob_get_clean();
  echo $headers;
  $response->AddResponse('login', false);
  $response->AddResponse('saved', false);
  $response->AddResponse('response', 'Användarnamnet kan inte vara tomt.');
  echo $response->GetResponse();
  die();
} 

if (!empty($jsonData['apitoken']) && $jsonData['apitoken'] === API_TOKEN) {
 
  $pdo = DB::get();

  if (!Tokens::validateToken($jsonData['logintoken'], 'login', $pdo)) {
    header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
    $headers = ob_get_clean();
    echo $headers;
    $response->AddResponse('login', false);
    $response->AddResponse('saved', false);
    $response->AddResponse('response', 'En tillfällig token som behövs för den här operationen har troligen gått ut. Prova ladda om sidan (F5).');
    echo $response->GetResponse();
    die();
  }

  try {
    $sql = "SELECT TOP 1 * FROM Auth WHERE username = :user ORDER BY AuthID;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':user', $user, \PDO::PARAM_STR);
    $sth->execute(); 
    $result = $sth->fetch(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
  }

  if (!$result) {
    wrongLogin($response);
  }

  $now = time();

    if (password_verify($jsonData['pwd'] . 'df' . PWD_PEPPER, $result['pwd'])) {
    
      $token = array(
      "iss"   => DOMAIN,
      "aud"   => DOMAIN,
      "sub"   => $user,
      "iat"   => $now,
      "nbf"   => $now - 120,
      "exp"   => $now + 600000,
      "jti"   => array(
        "mark"  => JWT_WATERMARK,
        "agent" => $_SERVER['HTTP_USER_AGENT'],
        "ip"    => $_SERVER['REMOTE_ADDR'],
        "ent"   => bin2hex(openssl_random_pseudo_bytes(6))
      ),
    );
    $jwtSecret = Tokens::createJWTToken('jwt', $user, $pdo);
    $jwt = JWT::encode($token, $jwtSecret, 'HS512');
    $response->AddResponse('login', true);
    $response->AddResponse('saved', false);
    $response->AddResponse('jwt', $jwt);
    $response->AddResponse('user', $user);
    $response->AddResponse('expires', ($now + 600000));
    echo $response->GetResponse();
  } else {
    wrongLogin($response);
  }


} else {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
  $headers = ob_get_clean();
  echo $headers;
  $response->AddResponse('login', false);
  $response->AddResponse('saved', false);
  $response->AddResponse('response', 'Fel APItoken sänd med begäran. Inte tillåten.');
  echo $response->GetResponse();
  die();
}

function wrongLogin($response) {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
  $headers = ob_get_clean();
  echo $headers;
  $response->AddResponse('login', false);
  $response->AddResponse('saved', false);
  $response->AddResponse('response', 'Fel användare eller lösenord.');
  echo $response->GetResponse();
  die();
}