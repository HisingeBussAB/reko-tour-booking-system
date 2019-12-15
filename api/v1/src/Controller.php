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


class Controller {

  private $response;
  private $pdo;
  private $pdoweb;
  private $userData;

  public function __construct() {
    $this->response = new Responder;
    $this->response->AddResponse('login',     false);
    $this->response->AddResponse('success',   false);
    $this->response->AddResponse('response',  'Ingen uppgift utförd.');
    $this->pdo = DB::get($this->response);
    $this->pdoweb = DB::getweb($this->response);
    if ($this->pdo == false) {
      $this->response->AddResponse('response',  'Kritiskt fel. Databasanslutning misslyckades.');
      $this->response->Exit(500);
    }
    if ($this->pdoweb == false) {
      $this->response->AddResponse('response',  'Databasanslutning till webbokningar misslyckades.');
      $this->response->AddResponse('error',  'Databasanslutning till webbokningar misslyckades.');
    }
  }

  /**
   * Starts the controller checks the method and authentication and executes method
   * @param $item Item type the API query is about.
   * @param $id The database ID of the item. If -1 treat as entire collection requested.
   * @param $login Is login required for the requested operation. Can be used override login check for testing and certain actions.
   */
  public function start($item, $id = -1, $login = true) {
    $isAuthenticated = false;
    if ($login) {
      $isAuthenticated = $this->authenticate();
    }
    if (!$login || $isAuthenticated) {
      $unvalidatedData = json_decode(trim(file_get_contents('php://input')), true);
      if ($id != -1) {
        $id = Functions::validateInt($id, 0);
        if (is_null($id)) {
          $this->response->AddResponse('error', 'Ogiltigt mål Id. Måste vara ett positivt heltal.');
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
          $this->modelInvoker($item, 'get', $unvalidatedData);
        break;
      
        case "POST":
          $this->modelInvoker($item, 'post', $unvalidatedData);
          if (!ENV_CRON_JOB) { Maintenance::refreshSecrets($this->response, $this->pdo); }
        break;

        case "PUT":
          $this->modelInvoker($item, 'put', $unvalidatedData);
          if (!ENV_CRON_JOB) { Maintenance::refreshSecrets($this->response, $this->pdo); }
          
        break;

        case "DELETE":
          $this->modelInvoker($item, 'delete', $unvalidatedData);
          if (!ENV_CRON_JOB) { Maintenance::refreshSecrets($this->response, $this->pdo); }
        break;
      }
    }
    return $this->response->GetResponse();
  
  }

  private function modelInvoker(string $model, string $function, array $unvalidatedData) {
    $rawmodel = $model;
    $model = 'RekoBooking\classes\models\\' . $model;
    if (class_exists($model)) {
      $REQUEST = NULL;
      if (substr($rawmodel, 0, 7) == 'Pending' ) {
        $REQUEST = new $model($this->response, $this->pdoweb);
      } else {
        $REQUEST = new $model($this->response, $this->pdo);
      }
      $data = $REQUEST->$function($unvalidatedData);
      if ($function == 'post' && $data != false) {
        $this->response->AddResponse('response', $data);
        $this->response->AddResponse('success', true);
        http_response_code(201);
      } 
      if ($function == 'get' && $data != false) {
        $this->response->AddResponse('response', $data);
        $this->response->AddResponse('success', true);
        http_response_code(200);
      }
      if (($function == 'delete' ||  $function == 'put') && $data != false) {
        $this->response->AddResponse('response', $data);
        $this->response->AddResponse('success', true);
        http_response_code(200);
      }
         
      if ($data == false) {
        $this->response->AddResponse('response', 'ERROR');
        http_response_code(501);
      }
      
    } else {
      $this->response->AddResponse('error', 'Okänt serverfel. Kunde inte utföra årgärden. Kontakta tekniker om det här felet kvarstår.');
      $this->response->LogError('Possible routing to unsupported model: ' . $model, __CLASS__);
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

          case "revokeall":
          if ( Auth::revokeall($this->response, $this->pdo )) {
            http_response_code(200);
          } else {
            header('WWW-Authenticate: Bearer');
            http_response_code(401);
          }
          break;
      }
      return $this->response->GetResponse();
  }

  public function Maintinance() {
    Maintenance::refreshSecrets($this->response, $this->pdo);
    return 'complete';
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
        $this->userData = $returnedArray['jwt'];
        if ($returnedArray['jwt']['client']['agent'] != $_SERVER['HTTP_USER_AGENT']) {
          $this->response->AddResponse('error', 'Webbläsaren har ändrats. Logga in igen.');
          header('WWW-Authenticate: Bearer');
          return false;
        }
        if (ENV_IP_LOCK && $returnedArray['jwt']['client']['ip'] != ENV_REMOTE_ADDR) {
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
