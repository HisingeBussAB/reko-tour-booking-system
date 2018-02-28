<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking\classes;

use RekoBooking\classes\DB;
use RekoBooking\classes\DBError;

$pdo = DB::get();

$bytes = openssl_random_pseudo_bytes(120);
$hex = hash('sha256', bin2hex($bytes));

$hashed = password_hash($hex . PWD_PEPPER, PASSWORD_DEFAULT);

var_dump($hex);
echo($hex);
$username = 'AutoUser';
$sql = "INSERT INTO Auth (username, pwd) VALUES (:username, :pwd)";


try {
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':username', $username, \PDO::PARAM_STR);
    $sth->bindParam(':pwd', $hashed, \PDO::PARAM_STR);
    $sth->execute(); 
  } catch(\PDOException $e) {
    DBError::showError($e, __CLASS__, $sql);
    $errorType = "Databasfel";
    throw new \RuntimeException("Databasfel:");
  }