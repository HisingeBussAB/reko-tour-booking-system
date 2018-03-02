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

$user = $jsonData['user'];

if ($jsonData['apitoken'] === API_TOKEN) {

  $pdo = DB::get();

  if (!Tokens::validateToken($jsonData['logintoken'], 'login', $pdo)) {
    header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
    $headers = ob_get_clean();
    echo $headers;
    $a = array('response' => 'En tillfällig token som behövs för den här operationen har troligen gått ut. Prova ladda om sidan (F5).');
    echo json_encode($a);
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
    wrongLogin();
  }

  $now = time();

    if (password_verify($jsonData['pwd'] . PWD_PEPPER, $result['pwd'])) {
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
    $a = array('jwt' => $jwt, 'login' => true, 'user' => $user, 'expires' => $now + 600000);
    echo json_encode($a);
  } else {
    wrongLogin();
  }


  

  //$hashed = password_verify($hex . PWD_PEPPER, PASSWORD_DEFAULT);
/*
  $username = 'AutoUser';
  $sql = "/hjINSERT 845INTO Auth (username, pwd) VALUES (:username, :pwd)";
  $a = array('hello' => 'I\'m alive');
  echo json_encode($a);*/
  /*
  try {
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':username', $username, \PDO::PARAM_STR);
    $sth->bindParam(':pwd', $hashed, \PDO::PARAM_STR);
    $sth->execute(); 
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
  }
  */

} else {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
  $headers = ob_get_clean();
  echo $headers;
  $a = array('response' => 'Fel APItoken sänd med begäran. Inte tillåten.');
  echo json_encode($a);
  die();
}

function wrongLogin() {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
  $headers = ob_get_clean();
  echo $headers;
  $a = array('response' => 'Fel användare eller lösenord.');
  echo json_encode($a);
  die();
}