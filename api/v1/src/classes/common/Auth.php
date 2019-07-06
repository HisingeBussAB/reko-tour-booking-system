<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

use \Firebase\JWT\JWT;
use RekoBooking\classes\common\Maintenance;

final class Auth {

  public static function login(Responder $response, \PDO $pdo): bool {
    if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
      $response->AddResponse('error', 'Både användarnamn och lösenord måste anges.');
      return false;
    }

    $user = trim(filter_var($_SERVER['PHP_AUTH_USER'], FILTER_SANITIZE_STRING));

    if ($user == "AutoUser" && !ENV_AUTOUSER) {
      $response->AddResponse('error', 'AutoUser är deaktiverad.');
      return false;
    }

    if (!self::HammerGuard($response, $pdo, false)) {
      $response->AddResponse('error', 'För många inloggningsförsök. Prova igen lite senare.');
      header('WWW-Authenticate: Basic');
      $response->LogNotice('HammerGuard activated for: ' . ENV_REMOTE_ADDR, __CLASS__);
      $response->Exit(429);
      return false;
    }
    
    try {
      $sql = "SELECT * FROM Auth WHERE user = :user ORDER BY id LIMIT 1;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }

    if (!$result) {
      $response->AddResponse('error', 'Användarnamnet eller lösenordet är felaktigt.');
      return false;
    }

    if (password_verify(trim($_SERVER['PHP_AUTH_PW']) . AUTH_PWD_PEPPER, $result['pwd'])) {

      $userid = $result['id'];
      $now = time();
      $accessExp = $now + 3700; //+1 hour
      $refreshExp = $now + 2592000;//+30 days
      
      //Generate JWT secret for login
      $session = self::getSession();
      if (!Maintenance::insertNewSecret($response, $pdo, md5($_SERVER['HTTP_USER_AGENT']))) {
        $response->LogError('Kunde inte skapa ny JWT nyckel, kritiskt databasfel!', __CLASS__);
        $response->AddResponse('response', 'Kunde inte skapa ny JWT nyckel, kritiskt databasfel!');
        $response->AddResponse('error', 'Kunde inte skapa ny JWT nyckel, kritiskt databasfel!');
      };


      $accessToken = '';
      $refreshToken = '';
      //Generate Access Token
      $accessToken = self::generateJWT('access', $response, $pdo, $userid, $user, $accessExp, $now);

      //Generate Refresh Token
      $refreshToken = self::generateJWT('refresh', $response, $pdo, $userid, $user, $refreshExp, $now);


      //Clear HammerGuard for IP
      self::HammerGuard($response, $pdo, true);

      //Log
      $response->LogNotice('User logged in succefully: ' . $user, __CLASS__);

      //Write login status and tokens to response and return true
      $response->AddResponse('login', true);
      $response->AddResponse('response', 'Tokens skapade och skickade. Inloggning lyckad!');
      $response->AddResponse('access', array('token' => $accessToken, 'expires' => $accessExp));
      $response->AddResponse('refresh', array('token' => $refreshToken, 'expires' => $refreshExp));
      $response->AddResponse('servertime', time());
      
      return true;
    } 
    
    $response->AddResponse('error', 'Användarnamn eller lösenord felaktigt.');
    return false;
    
    
  }

  private static function getUser(Responder $response, \PDO $pdo) {
    $session = self::getSession();
    $unvalidatedData = json_decode(trim(file_get_contents('php://input')), true);
    if (empty($unvalidatedData['user'])) {
      $response->AddResponse('error', 'Användarnamn måste anges (i JSON body user:) för att göra denna begäran.');
      return false;
    }
    $user = trim(filter_var($unvalidatedData['user'], FILTER_SANITIZE_STRING));
      
    // Get the users refresh JWT secret. Also verifies the user is logged in with a matching session.
    try {
      $sql = "SELECT token FROM Tokens WHERE username = :user AND tokentype = 'refreshsecret' AND session = :session ORDER BY created DESC LIMIT 1;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->bindParam(':session', $session, \PDO::PARAM_STR);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }

    if (!$result) {
      $response->AddResponse('error', 'Det finns ingen nyckel på servern som motsvarar denna förfrågan.');
      return false;
    }
    return $user;
  }

  public static function refresh(Responder $response, \PDO $pdo) {
    $session = self::getSession();
    if (!empty($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Bearer\s(.*\..*\..*)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
      $refreshJWT = $matches[1];
    } else {
      $response->AddResponse('error', 'Felformaterad authorization header.');
      return false;
    }
   
    $user = self::getUser($response, $pdo);
    try {
      $sql = "SELECT token FROM Tokens WHERE username = :user AND tokentype = 'refreshsecret' AND session = :session ORDER BY created DESC LIMIT 1;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->bindParam(':session', $session, \PDO::PARAM_STR);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }

    if (!$result) {
      $response->AddResponse('error', 'Det finns ingen nyckel på servern som motsvarar denna förfrågan.');
      return false;
    }
    $secret = $result['token'];
    $decoded = self::validateJWT($refreshJWT, $secret);

    //We should validate the user still exist before issuing new credentials

    try {
      $sql = "SELECT id FROM Auth WHERE user = :user ORDER BY id LIMIT 1;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->execute(); 
      $result = $sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }

    if (!$result) {
      $response->AddResponse('error', 'Användarnamnet hittades inte.');
      return false;
    }


    if ($decoded['decoded']) {
      if ($result['id'] != $decoded['jwt']['sub']) {
        $response->AddResponse('error', 'Refresh token tillhör inte den här användaren.');
        return false;
      }

      if ($result['id'] != $decoded['jwt']['sub']) {
        $response->AddResponse('error', 'Refresh token tillhör inte den här användaren.');
        return false;
      }

      $userid = $result['id'];
      $now = time();
      $accessExp = $now + 3700; //+1 hour
      $refreshExp = $now + 7776000;//+90 days
      
      $refreshToken = '';
      //Generate Access Token
      $accessToken = self::generateJWT('access', $response, $pdo, $userid, $user, $accessExp, $now);

      //Generate Refresh Token
      $refreshToken = self::generateJWT('refresh', $response, $pdo, $userid, $user, $refreshExp, $now);

      //Log
      $response->LogNotice('User refreshed in succefully: ' . $user, __CLASS__);
      //Write login status and tokens to response and return true
      $response->AddResponse('login', true);
      $response->AddResponse('response', 'Nya tokens skapade och skickade. Inloggning lyckad!');
      $response->AddResponse('access', array('token' => $accessToken, 'expires' => $accessExp));
      $response->AddResponse('refresh', array('token' => $refreshToken, 'expires' => $refreshExp));
      $response->AddResponse('servertime', time());
      
      return true;

    }
    $response->AddResponse('error', 'Kunde inte uppdatera din inloggning. ' . $decoded['error']);
    return false;
    
    
  }

  public static function revoke(Responder $response, \PDO $pdo) {
    // Logout session
    $session = self::getSession();
    $user = self::getUser($response, $pdo);
    self::refresh($response, $pdo);
    try {
      $sql = "DELETE FROM Tokens WHERE username = :user AND session = :session;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->bindParam(':session', $session, \PDO::PARAM_STR);
      $sth->execute(); 
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }
      $response->AddResponse('login', false);
      $response->AddResponse('login', true);
      $response->AddResponse('response', 'Du är utloggad');
      $response->AddResponse('access', '');
      $response->AddResponse('refresh', '');
      $response->AddResponse('servertime', time());
      return true;   
  }

  public static function revokeall(Responder $response, \PDO $pdo) {
    // Logout all users sessions
    $user = self::getUser($response, $pdo);
    self::refresh($response, $pdo);
    try {
      $sql = "DELETE FROM Tokens WHERE username = :user;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->execute(); 
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }
      $response->AddResponse('login', false);
      $response->AddResponse('login', true);
      $response->AddResponse('response', 'Du är utloggad från alla sessioner.');
      $response->AddResponse('access', '');
      $response->AddResponse('refresh', '');
      $response->AddResponse('servertime', time());
      return true;   
  }

  private static function HammerGuard(Responder $response, \PDO $pdo, bool $reset) {

    $hashedIP = '';
    $now = time();
    $limit = $now - 3600; //1 hour ago
    $attemptsAllowed = 45;
    
    $hashedIP = sha1(ENV_REMOTE_ADDR . ENV_GENERIC_SALT);
   
    if ($reset) {
      try {
        $sql = "DELETE FROM HammerGuard WHERE iphash = :ip";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':ip', $hashedIP, \PDO::PARAM_STR);
        $sth->execute(); 
      } catch (\Exception $e) {
        $response->LogError("HammerGuard reset failure ignored: " . $e->getMessage(), __CLASS__);
      }
      return true;
    }

    try {
      $sql = "DELETE FROM HammerGuard WHERE created < :lim";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':lim', $limit, \PDO::PARAM_INT);
      $sth->execute(); 
    } catch (\Exception $e) {
      $response->LogError("HammerGuard clear old failure ignored: " . $e->getMessage(), __CLASS__);
    }

    try {
      $sql = "SELECT iphash FROM HammerGuard WHERE iphash = :ip AND created > :lim";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':ip', $hashedIP, \PDO::PARAM_STR);
      $sth->bindParam(':lim', $limit, \PDO::PARAM_INT);
      $sth->execute(); 
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
      $response->LogError("HammerGuard check failure ignored: " . $e->getMessage(), __CLASS__);
    }

    
    if (count($result) > $attemptsAllowed) {
      return false;
    } else {
      try {
        $sql = "INSERT INTO HammerGuard (iphash, created) VALUES (:ip, :created)";
        $sth = $pdo->prepare($sql);
        $sth->bindParam(':ip', $hashedIP, \PDO::PARAM_STR);
        $sth->bindParam(':created', $now, \PDO::PARAM_INT);
        $sth->execute(); 
      } catch (\Exception $e) {
        $response->LogError("HammerGuard insert failure ignored: " . $e->getMessage(), __CLASS__);
      }
      return true;
    }
    

  }

  public static function getSecrets(Responder $response, \PDO $pdo) {
    $session = md5($_SERVER['HTTP_USER_AGENT']);
    try {
      $sql = "SELECT token FROM Tokens WHERE tokentype = 'jwtsecret' AND session = :session ORDER BY created DESC;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':session', $session, \PDO::PARAM_STR);
      $sth->execute(); 
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }
    if (count($result) < 1) {
      $response->AddResponse('error', 'Det hittades ingen nyckel att (av)kyptera din token med. Prova logga in på nytt.');
      $response->Exit(500);
    }
    return $result;
  } 

private static function generateJWT(string $type, Responder $response, \PDO $pdo, int $userid, string $user, int $exp, int $now) {
  $session = self::getSession();
  $token = array(
    "iss"   => ENV_DOMAIN,
    "aud"   => ENV_DOMAIN,
    "sub"   => $userid,
    "iat"   => $now,
    "nbf"   => $now - 10,
    "exp"   => $exp,
    "client" => array(
      "agent" => $_SERVER['HTTP_USER_AGENT'],
      "ip"    => ENV_REMOTE_ADDR,
    ),
    "jti"   => bin2hex(random_bytes(6)) //Not used, only adds some entropy
  );
  if ($type == "access") {
    $secrets = self::getSecrets($response, $pdo);
    try {
      $accessToken = JWT::encode($token, $secrets[0]['token'] . AUTH_JWT_SECRET_PEPPER, 'HS512');
    } catch (\Exception $e) {
      $response->AddResponse('error', 'Kunde inte kryptera accesstoken.');
      $response->LogError($e->getMessage(), __CLASS__);
      $response->Exit(500);
    }
    return $accessToken;
  } else if ($type == "refresh") {
    $refreshSecret = bin2hex(random_bytes(24));
    try {
      $refreshToken = JWT::encode($token, $refreshSecret . AUTH_JWT_SECRET_PEPPER, 'HS512');
      
    } catch (\Exception $e) {
      $response->AddResponse('error', 'Kunde inte kryptera refreshtoken.');
      $response->LogError($e->getMessage(), __CLASS__);
      $response->Exit(500);
    }

    //Clear user's saved refresh secrets for session
    try {
      $sql = "DELETE FROM Tokens WHERE tokentype = 'refreshsecret' AND username = :user AND session = :session;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->bindParam(':session', $session, \PDO::PARAM_STR);
      $sth->execute(); 
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }
    //Save refresh secret
    try {
      $sql = "INSERT INTO Tokens (Token, TokenType, Created, username, session) VALUES (:token, 'refreshsecret', :created, :user, :session);";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':token', $refreshSecret, \PDO::PARAM_STR);
      $sth->bindParam(':created', $now, \PDO::PARAM_INT);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->bindParam(':session', $session, \PDO::PARAM_STR);
      $sth->execute(); 
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }

    return $refreshToken;
  } else {
    throw new \Exception('$type can only be access or refresh.');
    return false;
  }
  }

  public static function validateJWT(string $jwt, string $secret): array {
    $returnArray = array('decoded' => false, 'jwt' => null, 'error' => '');

    try {
      JWT::$leeway = 45;
      $decodedJWT = (array)JWT::decode($jwt, $secret . AUTH_JWT_SECRET_PEPPER, array('HS512'));
      $decodedJWT['client'] = (array)$decodedJWT['client'];

    } catch (\Firebase\JWT\SignatureInvalidException $e) {
      if (ENV_DEBUG_MODE) {
        $returnArray['error'] = 'Kunde inte öppna token: ' . $e->getMessage();
      } else {
        $returnArray['error'] = 'Kunde inte öppna token.';
      }
      return $returnArray;
    } catch (\Firebase\JWT\ExpiredException $e) {
      if (ENV_DEBUG_MODE) {
        $returnArray['error'] = 'Token är för gammal: ' . $e->getMessage();
      } else {
        $returnArray['error'] = 'Token är för gammal.';
      }
      return $returnArray;
    } catch (\Exception $e) {
      if (ENV_DEBUG_MODE) {
        $returnArray['error'] = 'Kunde verfiera token: ' . $e->getMessage();
      } else {
        $returnArray['error'] = 'Kunde verifiera token.';
      }
      return $returnArray;
    }

    $returnArray['decoded'] = true;
    $returnArray['jwt'] = $decodedJWT;
    return $returnArray;

  }

  private static function getSession() {
    $cf = empty($_SERVER['HTTP_CF_CONNECTING_IP']) ? 'NOT-CF' : $_SERVER['HTTP_CF_CONNECTING_IP'];
    $addr = empty(ENV_REMOTE_ADDR) ? $_SERVER['REMOTE_ADDR'] : ENV_REMOTE_ADDR;
    $clientlang = empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? 'NO' : $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    return hash('sha256', $cf . $addr . $_SERVER['HTTP_USER_AGENT'] . $clientlang . $_SERVER['SERVER_NAME']);
  }

}

   