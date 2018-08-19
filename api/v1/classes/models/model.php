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

  abstract public function get($_params);

  abstract public function post($_params);

  abstract public function put($_params);

  abstract public function delete($_params);


}