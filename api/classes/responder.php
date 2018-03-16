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

  public function AddResponsePush($key, $array) {
    if (empty($this->output[$key]) || !is_array($this->output[$key])) {
      $this->output[$key] = array();
    }
    array_push($this->output[$key], $array);
    return true;
  }

  public function AddDeepArray($mainkey, $itemkey, $item) {
    if (empty($this->output[$mainkey]) || !is_array($this->output[$mainkey])) {
      $this->output[$mainkey] = array();
    }
    $this->output[$mainkey][$itemkey] = $item;
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
