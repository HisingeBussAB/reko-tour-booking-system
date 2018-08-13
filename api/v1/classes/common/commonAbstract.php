<?php
/**
 * CommonAbstract
 * This class sets constructur and arguments for the response handler and PDO
 * used in many classes * 
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

abstract class CommonAbstract {

  protected $response;
  protected $pdo;
  
  public function __construct(Responder $_response, \PDO $_pdo) {
    $this->response = $_response;
    $this->pdo = $_pdo;
  }
}