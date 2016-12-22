
        <?php
        include ("../config-connect.php");
        
$a=0;        
$myarray = array();        
$file = fopen("import.csv","r");

while(! feof($file))
  {
  //print_r(fgetcsv($file,59000000, ";"));
  //print_r("<br>");
  $myarray[$a] = fgetcsv($file,59000000, ";");
  $a++;
  }

fclose($file);

$test=0;
foreach ($myarray as $x) {

$date = "2016-01-01";

if (intval($x[8]) == 11)
  $date = "2011-01-01";
if (intval($x[8]) == 12)
  $date = "2012-01-01";
if (intval($x[8]) == 13)
  $date = "2013-01-01";
if (intval($x[8]) == 14)
  $date = "2014-01-01";
if (intval($x[8]) == 15)
  $date = "2015-01-01"; 
if (intval($x[8]) == 16)
  $date = "2016-01-01";

if ($x[3] == "")
$postnr = 0;
else
$postnr = $x[3];

if ($x[5] == "")
$telefon = 0;
else
$telefon = $x[5]; 

$postnr = intval(preg_replace('/[\D\s+]/', '',  trim($postnr)));
$telefon = intval(preg_replace('/[\D\s+]/', '',  trim($telefon)));

$tstring = "Programbeställning - Alla";

$sql = "INSERT INTO Programbestallningar (fornamn, efternamn, adress, postnr, postort, telefon, email, datum, kategori) 
       VALUES (
       '" . $x[0] . "',
       '" . $x[1] . "',
       '" . $x[2] . "',
       " . $postnr . ",
       '" . $x[4] . "',
       " . $telefon . ",
       '" . $x[6] . "',
       '" . $date . "',
       '" . $tstring . "'
       );";

echo $sql . "<br>";
          if (!sqlsrv_query($conn, $sql)) {
          echo "<br>Kritiskt fel." . $test;
          die( print_r( sqlsrv_errors(), true));
          }
$test++;


}



?>
        
        
        
   
        

 

<?php   
	  sqlsrv_close;
?> 