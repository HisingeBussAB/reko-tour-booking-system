<?php

namespace RekoBooking\tests\common\classes;

use \PHPUnit\Framework\TestCase;
use RekoBooking\classes\common\Tokens;
use RekoBooking\classes\common\Responder;
use RekoBooking\tests\PDOMock;


class TokenTest extends TestCase {

  /**
   * @test
   */
  public function tokenRequestRejects(): void {
    $pdoMock = new PDOMock;
    $response = new Responder;
    $tokens = new Tokens($response, $pdoMock);
    $this->assertFalse($tokens->issueToken('refresh'), "Refresh token without user is not rejected.");
    $this->assertFalse($tokens->issueToken('not a proper token type'), "Not a proper token type was not rejected.");
  }

  /**
   * @test
   */
  public function tokenRequestAccepted(): void {
    $pdoMock = new PDOMock;
    $response = new Responder;
    $tokens = new Tokens($response, $pdoMock);
    $this->assertTrue($tokens->issueToken('login'), "Blind login token creation returned false.");
    $this->assertTrue($tokens->issueToken('refresh', 'user'), "Refresh token creation returned false.");
  }

  /**
   * @test
   */
  public function tokenExistsInResponse(): void {
    $pdoMock = new PDOMock;
    $response = new Responder;
    $tokens = new Tokens($response, $pdoMock);
    $tokens->issueToken('login');
    $endResponse = json_decode($response->GetResponse(), true);
    $this->assertArrayHasKey('token', $endResponse, "There is no token key in response.");
    $this->assertEquals(gettype($endResponse['token']), "string", "There is no token string and responder.");
  }

  /**
   * @test
   */
  public function twoTokensAreNotEqual(): void {
    $pdoMock = new PDOMock;
    $response = new Responder;
    $tokens = new Tokens($response, $pdoMock);
    $tokens->issueToken('login');
    $firstResponse = json_decode($response->GetResponse(), true);
    $tokens->issueToken('login');
    $secondResponse = json_decode($response->GetResponse(), true);
    $this->assertNotEquals($firstResponse['token'], $secondResponse['token'], "Two identical tokens created.");
  }
}