<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

use \Firebase\JWT\JWT;

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
      return false;
    }
    
    try {
      $sql = "SELECT TOP 1 * FROM Auth WHERE username = :user ORDER BY AuthID;";
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

      $userid = $result['authid'];
      $now = time();
      $accessExp = $now + 3700; //+1 hour
      $refreshExp = $now + 7776000;//+90 days
      
      $refreshToken = '';
      //Generate Access Token
      $accessToken = self::generateJWT('access', $response, $pdo, $userid, $user, $accessExp, $now);

      //Generate Refresh Token
      $refreshToken = self::generateJWT('refresh', $response, $pdo, $userid, $user, $refreshExp, $now);


      //Clear HammerGuard for IP
      self::HammerGuard($response, $pdo, true);

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

  public static function refresh(Responder $response, \PDO $pdo) {
    if (!empty($_SERVER['HTTP_AUTHORIZATION']) && preg_match('/Bearer\s(.*\..*\..*)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
      $refreshJWT = $matches[1];
    } else {
      $response->AddResponse('error', 'Felformaterad authorization header.');
      return false;
    }

    $unvalidatedData = json_decode(trim(file_get_contents('php://input')), true);

    if (empty($unvalidatedData['user'])) {
      $response->AddResponse('error', 'Användarnamn måste anges (i JSON body user:) för att göra en refresh begäran.');
      return false;
    }
    
    $user = trim(filter_var($unvalidatedData['user'], FILTER_SANITIZE_STRING));
      
    // Get the users refresh JWT secret.
    try {
      $sql = "SELECT TOP 1 token FROM Tokens WHERE username = :user AND tokentype = 'refreshsecret' ORDER BY created DESC;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
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
      $sql = "SELECT TOP 1 AuthID FROM Auth WHERE username = :user ORDER BY AuthID;";
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
      if ($result['authid'] != $decoded['jwt']['sub']) {
        $response->AddResponse('error', 'Refresh token tillhör inte den här användaren.');
        return false;
      }

      $userid = $result['authid'];
      $now = time();
      $accessExp = $now + 3700; //+1 hour
      $refreshExp = $now + 7776000;//+90 days
      
      $refreshToken = '';
      //Generate Access Token
      $accessToken = self::generateJWT('access', $response, $pdo, $userid, $user, $accessExp, $now);

      //Generate Refresh Token
      $refreshToken = self::generateJWT('refresh', $response, $pdo, $userid, $user, $refreshExp, $now);


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
    // Just use the refresh method but dont send the new tokens for now
    if (self::refresh($response, $pdo)) {
      $response->AddResponse('login', false);
      $response->AddResponse('login', true);
      $response->AddResponse('response', 'Du är utloggad');
      $response->AddResponse('access', '');
      $response->AddResponse('refresh', '');
      $response->AddResponse('servertime', time());
      return true;
    } else {
      $response->AddResponse('error', 'Kunde inte utföra utloggning. Sannolikt är du inte inloggad');
      return false;
    }
    
  }

  private static function HammerGuard(Responder $response, \PDO $pdo, bool $reset) {

    $hashedIP = '';
    $now = time();
    $limit = $now - 3600; //1 hour ago
    $attemptsAllowed = 45;
    
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $hashedIP = sha1($_SERVER['HTTP_X_FORWARDED_FOR'] . ENV_GENERIC_SALT);
    } else {
      $hashedIP = sha1($_SERVER['REMOTE_ADDR'] . ENV_GENERIC_SALT);
    }

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
    try {
      $sql = "SELECT token FROM Tokens WHERE tokentype = 'jwtsecret' ORDER BY created DESC;";
      $sth = $pdo->prepare($sql);
      $sth->execute(); 
      $result = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }
    if (count($result) < 1) {
      $response->AddResponse('error', 'Databasen är korruperad, det hittades ingen nyckel att kyptera din token med.');
      $response->Exit(500);
    }
    return $result;
  } 

private static function generateJWT(string $type, Responder $response, \PDO $pdo, int $userid, string $user, int $exp, int $now) {

  $token = array(
    "iss"   => ENV_DOMAIN,
    "aud"   => ENV_DOMAIN,
    "sub"   => $userid,
    "iat"   => $now,
    "nbf"   => $now - 10,
    "exp"   => $exp,
    "client" => array(
      "agent" => $_SERVER['HTTP_USER_AGENT'],
      "ip"    => $_SERVER['REMOTE_ADDR'],
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

    //Clear user's saved refresh secrets
    try {
      $sql = "DELETE FROM Tokens WHERE tokentype = 'refreshsecret' AND username = :user;";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
      $sth->execute(); 
    } catch(\PDOException $e) {
      $response->DBError($e, __CLASS__, $sql);
      $response->Exit(500);
    }
    //Save refresh secret
    try {
      $sql = "INSERT INTO Tokens (Token, TokenType, Created, username) VALUES (:token, 'refreshsecret', :created, :user);";
      $sth = $pdo->prepare($sql);
      $sth->bindParam(':token', $refreshSecret, \PDO::PARAM_STR);
      $sth->bindParam(':created', $now, \PDO::PARAM_INT);
      $sth->bindParam(':user', $user, \PDO::PARAM_STR);
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

}

   