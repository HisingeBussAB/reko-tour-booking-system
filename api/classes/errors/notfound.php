<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\errors;

class NotFound {

  public static function PrintDie() {
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    $a = array('response' => 'Felaktig URL det finns inget innehåll på denna länk.');
    $headers = ob_get_clean();
    echo $headers;
    echo json_encode($a);
    die();
  }
}
