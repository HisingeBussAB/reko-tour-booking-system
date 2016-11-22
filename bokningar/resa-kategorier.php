<?php
$PageTitle = "Hantera kategorier";
include ("../config-connect.php");
include ("../top.php");

if (intval($_GET["ny"]) == 1) {

  if (isset($_POST["kategori"]))
      {
        $sql = "INSERT INTO Kategorier (kategori, aktiv)
          
          VALUES (
          '" . $_POST["kategori"] . "', 
          " . 1 . ")";
        
        if (!sqlsrv_query($conn, $sql)) {
          die( print_r( sqlsrv_errors(), true));
        }


      }
}

if (intval($_GET["activatechange"]) == 1) {

    if (isset($_GET["kategoriid"])) {
        
        $sql = "UPDATE Kategorier SET
        aktiv=" . intval($_GET["aktiv"]) . "
        WHERE kategoriid=". intval($_GET["kategoriid"]);
    
        if (!sqlsrv_query($conn, $sql)) {
          die( print_r( sqlsrv_errors(), true));
        }
     }   
     }


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<table width="600px" cellpadding=5 align="center">
	   <tr>
	   	   <td align='center' colspan=2><h2>Hantera kategorier</h2></td>
	   </tr>
	   <tr>
	   	   <td colspan=2><h3>Skapa kategori</h3></td>
	   </tr>
	   <td width="10%" nowrap><form name="form" action="resa-kategorier.php?ny=1" method="post"><input type="text" name="kategori"></td>
	   <td><input type="submit" value="Skapa"></form></td>
	   <tr>
	   	   <td colspan=2><hr></td>
	   </tr>
	   <tr>
	   	   <td colspan=2><h3>Aktiva kategorier</h3></td>
	   </tr>
	   
<?php
        $sql = "SELECT * FROM Kategorier WHERE aktiv=1 ORDER BY kategori ASC";
        $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        
        if (sqlsrv_num_rows($result) > 0) {
        
        while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
        
          echo "<tr>";
          echo "<td>". $row['kategori'] ."</td>";
          echo "<td nowrap><a href='resa-kategorier.php?activatechange=1&aktiv=0&kategoriid=" . $row['kategoriid'] . "'>INAKTIVERA</a></td>";
          echo "</tr>";
        }
        
        }
?>
	   <tr>
	   	   <td colspan=2><hr></td>
	   </tr>
	   <tr>
	   	   <td colspan=2><h3>Inaktiva kategorier</h3></td>
	   </tr>	   
<?php
        $sql = "SELECT * FROM Kategorier WHERE aktiv=0 ORDER BY kategori ASC";
        $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        
        if (sqlsrv_num_rows($result) > 0) {
        
        while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
        
          echo "<tr>";
          echo "<td>". $row['kategori'] ."</td>";
          echo "<td nowrap><a href='resa-kategorier.php?activatechange=1&aktiv=1&kategoriid=" . $row['kategoriid'] . "'>AKTIVERA</a></td>";
          echo "</tr>";
        }
        
        }
?>
</table>
</body>
</html>
<?php   
	  sqlsrv_close;
?> 
