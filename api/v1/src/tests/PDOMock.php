<?php

namespace RekoBooking\tests;
use RekoBooking\tests\PDOStatementMock;

class PDOMock extends \PDO {
  function __construct() {

  }

  public function prepare($statement, $options = NULL) {
    return new PDOStatementMock;
  }
}