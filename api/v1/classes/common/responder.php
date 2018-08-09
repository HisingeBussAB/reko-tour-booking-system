<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;


class Responder {

  private $output;
  private $category;

  function __construct() {
    $this->output = array();
    $this->category = array();
  }

  public function AddResponse(string $key, $value) {
    $this->output[$key] = $value;
    return true;
  }

  public function AddResponsePush(string $key, $array) {
    if (empty($this->output[$key]) || !is_array($this->output[$key])) {
      $this->output[$key] = array();
    }
    array_push($this->output[$key], $array);
    return true;
  }

  public function AddToArrayOnKey(string $mainkey, $item) {
    if (empty($this->output[$mainkey]) || !is_array($this->output[$mainkey])) {
      $this->output[$mainkey] = array();
    }
    $this->output[$mainkey][] = $item;
  }

  
  public function AddResponseArray(array $a) {
    foreach ($a as $key => $value) {
      $this->output[$key] = $value;
    }
    return true;
  }

  public function GetResponse() {
    return json_encode($this->output);
  }

  public function DBError(\PDOException $e, string $class, string $sql='NO QUERY') {
    header( $_SERVER["SERVER_PROTOCOL"] . ' 500 Internal Server Error');
    http_response_code(500);
    if (ENV_DEBUG_MODE) {
      $this->AddResponse('error', ('Databasfel från ' . $class . ': ' . $e->getMessage() . '. SQL: '. $sql));
    } else {
      $this->AddResponse('error', 'Databasfel. Kontakta administratör om felet kvarstår.');
    }
    $this->Exit();
  }

  public function Exit() {
    echo $this->response->GetResponse();
    $website = ob_get_clean();
    echo $website;
    die();
  }

}


