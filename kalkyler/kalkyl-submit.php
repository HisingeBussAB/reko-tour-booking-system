<?php
include ("../config-connect.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>Resekalkyl <?php echo $_POST['resa'] ?></title>
	<meta charset="utf-8"> 
	<style>
	td {font-size:15px;
	   font-family:serif;
	   }
	@media print
	{    
    .no-print, .no-print *
    {
        display: none !important;
    }}
	</style>
  </head>
  
<?php 
  
  $kalkylid="";
  
  if (isset($_POST['print_button'])) {
  echo "<body style='padding:10px;' onload='window.print();'>";
  } else {
  echo "<body style='padding:10px;'>";
  }
  

  
  if ($_POST['resaid'] == "NONE") {

	$sql = "INSERT INTO Kalkyler (resa, date, fixcost1, fixresult1, fixcost2, fixresult2, fixcost3, fixresult3, fixcost4, fixresult4, fixcost5, fixresult5, fixcost6, fixresult6, fixcost7, 
        fixresult7, fixcost8name, fixcost8, fixresult8, fixcost9name, fixcost9, fixresult9, amntsinglescost10, singlescost10, amntsinglesresult10, singlesresult10, peoplecalc, peopleresult, 
        costpers1, resultpers1, costpers2name, costpers2, resultpers2, costpers3name, costpers3, resultpers3, costpers4name, costpers4, resultpers4, costpers5name, costpers5, resultpers5, 
        costpers6name, costpers6, resultpers6, costpers7name, costpers7, resultpers7, costpers8name, costpers8, resultpers8, costpers9name, costpers9, resultpers9, price, insurancein, otherincome, pricesingle)
          
          VALUES (
          '" . $_POST["resa"] . "',
          '" . $_POST["date"] . "',
          " . intval($_POST["fixcost1"]) . ",
          " . intval($_POST["fixresult1"]) . ",
          " . intval($_POST["fixcost2"]) . ",
          " . intval($_POST["fixresult2"]) . ",
          " . intval($_POST["fixcost3"]) . ",
          " . intval($_POST["fixresult3"]) . ",
          " . intval($_POST["fixcost4"]) . ",
          " . intval($_POST["fixresult4"]) . ",
          " . intval($_POST["fixcost5"]) . ",
          " . intval($_POST["fixresult5"]) . ",
          " . intval($_POST["fixcost6"]) . ",
          " . intval($_POST["fixresult6"]) . ",
          " . intval($_POST["fixcost7"]) . ",
          " . intval($_POST["fixresult7"]) . ",
          '" . $_POST["fixcost8name"] . "',
          " . intval($_POST["fixcost8"]) . ",
          " . intval($_POST["fixresult8"]) . ",
          '" . $_POST["fixcost9name"] . "',
          " . intval($_POST["fixcost9"]) . ",
          " . intval($_POST["fixresult9"]) . ",
          " . intval($_POST["amntsinglescost10"]) . ",
          " . intval($_POST["singlescost10"]) . ",
          " . intval($_POST["amntsinglesresult10"]) . ",
          " . intval($_POST["singlesresult10"]) . ",
          " . intval($_POST["peoplecalc"]) . ",
          " . intval($_POST["peopleresult"]) . ",
          " . intval($_POST["costpers1"]) . ",
          " . intval($_POST["resultpers1"]) . ",
          '" . $_POST["costpers2name"] . "',
          " . intval($_POST["costpers2"]) . ",
          " . intval($_POST["resultpers2"]) . ",
          '" . $_POST["costpers3name"] . "',
          " . intval($_POST["costpers3"]) . ",
          " . intval($_POST["resultpers3"]) . ",
          '" . $_POST["costpers4name"] . "',
          " . intval($_POST["costpers4"]) . ",
          " . intval($_POST["resultpers4"]) . ",
          '" . $_POST["costpers5name"] . "',
          " . intval($_POST["costpers5"]) . ",
          " . intval($_POST["resultpers5"]) . ",
          '" . $_POST["costpers6name"] . "',
          " . intval($_POST["costpers6"]) . ",
          " . intval($_POST["resultpers6"]) . ",
          '" . $_POST["costpers7name"] . "',
          " . intval($_POST["costpers7"]) . ",
          " . intval($_POST["resultpers7"]) . ",
          '" . $_POST["costpers8name"] . "',
          " . intval($_POST["costpers8"]) . ",
          " . intval($_POST["resultpers8"]) . ",
          '" . $_POST["costpers9name"] . "',
          " . intval($_POST["costpers9"]) . ",
          " . intval($_POST["resultpers9"]) . ",
          " . intval($_POST["price"]) . ",
          " . intval($_POST["insurancein"]) . ",
          " . intval($_POST["otherincome"]) . ",
          " . intval($_POST["pricesingle"]) . ")";
          
          

        if (sqlsrv_query($conn, $sql)) {
          if (isset($_POST['save_button']))
            echo "<br><big>Resan sparad.</big></br>";
        } else {
          echo "<br>Error creating table: ";
        die( print_r( sqlsrv_errors(), true));
        }
        
        
        
        
        
        $sql = "SELECT TOP 1 * FROM Kalkyler ORDER BY kalkylid DESC ";
        
        
        $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC );

        $kalkylid=$row["kalkylid"];
        
        
        
        
        } else {

        
    
  	$sql = "UPDATE Kalkyler SET
    resa='" . $_POST["resa"] . "',
    date='" . $_POST["date"] . "',
    fixcost1=" . intval($_POST["fixcost1"]) . ",
    fixresult1=" . intval($_POST["fixresult1"]) . ",
    fixcost2=" . intval($_POST["fixcost2"]) . ",
    fixresult2=" . intval($_POST["fixresult2"]) . ",
    fixcost3=" . intval($_POST["fixcost3"]) . ",
    fixresult3=" . intval($_POST["fixresult3"]) . ",
    fixcost4=" . intval($_POST["fixcost4"]) . ",
    fixresult4=" . intval($_POST["fixresult4"]) . ", 
    fixcost5=" . intval($_POST["fixcost5"]) . ", 
    fixresult5=" . intval($_POST["fixresult5"]) . ", 
    fixcost6=" . intval($_POST["fixcost6"]) . ", 
    fixresult6=" . intval($_POST["fixresult6"]) . ", 
    fixcost7=" . intval($_POST["fixcost7"]) . ", 
    fixresult7=" . intval($_POST["fixresult7"]) . ",
    fixcost8name='" . $_POST["fixcost8name"] . "', 
    fixcost8=" . intval($_POST["fixcost8"]) . ",
    fixresult8=" . intval($_POST["fixresult8"]) . ", 
    fixcost9name='" . $_POST["fixcost9name"] . "', 
    fixcost9=" . intval($_POST["fixcost9"]) . ", 
    fixresult9=" . intval($_POST["fixresult9"]) . ", 
    amntsinglescost10=" . intval($_POST["amntsinglescost10"]) . ", 
    singlescost10=" . intval($_POST["singlescost10"]) . ",
    amntsinglesresult10=" . intval($_POST["amntsinglesresult10"]) . ",
    singlesresult10=" . intval($_POST["singlesresult10"]) . ",
    peoplecalc=" . intval($_POST["peoplecalc"]) . ",
    peopleresult=" . intval($_POST["peopleresult"]) . ",
    costpers1=" . intval($_POST["costpers1"]) . ",
    resultpers1=" . intval($_POST["resultpers1"]) . ",
    costpers2name='" . $_POST["costpers2name"] . "',
    costpers2=" . intval($_POST["costpers2"]) . ",
    resultpers2=" . intval($_POST["resultpers2"]) . ",
    costpers3name='" . $_POST["costpers3name"] . "',
    costpers3=" . intval($_POST["costpers3"]) . ",
    resultpers3=" . intval($_POST["resultpers3"]) . ",
    costpers4name='" . $_POST["costpers4name"] . "',
    costpers4=" . intval($_POST["costpers4"]) . ",
    resultpers4=" . intval($_POST["resultpers4"]) . ",
    costpers5name='" . $_POST["costpers5name"] . "',
    costpers5=" . intval($_POST["costpers5"]) . ",
    resultpers5=" . intval($_POST["resultpers5"]) . ",
    costpers6name='" . $_POST["costpers6name"] . "',
    costpers6=" . intval($_POST["costpers6"]) . ",
    resultpers6=" . intval($_POST["resultpers6"]) . ",
    costpers7name='" . $_POST["costpers7name"] . "',
    costpers7=" . intval($_POST["costpers7"]) . ",
    resultpers7=" . intval($_POST["resultpers7"]) . ",
    costpers8name='" . $_POST["costpers8name"] . "',
    costpers8=" . intval($_POST["costpers8"]) . ",
    resultpers8=" . intval($_POST["resultpers8"]) . ",
    costpers9name='" . $_POST["costpers9name"] . "',
    costpers9=" . intval($_POST["costpers9"]) . ",
    resultpers9=" . intval($_POST["resultpers9"]) . ",
    price=" . intval($_POST["price"]) . ",
    insurancein=" . intval($_POST["insurancein"]) . ",
    otherincome=" . intval($_POST["otherincome"]) . ",
    pricesingle=" . intval($_POST["pricesingle"]) . "  
     WHERE kalkylid=" . intval($_POST["resaid"]);
    
    $kalkylid=intval($_POST["resaid"]);


        if (sqlsrv_query($conn, $sql)) {
          if (isset($_POST['save_button']))
            echo "<br><big>Resan sparad.</big></br>";
        } else {
          echo "<br>Error creating table: ";
          die( print_r( sqlsrv_errors(), true));
        }      


	}
	
	if (isset($_POST['save_button'])) {
    
      echo "<br><a href='kalkyl.php?id=" . $kalkylid . "'>Sparad! Återgår till kalkylen.<a>";
      header( "refresh:4; url=kalkyl.php?id=" . $kalkylid);
	
	}

  if (isset($_POST['print_button'])) {
    //print action
	
	
	echo "<body onload='window.print();'>
	<table>
	<tr>
		<td><img src='../img/rekalogga.gif' height='40px'></td>
		<td align='center' colspan=2><b><big><big><big>Resekalkyl</big></big></big></b></td>
		<td><a href='kalkyl.php?id=" . $kalkylid . "'><input class='no-print' type='button' style='float:right;' value='<<-Tillbaka'></a></td>
	</tr>
	<tr>
		 <td align='center' colspan=4><b><big><big><big><big>".$_POST['resa']."</big> ".$_POST['date']."</big></big></big></b></td>
	</tr>
	<tr>
		 <td colspan=4>&nbsp; </td>
	</tr>
	<tr>
		<td colspan=2><b><big>Förkalkyl</big></b></td>
		<td></td>
		<td><b><big>Efterkalkyl</big></b></td>
	</tr>
	<tr>
		 <td colspan=4>&nbsp; </td>
	</tr>
	<tr>
		<td colspan=3><big>Kostnader för gruppen som helhet</big></td>
		<td></td>
	</tr>
	";
	
	if (intval($_POST['fixcost1']) != 0 OR intval($_POST['fixresult1']) != 0) {
	echo "
	<tr>
		<td>Buss inkl. moms</td>
		<td align='right'>".$_POST['fixcost1']." kr</td>
		<td></td>
		<td align='right'>".$_POST['fixresult1']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['fixcost2']) != 0 OR intval($_POST['fixresult2']) != 0) {
	echo "
	<tr>
		<td>Färjor</td>
		<td align='right'>".$_POST['fixcost2']." kr</td>
		<td></td>
		<td align='right'>".$_POST['fixresult2']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['fixcost3']) != 0 OR intval($_POST['fixresult3']) != 0) {
	echo "
	<tr>
		<td>Vägskatter</td>
		<td align='right'>".$_POST['fixcost3']." kr</td>
		<td></td>
		<td align='right'>".$_POST['fixresult3']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['fixcost4']) != 0 OR intval($_POST['fixresult4']) != 0) {
	echo "
	<tr>
		<td>BF och RL logi</td>
		<td align='right'>".$_POST['fixcost4']." kr</td>
		<td></td>
		<td align='right'>".$_POST['fixresult4']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['fixcost5']) != 0 OR intval($_POST['fixresult5']) != 0) {
	echo "
	<tr>
		<td>2:e förare</td>
		<td align='right'>".$_POST['fixcost5']." kr</td>
		<td></td>
		<td align='right'>".$_POST['fixresult5']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['fixcost6']) != 0 OR intval($_POST['fixresult6']) != 0) {
	echo "
	<tr>
		<td>RL-arvode</td>
		<td align='right'>".$_POST['fixcost6']." kr</td>
		<td></td>
		<td align='right'>".$_POST['fixresult6']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['fixcost7']) != 0 OR intval($_POST['fixresult7']) != 0) {
	echo "
	<tr>
		<td>Friplats i gruppen</td>
		<td align='right'>".$_POST['fixcost7']." kr</td>
		<td></td>
		<td align='right'>".$_POST['fixresult7']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['fixcost8']) != 0 OR intval($_POST['fixresult8']) != 0) {
	echo "
	<tr>
		<td>".$_POST['fixcost8name']."</td>
		<td align='right'>".$_POST['fixcost8']." kr</td>
		<td></td>
		<td align='right'>".$_POST['fixresult8']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['fixcost9']) != 0 OR intval($_POST['fixresult9']) != 0) {
	echo "
	<tr>
		<td>".$_POST['fixcost9name']."</td>
		<td align='right'>".$_POST['fixcost9']." kr</td>
		<td></td>
		<td align='right'>".$_POST['fixresult9']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['amntsinglescost10']) != 0 OR intval($_POST['amntsinglesresult10']) != 0) {
	echo "
	<tr>
		<td>Enkelrumskostnad</td>
		<td align='right'>".intval($_POST['amntsinglescost10'])*intval($_POST['singlescost10'])." kr</td>
		<td></td>
		<td align='right'>".intval($_POST['amntsinglesresult10'])*intval($_POST['singlesresult10'])." kr</td>
	</tr>";
	
	}
	
  echo "
	<tr>
		<td><b>Summa gruppkostnad</b></td>
		<td align='right'><b>".$_POST['sumfixcost']." kr</b></td>
		<td></td>
		<td align='right'><b>".$_POST['sumfixresult']." kr</b></td>
	</tr>
	<tr>
		<td colspan=4>&nbsp; </td>
	</tr>
	<tr>
		<td colspan=3><big>Personbundna kostnader</big></td>
		</td>
	</tr>
	<tr>
		<td>Antal personer</td>
		<td align='right'>".$_POST['peoplecalc']." pers.</td>
		<td></td>
		<td align='right'>".$_POST['peopleresult']." pers.</td>
	</tr>
	";
	
	if (intval($_POST['costpers1']) != 0 OR intval($_POST['resultpers1']) != 0) {
	echo "
	<tr>
		<td>Logiform i dubbelrum</td>
		<td align='right'>".$_POST['costpers1']." kr</td>
		<td></td>
		<td align='right'>".$_POST['resultpers1']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['costpers2']) != 0 OR intval($_POST['resultpers2']) != 0) {
	echo "
	<tr>
		<td>".$_POST['costpers2name']."</td>
		<td align='right'>".$_POST['costpers2']." kr</td>
		<td></td>
		<td align='right'>".$_POST['resultpers2']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['costpers3']) != 0 OR intval($_POST['resultpers3']) != 0) {
	echo "
	<tr>
		<td>".$_POST['costpers3name']."</td>
		<td align='right'>".$_POST['costpers3']." kr</td>
		<td></td>
		<td align='right'>".$_POST['resultpers3']." kr</td>
	</tr>";
	
	}

	if (intval($_POST['costpers4']) != 0 OR intval($_POST['resultpers4']) != 0) {
	echo "
	<tr>
		<td>".$_POST['costpers4name']."</td>
		<td align='right'>".$_POST['costpers4']." kr</td>
		<td></td>
		<td align='right'>".$_POST['resultpers4']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['costpers5']) != 0 OR intval($_POST['resultpers5']) != 0) {
	echo "
	<tr>
		<td>".$_POST['costpers5name']."</td>
		<td align='right'>".$_POST['costpers5']." kr</td>
		<td></td>
		<td align='right'>".$_POST['resultpers5']." kr</td>
	</tr>";
	
	}
	
	if (intval($_POST['costpers6']) != 0 OR intval($_POST['resultpers6']) != 0) {
	echo "
	<tr>
		<td>".$_POST['costpers6name']."</td>
		<td align='right'>".$_POST['costpers6']." kr</td>
		<td></td>
		<td align='right'>".$_POST['resultpers6']." kr</td>
	</tr>";
	
	}

	if (intval($_POST['costpers7']) != 0 OR intval($_POST['resultpers7']) != 0) {
	echo "
	<tr>
		<td>".$_POST['costpers7name']."</td>
		<td align='right'>".$_POST['costpers7']." kr</td>
		<td></td>
		<td align='right'>".$_POST['resultpers7']." kr</td>
	</tr>";
	
	}

	if (intval($_POST['costpers8']) != 0 OR intval($_POST['resultpers8']) != 0) {
	echo "
	<tr>
		<td>".$_POST['costpers8name']."</td>
		<td align='right'>".$_POST['costpers8']." kr</td>
		<td></td>
		<td align='right'>".$_POST['resultpers8']." kr</td>
	</tr>";
	
	}

	if (intval($_POST['costpers9']) != 0 OR intval($_POST['resultpers9']) != 0) {
	echo "
	<tr>
		<td>".$_POST['costpers9name']."</td>
		<td align='right'>".$_POST['costpers9']." kr</td>
		<td></td>
		<td align='right'>".$_POST['resultpers9']." kr</td>
	</tr>";
	
	}

	if (intval($_POST['margintaxcalc']) != 0 OR intval($_POST['margintaxresult']) != 0) {
	echo "
	<tr>
		<td>Marginalskatt</td>
		<td align='right'>".$_POST['margintaxcalc']." kr</td>
		<td></td>
		<td align='right'>".$_POST['margintaxresult']." kr</td>
	</tr>";
	
	}	
	
	echo "
	<tr>
		<td><b>Summa personkostnad</b></td>
		<td align='right'><b>".$_POST['sumpersoncost']." kr</b></td>
		<td></td>
		<td align='right'><b>".$_POST['sumpersonresult']." kr</b></td>
	</tr>
	<tr>
		 <td colspan=4>&nbsp; </td>
	</tr>";
	
	echo "
	<tr>
		<td><b>Beräknade intäkter</b></td>
		<td align='right'><b>".$_POST['totincomecalc']." kr</b></td>
		<td align='right'></td>
		<td align='right'></td>
	</tr>
	<tr>
		<td><b>Totala intäkter</b></td>
		<td align='right'><b>".$_POST['totincomeresult']." kr</b></td>
		<td align='right'></td>
		<td align='right'></td>
	</tr>
	<tr>
		 <td colspan=4>&nbsp; </td>
	</tr>
		<tr>
		 <td colspan=4>&nbsp; </td>
	</tr>
	<tr>
		<td colspan=3>&nbsp; &nbsp; <b><big>Kundpris</big></b></td>
		<td align='right'><b><big>".$_POST['price']." kr</big></b></td>
	</tr>
	<tr>	
		<td colspan=3>&nbsp; &nbsp; <b><big>Beräknat bruttöverskott</big></b></td>
		<td align='right'><b><big>".$_POST['calcgrossprofit']." kr</big></b></td>
	</tr>
	<tr>
		<td colspan=3>&nbsp; &nbsp; <b><big>Slutgiltigt bruttöverskott</big></b></td>
		<td align='right'><b><big>".$_POST['finalgrossprofit']." kr</big></b></td>
	</tr>
	<tr>
		<td colspan=4>&nbsp; &nbsp; <b>som utgör ".$_POST['percentofturnover']."% av omsättningen.</b></td>
	</tr>";
	
	
	
	if (intval($_POST['pricesingle']) != 0) {
	echo "
	<tr>
		 <td colspan=4>&nbsp; </td>
	</tr>
	<tr>
		<td><i>Enkelrumstillägg</i></td>
		<td align='right'><i>" . intval($_POST['pricesingle']) . " kr</i></td>
		<td></td>
		<td align='right'></td>
	</tr>
	";
	
	}
	echo "
	</table>
	";
	
	
	}
	    sqlsrv_close;  
	?>
  </body>
</html>