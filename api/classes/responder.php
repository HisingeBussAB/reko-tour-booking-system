<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes;

class Responder {

  public $output;

  function __construct() {
    $this->output = array();
  }

  public function AddResponse($key, $value) {
    $this->output[$key] = $value;
    return true;
  }

  public function AddResponseArray($a) {
    foreach ($a as $key => $value) {
      $this->output[$key] = $value;
    }
    return true;
  }

  public function GetResponse() {
    return json_encode($this->output);
  }

}
