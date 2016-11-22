<?php
include ("../config-connect.php");

if (!isset($_GET["resaid"]))
  die ( print_r("Något är fel, ingen resa vald. <a href='index.php'>Tillbaka</a>"));
  
  if (!isset($_GET["deactivate"]))
  die ( print_r("Deaktiveringsmarkör hittades inte. Något är fel <a href='index.php'>Tillbaka</a>"));


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Resa deaktiverad</title>
	<meta charset="utf-8"> 
	<style>
    body {
      font-size:18px;
      }
	</style>

</head>
<body style="padding:15px;">
<?php
$sql = "UPDATE Resor SET
     
          aktiv='false'
          WHERE resaid=" . intval($_GET["resaid"]);
          
          if (sqlsrv_query($conn, $sql)) {
            echo "<big><b>Resan är deaktiverad.</b><br><a href='index.php'>Tillbaka till start (automatiskt om 3 sekunder).</a></big>";
            sqlsrv_close; 
            header( "refresh:3; url=index.php" );
        } else {
          echo "<br>Error creating table: ";
          die( print_r( sqlsrv_errors(), true));
        }
        ?>
</body>
</html>
