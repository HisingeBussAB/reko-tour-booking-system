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
use RekoBooking\classes\Functions;
use RekoBooking\classes\actions\Getter;
use RekoBooking\classes\actions\Putter;
use RekoBooking\classes\actions\Poster;
use RekoBooking\classes\actions\Deleter;


class Controller {

  private $response;
  private $pdo;
  private $userData;
  private $Validator;

  public function __construct() {
    $this->response = new Responder;
    $this->response->AddResponse('saved',     false);
    $this->response->AddResponse('login',     false);
    $this->response->AddResponse('success',   false);
    $this->response->AddResponse('validated', false);
    $this->response->AddResponse('response',  'Ingen uppgift utförd.');
    $this->pdo = DB::get($this->response);
    if ($this->pdo == false) {
      $this->response->AddResponse('response',  'Kritiskt fel. Databasanslutning misslyckades.');
      $this->response->Exit(500);
    }
    $this->Validator = new Validator($response);
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
      if ($id !== -1) {
        $id = Functions::validateInt($id, 0);
        if (is_null($id)) {
          $this->response->AddResponse('error', 'Ogiltigt mål ID. Måste vara ett positivt heltal.');
          $this->response->Exit(404);
        }
      }

      if (gettype($unvalidatedData) !== "array") {
        if ($_SERVER['REQUEST_METHOD'] == "POST" || $_SERVER['REQUEST_METHOD'] == "PUT") {
          $this->response->AddResponse('error', 'Ogiltig indata. Ingen indata eller inte korrekt formatterad JSON.');
          $this->response->Exit(400);
        } else {
          $unvalidatedData = array();
        }
      }
      $unvalidatedData['id'] = $id;
      switch($_SERVER['REQUEST_METHOD'])
      {
        case "GET":
          $this->modelInvoker($item, 'GET', $unvalidatedData);
        break;
      
        case "POST":
          $this->modelInvoker($item, 'POST', $unvalidatedData);
          if (!ENV_CRON_JOB) { Maintenance::refreshSecrets($this->response, $this->pdo); }
        break;

        case "PUT":
          $this->modelInvoker($item, 'PUT', $id, $unvalidatedData);
          if (!ENV_CRON_JOB) { Maintenance::refreshSecrets($this->response, $this->pdo); }
          
        break;

        case "DELETE":
          $this->modelInvoker($item, 'DELETE', $unvalidatedData);
          if (!ENV_CRON_JOB) { Maintenance::refreshSecrets($this->response, $this->pdo); }
        break;
      }
    }
    return $this->response->GetResponse();
  
  }

  private function modelInvoker(string $item, string $function, $unvalidatedData) {
    $model = "Try";
    $function = 'Go';
    try {
      $REQUEST = new $model;
      $data = $REQUEST->$function();
      $this->response->AddResponse('response', $data);
    } catch (\Exception $e) {
      $this->response->AddResponse('response', 'Okänt serverfel. Kunde inte utföra årgärden. Kontakta tekniker om det här felet kvarstår.');
      $this->response->LogError('Possible routing to unsupported model: ' . $e->getMessage(), __CLASS__);
      $this->response->Exit(500);
    }
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

  private function authenticate() {
    if (!empty($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Bearer\s(.*\..*\..*)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
      $refreshJWT = $matches[1];
    } else {
      $this->response->AddResponse('error', 'Felformaterad authorization header.');
      header('WWW-Authenticate: Bearer');
      return false;
    }

    $secrets = Auth::getSecrets($this->response, $this->pdo);
    $returnedArray = array('decoded' => false, 'jwt' => null, 'error' => '');
    foreach ($secrets as $secret) {
      $returnedArray = Auth::validateJWT($refreshJWT, $secret['token']);
      if ($returnedArray['decoded']) {
        $this->userData = $returedArray['jwt'];
        if ($returnedArray['jwt']['client']['agent'] != $_SERVER['HTTP_USER_AGENT']) {
          $this->response->AddResponse('error', 'Webbläsaren har ändrats. Logga in igen.');
          header('WWW-Authenticate: Bearer');
          return false;
        }
        if (ENV_IP_LOCK && $returnedArray['jwt']['client']['ip'] != $_SERVER['REMOTE_ADDR']) {
          $this->response->AddResponse('error', 'IP-adress har ändrats. Logga in igen.');
          header('WWW-Authenticate: Bearer');
          return false;
        }
        $this->response->AddResponse('login', true);
        return true;
      }
    }
    if (!empty($returnedArray['error'])) {
      $this->response->AddResponse('error', $returnedArray['error']);
    } else {
      $this->response->AddResponse('error', 'Felformaterad authorization header.');
    }
    header('WWW-Authenticate: Bearer');
    return false;
  }


}
