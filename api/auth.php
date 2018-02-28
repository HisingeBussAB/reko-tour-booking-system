<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking\classes;

use RekoBooking\classes\DB;
use RekoBooking\classes\DBError;

/*
$pdo = DB::get();
try {
  $sth = $pdo->prepare($sql);
  $sth->bindParam(':username', $username, \PDO::PARAM_STR);
  $sth->bindParam(':pwd', $hashed, \PDO::PARAM_STR);
  $sth->execute(); 
} catch(\PDOException $e) {
  DBError::showError($e, __CLASS__, $sql);
}
*/

//$hashed = password_verify($hex . PWD_PEPPER, PASSWORD_DEFAULT);


$username = 'AutoUser';
$sql = "/hjINSERT INTO Auth (username, pwd) VALUES (:username, :pwd)";
$a = array('hello' => 'I\'m alive');
echo json_encode($a);
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