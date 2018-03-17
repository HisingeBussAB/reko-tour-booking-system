<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes;


class Responder {

  public $output;
  private $category;

  function __construct() {
    $this->output = array();
    $this->category = array();
  }

  public function AddResponse($key, $value) {
    $this->output[$key] = $value;
    return true;
  }

  public function AddResponsePush($key, $array) {
    if (empty($this->output[$key]) || !is_array($this->output[$key])) {
      $this->output[$key] = array();
    }
    array_push($this->output[$key], $array);
    return true;
  }

  public function AddToArrayOnKey($mainkey, $item) {
    if (empty($this->output[$mainkey]) || !is_array($this->output[$mainkey])) {
      $this->output[$mainkey] = array();
    }
    $this->output[$mainkey][] = $item;
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


