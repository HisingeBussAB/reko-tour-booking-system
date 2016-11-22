<?php
$PageTitle = "Sparad betalning";
include ("../config-connect.php");
include ("../top.php");

$metod = trim($_POST["metod"]);
$summa = intval(trim($_POST["summa"]));
if ($_POST["avbskyddbet"] == "true") {
  $avbskyddbet = 1;
  $summavb = $_POST["summavb"];
 } else {
  $avbskyddbet = 0;
  $summavb = 0;
  }
$bokning = intval(preg_replace('/[\D\s+]/', '',  substr(trim($_POST["bokning"]), 2)));
$bekraftelse = intval($_POST["bekraftelse"]);
$referens = trim($_POST["referens"]);
 
 $sql = "SELECT * FROM Bokningar WHERE bokningid=" . $bokning;
 $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
 if (sqlsrv_num_rows($result) > 0) {
  $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC );
  
  $resaid = $row["resaid"];
  $antal = $row["antalresande"];
  $grundpris = $row["pris"];
  
  if (intval($row["anmavg"] == 1))
    $anmavgpris = intval($row["anmavgpris"]);
  else
    $anmavgpris = 0;
  
   
} else {
  echo "Den här bokningen finns inte. <a href='index.php'>Gå tillbaka.</a></body></html>";
  exit;
  }
 
 $x=0;
 $totprisjustering = 0;
 $sql = "SELECT * FROM Resenarer WHERE bokningid=" . $bokning . " AND bekraftelse=" . $bekraftelse;
 $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
 $antalpabek = sqlsrv_num_rows($result);
 if (sqlsrv_num_rows($result) > 0) {
  while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
  
    if ($x == 0) {
      $fornamn = $row["fornamn"];
      $efternamn = $row["efternamn"];
      $adress = $row["adress"];
      $postort = $row["postort"];
      $postnr = $row["postnr"];
      }
  $totprisjustering = $totprisjustering + $row["prisjustering"];
  $x++;
  } 
  } else {
  echo "Den här bekräftelsen finns inte. <a href='index.php'>Gå tillbaka.</a></body></html>";
  exit;
  }
  
  
 
 $sql = "SELECT resa, date FROM Resor WHERE resaid=" . $resaid;
 $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
 if (sqlsrv_num_rows($result) > 0) {
  $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC );
   
  $resa = $row["resa"];
  $dateresa = $row["date"]->format('Y-m-d');

  }
  
 

  $sql = "INSERT INTO Betalningar (datum, summa, avbskyddbet, bokning, summavb, bekraftelse, referens, metod)
          
          VALUES (
          '" . $_POST["datum"] . "',
          " . $summa . ", 
          " . $avbskyddbet . ", 
          " . $bokning . ", 
          " . $summavb . ", 
          " . $bekraftelse . ", 
          '" . $referens . "', 
          '" . $metod . "')";
          
          
          if (!sqlsrv_query($conn, $sql)) {
          echo "<br>Kritiskt fel. Kunde inte spara. Gå tillbaka (tillbaka pil i webbläsaren) och kontrollera att det inte står bokstäver i sifferfält eller dylikt.<br>Error creating table: ";
          die( print_r( sqlsrv_errors(), true));
        }



  $sql = "SELECT IDENT_CURRENT('Betalningar') AS 'id';";
  $result = sqlsrv_query($conn, $sql);
  $LastID = sqlsrv_fetch_array( $result );
  $LastID = $LastID['id'];




 $allaavbskydd = 0;
 $allasumma = 0; 
 $allaprisavbskydd = 0;  
  
  
 $sql = "SELECT * FROM Betalningar WHERE bokning=" . $bokning . " AND bekraftelse=" . $bekraftelse;
 
 $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
 
 if (sqlsrv_num_rows($result) > 0) {
  while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
  $allasumma = ($allasumma + intval($row["summa"]));
  
  if ($row["avbskyddbet"] == 1) {  
    $allaprisavbskydd = $allaprisavbskydd + $row["summavb"];
    $allasumma = $allasumma + intval($row["summavb"]);
    $allaavbskydd = 1;
    
  }}}
 
 
 
  
 
 
 $multibek=false; 
        $sql = "SELECT bekraftelse FROM Resenarer WHERE bokningid=" . $bokning . ";";
        $x=0;
        
          if ($result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
              
             if (sqlsrv_num_rows($result) > 0) {
                
                
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
                if ($x == 0) {
                  $bektemp = $row["bekraftelse"];
                  
                 } else {
                  if ($bektemp != $row["bekraftelse"])
                  $multibek=true;
                  $bektemp = $row["bekraftelse"];
                  }
                  $x++;
                }
              }
            }  
 
 $grundpris = $grundpris * $antalpabek; 
 if ($allaavbskydd == 1)
  $grundpris = $grundpris + $allaprisavbskydd;
 $pris = $grundpris + $totprisjustering; 
 
 $sql = "SELECT *,REPLACE(referens, CHAR(13) + CHAR(10), '<br/>') AS referens FROM Betalningar WHERE betalningid=" . $LastID;
 
 $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
 
 if (sqlsrv_num_rows($result) > 0) {
  $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC );

  $dennaavbskydd = intval($row['avbskyddbet']);
  $dennasumma = $row['summa'];
  $dennametod = $row['metod'];
  
  echo "<h3>Betalning för resa</h3>";
  echo "Till:<br>Rekå Resor AB<br>Aröds indsutriväg 30<br>422 43 Göteborg<br>Org-nr: 556176-4456";
  echo "<br><br>Inbetalningsdatum: " . $_POST['datum'];
  echo "<br><br>Betalning avser bokningsnr: <b>" . $_POST['bokning'];
  if ($multibek==true) 
    echo "-" . $_POST['bekraftelse'];
  echo "</b><br>";
  echo "<br>" . $resa . " " . $dateresa;
  echo "<br>Från:<br>" . $fornamn . " " . $efternamn;
  echo "<br>" . $adress;
  echo "<br>" . $postnr . " " . $postort;
  echo "<br><br>Totalt att betala: " . $pris . "kr";
  if ($allaprisavbskydd>0)
    echo " (inkl " . $allaprisavbskydd . "kr avb.skydd)";
  echo "<br>Varav anmälningsavgift " . $anmavgpris . "kr.";
  echo "<br><br><b>Betalt denna betalning: " . ($dennasumma + $summavb) . "kr</b><br>";
  echo "<br>Kvar att betala: " . ($pris - $allasumma) . "kr<br>";  
  if ($dennaavbskydd == 1)
   echo "<br>Avbeställningsskydd ingår i denna betalning med " . $summavb . "kr<br>";
  echo "<br><br>Betalningsmetod: ";
  if ($dennametod=="bg") 
    echo "bankgiro";
  else  
    echo $dennametod;
  echo "<br><br>Noteringar: " . $row["referens"];
  
  echo "<br><br>Betalningsnummer: " . $LastID . "<br>";
  
  
  }
  
  
 ?>
 
<br>
<input style="font-size:18px" class="no-print" type="button" value="Skriv ut" onclick="window.print()">
<br><br><br>
<a href="betalning-ny.php"><input style="font-size:18px" class="no-print" type="button" value="Registrera en till betalning"></a>
  
 
</body>
</html>
<?php   
	  sqlsrv_close;
?> 
