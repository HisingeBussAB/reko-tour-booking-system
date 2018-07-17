<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes;

use RekoBooking\classes\Responder;

class DBError
{

  /**
   * Writes out database error at desired level from config.php DEBUF_MODE
   *
   * @uses DEBUG_MODE
   * @param object $e standard thrown
   * @param string $class __CLASS__ of origin
   * @param string $sql SQL query that triggered the error if any
   */
  public static function showError($e, $class, $sql='NO QUERY', $response = false) {
    if (!$response) {
      $response = new Responder;
    }
    if (DEBUG_MODE) {
      var_dump('setting header');
      header( $_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error');
      $headers = ob_get_clean();
      echo $headers;
      $response->AddResponse('response', ('Databasfel från ' . $class . ': ' . $e->getMessage() . '. SQL: '. $sql));
    } else {
      header( $_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error');
      $headers = ob_get_clean();
      echo $headers;
      $response->AddResponse('response', 'Databasfel. Kontakta administratör om felet kvarstår.');
    }
  }
}
