<?php
$PageTitle = "Ändra resa";
include ("../config-connect.php");
include ("../top.php");

if (!isset($_GET["resaid"]))
  die ( print_r("Något är fel, ingen resa vald. <a href='index.php'>Tillbaka</a>"));


$sql = "SELECT * FROM Resor WHERE resaid=" . intval($_GET["resaid"]);
        $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        
        if (sqlsrv_num_rows($result) > 0) {
        
          $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC );
          
          $resa = $row["resa"];
          $resaid = $row["resaid"];
          $date = $row["date"]->format('Y-m-d');
          $kategori = $row["kategori"];
          $pris = $row["pris"];
          $ertill = $row["ertill"];
          $dr = $row["dr"];
          $er = $row["er"];
          $erextra = $row["erextra"];
          $drextra = $row["drextra"];
          
          $boendealt1 = $row["boendealt1"];
          $boendealt2 = $row["boendealt2"];
          $boendealt3 = $row["boendealt3"];
          $boendealt4 = $row["boendealt4"];
          $boendealt5 = $row["boendealt5"];
          $boendealt1antal = $row["boendealt1antal"];
          $boendealt2antal = $row["boendealt2antal"];
          $boendealt3antal = $row["boendealt3antal"];
          $boendealt4antal = $row["boendealt4antal"];
          $boendealt5antal = $row["boendealt5antal"];
          $persperrum1 = $row["persperrum1"];
          $persperrum2 = $row["persperrum2"];
          $persperrum3 = $row["persperrum3"];
          $persperrum4 = $row["persperrum4"];
          $persperrum5 = $row["persperrum5"];
          $reserverade1 = $row["reserverade1"];
          $reserverade2 = $row["reserverade2"];
          $reserverade3 = $row["reserverade3"];
          $reserverade4 = $row["reserverade4"];
          $reserverade5 = $row["reserverade5"];
          $pris1 = $row["pris1"];
          $pris2 = $row["pris2"];
          $pris3 = $row["pris3"];
          $pris4 = $row["pris4"];
          $pris5 = $row["pris5"];
          
 
        } else {
        die ( print_r("Något är fel, resan existerar inte. <a href='index.php'>Tillbaka</a>"));
        }


?>

	<script type='text/javascript'>
	function validateForm() {
    var x = document.forms["form"]["kategori"].value;
    var y = document.forms["form"]["pris"].value;
    var z = document.forms["form"]["resa"].value;
    
    if (x == "INVALID") {
        alert("Välj kategori för resan");
        return false;
    }
    if (y == "") {
        alert("Ange ett pris för resan");
        return false;
    }
    if (z == "") {
        alert("Ange ett namn på resan");
        return false;
    }
  }
  </script>


<form name="form" action="resa-spara.php" method="post" onsubmit="return validateForm()">
<input type="hidden" name="resaid" value="<?php echo $resaid ?>">
<table width="800px" cellpadding=5 align="center">
	   <tr>
	   	   <td colspan=5><h2>Ändra i resa</h2></td>
	   </tr>
	   <tr>
	   	 <td width="160px" colspan="2">Resans namn</td>
		   <td colspan="3"><input name="resa" type="text" maxlength="25" value="<?php echo $resa; ?>"></td>
	   </tr>
	   <tr>
	   	 <td colspan="2">Avresedatum</td>
		   <td colspan="3"><input name="datum" type="date" value="<?php echo $date; ?>"></td>
	   </tr>
	   <tr>
	   	 <td colspan="2">Kategori</td>
		   <td colspan="3">
		   <select name="kategori">
		   <option value="INVALID">--VÄLJ KATEGORI--</option>
		   <?php
        $sql = "SELECT * FROM Kategorier WHERE aktiv=1 ORDER BY kategori ASC";
        $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        
        if (sqlsrv_num_rows($result) > 0) {
        
        while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
          if ($kategori == $row["kategori"])
            echo "<option value='" . $row['kategori'] . "' selected>" . $row['kategori'] . "</option>";
          else
            echo "<option value='" . $row['kategori'] . "'>" . $row['kategori'] . "</option>";
        }
        
        }
?>
       </select>
		   </td>
	
	   
	   </tr>
	   	   <tr>
	   	 <td>Boende</td>
	   	 <td>Pers. per rum</td>
		   <td>Pris</td>
		   <td>Antal bokade</td>
		   <td>Res. chauf./resel.</td>
	   </tr>
	   
	    </tr>
	   	   <tr>
	   	 <td><input name="boendealt1" type="text" maxlength="25" value="<?php echo $boendealt1; ?>" size="12"></td>
		   <td><input name="persperrum1" type="text" maxlength="4" value="<?php echo $persperrum1; ?>" size="1">pers</td>
		   <td><input name="pris1" type="text" maxlength="8" value="<?php echo $pris1; ?>" size="2">kr</td>
		   <td><input name="boendealt1antal" type="text" maxlength="4" value="<?php echo $boendealt1antal; ?>" size="2">st</td>
		   <td><input name="reserverade1" type="text" maxlength="4" value="<?php echo $reserverade1; ?>" size="2">st</td>
	   </tr>
	   
	   </tr>
	   	   <tr>
	   	 <td><input name="boendealt2" type="text" maxlength="25" value="<?php echo $boendealt2; ?>" size="12"></td>
		   <td><input name="persperrum2" type="text" maxlength="4" value="<?php echo $persperrum2; ?>" size="1">pers</td>
		   <td><input name="pris2" type="text" maxlength="8" value="<?php echo $pris2; ?>" size="2">kr</td>
		   <td><input name="boendealt2antal" type="text" maxlength="4" value="<?php echo $boendealt2antal; ?>" size="2">st</td>
		   <td><input name="reserverade2" type="text" maxlength="4" value="<?php echo $reserverade2; ?>" size="2">st</td>
	   </tr>
	   
	   </tr>
	   	   <tr>
	   	 <td><input name="boendealt3" type="text" maxlength="25" value="<?php echo $boendealt3; ?>" size="12"></td>
		   <td><input name="persperrum3" type="text" maxlength="4" value="<?php echo $persperrum3; ?>" size="1">pers</td>
		   <td><input name="pris3" type="text" maxlength="8" value="<?php echo $pris3; ?>" size="2">kr</td>
		   <td><input name="boendealt3antal" type="text" maxlength="4" value="<?php echo $boendealt3antal; ?>" size="2">st</td>
		   <td><input name="reserverade3" type="text" maxlength="4" value="<?php echo $reserverade3; ?>" size="2">st</td>
	   </tr>
	   
	   </tr>
	   	   <tr>
	   	 <td><input name="boendealt4" type="text" maxlength="25" value="<?php echo $boendealt4; ?>" size="12"></td>
		   <td><input name="persperrum4" type="text" maxlength="4" value="<?php echo $persperrum4; ?>" size="1">pers</td>
		   <td><input name="pris4" type="text" maxlength="8" value="<?php echo $pris4; ?>" size="2">kr</td>
		   <td><input name="boendealt4antal" type="text" maxlength="4" value="<?php echo $boendealt4antal; ?>" size="2">st</td>
		   <td><input name="reserverade4" type="text" maxlength="4" value="<?php echo $reserverade4; ?>" size="2">st</td>
	   </tr>
	   
	   </tr>
	   	   <tr>
	   	 <td><input name="boendealt5" type="text" maxlength="25" value="<?php echo $boendealt5; ?>" size="12"></td>
		   <td><input name="persperrum5" type="text" maxlength="4" value="<?php echo $persperrum5; ?>" size="1">pers</td>
		   <td><input name="pris5" type="text" maxlength="8" value="<?php echo $pris5; ?>" size="2">kr</td>
		   <td><input name="boendealt5antal" type="text" maxlength="4" value="<?php echo $boendealt5antal; ?>" size="2">st</td>
		   <td><input name="reserverade5" type="text" maxlength="4" value="<?php echo $reserverade5; ?>" size="2">st</td>
	   </tr>
	   
	   
	   
	   	   <tr>
	   	 <td colspan=1></td>
		   <td colspan=4><input type="submit" value="Spara"></td>
	   </tr>

	   
	   

</table>
</body>
</html>
<?php   
	  sqlsrv_close;
?> 

