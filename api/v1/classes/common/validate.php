<?php
/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */
namespace RekoBooking\classes\common;

use RekoBooking\classes\Functions;

class Validate {

  private $response;
  private $validated; //No exception because we want to return all failed validations and not just the first to the client

  public function __construct(Responder $_response) {
    $this->response = $_response;
    $this->validationErrors = array();

  }

  public function validateData($data) {
    if (!is_array($data)) {
      $this->response->AddResponse('response', 'Felformaterad data skickad, kunde inte läsa JSON.');
      $this->response->AddResponse('error', 'Felformaterad data skickad, kunde inte läsa JSON.');
      return false;
    }
    $res = Functions::array_map_assoc_recursive([$this, 'validateItem'], $data);
    if (!empty($this->validationErrors)) {
      if (ENV_DEBUG_MODE) {  
        $this->response->AddResponse('response', 'Valideringsfel');
        $this->response->AddResponsePushToArray('debugValidationErrors', $this->validationErrors);
      } else {
        $this->response->AddResponse('response', 'Valideringsfel på mottagen data. Begäran stoppad.');
      }
      return false;
    }
    $this->response->AddResponse('validated', true);
    return $res;
  }

  /**
   * Validation procedure for all acceptable keys in dot notation.
   * Note: ValidateInt uses PHP_MAX_INT PHP_MIN_INT this equals an SQL bigint when runnoing x64 PHP
   * This validation corresponds to the database documentation
   * 
   * Strings are not sanitized just casted and RAW filtered.
   * We need the actual data in the database - prepare statements and API should not be excplicilty trusted in front-end ie. do not dangerously set innerHTML..
   * 
   * This mastodont switch should be split down using actions and item cases.
   * But the database does not have that many tables or ambigious keys so this should be sufficient for now.
   * 
   * Will return a verbose response only for the last validation failure encountered, but a brief list of all failures as well.
   * 
   */
  public function validateItem($key, $value) {
    $newValue = NULL;
    switch (strtolower($key)) {
      
      //BOKNING
      case "bokningnr":
        $newValue = Functions::validateInt($value, 0);
        if (is_null($newValue)) {
          $this->response->AddResponse('error', 'Bokningsnummer är i ogiltigt format. Ett positivt heltal måste anges.');}
        break;
      
      case "gruppbokning":
        $newValue = Functions::validateBoolToBit($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Gruppbokning måste anges som true eller false.');}
        break;

      case "markulerad":
        $newValue = Functions::validateBoolToBit($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Markulerad måste anges som true eller false.');}
        break;
      
      case "makuleraddatum":
        $newValue = Functions::validateDate($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Markuleraddatum måste vara ett datum, helst i format YYYY-MM-DD.');}
        break;
      
      case "betaldatum1":
        $newValue = Functions::validateDate($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Betaldatum1 måste vara ett datum, helst i format YYYY-MM-DD.');}
        break;
      
      case "betaldatum2":
        $newValue = Functions::validateDate($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Betaldatum2 måste vara ett datum, helst i format YYYY-MM-DD.');}
        break;

      //RESERVATION
      case "reservation":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Reservationen måste ha ett namn.');}
        if (strlen($newValue) > 200) {$newValue = NULL; $this->response->AddResponse('error', 'Reservationens benämning är för lång. Max 200 tecken.');}
        break;
     
      //KUND
      case "fornamn":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Förnamn måste anges.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Förnamnet är för lång. Max 100 tecken.');}
        break;
      
      case "efternamn":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Efternamn måste anges.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Efternamnet är för lång. Max 100 tecken.');}
        break;

      case "gatuadress":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Gatuadress måste anges.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Gatuadressen är för lång. Max 100 tecken.');}
        break;

      case "postnr":
        $newValue = Functions::validateZIP($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Postnummer måste anges med fem siffor. För utländska addresser ange 0 och skriv postnummer tillsammans med staden.');}
        break;

      case "telefon":
        $newValue = Functions::validatePhone($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Telefonnummer inte angett med tillräckligt många siffror.');}
        if (strlen($newValue) > 25) {$newValue = NULL; $this->response->AddResponse('error', 'Telefonnummret är för lång. Max 25 tecken.');}
        break;

      case "email":
        $newValue = Functions::validateEmail($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'E-posten verkar inte vara en giltig e-post. example@example.com');}
        if (strlen($newValue) > 60) {$newValue = NULL; $this->response->AddResponse('error', 'E-poste är för lång. Max 60 tecken.');}
        break;

      case "personnr":
        $newValue = Functions::validatePersonalNumber($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Personnummer måste anges med 6 eller 10 siffror.');}
        if (strlen($newValue) != 10 && strlen($newValue) != 6) {$newValue = NULL; $this->response->AddResponse('error', 'Personnummer måste anges med 6 eller 10 siffror.');}
        break;

      case "datum":
        $newValue = Functions::validateDate($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Datum måste vara ett datum, helst i format YYYY-MM-DD.');}
        break;

      //BOKNING_KUND

      case "onskemal":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Önskemål måste anges.');}
        if (strlen($newValue) > 360) {$newValue = NULL; $this->response->AddResponse('error', 'Önskemål är för långt. Max 360 tecken.');}
        break;

      case "prisjustering":
        $newValue = Functions::validateInt($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Prisjustering måste vara ett heltal.');}
        break;

      case "avresa":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Avreseplats måste anges.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Avreseplats är för lång. Max 100 tecken.');}
        break;

      case "avresatid":
        $newValue = Functions::validateTime($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Avresetid skall anges HH:mm tex 13:00.');}
        break;

      case "avbskyddbetalt":
        $newValue = Functions::validateBoolToBit($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Avbeställningskydd betalt skall anges som true eller false.');}
        break;

      //BOENDE

      case "boende.boendenamn":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Rumstyp måste anges.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Rumstyp är för långt. Max 100 tecken.');}
        break;

      case "boende.pris":
        $newValue = Functions::validateInt($value, -2147483648, 2147483647);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Priset måste anges som ett heltal och får inte vara orimligt stort.');}
        break;

      case "boende.antaltillg":
        $newValue = Functions::validateInt($value, -2147483648, 2147483647);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Tilläggspriset måste anges som ett heltal och får inte vara orimligt stort.');}
        break;

      //RESA

      case "resa":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Resan måste ha ett namn.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Namnet på resan är för långt. Max 100 tecken.');}
        break;

      case "avbskyddpris":
        $newValue = Functions::validateInt($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Priset för avbeställningsskydd måste anges som ett heltal.');}
        break;

      case "anmavgpris":
        $newValue = Functions::validateInt($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Anmälningsavgift måste anges som ett heltal.');}
        break;

      //KATEGORI



      //BETALNING
      case "betalningnr":
        $newValue = Functions::validateInt($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Betalningsnummer måste anges som ett heltal.');}
        break;

      //case datum already covered in KUND, same format here

      case "summa":
        $newValue = Functions::validateInt($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Betald summa måste anges som ett heltal.');}
        break;

      case "avbskyddsumma":
        $newValue = Functions::validateInt($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Betalningssumma för avbeställningsskydd måste anges som ett heltal.');}
        break;

      case "betalningsmetod":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Betalningsmetod måste anges.');}
        if (strlen($newValue) > 30) {$newValue = NULL; $this->response->AddResponse('error', 'Betalningsmetoden har en för mång benämning. Max 30 tecken.');}
        break;

      //PROGRAMBEST

      //All these cases are covered under KUND and have same restrictions

      //DEADLINE

      case "deadlinenote":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'En notering vad deadlinen gäller måste anges.');}
        if (strlen($newValue) > 30) {$newValue = NULL; $this->response->AddResponse('error', 'Noteringen får inte vara längre än 200 tecken.');}
        break;

      //aktiv already covered

      case "forfallodatum":
        $newValue = Functions::validateDate($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Deadlinens förfallodatum måste vara ett datum, helst i format YYYY-MM-DD.');}
        break;

      //KALKYL

      case "resanamn":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Resan måste ha ett namn.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Namnet på resan är för långt. Max 100 tecken.');}
        break;

      case "antal":
        $newValue = Functions::validateInt($value, -2147483648, 2147483647);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Antal måste anges som ett heltal, eller är för stort.');}
        break;

      case "antaler":
        $newValue = Functions::validateInt($value, -2147483648, 2147483647);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Antal enkelrum måste anges som ett heltal, eller är för stort.');}
        break;

      case "beraknatpris":
        $newValue = Functions::validateInt($value, -2147483648, 2147483647);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Beräknat pris måste vara ett heltal, eller är för stort.');}
        break;

      //KALKYL_KOSTNAD

      case "kostnad":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Kostnaden måste ha en benämning.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Benämningen på kostnaden får inte vara längre än 100 tecken.');}
        break;

      case "fixed":
        $newValue = Functions::validateBoolToBit($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Om det är en fast kostnad anges som true eller false.');}
        break;

      //KALKYL_INTAKT

      case "intakt":
        $newValue = Functions::sanatizeStringUnsafe($value);
        if (is_null($newValue)) {$this->response->AddResponse('error', 'Intäkten måste ha en benämning.');}
        if (strlen($newValue) > 100) {$newValue = NULL; $this->response->AddResponse('error', 'Benämningen på intäkten får inte vara längre än 100 tecken.');}
        break;

      //fixed same as in KalkylKostnad

      default: 
        $this->response->AddResponse('error', "Servern kan inte ta emot fältet " . $key . ".");
        $this->response->AddResponsePushToArray('invalidKey', array($key => $value));
        array_push($this->validationErrors, array($key => "ForbiddenKey"));
        $newValue = "ForbiddenKey";
        break;

    }

    if (is_null($newValue)) {
      $this->response->AddResponsePushToArray('invalidData', array($key => $value));
      array_push($this->validationErrors, array($key => $value));
    }
    return $newValue;
  }

  

  
}
