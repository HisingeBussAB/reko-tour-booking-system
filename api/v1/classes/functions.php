<?php

namespace RekoBooking\classes;

final class Functions {

/** 
   * array_map_assoc_recursive
   * Recursivly maps an associative array preserving nestling and keys.
   * 
   * callback($key, $value, $keytree)
   * return $value
   * 
   * Keytree will keys to the current sublevel in dot notation. 
   * This might be usefull if you are doing something like validating user inputted JSON
   * 
   * $kt is an array that keeps the keytree and a int that is the lenght of the last array found counting down
   * 
   * From https://stackoverflow.com/questions/13036160/phps-array-map-including-keys
   * Recursivity and key tree tracker added by Håkan Arnoldson
   **/
  public static function array_map_assoc_recursive(callable $f, array $a, array $kt = ['', 0]) {
    return array_column(array_map(function ($key, $value) use ($f, $kt) {
      if (is_array($value)) {
        $kt = [$kt[0] . '.' . $key, count($value)];
        return [$key, self::array_map_assoc_recursive($f, $value, $kt)];
      } else {
        if ($kt[1] > 0) { $kt = [$kt[0], $kt[1]-1]; } else { $kt = [substr($kt[0], 0, strrpos( $kt[0], '.')), $kt[1]-1]; }
        $ktout = $kt[0] == '' ? $key : $kt[0] . '.' . $key;
        return [$key, call_user_func($f, $key, $value, $ktout)];
      }
    }, array_keys($a), $a), 1, 0);
  }

}