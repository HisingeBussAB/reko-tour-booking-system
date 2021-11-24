<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

final class DB {

  protected static $pdo;
  protected static $pdoweb;

  public static function get(Responder $response) {

  if(!isset(self::$pdo)) {
    try {
      self::$pdo = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD);
      self::$pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER); //Forces table names to lower case
      self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); //Error throw exceptions, catch with code.
      self::$pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); //Not compatible with all drives, defaults to false if not supported. Prepare each statement instead.
      self::$pdo->setAttribute(\PDO::ATTR_AUTOCOMMIT, 1);
      } catch(\PDOException $e) {
        $response->DBError($e, __CLASS__);
        return false;
      }
    }
  return self::$pdo;
  }

  public static function getweb(Responder $response) {
  if(!isset(self::$pdoweb)) {
    try {
      self::$pdoweb = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_WEB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD);
      self::$pdoweb->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_LOWER); //Forces table names to lower case
      self::$pdoweb->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); //Error throw exceptions, catch with code.
      self::$pdoweb->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); //Not compatible with all drives, defaults to false if not supported. Prepare each statement instead.
      } catch(\PDOException $e) {
        $response->DBError($e, __CLASS__);
        return false;
      }
    }
  return self::$pdoweb;
  }

  /**
  * Private contructor - Creates error if trying to call this class with new
  */
  private function __construct() {}
}


