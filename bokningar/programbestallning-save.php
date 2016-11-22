<?php
$PageTitle = "Programbeställning sparad";
include ("../config-connect.php");
include ("../top.php");
$fornamn = trim($_POST["fornamn"]);
$efternamn = trim($_POST["efternamn"]);
$adress = trim($_POST["adress"]);
$postnr = intval(preg_replace('/[\D\s+]/', '',  trim($_POST["postnr"])));
$postort = trim($_POST["postort"]);
$telefon = intval(preg_replace('/[\D\s+]/', '',  trim($_POST["telefon"])));
$email = trim($_POST["email"]);
$notes = trim($_POST["notes"]);
$kategori = trim($_POST["kategori"]);

  $sql = "INSERT INTO Programbestallningar (fornamn, efternamn, adress, postnr, postort, telefon,
            email, notering, datum, kategori)
          
          VALUES (
          '" . $fornamn . "',
          '" . $efternamn . "', 
          '" . $adress . "', 
          " . $postnr . ", 
          '" . $postort . "', 
          " . $telefon . ", 
          '" . $email . "', 
          '" . $notes . "',
          '" . $_POST["datum"] . "',
          '" . $kategori . "');";
          
          
          if (!sqlsrv_query($conn, $sql)) {
          echo "<br>Kritiskt fel. Kunde inte spara. Gå tillbaka (tillbaka pil i webbläsaren) och kontrollera att det inte står bokstäver i sifferfält eller dylikt.<br>Error creating table: ";
          die( print_r( sqlsrv_errors(), true));
        }


  $sql = "SELECT IDENT_CURRENT('Programbestallningar') AS 'id';";
  $result = sqlsrv_query($conn, $sql);
  $LastID = sqlsrv_fetch_array( $result );
  $LastID = $LastID['id'];




?>

 Sparade programbeställning:<br>
 <?php 
 
 $sql = "SELECT * FROM Programbestallningar WHERE id=" . $LastID;
 
 $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        
 
 if (sqlsrv_num_rows($result) > 0) {
  $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC );
 
 echo $row['fornamn'] . " " . $row['efternamn'] . "<br>";
 echo $row['adress'] . "<br>";
 echo $row['postnr'] . " " . $row['postort'] . "<br><br>";
 echo $row['telefon'] . "<br>";
 echo $row['email'] . "<br><br>";
 echo $row['notering'] . "<br>";
 echo $row['datum']->format('Y-m-d') . " " . $row['kategori'] . "<br><br>";
  echo "Programbeställningsnummer:" . $LastID . "<br>";
  } 
 ?>
 
<br>
 <a href="programbestallning-ny.php"><input style="font-size:18px" type="button" value="Registrera en till programbeställning"></a>
  <br><br><br>
 <a href="index.php"><input style="font-size:18px" type="button" value="Tillbaka till bokningsmenyn"></a>
 </BODY>
</HTML>
<?php   
	  sqlsrv_close;
?> 