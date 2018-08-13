<?php

namespace RekoBooking\tests\common\classes;

use \PHPUnit\Framework\TestCase;
use RekoBooking\classes\common\Auth;
use RekoBooking\classes\common\Responder;
use RekoBooking\tests\PDOMock;


class AuthTest extends TestCase {

  /**
   * @test
   */
  public function shortOrEmptyCredentialsAreRejected(): void {
    $pdoMock = new PDOMock;
    $response = new Responder;
    Auth::login($response, $pdoMock);
    
    

  }

 
}