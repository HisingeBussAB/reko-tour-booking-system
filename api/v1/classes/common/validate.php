<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

use RekoBooking\classes\Functions;

class Validate {

  private $response;

  public function __construct(Responder $_response) {
    $this->response = $_response;
  }

  public function validateData($data) {
    $res = Functions::array_map_assoc_recursive([$this, 'validateItem'], $data);
    var_dump($res);
  }

  public function validateItem($key, $value, $keytree) {
    return $keytree;
  }

  
}
