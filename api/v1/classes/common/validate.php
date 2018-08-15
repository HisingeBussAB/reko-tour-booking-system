<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

use RekoBooking\classes\Functions;

class Validate {

  private $response;

  public function __construct(Responder $_response) {
    $this->response = $_response;
  }

  public function validateData($data) {
    if (!is_array($data)) {
      $this->response->AddResponse('response', 'Felformaterad data skickad, kunde inte läsa JSON.');
      $this->response->AddResponse('error', 'Felformaterad data skickad, kunde inte läsa JSON.');
      return false;
    }
    try {
      $res = Functions::array_map_assoc_recursive([$this, 'validateItem'], $data);
    } catch (\UnexpectedValueException $e) {
      if (ENV_DEBUG_MODE) {  
        $this->response->AddResponse('response', 'Valideringsfel: ' . $e->getMessage());
      } else {
        $this->response->AddResponse('response', 'Valideringsfel på mottagen data. Begäran stoppad.');
      }
      return false;
    }
    return $res;
  }

  public function validateItem($key, $value) {
    $newValue = NULL;
    switch (strtolower($key)) {
      
      //TODO Max lenghts!!!
      //BOKNING
      case "bokningnr":
        $newValue = $this->validateInt($value, 0);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Bokningsnummer är i ogiltigt format. Ett positivt heltal måste anges.');}
        break;
      
      case "gruppbokning":
        $newValue = $this->validateBoolToBit($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Gruppbokning måste anges som true eller false.');}
        break;

      case "markulerad":
        $newValue = $this->validateBoolToBit($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Markulerad måste anges som true eller false.');}
        break;
      
      case "makuleraddatum":
        $newValue = $this->validateDate($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Markuleraddatum måste vara ett datum, helst i format YYYY-MM-DD.');}
        break;
      
      case "betaldatum1":
        $newValue = $this->validateDate($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Betaldatum1 måste vara ett datum, helst i format YYYY-MM-DD.');}
        break;
      
      case "betaldatum2":
        $newValue = $this->validateDate($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Betaldatum2 måste vara ett datum, helst i format YYYY-MM-DD.');}
        break;

      //RESERVATION
      case "reservation":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Reservationen måste ha ett namn.');}
        break;
     
      //KUND
      case "fornamn":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Förnamn måste anges.');}
        break;
      
      case "efternamn":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Efternamn måste anges.');}
        break;

      case "gatuadress":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Gatuadress måste anges.');}
        break;

        case "postnr":
        $newValue = $this->validateZIP($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Postnummer måste anges med fem siffor. För utländska addresser ange 0 och skriv postnummer tillsammans med staden.');}
        break;

        case "telefon":
        $newValue = $this->validatePhone($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Telefonnummer inte angett med tillräckligt många siffror.');}
        break;

        case "email":
        $newValue = $this->validateEmail($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'E-posten verkar inte vara en giltig e-post. example@example.com');}
        break;

        case "efternamn":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Efternamn måste anges.');}
        break;

      //BOKNING_KUND

      //BOENDE

      //RESA

      //KATEGORI

      //PROGRAMBEST


      //DEADLINE

      //KALKYL

      //KALKYL_KOSTNAD

      //KALKYL_INTAKT


    }

    if (is_null($newValue)) {
      throw new \UnexpectedValueException("$key => $value does not have a validation/sanatization rule.");
    }
    return $newValue;
  }

  

  
}
