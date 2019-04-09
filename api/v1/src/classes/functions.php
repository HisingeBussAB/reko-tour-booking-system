<?php

namespace RekoBooking\classes;

use \Moment\Moment;

final class Functions {

/** 
   * array_map_assoc_recursive
   * Recursivly maps an associative array preserving nestling and keys.
   * 
   * callback($key, $value)
   * return $value
   * 
   *  The key will contain all levels of keys using dot notation comparable to the front-end js
   * 
   * $kt is an array that keeps the keytree and a int that is the lenght of the last array found counting down (keeps track of when to move back up one level)
   * 
   * Based on answer in https://stackoverflow.com/questions/13036160/phps-array-map-including-keys
   * Recursivity and key tree tracker added by Håkan Arnoldson
   * 
   * Unused fuction!!!
   **/
  public static function array_map_assoc_recursive(callable $f, array $a, array $kt = ['', 0]) {
    return array_column(array_map(function ($key, $value) use ($f, $kt) {
      if (is_array($value)) {
        $kt = [$kt[0] . '.' . $key, count($value)];
        return [$key, self::array_map_assoc_recursive($f, $value, $kt)];
      } else {
        if ($kt[1] > 0) { $kt = [$kt[0], $kt[1]-1]; } else { $kt = [substr($kt[0], 0, strrpos( $kt[0], '.')), $kt[1]-1]; }
        $ktout = $kt[0] == '' ? $key : $kt[0] . '.' . $key;
        return [$key, call_user_func($f, $ktout, $value)];
      }
    }, array_keys($a), $a), 1, 0);
  }

  /**
   * Validation/sanitazion functions
   * These functions return validated/santazited value or NULL if fail
   * 
   * Note that they return NULL instead of the standard behaviour FALSE. 
   * This is because NULL is never a valid user input in but false is.  
   */

  /**
   * Validates integer 
   * Checks so type is int or string for trim not to cause exception
   * Also check so value isnt true because this will be cast to 1 by filter_var
   */
  public static function validateInt($int, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX) {
    if ((gettype($int) != "integer" && gettype($int) != "string") || $int === true) { return NULL; }
    $new = filter_var(trim($int), FILTER_VALIDATE_INT, array("options" => array("min_range"=>$min, "max_range"=>$max)));
    if (empty($new)) {return NULL;}
    return (int)$new;
  }

  public static function validateBoolToBit($bool) {
    if (gettype($bool) != "integer" && gettype($bool) != "string" && gettype($bool) != "boolean") { return NULL; }
    $new = filter_var(trim($bool), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    if(is_null($new)) { return NULL; }
    if($new === true) { return 1; } else { return 0; }
  }

  public static function validateDate($date) {
    if (gettype($date) != "integer" && gettype($date) != "string") { return NULL; }
    Moment::setDefaultTimezone('CET');
    Moment::setLocale('se_SV');
    try {
      $m = new \Moment\Moment($date);
    } catch (\Exception $e) {
      return NULL;
    }
    return (string)$m->format('Y-m-d');
  }

  public static function sanatizeStringUnsafe($string) {
    //Just make sure it is a string, trim and casted as such. 
    //statements should be prepared and the API should not be excplicilty trusted in front-end ie. do not dangerously set innerHTML.
    if (gettype($string) != "integer" && gettype($string) != "string") { return NULL; }
    $new = filter_var(trim($string), FILTER_UNSAFE_RAW);
    if(empty($new)) { return NULL; }
    return (string)$new;
  }

  public static function validateZIP($zip) {
    if (gettype($zip) != "integer" && gettype($zip) != "string") { return NULL; }
    $new = self::validateInt(str_replace(['+','-'],'',filter_var(trim($zip), FILTER_SANITIZE_NUMBER_INT)), 10000, 99999);
    if (empty($new)) {return NULL;}
    return (int)$new;
  }

  public static function validatePhone($phone) {
    if (gettype($phone) != "integer" && gettype($phone) != "string") { return NULL; }
    $new = str_replace(['-'],'',filter_var(trim($phone), FILTER_SANITIZE_NUMBER_INT));
    if (empty($new)) {return NULL;}
    return (string)$new;
  }

  public static function validateEmail($email) {
    if (gettype($email) != "integer" && gettype($email) != "string") { return NULL; }
    $new = filter_var(trim($email), FILTER_VALIDATE_EMAIL);
    if(empty($new)) { return NULL; }
    return (string)$new;
  }

  public static function validatePersonalNumber($pnumb) {
    if (gettype($pnumb) != "integer" && gettype($pnumb) != "string") { return NULL; }
    $new = str_replace(['+','-'],'',filter_var(trim($pnumb), FILTER_SANITIZE_NUMBER_INT));
    if(empty($new)) { return NULL; }
    return (string)$new;
  }

  public static function validateTime($time) {
    if (gettype($time) != "integer" && gettype($time) != "string") { return NULL; }
    Moment::setDefaultTimezone('CET');
    Moment::setLocale('se_SV');
    try {
      $m = new \Moment\Moment($time);
    } catch (\Exception $e) {
      return NULL;
    }
    return (string)$m->format('H:i:s');
  }


}