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

  /**
   * Validation procedure for all acceptable keys in dot notation.
   * Note ValidateInt uses PHP_MAX_INT PHP_MIN_INT this equals an SQL bigint when runnoing x64 PHP
   * This validation corresponds to the database documentation
   * 
   * Strings are not sanitized just casted and RAW filtered.
   * We need the actual data in the database, just htmlspecialchars on output and prepare statements.
   * 
   * This mastodont switch should be split down using action cases.
   * But the database does not have that many tabled or ambigious keys so this should be sufficient until there is more time.
   * 
   */
  public function validateItem($key, $value) {
    $newValue = NULL;
    switch (strtolower($key)) {
      
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
        if (strlen($newValue) > 200) {$newValue = NULL; $this->response->AddResponse('error', 'Reservationens benämning är för lång. Max 200 tecken.');}
        break;
     
      //KUND
      case "fornamn":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Förnamn måste anges.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Förnamnet är för lång. Max 100 tecken.');}
        break;
      
      case "efternamn":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Efternamn måste anges.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Efternamnet är för lång. Max 100 tecken.');}
        break;

      case "gatuadress":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Gatuadress måste anges.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Gatuadressen är för lång. Max 100 tecken.');}
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
        if (strlen($newValue) > 60) {$newValue = NULL; $this->response->AddResponse('error', 'E-poste är för lång. Max 60 tecken.');}
        break;

      case "personnr":
        $newValue = $this->validatePersonalNumber($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Personnummer måste anges med 6 eller 10 siffror.');}
        if (strlen($newValue) != 10 && strlen($newValue) != 6) {$newValue = NULL; $this->response->AddResponse('error', 'Personnummer måste anges med 6 eller 10 siffror.');}
        break;

      case "datum":
        $newValue = $this->validateDate($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Datum måste vara ett datum, helst i format YYYY-MM-DD.');}
        break;

      //BOKNING_KUND

      case "onskemal":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Önskemål måste anges.');}
        if (strlen($newValue) > 360) {$newValue = NULL; $this->response->AddResponse('error', 'Önskemål är för långt. Max 360 tecken.');}
        break;

      case "prisjustering":
        $newValue = $this->validateInt($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Prisjustering måste vara ett heltal.');}
        break;

      case "avresa":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Avreseplats måste anges.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Avreseplats är för lång. Max 100 tecken.');}
        break;

      case "avresatid":
        $newValue = $this->validateTime($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Avresetid skall anges HH:mm tex 13:00.');}
        break;

      case "avbskyddbetalt":
        $newValue = $this->validateBoolToBit($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Avbeställningskydd betalt skall anges som true eller false.');}
        break;

      //BOENDE

      case "boende.boendenamn":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Rumstyp måste anges.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Rumstyp är för långt. Max 100 tecken.');}
        break;

      case "boende.pris":
        $newValue = $this->validateInt($value, -2147483648, 2147483647);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Priset måste anges som ett heltal och får inte vara orimligt stort.');}
        break;

      case "boende.antaltillg":
        $newValue = $this->validateInt($value, -2147483648, 2147483647);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Tilläggspriset måste anges som ett heltal och får inte vara orimligt stort.');}
        break;

      //RESA

      case "resa":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Resan måste ha ett namn.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Namnet på resan är för långt. Max 100 tecken.');}
        break;

      case "avbskyddpris":
        $newValue = $this->validateInt($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Priset för avbeställningsskydd måste anges som ett heltal.');}
        break;

      case "anmavgpris":
        $newValue = $this->validateInt($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Anmälningsavgift måste anges som ett heltal.');}
        break;

      //KATEGORI

      case "kategori":
        $newValue = $this->sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Kategorin måste ha en benämning.');}
        if (strlen($newValue) > 60) {$newValue = NULL; $this->response->AddResponse('error', 'Kategorinamnet är för långt. Max 60 tecken.');}
        break;

      case "aktiv":
        $newValue = $this->validateBoolToBit($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Aktiv måste anges som true eller false.');}
        break;

      //BETALNING
        //TODO
      case "anmavgpris":
        $newValue = $this->validateInt($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Anmälningsavgift måste anges som ett heltal.');}
        break;

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
