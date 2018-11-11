<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

final class DB {

  protected static $pdo;

  public static function get(Responder $response) {
  if(!isset(self::$pdo)) {
    try{
        self::$pdo = new \PDO(DB_CONNECTION, DB_USER, DB_PASSWORD);
        self::$pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL); 
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); //Error throw exceptions, catch with code.
      } catch(\PDOException $e) {
        $response->DBError($e, __CLASS__);
        return false;
      }
    }
  return self::$pdo;
  }

  /**
  * Private contructor - Creates error if trying to call this class with new
  */
  private function __construct() {}
}