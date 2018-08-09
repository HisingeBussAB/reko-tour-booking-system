<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

class Validate {

  public static function sanatizeString($unvalidatedData) {
    
  }

  public static function validateGeneric($data) {
    var_dump(gettype($data));
    $validated = array();
    foreach ($data as $key => $item) {
      var_dump($key);
      var_dump($item);
      switch(gettype($item)) {
        case "boolean":
        break;
        case "integer":
        break;
        case "string":
        break;
      }
    }
  }

}