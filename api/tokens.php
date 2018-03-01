<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking;

use RekoBooking\classes\DB;
use RekoBooking\classes\DBError;


$jsonData = json_decode(trim(file_get_contents('php://input')), true);



if ($jsonData['apitoken'] === API_TOKEN) {
  $bytes     = openssl_random_pseudo_bytes(12);
  $hex       = bin2hex($bytes);
  $created   = time();
  $newtoken  = hash('sha256', $hex . $created);

  $pdo = DB::get();
  try {
    $sql = "INSERT INTO Tokens VALUES (:token, :tokentype, :created);";
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':token', $newtoken, \PDO::PARAM_STR);
    $sth->bindParam(':tokentype', $tokentype, \PDO::PARAM_STR);
    $sth->bindParam(':created', $created, \PDO::PARAM_INT);
    $sth->execute(); 
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
  }
  $a = array($tokentype . 'token' => $newtoken);
  echo json_encode($a);

} else {
  header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
  $headers = ob_get_clean();
  echo $headers;
  $a = array('response' => 'Fel APItoken sänd med begäran. Inte tillåten.');
  echo json_encode($a);
  die();
}
