<?php

namespace RekoBooking\classes\models;

use RekoBooking\classes\common\Responder;

abstract class Model {

  protected $response;
  protected $pdo;

  public function __construct(Responder $response, \PDO $pdo) {
    $this->response = $response;
    $this->pdo = $pdo;
  }

  abstract public function get(array $_params);

  abstract public function post(array $_params, bool $trash = false);

  abstract public function put(array $_params);

  abstract public function delete(array $_params);


}