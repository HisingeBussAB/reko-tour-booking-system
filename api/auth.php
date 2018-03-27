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

if (empty($jsonData['logintoken']) || empty($jsonData['pwd'])) {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
  $headers = ob_get_clean();
  echo $headers;
  $response->AddResponse('login', false);
  $response->AddResponse('saved', false);
  $response->AddResponse('response', 'För lite data skickad för att validera inloggning.');
  echo $response->GetResponse();
  die();
}

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

$userid = $result['authid'];


$now = time();
$expires = ($now + 600000);
  if (password_verify($jsonData['pwd'] . PWD_PEPPER, $result['pwd']) || validateOnce($userid, $jsonData['pwd'], $pdo)) {

    //Generate JWT
  
    $token = array(
    "iss"   => DOMAIN,
    "aud"   => DOMAIN,
    "sub"   => $user,
    "iat"   => $now,
    "nbf"   => $now - 30,
    "exp"   => $expires,
    "jti"   => array(
      "mark"  => JWT_WATERMARK,
      "agent" => $_SERVER['HTTP_USER_AGENT'],
      "ip"    => $_SERVER['REMOTE_ADDR'],
      "ent"   => bin2hex(openssl_random_pseudo_bytes(6))
    ),
  );
  $jwtSecret = Tokens::createJWTToken('jwt', $user, $pdo);
  $jwt = JWT::encode($token, $jwtSecret . JWT_SECRET_PEPPER, 'HS512');
  $response->AddResponse('login', true);
  $response->AddResponse('saved', false);
  $response->AddResponse('jwt', $jwt);
  $response->AddResponse('user', $user);
  $response->AddResponse('expires', $expires);


  //Generate Once Login

  $onceid = bin2hex(openssl_random_pseudo_bytes(14));
  $oncetoken = hash('sha512', bin2hex(openssl_random_pseudo_bytes(12)) . microtime());
  try {
    $sql = "INSERT INTO Auth_Once (userID, tokenid, token, created) VALUES (:userid, :tokenid, :token, :created);";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':userid',  $userid,       \PDO::PARAM_INT);
    $sth->bindParam(':tokenid', $onceid,       \PDO::PARAM_STR);
    $sth->bindParam(':token',   $oncetoken,    \PDO::PARAM_STR);
    $sth->bindParam(':created', $now,          \PDO::PARAM_INT);
    $sth->execute(); 
    
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
  }

  $onceObj = (object) array('user' => $user, 'tokenid' =>  $onceid, 'token' => $oncetoken, 'expires' => ($now + 259200));
  $response->AddResponse('once', $onceObj);



  echo $response->GetResponse();
} else {
  wrongLogin($response);
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


function validateOnce($userId, $pwd, $pdo) {
  $tokens = explode(ONCE_LOGIN_TOKEN, $pwd);
  if (count($tokens) != 2) {
    return false;
  }

  if (!flushAuthOnce($pdo)) {
    return false;
  }

  try {
    $tokens1 = $tokens[0];
    $tokens2 = $tokens[1];
  } catch(\Expection $e) {
    return false;
  }

  try {
    $sql = "SELECT TOP 1 * FROM Auth_Once WHERE userId = :userid AND tokenid = :tokenid AND token = :token ORDER BY created DESC;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':userid',  $userId,    \PDO::PARAM_INT);
    $sth->bindParam(':tokenid', $tokens1,   \PDO::PARAM_STR);
    $sth->bindParam(':token',   $tokens2,   \PDO::PARAM_STR);
    $sth->execute(); 
    $result = $sth->fetch(\PDO::FETCH_ASSOC);
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    return false;
  }

  
  //Destroy all of users onces. Done regardless if the login was found or not. 
  //Invalid once try will force manual login only.
  try {
    $sql = "DELETE FROM Auth_Once WHERE userId = :userid;";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':userid', $userId, \PDO::PARAM_INT);
    $sth->execute(); 
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    return false;
  }

  try{
    if ($result['userid'] == $userId && $result['token'] == $tokens2) {
      return true;
    }
  } catch(\Expection $e) {
    return false;
  }


}

function flushAuthOnce($pdo) {
  $expired = time() - 259200;
    try {
      $sql = "DELETE FROM Auth_Once WHERE Created < :expired;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':expired', $expired, \PDO::PARAM_INT);
      $sth->execute(); 
      
    } catch(\PDOException $e) {
      DBError::showError($e, __CLASS__, $sql);
      return false;
    }
    return true;
}