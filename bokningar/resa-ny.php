<?php
$PageTitle = "Ny resa";
include ("../config-connect.php");
include ("../top.php");
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
<table width="800px" cellpadding=5 align="center">
	   <tr>
	   	   <td align='center' colspan=5><h2>Skapa ny resa</h2></td>
	   </tr>
	   <tr>
	   	 <td colspan=2 width="160px">Resans namn</td>
		   <td colspan=3><input name="resa" type="text" maxlength="40"></td>
	   </tr>
	   <tr>
	   	 <td colspan=2>Avresedatum</td>
		   <td colspan=3><input name="datum" type="date"></td>
	   </tr>
	   <tr>
	   	 <td colspan=2>Kategori</td>
		   <td colspan=3>
		   <select name="kategori">
		   <option value="INVALID" selected>--VÄLJ KATEGORI--</option>
		   <?php
        $sql = "SELECT * FROM Kategorier WHERE aktiv=1 ORDER BY kategori ASC";
        $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        
        if (sqlsrv_num_rows($result) > 0) {
        
        while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
        
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
		   <td>Pris/pers.</td>
		   <td>Antal bokade</td>
		   <td>Resverade (chauf./resel.)</td>
	   </tr>
	   
	    </tr>
	   	   <tr>
	   	 <td><input name="boendealt1" type="text" maxlength="25" value="Enkelrum" size="12"></td>
		   <td><input name="persperrum1" type="text" maxlength="4" value="1" size="1">pers</td>
		   <td><input name="pris1" type="text" maxlength="8" placeholder="0" size="2">kr</td>
		   <td><input name="boendealt1antal" type="text" maxlength="4" placeholder="0" size="2">st</td>
		   <td><input name="reserverade1" type="text" maxlength="4" placeholder="0" size="2">st</td>
	   </tr>
	   
	   </tr>
	   	   <tr>
	   	 <td><input name="boendealt2" type="text" maxlength="25" value="Dubbelrum" size="12"></td>
		   <td><input name="persperrum2" type="text" maxlength="4" value="2" size="1">pers</td>
		   <td><input name="pris2" type="text" maxlength="8" placeholder="0"  size="2">kr</td>
		   <td><input name="boendealt2antal" type="text" maxlength="4" placeholder="0"  size="2">st</td>
		   <td><input name="reserverade2" type="text" maxlength="4" placeholder="0"  size="2">st</td>
	   </tr>
	   
	   </tr>
	   	   <tr>
	   	 <td><input name="boendealt3" type="text" maxlength="25" value="" size="12"></td>
		   <td><input name="persperrum3" type="text" maxlength="4" value="" size="1">pers</td>
		   <td><input name="pris3" type="text" maxlength="8" placeholder="0"  size="2">kr</td>
		   <td><input name="boendealt3antal" type="text" maxlength="4" placeholder="0"  size="2">st</td>
		   <td><input name="reserverade3" type="text" maxlength="4" placeholder="0"  size="2">st</td>
	   </tr>
	   
	   </tr>
	   	   <tr>
	   	 <td><input name="boendealt4" type="text" maxlength="25" value="" size="12"></td>
		   <td><input name="persperrum4" type="text" maxlength="4" value="" size="1">pers</td>
		   <td><input name="pris4" type="text" maxlength="8" placeholder="0"  size="2">kr</td>
		   <td><input name="boendealt4antal" type="text" maxlength="4" placeholder="0"  size="2">st</td>
		   <td><input name="reserverade4" type="text" maxlength="4" placeholder="0"  size="2">st</td>
	   </tr>
	   
	   </tr>
	   	   <tr>
	   	 <td><input name="boendealt5" type="text" maxlength="25" value="" size="12"></td>
		   <td><input name="persperrum5" type="text" maxlength="4" value="" size="1">pers</td>
		   <td><input name="pris5" type="text" maxlength="8" placeholder="0"  size="2">kr</td>
		   <td><input name="boendealt5antal" type="text" maxlength="4" placeholder="0"  size="2">st</td>
		   <td><input name="reserverade5" type="text" maxlength="4" placeholder="0"  size="2">st</td>
	   </tr>
	   	   <tr>
		   <td colspan=5><input type="submit" value="Spara"></td>
	   </tr>


	   
	   

</table>
</body>
</html>
<?php   
	  sqlsrv_close;
?> 

