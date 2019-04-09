<?php

namespace RekoBooking\tests\common;

use \PHPUnit\Framework\TestCase;
use RekoBooking\classes\Functions as F;


class FunctionsTest extends TestCase {

  /**
   * @test
   */
  public function validateIntFloatIsNotInt():void {
    $this->assertNull(F::validateInt(2.5),"Float should not validate as integer.");
  }
  /**
   * @test
   */
  public function validateIntFloatStringIsNotInt():void {
    $this->assertNull(F::validateInt("2.5"),"\"2.5\" should not validate as integer.");
  }
  /**
   * @test
   */
  public function validateIntIntStringIsInt():void {
    $this->assertEquals(gettype(F::validateInt("20")), "integer", "\"20\" should validate as integer");
  }
  /**
   * @test
   */
  public function validateIntNaNStringIsNotInt():void {
    $this->assertNull(F::validateInt("A"), "String should not validate as integer");
  }
  /**
   * @test
   */
  public function validateIntIntegerBelowRangeIsInvalid():void {
    $this->assertNull(F::validateInt(5, 6, 8), "Integer out of range should not validate.");
  }
  /**
   * @test
   */
  public function validateIntBelowAboveIsInvalid():void {
    $this->assertNull(F::validateInt(-4, -3, -1), "Integer out of range should not validate.");
  }
  /**
   * @test
   */
  public function validateIntFalseIsNotInt():void {
    $this->assertNull(F::validateInt(false), "false should not validate as integer.");
  }
  /**
   * @test
   */
  public function validateIntTrueIsNotInt():void {
    $this->assertNull(F::validateInt(true), "true should not validate as integer.");
  }
  /**
   * @test
   */
  public function validateIntTrueStringIsNotInt():void {
    $this->assertNull(F::validateInt("true"), "\"true\" should not validate as integer.");
  }
  /**
   * @test
   */
  public function validateIntOneIsInt():void {
    $this->assertEquals(gettype(F::validateInt(1)), "integer", "1 should validate as integer");
  }
  /**
   * @test
   */
  public function validateIntArrayIsNotInt():void {
    $this->assertNull(F::validateInt(array("test", "testing")), "Array should not validate as integer.");
  }
  /**
   * @test
   */
  public function validateIntIntIsInt():void {
    $this->assertEquals(gettype(F::validateInt(200)), "integer", "200 should validate as integer");
  }
  /**
   * @test
   */
  public function validateIntNegIntIsInt():void {
    $this->assertEquals(gettype(F::validateInt(-200)), "integer", "-200 should validate as integer");
  }
  /**
   * @test
   */
  public function validateIntIntInRangeIsInt():void {
    $this->assertEquals(gettype(F::validateInt(100, 50, 200)), "integer", "100 in range 50->200 should validate as integer");
  }
  /**
   * @test
   */
  public function validateIntNegativeIntInRangeIsInt():void {
    $this->assertEquals(gettype(F::validateInt(-100, -200, -50)), "integer", "-100 in range -200->-50 should validate as integer");
  }

  /**
   * @test
   */
  public function validateBoolToBitTrueIs1():void {
    $this->assertEquals(F::validateBoolToBit(true), 1, "true should return 1");
  }
  /**
   * @test
   */
  public function validateBoolToBitTrueStringIs1():void {
    $this->assertEquals(F::validateBoolToBit("True"), 1, "\"True\" should return 1");
  }
  /**
   * @test
   */
  public function validateBoolToBitFalseIs1():void {
    $this->assertEquals(F::validateBoolToBit(false), 0, "false should return 0");
  }
  /**
   * @test
   */
  public function validateBoolToBitFalseStringIs1():void {
    $this->assertEquals(F::validateBoolToBit("false"), 0, "\"false\" should return 0");
  }
  /**
   * @test
   */
  public function validateBoolToBit1Is1():void {
    $this->assertEquals(F::validateBoolToBit(1), 1, "1 should return 1");
  }
  /**
   * @test
   */
  public function validateBoolToBit0Is0():void {
    $this->assertEquals(F::validateBoolToBit(0), 0, "0 should return 0");
  }
  /**
   * @test
   */
  public function validateBoolToBit3IsNull():void {
    $this->assertNull(F::validateBoolToBit(3), "3 should return null");
  }
  /**
   * @test
   */
  public function validateBoolToBitStringIsNull():void {
    $this->assertNull(F::validateBoolToBit("55asd"), "String should return null");
  }
  /**
   * @test
   */
  public function validateBoolToBitArrayIsNull():void {
    $this->assertNull(F::validateBoolToBit(array()), "array should return null");
  }

  /**
   * @test
   */
  public function validateDateNormalValidates():void {
    $this->assertEquals(F::validateDate('2019-02-03'), '2019-02-03', "Valid YYYY-MM-DD should return");
  }
  /**
   * @test
   */
  public function validateDateOutOfRangeInvalidates():void {
    $this->assertEquals(F::validateDate('2019-13-03'), NULL, "2019-13-03 should return null");
  }
  /**
   * @test
   */
  public function validateDateArrayInvalidates():void {
    $this->assertEquals(F::validateDate(array()), NULL, "array should return null");
  }
  /**
   * @test
   */
  public function validateDateUnixTimeStampValid():void {
    $this->assertEquals(F::validateDate(1534349990), '2018-08-15', "1534349990 should return 2018-08-15");
  }

  /**
   * @test
   */
  public function sanatizeStringUnsafeStringisString():void {
    $this->assertEquals(F::sanatizeStringUnsafe("Hej<Hej></Hej>"), 'Hej<Hej></Hej>', "input should equal output");
  }
  /**
   * @test
   */
  public function sanatizeStringUnsafeArrayIsNull():void {
    $this->assertEquals(F::sanatizeStringUnsafe(array()), NULL, "array() should give null.");
  }

  /**
   * @test
   */
  public function validateZIPValidZIPOK():void {
    $this->assertEquals(F::validateZIP('152 15'), 15215, "Valid zip should validate.");
  }
  /**
   * @test
   */
  public function validateZIPValidToShort():void {
    $this->assertEquals(F::validateZIP('152 1'), NULL, "Too short ZIP should be rejected.");
  }
  /**
   * @test
   */
  public function validateZIPValidZIPToLong():void {
    $this->assertEquals(F::validateZIP('152 150'), NULL, "Too long ZIP should be rejected.");
  }
  /**
   * @test
   */
  public function validateZIPArrayIsRejected():void {
    $this->assertEquals(F::validateZIP(array()), NULL, "array() should give null.");
  }
    /**
   * @test
   */
  public function validatePhoneValidNonNumericIsStripped1():void {
    $this->assertEquals(F::validatePhone('+467 9122-54'), 467912254, "Non digits wasnt stripped properly.");
  }
  /**
   * @test
   */
  public function validatePhoneValidNonNumericIsStripped2():void {
    $this->assertEquals(F::validatePhone('88-888-88842'), 8888888842, "Non digits wasnt stripped properly.");
  }
  /**
   * @test
   */
  public function validatePhoneArrayIsRejected():void {
    $this->assertEquals(F::validatePhone(array()), NULL, "array() should give null.");
  }

  /**
   * @test
   */
  public function validateEmailAccepted():void {
    $this->assertEquals(F::validateEmail('test@test.com'), 'test@test.com', "test@test.com should validate as e-mail.");
  }
  /**
   * @test
   */
  public function validateEmailRejected():void {
    $this->assertNull(F::validateEmail("hej@hej@hej"), "invalid e-mail should be null.");
  }

  /**
   * @test
   */
  public function validateTimeAcceptedWithoutSeconds():void {
    $this->assertEquals(F::validateTime("14:12"), "14:12:00", "11:20 should be valid time.");
  }
  /**
   * @test
   */
  public function validateTimeAcceptedWithSeconds():void {
    $this->assertEquals(F::validateTime("14:12:00"), "14:12:00", "11:20 should be valid time.");
  }
  /**
   * @test
   */
  public function validateTimeStringRejected():void {
    $this->assertNull(F::validateTime("ABC"), "ABC should not be valid time.");
  }




}