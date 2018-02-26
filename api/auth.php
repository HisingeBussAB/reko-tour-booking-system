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

$hashed = password_hash($hex . GLOBAL_PEPPER, PASSWORD_DEFAULT);

var_dump($hex);
$username = 'AutoUser';
$sql = "INSERT INTO dbo.Auth VALUES (:id, :user, :pwd)";


try {
    $pdo->beginTransaction();
    $sth = $pdo->prepare("SELECT(NEXT VALUE FOR dbo.Bokningsnr)");
    $sth->execute();
    $nextId = $sth->fetch(\PDO::FETCH_NUM);
    //$sth->bindParam(':user', $username, \PDO::PARAM_STR);
    //$sth->bindParam(':id', $nextId, \PDO::PARAM_INT);
    //$sth->bindParam(':pwd', $hashed, \PDO::PARAM_STR);
    //$sth->execute();
    //$nextId = $pdo->exec();
    $next = $nextId[0];
    var_dump($next);
    $sth = $pdo->prepare($sql);
    $sth->bindParam(':user', $username, \PDO::PARAM_STR);
    $sth->bindParam(':id', $next, \PDO::PARAM_INT);
    $sth->bindParam(':pwd', $hashed, \PDO::PARAM_STR);
    $sth->execute();
    
    $pdo->rollBack();
  } catch(\PDOException $e) {
    $pdo->rollBack();
    DBError::showError($e, __CLASS__, $sql);
    $errorType = "Databasfel";
    throw new \RuntimeException("Databasfel:");
  }