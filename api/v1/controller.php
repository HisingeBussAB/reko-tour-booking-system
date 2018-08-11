<?php
/**
 * Rekå Resor Bokningssystem Main Controller
 * @author    Håkan Arnoldson
 */
namespace RekoBooking;

use RekoBooking\classes\common\Responder;
use RekoBooking\classes\common\DB;
use RekoBooking\classes\common\Tokens;
use \Firebase\JWT\JWT;

class Controller {

  private $response;
  private $pdo;
  private $tokens;

  function __construct() {
    $this->response = new Responder;
    $this->response->AddResponse('saved',     false);
    $this->response->AddResponse('login',     false);
    $this->response->AddResponse('success',   false);
    $this->response->AddResponse('response',  'Ingen uppgift utförd.');
    $this->pdo = DB::get($this->response);
    $this->tokens = new Tokens($this->response, $this->pdo);
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
        break;

        case "PUT":
          $unvalidatedData = json_decode(trim(file_get_contents('php://input')), true);
          $this->put($item, $id, $unvalidatedData);
        break;

        case "DELETE":
          $this->delete($item, $id);
        break;
      }
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

  public function doLogin() {
    $unvalidatedData = json_decode(trim(file_get_contents('php://input')), true);
    $success = $this->issueToken('login');
    return $this->response->GetResponse();
  }

  /**
   * Request a new token using common/Tokens
   * @param string $tokenType Type of the token
   */
  public function issueToken(string $tokenType) {
    if ($this->tokens->issueToken($tokenType)) {
      $this->response->AddResponse("response", "Token skapad!");
      $this->response->AddResponse("success", true);
      http_response_code(201); 
    } else {
      $this->response->AddResponse("response", "Kunde inte generera en token. Serverfel!");
      http_response_code(500); 
    }
    return $this->response->GetResponse();
  }

}
