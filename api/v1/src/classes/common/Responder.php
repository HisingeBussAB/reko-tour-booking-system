<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

use \Monolog\Logger;
use \Monolog\Handler\RotatingFileHandler;
use \Monolog\ErrorHandler;


class Responder {

  private $output;
  private $logger;

  function __construct() {
    $this->output = array();
    // create a log channel
    $this->logger = new Logger('response_logger');
    $this->logger->pushHandler(new RotatingFileHandler(ENV_LOG_PATH, 14, Logger::NOTICE));
    if (!ENV_DEBUG_MODE) {
      ErrorHandler::register($this->logger);
    }
  }

  public function AddResponse(string $key, $value) {
    $this->output[$key] = $value;
  }

  public function AddResponsePushToArray(string $key, array $array) {
    if (empty($this->output[$key]) || !is_array($this->output[$key])) {
      $this->output[$key] = array();
    }
    array_push($this->output[$key], $array);
  }

  public function GetResponse() {
    return json_encode($this->output);
  }

  public function DBError(\PDOException $e, string $class, string $sql='NO QUERY') {
    http_response_code(500);
    $this->logger->error('Databasfel från ' . $class . ': ' . $e->getMessage() . '. SQL: '. $sql);
    if (ENV_DEBUG_MODE) {
      $this->AddResponse('error', ('Databasfel från ' . $class . ': ' . $e->getMessage() . '. SQL: '. $sql));
    } else {
      $this->AddResponse('error', 'Databasfel. Kontakta administratör om felet kvarstår.');
    }
  }

  public function LogError($message, $class) {
    $this->logger->error('Fel i ' . $class . ': ' . $message);
  }

  public function LogNotice($message, $class) {
    $this->logger->notice('Event i ' . $class . ': ' . $message);
  }

  public function Exit($response_code) {
    http_response_code($response_code);
    echo $this->GetResponse();
    $content = ob_get_clean();
    echo $content;
    die();
  }

}


