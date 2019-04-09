<?php

namespace RekoBooking\tests\common\classes;

use \PHPUnit\Framework\TestCase;
use RekoBooking\classes\common\Validate;
use RekoBooking\classes\common\Responder;



class ValidateTest extends TestCase {

    /**
   * @test
   */
  public function validateDataInvalidDataShouldNotValidate(): void {
    $r = new Responder;
    $v = new Validate($r);
    $testArray = array("hello" => "jassad", "I am not a valid key" => "testvalue", "Another invalid key" => array(1,2,3));
    $this->assertFalse($v->validateData($testArray), "Invalid keys must not validate.");
    $testArray = false;
    $this->assertFalse($v->validateData($testArray), "False should not validate.");
    $testArray = NULL;
    $this->assertFalse($v->validateData($testArray), "Null should not validate.");
    $testArray = "test";
    $this->assertFalse($v->validateData($testArray), "String and not array should not validate.");
  }

  
 
}