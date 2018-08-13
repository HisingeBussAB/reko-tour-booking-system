<?php
/**
 * Rekå Resor Bokningssystem Main Controller
 * @author    Håkan Arnoldson
 */
namespace RekoBooking;

use RekoBooking\classes\common\Responder;
use RekoBooking\classes\common\DB;
use RekoBooking\classes\common\Maintenance;
use RekoBooking\classes\common\Auth;
use \Firebase\JWT\JWT;


class Controller {

  private $response;
  private $pdo;

  function __construct() {
    $this->response = new Responder;
    $this->response->AddResponse('saved',     false);
    $this->response->AddResponse('login',     false);
    $this->response->AddResponse('success',   false);
    $this->response->AddResponse('response',  'Ingen uppgift utförd.');
    $this->pdo = DB::get($this->response);
    if ($this->pdo == false) {
      $this->response->AddResponse('response',  'Kritiskt fel. Databasanslutning misslyckades.');
      $this->response->Exit(500);
    }
  }

  /**
   * Starts the controller checks the method and authentication and executes method
   * @param string $item Item type the API query is about
   * @param int $id The database ID of the item. If -1 treat as entire collection requested
   * @param boolean $login Is login required for the requested operation
   */
  public function start(string $item, int $id = -1, bool $login = true) {
    $isAuthenticated = false;
    if ($login) {
      $unvalidatedData = json_decode(trim(file_get_contents('php://input')), true);
      $isAuthenticated = $this->authenticate($unvalidatedData);
    }
    if (!$login || $isAuthenticated) {
      switch($_SERVER['REQUEST_METHOD'])
      {
        case "GET":
          $this->get($item, $id);
        break;
      
        case "POST":
          $unvalidatedData = json_decode(trim(file_get_contents('php://input')), true);
          $this->post($item, $unvalidatedData);
          if (!ENV_CRON_JOB) { Maintenance::refreshSecrets($this->response, $this->pdo); }
        break;

        case "PUT":
          $unvalidatedData = json_decode(trim(file_get_contents('php://input')), true);
          $this->put($item, $id, $unvalidatedData);
          if (!ENV_CRON_JOB) { Maintenance::refreshSecrets($this->response, $this->pdo); }
          
        break;

        case "DELETE":
          $this->delete($item, $id);
          if (!ENV_CRON_JOB) { Maintenance::refreshSecrets($this->response, $this->pdo); }
        break;
      }
    }
    return $this->response->GetResponse();
  
  }

  public function auth($action) {

    if (!ENV_CRON_JOB) { Maintenance::refreshSecrets($this->response, $this->pdo); }

    switch($action)
      {
        case "login":
          if (Auth::login($this->response, $this->pdo)) {
            http_response_code(202);
          } else {
            header('WWW-Authenticate: Basic"');
            http_response_code(401);
          }
        break;
      
        case "refresh":
          Auth::refresh($this->response, $this->pdo);
        break;

        case "revoke":
          Auth::revoke($this->response, $this->pdo);
        break;
      }
      return $this->response->GetResponse();
  }

  private function get(string $item, int $id) {

  }

  private function post(string $item,$unvalidatedData) {

  }

  private function put(string $item, int $id, $unvalidatedData) {


  }

  private function delete(string $item, int $id) {

  }

  private function authenticate($unvalidatedData) {

  }


}
