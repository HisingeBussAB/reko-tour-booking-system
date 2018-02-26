<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes;

final class DB {

  protected static $pdo;

  public static function get() {
  if(!isset(self::$pdo)) {
    try{
        self::$pdo = new \PDO(DB_CONNECTION, DB_USER, DB_PASSWORD);
        self::$pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER); //Forces table names to lower case
        self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); //Error throw exceptions, catch with code.
        self::$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); //Not compatible with all drives, defaults to false if not supported. Prepare each statement instead.
      } catch(\PDOException $e) {
        DBError::showError($e, __CLASS__);
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