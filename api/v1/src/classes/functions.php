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
   * Compare creator. String used screen for duplicates. 
   * Consists of parts of name and address
   * First 35 are comparison without addr, last 7 is adr
   */

  public static function getCompString($_firstName = '',$_lastName = '',$_zip = '', $_street='') {
    $firstName = str_pad(str_replace(['-',' '],'',substr(filter_var(filter_var(strtolower(trim($_firstName)), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_BACKTICK), FILTER_SANITIZE_EMAIL), 0, 15)),15,"0",STR_PAD_RIGHT);
    $lastName = str_pad(str_replace(['-',' '],'',substr(filter_var(filter_var(strtolower(trim($_lastName)), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_BACKTICK), FILTER_SANITIZE_EMAIL), 0, 15)),15,"0",STR_PAD_RIGHT);
    $zip = str_pad(str_replace(['+','-',' '],'',filter_var(substr(trim($_zip), 0, 5), FILTER_SANITIZE_NUMBER_INT)),5,"0",STR_PAD_RIGHT);
    $street = str_pad(str_replace(['-',' '],'',substr(filter_var(filter_var(strtolower(trim($_street)), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_BACKTICK), FILTER_SANITIZE_EMAIL), 3, 9)),7,"0",STR_PAD_RIGHT);
    return (string)substr(($firstName . $lastName . (string)$zip . $street),0,200);
  }

  /**
   * Compare two comp strings with or without address. First 35 are comparison without addr, last 7 is adr
   */
  public static function compCompString(string $str1, string $str2, $useAdr = false) {
    return $useAdr ? (substr($str1,0,35) == substr($str2,0,35)) : ($str1 == $str2);
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
    Moment::setLocale('sv_SE');
    try {
      $m = new \Moment\Moment($date);
    } catch (\Exception $e) {
      return NULL;
    }
    return (string)$m->format('Y-m-d');
  }

  public static function sanatizeStringUnsafe($string, $len = 255) {
    //Just make sure it is a string, trim and casted as such. 
    //statements should be prepared and the API should not be excplicilty trusted in front-end ie. do not dangerously set innerHTML.
    //Optinally cut string to fit DB field
    if (gettype($string) != "integer" && gettype($string) != "string") { return NULL; }
    $new = substr(filter_var(trim($string), FILTER_UNSAFE_RAW),0,$len);
    if(empty($new)) { return NULL; }
    return (string)$new;
  }

  public static function validateZIP($zip) {
    if (gettype($zip) != "integer" && gettype($zip) != "string") { return NULL; }
    $new = self::validateInt(str_replace(['+','-',' '],'',filter_var(trim($zip), FILTER_SANITIZE_NUMBER_INT)), 1000, 999999); //Shouldnt block foreing zips totally
    if (empty($new)) {return NULL;}
    return (int)$new;
  }

  public static function validatePhone($phone) {
    if (gettype($phone) != "integer" && gettype($phone) != "string") { return NULL; }
    $new = substr(str_replace(['+'],'',filter_var(trim($phone), FILTER_SANITIZE_NUMBER_INT)),0,25); //cut to 25
    if (empty($new)) {return NULL;}
    return (string)$new;
  }

  public static function validateEmail($email) {
    if (gettype($email) != "integer" && gettype($email) != "string") { return NULL; }
    $new = substr(filter_var(trim($email), FILTER_VALIDATE_EMAIL),0,60); //Cut to 60 chars
    if(empty($new)) { return NULL; }
    return (string)$new;
  }

  public static function validatePersonalNumber($pnumb) {
    if (gettype($pnumb) != "integer" && gettype($pnumb) != "string") { return NULL; }
    preg_match('/^(18|19|20|21)?([0-9]{6}[-+][0-9]{4})$/', trim($pnumb), $matched, PREG_UNMATCHED_AS_NULL | PREG_OFFSET_CAPTURE);
    if(empty($matched[2][0])) { return NULL; } 
    $sanitizedmatch = str_replace(['+','-'],'',filter_var(trim($matched[2][0]), FILTER_SANITIZE_NUMBER_INT));
    $controlnr = substr((string)$sanitizedmatch, -1);
    $numbers = str_split(substr((string)$sanitizedmatch, 0,9));
    $multipler = 2;
    $sums = '';
    foreach($numbers as $nr) {
      $sums = $sums . (string)((int)$nr * (int)$multipler);
      $multipler = $multipler == 2 ? 1 : 2;
    }
    $sum = 0;
    foreach(str_split($sums) as $nr) {
      $sum = $sum + (int)$nr;
    }
    if ((int)$controlnr != (int)((10 - ($sum % 10)) % 10)) {
      return NULL;
    }

    $new = trim($matched[2][0]);
    return (string)$new;
  }

  public static function validateTime($time) {
    if (gettype($time) != "integer" && gettype($time) != "string") { return NULL; }
    Moment::setDefaultTimezone('CET');
    Moment::setLocale('sv_SE');
    try {
      $m = new \Moment\Moment($time);
    } catch (\Exception $e) {
      return NULL;
    }
    return (string)$m->format('H:i:s');
  }


}