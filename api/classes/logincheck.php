<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes;

use RekoBooking\classes\DB;
use RekoBooking\classes\DBError;
use \Firebase\JWT\JWT;

class LoginCheck {

  public static function JWTExceptionHandler($error='JWT DECYPTION ERROR', $message='DEBUG') {
    header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
    $headers = ob_get_clean();
    echo $headers;
    if (DEBUG_MODE) {
      $a = array(
        'response' => 'JWT decryption error: ' . $error,
        'login' => false,
        'saved' => false);
    } else {
      $a = array(
        'response' => 'Du verkar inte vara inloggad. ' . $message,
        'login' => false,
        'saved' => false);
    }
    echo json_encode($a);
    die();
  }

  public static function isLoggedin($response) {

    $jsonData = json_decode(trim(file_get_contents('php://input')), true);
    if ($jsonData['apitoken'] === API_TOKEN ) {

      $JWTpassed = true;
      $JWTerror = 'unknown';

      if (REQUIRE_JWT) {

        if (empty($jsonData['user']) || empty($jsonData['jwt'])) {
          $JWTpassed = false;
          header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
          $headers = ob_get_clean();
          echo $headers;
          $response->AddResponse('login', false);
          $response->AddResponse('saved', false);
          $response->AddResponse('response', 'Du verkar inte vara inloggad eller så har systemet varit inaktivt för länge.');
          echo $response->GetResponse();
          die();
        }

        $user = trim(filter_var($jsonData['user'], FILTER_SANITIZE_STRING));
        $jwt  = trim(filter_var($jsonData['jwt'], FILTER_SANITIZE_STRING));

        $pdo = DB::get();

        try {
          $sql = "SELECT TOP 1 Token FROM Tokens WHERE TokenType = 'jwt' AND username = :user ORDER BY Created DESC;";
          $sth = $pdo->prepare($sql);
          $sth->bindParam(':user', $user, \PDO::PARAM_STR);
          $sth->execute(); 
          $result = $sth->fetch(\PDO::FETCH_ASSOC);
        } catch(\PDOException $e) {
          $JWTpassed = false;
          DBError::showError($e, __CLASS__, $sql);
          die();
        }

        if (!$result) {
          $JWTpassed = false;
          $result = array('token' => 'broken'); //Let it fail in decryption
        }

        try {
          JWT::$leeway = 45;
          $decodedJWT = (array)JWT::decode($jwt, $result['token'], array('HS512'));
          $decodedJWT['jti'] = (array)$decodedJWT['jti'];
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
          $JWTpassed = false;
          self::JWTExceptionHandler($e->getMessage(), 'Kunde öppna login token.');
        } catch (\Firebase\JWT\ExpiredException $e) {
          $JWTpassed = false;
          self::JWTExceptionHandler($e->getMessage(), 'Systemet har varit inaktivt för länge.');
        } catch (\Exception $e) {
          $JWTpassed = false;
          self::JWTExceptionHandler($e->getMessage(), 'Kunde inte verifiera login token.');
        }
        
        
        if ($decodedJWT['iss'] != DOMAIN || $decodedJWT['aud'] != DOMAIN) {
          $JWTpassed = false;
          $JWTerror = 'issuer';
        }

        if ($decodedJWT['sub'] != $user) {
          $JWTpassed = false;
          $JWTerror = 'user';
        }

        if ($decodedJWT['jti']['mark'] != JWT_WATERMARK) {
          $JWTpassed = false;
          $JWTerror = 'mark';
        }

        if ($decodedJWT['jti']['agent'] != $_SERVER['HTTP_USER_AGENT']) {
          $JWTpassed = false;
          $JWTerror = 'agent';
        }

        if (IP_LOCK) {
          if ($decodedJWT['jti']['ip'] != $_SERVER['REMOTE_ADDR']) {
            $JWTpassed = false;
            $JWTerror = 'ip';
          }
        }

      }

      $message = "Du verkar inte vara inloggad.";
      if ($JWTpassed != true) {
        switch($JWTerror) {
          case 'issuer':
            $message = $message . " Fel utgivare av inloggningstoken.";
            break;
          case 'user':
            $message = $message . " Fel användare i inloggningstoken.";
            break;
          case 'mark':
            $message = $message . " Fel information i inloggningstoken.";
            break;
          case 'agent':
            $message = $message . " Browsern har ändrats du måste logga in igen.";
            break;
          case 'ip':
            $message = $message . " IP adressen har ändrats du måste logga in igen.";
            break;
        }
        header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
        $headers = ob_get_clean();
        echo $headers;
        $a = array(
          'response' => $message,
          'login' => false,
          'saved' => false);
        echo json_encode($a);
        die();
      } else {
        $response->AddResponse('login', true);
        return true;
      }
    
    
    } else {
      header( $_SERVER["SERVER_PROTOCOL"] . ' 401 Unauthorized');
      $headers = ob_get_clean();
      echo $headers;
      $a = array('response' => 'Fel APItoken sänd med begäran. Inte tillåten.');
      echo json_encode($a);
      die();
    }
  }
}