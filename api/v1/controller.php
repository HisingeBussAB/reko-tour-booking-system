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
use RekoBooking\classes\common\Validate;


class Controller {

  private $response;
  private $pdo;
  private $userData;

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
   * @param string $item Item type the API query is about.
   * @param int $id The database ID of the item. If -1 treat as entire collection requested.
   * @param boolean $login Is login required for the requested operation. Can be used override login check for testing and certain actions.
   */
  public function start(string $item, int $id = -1, bool $login = true) {
    $isAuthenticated = false;
    if ($login) {
      $isAuthenticated = $this->authenticate();
    }
    if (!$login || $isAuthenticated) {
      $unvalidatedData = json_decode(trim(file_get_contents('php://input')), true);
      $Validator = new Validate($this->response);
      $validatedData = $Validator->ValidateData($unvalidatedData);
      switch($_SERVER['REQUEST_METHOD'])
      {
        case "GET":
          $this->get($item, $id);
        break;
      
        case "POST":
          $this->post($item, $validatedData);
          if (!ENV_CRON_JOB) { Maintenance::refreshSecrets($this->response, $this->pdo); }
        break;

        case "PUT":
          $this->put($item, $id, $validatedData);
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
          if (Auth::login($this->response, $this->pdo )) {
            http_response_code(202);
          } else {
            header('WWW-Authenticate: Basic');
            http_response_code(401);
          }
        break;
      
        case "refresh":
        if ( Auth::refresh($this->response, $this->pdo )) {
          http_response_code(202);
        } else {
          header('WWW-Authenticate: Bearer');
          http_response_code(401);
        }
        break;

        case "revoke":
          if ( Auth::revoke($this->response, $this->pdo )) {
            http_response_code(200);
          } else {
            header('WWW-Authenticate: Bearer');
            http_response_code(401);
          }
          break;
      }
      return $this->response->GetResponse();
  }

  public function getUserData() {
    return $this->userData;
  }

  private function get(string $item, int $id) {

  }

  private function post(string $item, $unvalidatedData) {

  }

  private function put(string $item, int $id, $unvalidatedData) {


  }

  private function delete(string $item, int $id) {

  }

  private function authenticate() {
    if (!empty($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Bearer\s(.*\..*\..*)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
      $refreshJWT = $matches[1];
    } else {
      $this->response->AddResponse('error', 'Felformaterad authorization header.');
      header('WWW-Authenticate: Bearer');
      return false;
    }

    $secrets = Auth::getSecrets($this->response, $this->pdo);
    $returnArray = array('decoded' => false, 'jwt' => null, 'error' => '');
    foreach ($secrets as $secret) {
      $returnArray = Auth::validateJWT($refreshJWT, $secret['token']);
      if ($returnArray['decoded']) {
        $this->userData = $returnArray['jwt'];
        if ($returnArray['jwt']['client']['agent'] != $_SERVER['HTTP_USER_AGENT']) {
          $this->response->AddResponse('error', 'Webbläsaren har ändrats. Logga in igen.');
          header('WWW-Authenticate: Bearer');
          return false;
        }
        if (ENV_IP_LOCK && $returnArray['jwt']['client']['ip'] != $_SERVER['REMOTE_ADDR']) {
          $this->response->AddResponse('error', 'IP-adress har ändrats. Logga in igen.');
          header('WWW-Authenticate: Bearer');
          return false;
        }
        $this->response->AddResponse('login', true);
        return true;
      }
    }
    if (!empty($returnArray['error'])) {
      $this->response->AddResponse('error', $returnArray['error']);
    } else {
      $this->response->AddResponse('error', 'Felformaterad authorization header.');
    }
    header('WWW-Authenticate: Bearer');
    return false;
  }


}
