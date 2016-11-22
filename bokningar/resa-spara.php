<?php
$PageTitle = "Resa sparad";
include ("../config-connect.php");
include ("../top.php");

    if (!isset($_POST["resa"])) {
      die( print_r("FEL: Inget resenamn skickat, använd tillbaka filen"));
      }
 
       
    if (isset($_POST["resaid"])) {
    
          $sql = "UPDATE Resor SET
          resa='" . $_POST["resa"] . "',
          date='" . $_POST["datum"] . "',
          kategori='" . $_POST["kategori"] . "',
          boendealt1='" . $_POST["boendealt1"] . "',
          boendealt2='" . $_POST["boendealt2"] . "',
          boendealt3='" . $_POST["boendealt3"] . "',
          boendealt4='" . $_POST["boendealt4"] . "',
          boendealt5='" . $_POST["boendealt5"] . "',
          boendealt1antal=" . intval($_POST["boendealt1antal"]) . ",
          boendealt2antal=" . intval($_POST["boendealt2antal"]) . ",
          boendealt3antal=" . intval($_POST["boendealt3antal"]) . ",
          boendealt4antal=" . intval($_POST["boendealt4antal"]) . ",
          boendealt5antal=" . intval($_POST["boendealt5antal"]) . ",
          persperrum1=" . intval($_POST["persperrum1"]) . ",
          persperrum2=" . intval($_POST["persperrum2"]) . ",
          persperrum3=" . intval($_POST["persperrum3"]) . ",
          persperrum4=" . intval($_POST["persperrum4"]) . ",
          persperrum5=" . intval($_POST["persperrum5"]) . ",
          reserverade1=" . intval($_POST["reserverade1"]) . ",
          reserverade2=" . intval($_POST["reserverade2"]) . ",
          reserverade3=" . intval($_POST["reserverade3"]) . ",
          reserverade4=" . intval($_POST["reserverade4"]) . ",
          reserverade5=" . intval($_POST["reserverade5"]) . ",
          pris1=" . intval($_POST["pris1"]) . ",
          pris2=" . intval($_POST["pris2"]) . ",
          pris3=" . intval($_POST["pris3"]) . ",
          pris4=" . intval($_POST["pris4"]) . ",
          pris5=" . intval($_POST["pris5"]) . "
          
          
          WHERE resaid=" . intval($_POST["resaid"]);
          
     } else {
     
     $sql = "INSERT INTO Resor (resa, date, kategori, boendealt1, boendealt2, boendealt3, boendealt4, boendealt5, boendealt1antal,
        boendealt2antal, boendealt3antal, boendealt4antal, boendealt5antal, persperrum1, persperrum2, persperrum3, persperrum4, persperrum5, reserverade1,
        reserverade2, reserverade3, reserverade4, reserverade5, pris1, pris2, pris3, pris4, pris5, aktiv)
          
          VALUES (
          '" . $_POST["resa"] . "',
          '" . $_POST["datum"] . "',
          '" . $_POST["kategori"] . "',
          '" . $_POST["boendealt1"] . "',
          '" . $_POST["boendealt2"] . "',
          '" . $_POST["boendealt3"] . "',
          '" . $_POST["boendealt4"] . "',
          '" . $_POST["boendealt5"] . "',
          " . intval($_POST["boendealt1antal"]) . ",
          " . intval($_POST["boendealt2antal"]) . ",
          " . intval($_POST["boendealt3antal"]) . ",
          " . intval($_POST["boendealt4antal"]) . ",
          " . intval($_POST["boendealt5antal"]) . ",
          " . intval($_POST["persperrum1"]) . ",
          " . intval($_POST["persperrum2"]) . ",
          " . intval($_POST["persperrum3"]) . ",
          " . intval($_POST["persperrum4"]) . ",
          " . intval($_POST["persperrum5"]) . ",
          " . intval($_POST["reserverade1"]) . ",
          " . intval($_POST["reserverade2"]) . ",
          " . intval($_POST["reserverade3"]) . ",
          " . intval($_POST["reserverade4"]) . ",
          " . intval($_POST["reserverade5"]) . ",
          " . intval($_POST["pris1"]) . ",
          " . intval($_POST["pris2"]) . ",
          " . intval($_POST["pris3"]) . ",
          " . intval($_POST["pris4"]) . ",
          " . intval($_POST["pris5"]) . ",
          " . 1 . ")";
     
     
     }
          
          if (sqlsrv_query($conn, $sql)) {
            echo "<div align='center'><big><b>Resan är sparad.</b><br><a href='index.php'>Tillbaka till start (automatiskt om 3 sekunder).</a></big></div>";
            sqlsrv_close; 
            header( "refresh:3; url=index.php" );
        } else {
          echo "<br>Error creating table: ";
          die( print_r( sqlsrv_errors(), true));
        }
        
    
       

     	  
?> 
	  

</body>
</html>
<?php   
	  sqlsrv_close;
?> 