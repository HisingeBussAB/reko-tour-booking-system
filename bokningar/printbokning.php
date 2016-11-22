<?php
include ("../config-connect.php");

$boendealt = "";
$pris = 0;
$persperrum =0;
$resenar = array();
$count = intval(trim($_POST["count"]));

if (isset($_POST["lastid"])) { 
$count++;
$LastID = intval(trim($_POST["lastid"]));
$antalbekraftelser = intval(trim($_POST["antalbekraftelser"]));

$sql = "SELECT * FROM Bokningar WHERE bokningid=" . $LastID . ";"; 
     $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
     if (sqlsrv_num_rows($result) > 0) {
         $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC );

$resaid = $row["resaid"];
$gruppbokn = $row["gruppbokn"];
$pris = $row["pris"];
$boendealt = $row["boendealt"];
$boendealtkod = $row["boendealtkod"];
$antalresande = $row["antal"];
$avbskyddpris = $row["avbskyddpris"];
$betalningsdatum2 = $row["betalningsdatum2"]->format('Y-m-d');
$betalningsdatum1 = $row["betalningsdatum1"]->format('Y-m-d');
$anmavg = $row["anmavg"];
if ($anmavg == 1) {
$anmavgsql = 1;
$anmavg = "TRUE";
} else {
$anmavgsql = 0;
$anmavg = "FALSE";
}

$anmavgpris = $row["anmavgpris"];
$persperrum = $row["persperrum"];
$resenar1 = $row["resenar1"];
$resenar2 = $row["resenar2"];
$resenar3 = $row["resenar3"];
$resenar4 = $row["resenar4"];
$resenar5 = $row["resenar5"];
$resenar6 = $row["resenar6"];

}

$backID = $LastID;
if ($gruppbokn=="TRUE") {
  $bokningid = "20" . $LastID;
  $backID = $LastID;
  } else {
  $bokningid = "10" . $LastID;
  $backID = $LastID;
  }

$sql = "SELECT resa, date FROM Resor WHERE resaid=" . $resaid . ";"; 
     $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
     if (sqlsrv_num_rows($result) > 0) {
         $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC ); 
        $resanamn = $row["resa"];
        $resadatum = $row["date"]->format('Y-m-d');
}



} else {
$resaid = intval(trim($_POST["resaid"]));
$resanamn = trim($_POST["resanamn"]);
$gruppbokn = trim($_POST["gruppbokn"]);
$fornamn = $_POST["fornamn"];
$efternamn = $_POST["efternamn"];
$adress = $_POST["adress"];
$postnr = $_POST["postnr"];
$postort = $_POST["postort"];
$telefon = $_POST["telefon"];
$email = $_POST["email"];
$onskemal = $_POST["onskemal"];
$prisjustering = $_POST["prisjustering"];
$bekraftelse = $_POST["bekraftelse"];
$boendealtkod = intval(trim($_POST["boendealtkod"]));
$antalresande = intval(trim($_POST["antal"]));
$prisjustering = $_POST["prisjustering"];
$avresa = $_POST["avresa"];
$avresatid = $_POST["avresatid"];
$avbskyddpris = intval(trim($_POST["avbskyddpris"]));
$betalningsdatum2 = trim($_POST["betalningsdatum2"]);
$anmavg = trim($_POST["anmavg"]);
$resadatum = trim($_POST["resadatum"]);
$anmavgpris = intval(trim($_POST["anmavgpris"]));


if ($boendealtkod == 1) {
$boendealt = trim($_POST["boendealt1"]);
$pris = intval(trim($_POST["pris1"]));
$persperrum = intval(trim($_POST["persperrum1"]));
}
if ($boendealtkod == 2) {
$boendealt = trim($_POST["boendealt2"]);
$pris = intval(trim($_POST["pris2"]));
$persperrum = intval(trim($_POST["persperrum2"]));
}
if ($boendealtkod == 3) {
$boendealt = trim($_POST["boendealt3"]);
$pris = intval(trim($_POST["pris3"]));
$persperrum = intval(trim($_POST["persperrum3"]));
}
if ($boendealtkod == 4) {
$boendealt = trim($_POST["boendealt4"]);
$pris = intval(trim($_POST["pris4"]));
$persperrum = intval(trim($_POST["persperrum4"]));
}
if ($boendealtkod == 5) {
$boendealt = trim($_POST["boendealt5"]);
$pris = intval(trim($_POST["pris5"]));
$persperrum = intval(trim($_POST["persperrum5"]));
}

$avresatidsql = array();
for ($i=0;$i<$antalresande;$i++) {

$postnr[$i] = intval(preg_replace('/[\D\s+]/', '',  trim($postnr[$i])));
$fornamn[$i] = trim($fornamn[$i]);
$efternamn[$i] = trim($efternamn[$i]);
$adress[$i] = trim($adress[$i]);
$postort[$i] = trim($postort[$i]);
$telefon[$i] = intval(preg_replace('/[\D\s+]/', '',  trim($telefon[$i])));
$email[$i] = trim($email[$i]);
$onskemal[$i] = trim($onskemal[$i]);
$prisjustering[$i] = intval(trim($prisjustering[$i]));
$bekraftelse[$i] = intval(trim($bekraftelse[$i]));
if ($avresa[$i] == "") {
$avresa[$i] = $avresa[0];
$avresatid[$i] = $avresatid[0];
}
$avresa[$i] = trim($avresa[$i]);
$avresatid[$i] = trim($avresatid[$i]);
$avresatidsql[$i] = date('H:i', strtotime($avresatid[$i]));
}

$antalbekraftelser = sizeof(array_unique($bekraftelse));

$a = 0;
$b = 0;
$c = 0;
$d = 0;
$e = 0;
$f = 0;
$x = 0;

for ($i=0;$i<$antalresande;$i++) {

if ($bekraftelse[$i] == 1) {
$postnrbt[1][$a] = $postnr[$i];
$fornamnbt[1][$a] = $fornamn[$i];
$efternamnbt[1][$a] = $efternamn[$i];
$adressbt[1][$a] = $adress[$i];
$postortbt[1][$a] = $postort[$i];
$telefonbt[1][$a] = $telefon[$i];
$emailbt[1][$a] = $email[$i];
$onskemalbt[1][$a] = $onskemal[$i];
$prisjusteringbt[1][$a] = $prisjustering[$i];
$bekraftelsebt[1][$a] = 1;
$avresabt[1][$a] = $avresa[$i];
$avresatidbt[1][$a] = $avresatid[$i];
$avresatidsqlbt[1][$a] = $avresatidsql[$i];
$a++;
}

if ($bekraftelse[$i] == 2) {
$postnrbt[2][$b] = intval(preg_replace('/[\D\s+]/', '',  trim($postnr[$i])));
$fornamnbt[2][$b] = trim($fornamn[$i]);
$efternamnbt[2][$b] = trim($efternamn[$i]);
$adressbt[2][$b] = trim($adress[$i]);
$postortbt[2][$b] = trim($postort[$i]);
$telefonbt[2][$b] = intval(preg_replace('/[\D\s+]/', '',  trim($telefon[$i])));
$emailbt[2][$b] = trim($email[$i]);
$onskemalbt[2][$b] = trim($onskemal[$i]);
$prisjusteringbt[2][$b] = intval(trim($prisjustering[$i]));
$bekraftelsebt[2][$b] = 2;
$avresabt[2][$b] = $avresa[$i];
$avresatidbt[2][$b] = $avresatid[$i];
$avresatidsqlbt[2][$b] = $avresatidsql[$i];
$b++;

}

if ($bekraftelse[$i] == 3) {
$postnrbt[3][$c] = intval(preg_replace('/[\D\s+]/', '',  trim($postnr[$i])));
$fornamnbt[3][$c] = trim($fornamn[$i]);
$efternamnbt[3][$c] = trim($efternamn[$i]);
$adressbt[3][$c] = trim($adress[$i]);
$postortbt[3][$c] = trim($postort[$i]);
$telefonbt[3][$c] = intval(preg_replace('/[\D\s+]/', '',  trim($telefon[$i])));
$emailbt[3][$c] = trim($email[$i]);
$onskemalbt[3][$c] = trim($onskemal[$i]);
$prisjusteringbt[3][$c] = intval(trim($prisjustering[$i]));
$bekraftelsebt[3][$c] = 3;
$avresabt[3][$c] = $avresa[$i];
$avresatidbt[3][$c] = $avresatid[$i];
$avresatidsqlbt[3][$c] = $avresatidsql[$i];
$c++;

}

if ($bekraftelse[$i] == 4) {
$postnrbt[4][$d] = intval(preg_replace('/[\D\s+]/', '',  trim($postnr[$i])));
$fornamnbt[4][$d] = trim($fornamn[$i]);
$efternamnbt[4][$d] = trim($efternamn[$i]);
$adressbt[4][$d] = trim($adress[$i]);
$postortbt[4][$d] = trim($postort[$i]);
$telefonbt[4][$d] = intval(preg_replace('/[\D\s+]/', '',  trim($telefon[$i])));
$emailbt[4][$d] = trim($email[$i]);
$onskemalbt[4][$d] = trim($onskemal[$i]);
$prisjusteringbt[4][$d] = intval(trim($prisjustering[$i]));
$bekraftelsebt[4][$d] = 4;
$avresabt[4][$d] = $avresa[$i];
$avresatidbt[4][$d] = $avresatid[$i];
$avresatidsqlbt[4][$d] = $avresatidsql[$i];
$d++;

}

if ($bekraftelse[$i] == 5) {
$postnrbt[5][$e] = intval(preg_replace('/[\D\s+]/', '',  trim($postnr[$i])));
$fornamnbt[5][$e] = trim($fornamn[$i]);
$efternamnbt[5][$e] = trim($efternamn[$i]);
$adressbt[5][$e] = trim($adress[$i]);
$postortbt[5][$e] = trim($postort[$i]);
$telefonbt[5][$e] = intval(preg_replace('/[\D\s+]/', '',  trim($telefon[$i])));
$emailbt[5][$e] = trim($email[$i]);
$onskemalbt[5][$e] = trim($onskemal[$i]);
$prisjusteringbt[5][$e] = intval(trim($prisjustering[$i]));
$bekraftelsebt[5][$e] = 5;
$avresabt[5][$e] = $avresa[$i];
$avresatidbt[5][$e] = $avresatid[$i];
$avresatidsqlbt[5][$e] = $avresatidsql[$i];
$e++;

}

if ($bekraftelse[$i] == 6) {
$postnrbt[6][$f] = intval(preg_replace('/[\D\s+]/', '',  trim($postnr[$i])));
$fornamnbt[6][$f] = trim($fornamn[$i]);
$efternamnbt[6][$f] = trim($efternamn[$i]);
$adressbt[6][$f] = trim($adress[$i]);
$postortbt[6][$f] = trim($postort[$i]);
$telefonbt[6][$f] = intval(preg_replace('/[\D\s+]/', '',  trim($telefon[$i])));
$emailbt[6][$f] = trim($email[$i]);
$onskemalbt[6][$f] = trim($onskemal[$i]);
$prisjusteringbt[6][$f] = intval(trim($prisjustering[$i]));
$bekraftelsebt[6][$f] = 6;
$avresabt[6][$f] = $avresa[$i];
$avresatidbt[6][$f] = $avresatid[$i];
$avresatidsqlbt[6][$f] = $avresatidsql[$i];
$f++;

}

}

$y=0;
for ($i=0;$i<=6;$i++) {
  for ($x=0;$x<count($bekraftelsebt[$i]);$x++) {
    $postnrb[$y][$x] = $postnrbt[$i][$x];
    $fornamnb[$y][$x] = $fornamnbt[$i][$x];
    $efternamnb[$y][$x] = $efternamnbt[$i][$x];
    $adressb[$y][$x] = $adressbt[$i][$x];
    $postortb[$y][$x] = $postortbt[$i][$x];
    $telefonb[$y][$x] = $telefonbt[$i][$x];
    $emailb[$y][$x] = $emailbt[$i][$x];
    $onskemalb[$y][$x] = $onskemalbt[$i][$x];
    $prisjusteringb[$y][$x] = $prisjusteringbt[$i][$x];
    $bekraftelseb[$y][$x] = $y+1;
    $avresab[$y][$x] = $avresabt[$i][$x];
    $avresatidb[$y][$x] = $avresatidbt[$i][$x];
    $avresatidsqlb[$y][$x] = $avresatidsqlbt[$i][$x];

}
if (count($bekraftelsebt[$i])>0)
$y++;
}




if ($anmavg=="TRUE") {
  $betalningsdatum1 = date("Y-m-d", strtotime("+13 days"));
  $anmavgsql = 1;
  } else {
  $betalningsdatum1 = $betalningsdatum2;
  $anmavgsql = 0;
  }

$sql = "SELECT IDENT_CURRENT('Bokningar') AS 'id';";
$result = sqlsrv_query($conn, $sql);
  $LastID = sqlsrv_fetch_array( $result );
  $LastID = $LastID['id'];

if ($gruppbokn=="TRUE") {
  $bokningid = "20" . $LastID+1;
  $backID = ($LastID+1);
  } else {
  $bokningid = "10" . $LastID+1;
  $backID = ($LastID+1);
  }


  $betalningsdatum2sql = date('Y-m-d', strtotime($betalningsdatum2));
  $betalningsdatum1sql = date('Y-m-d', strtotime($betalningsdatum1));
  
  
  
  if (isset($_POST["bokningid"])) {
  
  $sql = "UPDATE Bokningar SET 
  resaid=" . $resaid . ", 
  gruppbokn='" . $gruppbokn . "', 
  pris=" . $pris . ", 
  boendealt='" . $boendealt . "',
  boendealtkod=" . $boendealtkod . ", 
  antalresande=" . $antalresande . ", 
  avbskyddpris=" . $avbskyddpris . ", 
  anmavgpris=" . $anmavgpris . ", 
  betalningsdatum2='" . $betalningsdatum2sql . "', 
  betalningsdatum1='" . $betalningsdatum1sql . "',
  persperrum=" . $persperrum . ",
  makulerad=" . 0 . ",
  anmavg=" . $anmavgsql . "
  WHERE bokningid=" . intval($_POST["bokningid"]);

  $LastID = intval($_POST["bokningid"]);     
  if ($gruppbokn=="TRUE") {
  $bokningid = "20" . $LastID;
  $backID = $LastID;
  } else {
  $bokningid = "10" . $LastID;
  $backID = $LastID;
  }
         
     
          if (!sqlsrv_query($conn, $sql)) {
          echo "<br>Kritiskt fel. Kunde inte spara. Gå tillbaka (tillbaka pil i webbläsaren) och kontrollera att det inte står bokstäver i sifferfält eller dylikt.<br>Error creating table: ";
          die( print_r( sqlsrv_errors(), true));
        }
  
  $ix=0;
  $resenarid = array();
  $resenarid = $_POST["resenarid"];
  
  foreach ($resenarid as $resenaridi) {
  
  $sql = "UPDATE Resenarer SET 
  bekraftelse=" . $bekraftelse[$ix] . ", 
  fornamn='" . $fornamn[$ix] . "', 
  efternamn='" . $efternamn[$ix] . "', 
  adress='" . $adress[$ix] . "', 
  postnr=" . $postnr[$ix] . ", 
  postort='" . $postort[$ix] . "', 
  avresa='" . $avresa[$ix] . "', 
  avresatid='" . $avresatidsql[$ix] . "', 
  telefon=" . $telefon[$ix] . ", 
  email='" . $email[$ix] . "', 
  prisjustering=" . $prisjustering[$ix] . ", 
  onskemal='" . $onskemal[$ix] . "'
  
  WHERE resenarid=" . $resenaridi . ";";

          if (!sqlsrv_query($conn, $sql)) {
          echo "<br>Kritiskt fel. Kunde inte spara resenar. Gå tillbaka (tillbaka pil i webbläsaren) och kontrollera att det inte står bokstäver i sifferfält eller dylikt.<br>Error creating table: ";
          die( print_r( sqlsrv_errors(), true));
        }
  $ix++;
  }
  
  } else {
  
  $a=0;
  $y=0;
  for ($i=0;$i<=$antalresande;$i++) {
  
  for ($x=0;$x<count($bekraftelseb[$i]);$x++) {

  
  $sql = "INSERT INTO Resenarer (fornamn, efternamn, prisjustering, onskemal, adress, postnr, postort, 
            telefon, email, resaid, bokningid, avresa, avresatid, bekraftelse)
          
          VALUES (
          '" . $fornamnb[$y][$x] . "', 
          '" . $efternamnb[$y][$x] . "', 
          " . $prisjusteringb[$y][$x] . ", 
          '" . $onskemalb[$y][$x] . "', 
          '" . $adressb[$y][$x] . "', 
          " . $postnrb[$y][$x] . ", 
          '" . $postortb[$y][$x] . "', 
          " . $telefonb[$y][$x] . ", 
          '" . $emailb[$y][$x] . "', 
          " . $resaid . ", 
          " . $backID . ", 
          '" . $avresab[$y][$x] . "',
          '" . $avresatidsqlb[$y][$x] . "',
          " . $bekraftelseb[$y][$x] . ");";
          
      
          if (!sqlsrv_query($conn, $sql)) {
          echo "<br>Kritiskt fel. Kunde inte spara resenar" . ($a+1) . ". Gå tillbaka (tillbaka pil i webbläsaren) och kontrollera att det inte står bokstäver i sifferfält eller dylikt.<br>Error creating table: ";
          die( print_r( sqlsrv_errors(), true));
          }
          
          $sql2 = "SELECT IDENT_CURRENT('Resenarer') AS 'id';";
          $result2 = sqlsrv_query($conn, $sql2);
            $resenarsql = sqlsrv_fetch_array( $result2 );
            $resenar[$a] = $resenarsql['id'];
            $a++;
  
}
if (count($bekraftelseb[$i])>0)
$y++;
}


  
if (count($resenar)>0)
    $resenar1 = $resenar[0];
else
    $resenar1 = -1;

if (count($resenar)>1)
    $resenar2 = $resenar[1];
else
    $resenar2 = -1;

if (count($resenar)>2)
    $resenar3 = $resenar[2];
else
    $resenar3 = -1;

if (count($resenar)>3)
    $resenar4 = $resenar[3]; 
else
    $resenar4 = -1;   

if (count($resenar)>4)
    $resenar5 = $resenar[4];
else
    $resenar5 = -1;    

if (count($resenar)>5)
    $resenar6 = $resenar[5];
else
    $resenar6 = -1;
    
     
  $sql = "INSERT INTO Bokningar (resaid, gruppbokn, pris, boendealt, boendealtkod, antalresande, 
            avbskyddpris, anmavgpris, betalningsdatum2, betalningsdatum1, persperrum, anmavg, makulerad,  
            resenar1, resenar2, resenar3, resenar4, resenar5, resenar6)
          
          VALUES (
          " . $resaid . ", 
          '" . $gruppbokn . "', 
          " . $pris . ", 
          '" . $boendealt . "', 
          " . $boendealtkod . ", 
          " . $antalresande . ", 
          " . $avbskyddpris . ", 
          " . $anmavgpris . ", 
          '" . $betalningsdatum2sql . "', 
          '" . $betalningsdatum1sql . "',
          " . $persperrum . ",
          " . $anmavgsql . ",
          " . 0 . ",
          " . $resenar1 . ",
          " . $resenar2 . ",
          " . $resenar3 . ",
          " . $resenar4 . ",
          " . $resenar5 . ",
          " . $resenar6 . ");";
          
          
          if (!sqlsrv_query($conn, $sql)) {
          echo "<br>Kritiskt fel. Kunde inte spara bokning. Gå tillbaka (tillbaka pil i webbläsaren) och kontrollera att det inte står bokstäver i sifferfält eller dylikt.<br>Error creating table: ";
          die( print_r( sqlsrv_errors(), true));
        }
}

}
///END FIRST ROUND

$counter = 0;
$firstcounter = 0;
$prisjustering = array();
$arraycounter = 0;
$arraycounter2 = 0;
$namn = array();
$sql = "SELECT * FROM Resenarer WHERE bokningid =" . $backID . " AND bekraftelse =" . ($count+1) . ";"; 

          $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
          if (sqlsrv_num_rows($result) > 0) {
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
            
            if ($counter == 0) {
              $avresa = $row["avresa"];
              $avresatid = $row["avresatid"]->format('H:i');
              $fornamn = $row["fornamn"];
              $efternamn = $row["efternamn"];
              $adress = $row["adress"];
              $postnr = $row["postnr"];
              $postort = $row["postort"];
              $bekraftelse = $row["bekraftelse"];
              $prisjustering[$arraycounter2] = $row["prisjustering"];
              $firstflag = true;
              $counter++;
              $firstcounter++;
              $arraycounter2++;
            } else {
            
            $namn[$arraycounter] = $row["fornamn"] . " " . $row["efternamn"];
            $prisjustering[$arraycounter2] = $row["prisjustering"];
            $firstcounter++;
            $counter++;
            $arraycounter++;
            $arraycounter2++;
            
            
            }
            

            }
          }


$sql = "SELECT * FROM Resenarer WHERE bokningid =" . $backID . " AND bekraftelse !=" . ($count+1) . ";"; 

          $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
          if (sqlsrv_num_rows($result) > 0) {
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
            
                      
            $namn[$arraycounter] = $row["fornamn"] . " " . $row["efternamn"];
            $prisjustering[$arraycounter2] = $row["prisjustering"];
            $counter++;
            $arraycounter++;
            $arraycounter2++;
            }
          }


$antal = $antalresande;
$antalresande = $firstcounter;



?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<HTML>
 <HEAD>
 <TITLE>Resebekräftelse</TITLE>
 <link rel="stylesheet" type="text/css" href="/css/print.css" media="print">
 <meta http-equiv="content-type" content="text/html" charset="UTF-8" />
 <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 <style>
 @media print {
    .page-break	{ display: block; page-break-before: always; }
    
    .no-print, .no-print *
    {
        display: none !important;
    }
  }
 
 @page {
    size:8.27in 11.69in; 
    margin:0; 
    mso-header-margin:0mm;  
    mso-footer-margin:0mm; 
    mso-paper-source:1;
  }
  body  
  { 
    margin: 0px;  
  } 
 </style>
 </HEAD>
 <BODY style="font-family: Arial, Helvetica, sans-serif" >
 <div class="no-print" style="padding:30px;">
 <?php
 if ($count == 0)
  echo "<a href='boka-ny.php?resaid=" . $resaid . "&antal=" . $antal . "&bokningid=" . $backID . "'><input class='no-print' type='button' value='Tillbaka och ändra' style='font-size:18px;'></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
 ?>
 
 <a href="index.php"><input class="no-print" type="button" value="Huvudmeny - Bokningar" style="font-size:18px;"></a>
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
 <input class="no-print" type="button" value="Skriv ut" onclick="window.print();" style="font-size:18px;">
 <?php
 if (($count+1) < $antalbekraftelser) {
    
    echo "<form style='display: inline;' action='printbokning.php' class='no-print' method='post' name='form'>";
    echo "<input type='hidden' name='antalbekraftelser' value='" . $antalbekraftelser . "'>";
    echo "<input type='hidden' name='count' value='" . $count . "'>";
    echo "<input type='hidden' name='lastid' value='" . $backID . "'>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<input type='submit' style='font-size:18px;' class='no-print' value='Till nästa bekräftelse'>";
    echo "</form>";


}

 $boendealtArr = explode(' ', $boendealt);
 $boendealtnew = "";
 
 $i = 0;
  foreach ($boendealtArr as $value) {
  
  
  if ($i == 0)
    $boendealtnew = $value;
    
  if (((strlen($boendealtnew)+strlen($value))<12) AND ($i !=0))
    $boendealtnew = $boendealtnew . " " . $value;
  
  $i++;
  }
  
  $boendealt = $boendealtnew;
  
  
  if ($antalbekraftelser > 1)
    $bokningid = $bokningid . "-" . ($count+1);
  

 ?>
 </div> 
 <table class="page-break">
 <tr>
 <td style="height:193mm;width:200mm;padding-left:14mm;padding-top:16mm;vertical-align:top;text-align:left">
 <!-- START TOP FIELD -->
 <img src="../img/logga-small.jpg" style="float:left;padding-right:35mm">
 <small>(Biljett)<span style="padding-left:27mm">
 <?php
 date_default_timezone_set('GMT+1');
 echo date("Y-m-d"); 
 ?>
 </small><br>
 <big><big><big><b>Resebekräftelse</b></big></big></big><br><br><br>
 <small><b>Bokningsnummer: 
 <?php echo $bokningid; ?>
  </b></small>
 <br><br><br><br>
 
 
 <br>
 
 
 
 <table style="padding:0px;margin:0px">
 <tr><td style="padding:0px;margin:0px;width:117mm;vertical-align:top;text-align:left">
 Vi tackar för er bokning av vår resa:<br><br>
 <b>
 <?php echo $resanamn; ?>&nbsp;<?php echo date('j/n', strtotime($resadatum)); ?>
 </b>
 </td><td style="padding:0px;margin:0px;width:83mm;vertical-align:top;text-align:left">
 <?php echo $fornamn; ?>&nbsp;<?php echo $efternamn; ?><br><br>
 <?php echo $adress; ?><br>
 <?php echo wordwrap($postnr , 3 , ' ' , true ); ?>&nbsp;<?php echo $postort; ?>
 </td></tr></table>
 <br><br>
 <table style="width:174mm;padding:0px;margin:0px;vertical-align:top;text-align:left">
 <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left">Resenär</td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left">Boende</td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left">Pris</td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left">Avb. skydd</td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left">Summa</td>
 </tr>
 </table>
 <hr style="padding:0px;margin:0px;border-style:solid;border-width:1px;width:170mm">
 <table style="width:174mm;padding:0px;margin:0px;vertical-align:top;text-align:left">
 <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><?php echo $fornamn; ?>&nbsp;<?php echo $efternamn; ?></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><?php echo $boendealt; ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php echo intval($pris) + intval($prisjustering[0]); ?>,00 kr</td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php echo $avbskyddpris; ?>,00 kr</td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php echo intval($pris) + intval($prisjustering[0]) + intval($avbskyddpris); ?>,00 kr</td>
 </tr>

 <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><?php echo $namn[0]; ?></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 1){ echo $boendealt; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 1){ echo intval($pris) + intval($prisjustering[1]) . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 1){ echo $avbskyddpris . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 1){ echo intval($pris) + intval($prisjustering[1]) + intval($avbskyddpris) . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 </tr>
 
 <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><?php echo $namn[1]; ?></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 2){ echo $boendealt; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 2){ echo intval($pris) + intval($prisjustering[2]) . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 2){ echo $avbskyddpris . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 2){ echo intval($pris) + intval($prisjustering[2]) + intval($avbskyddpris) . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 </tr>
 
 <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><?php echo $namn[2]; ?></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 3){ echo $boendealt; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 3){ echo intval($pris) + intval($prisjustering[3]) . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 3){ echo $avbskyddpris . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 3){ echo intval($pris) + intval($prisjustering[3]) + intval($avbskyddpris) . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 </tr>
 
 <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><?php echo $namn[3]; ?></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 4){ echo $boendealt; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 4){ echo intval($pris) + intval($prisjustering[4]) . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 4){ echo $avbskyddpris . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 4){ echo intval($pris) + intval($prisjustering[4]) + intval($avbskyddpris) . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 </tr>
 
 <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><?php echo $namn[4]; ?></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 5){ echo $boendealt; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 5){ echo intval($pris) + intval($prisjustering[5]) . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 5){ echo $avbskyddpris . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><?php if (intval($antalresande) > 5){ echo intval($pris) + intval($prisjustering[5]) + intval($avbskyddpris) . ",00 kr"; } else { echo "&nbsp;"; } ?></td>
 </tr>
 
 </table>
 <br>
 
 <?php
 
 $totalprisjustering = intval($prisjustering[0]);
if (intval($antalresande) > 5)
  $totalprisjustering += intval($prisjustering[1]) + intval($prisjustering[2]) + intval($prisjustering[3]) + intval($prisjustering[4]) + intval($prisjustering[5]);
elseif (intval($antalresande) > 4)
  $totalprisjustering += intval($prisjustering[1]) + intval($prisjustering[2]) + intval($prisjustering[3]) + intval($prisjustering[4]);
elseif (intval($antalresande) > 3)
  $totalprisjustering += intval($prisjustering[1]) + intval($prisjustering[2]) + intval($prisjustering[3]);
elseif (intval($antalresande) > 2)
  $totalprisjustering += intval($prisjustering[1]) + intval($prisjustering[2]);  
elseif (intval($antalresande) > 1)
  $totalprisjustering += intval($prisjustering[1]);
 
 if ($anmavg != "TRUE") {
 
 echo "<table style='width:174mm;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<tr>";
 echo "<td style='padding:0px;margin:0px;width:174mm;vertical-align:top;text-align:left'>";
 echo "&nbsp;<br>";
 echo "&nbsp;<br>";
 echo "&nbsp;<br>";

 echo "Slutlikvid om " . ((intval($pris) * intval($antalresande)) + (intval($antalresande) * intval($avbskyddpris)) + intval($totalprisjustering)) . ",00 kr ska vara oss tillhanda senast den " . date('j/n', strtotime($betalningsdatum2)) . "<br>";
 echo "Önskas ej avbeställningsskydd avräknas detta från slutlikviden.<br>";
 echo "<br>";
 echo "Avresa från " . $avresa . " den " . date('j/n', strtotime($resadatum)) . " kl. " . date('H:i', strtotime($avresatid));
 echo "</td>";
 echo "</tr>";
 echo "</table>";
 
 } else {
 
 echo "<table style='width:174mm;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<tr>";
 echo "<td style='padding:0px;margin:0px;width:174mm;vertical-align:top;text-align:left'>";
 echo "För aktuell resa erläggs en anmälningsavift för " . $antalresande . " personer á " . $anmavgpris . ",00 kr = " . intval($antalresande) * intval($anmavgpris) . ",00 kr.<br>";
 echo "Denna inbetalas senast 10 dagar efter bokning tillsammans med avbeställningsskyddet om<br>";
 echo "sådant önskas. (Inbetalningskort för anmälningsavgift se sida 2)<br>";
 echo "<br>";
 echo "Slutlikvid om " . (((intval($pris) * intval($antalresande)) - (intval($antalresande) * intval($anmavgpris))) + $totalprisjustering) . ",00 kr ska vara oss tillhanda senast den " . date('j/n', strtotime($betalningsdatum2)) . "<br>";
 echo "<br>";
 echo "Avresa från " . $avresa . " den " . date('j/n', strtotime($resadatum)) . " kl. " . date('H:i', strtotime($avresatid));
 echo "</td>";
 echo "</tr>";
 echo "</table>";

 }
 ?>
 
 <br>
 <hr style="padding:0px;margin:0px;border-style:solid;border-width:1px;width:170mm">
 <small>
 <table style="border-spacing:0;width:186mm;padding:0px;margin:0px;vertical-align:top;text-align:left">
 <tr>
 <td style="border-spacing:0;padding:0px;margin:0px;width:38mm;vertical-align:top;text-align:left">

 <b>Postadress</b><br>
 Your box<br>
 Your city<br>

 </td>
 <td style="border-spacing:0;padding:0px;margin:0px;width:38mm;vertical-align:top;text-align:left">

 <b>Besöksadress</b><br style="line-height:0px;" />
 Your street<br style="line-height:0px;" />
 Your city<br>

 </td>
 <td style="border-spacing:0;padding:0px;margin:0px;width:55mm;vertical-align:top;text-align:left">

 <table style="border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:left">
 <tr><td style="border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:left">
 <b>Telefon<br>
 Hemsida<br>
 E-post</b>
 </td><td>&nbsp;</td><td style="border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:right">
 XX-XXXXXX<br>
 www.yourcompany.se<br>
 info@yourcompany.se
 </td></tr></table>

 </td>
 <td style="padding:0px;margin:0px;width:55mm;vertical-align:top;text-align:left">

 <table style="border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:left">
 <tr><td style="border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:left">
 <b>Bankgiro<br>
 Org. nr</b>
 </td><td>&nbsp;</td><td style="border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:right">
 XXX-XXXX<br>
 XXXXXX-XXXX
 </td></tr></table>

 </td>
 </tr>
 </table>
 </small>
 
 
 
 <!-- END TOP FIELD -->
 </td>
 </tr>
 <tr>
 
 <td style="height:69mm;width:200mm;vertical-align:top;text-align:left">
 
  <table>
  <tr>
  <td style="height:31mm;width:200mm;padding-left:14mm;vertical-align:top;text-align:left">
  <!-- START TOP FIELD AVI -->
  <?php
  
  if ($anmavg != "TRUE") { 
  
  echo "Slutlikvid avseende resa " . $resanamn . "&nbsp;" . date('j/n', strtotime($resadatum)) . "<br>";
  echo "<table><tr><td>Önskas avbeställningsskydd inbetalas:</td><td>" . ((intval($pris) * intval($antalresande)) + (intval($antalresande) * intval($avbskyddpris)) + $totalprisjustering) . ",00 kr</td></tr>";
  echo "<tr><td>Önskas ej avbeställningsskydd inbetalas:</td><td>" . ((intval($pris) * intval($antalresande)) + $totalprisjustering) . ",00 kr</td></tr></table>";
  echo "<small>Vid betalning på annat sätt än med detta inbetalningskort v.v. ange bokningsnr, namn och resa</small>";  
  
  } else {
  
  echo "Slutlikvid avseende resa: " . $resanamn . "&nbsp;" . date('j/n', strtotime($resadatum)) . "<br><br>";
  echo "<small>Vid betalning på annat sätt än med detta inbetalningskort v.v. ange bokningsnr, namn och resa</small>";  
  
  }
  
  ?> 
  <!-- END TOP FIELD AVI -->
  </td>
  </tr>
  <tr>
  <td style="height:18mm;width:200mm;padding-left:14mm;vertical-align:top;text-align:left">
    <table>
    <tr><td style="height:18mm;width:100mm;vertical-align:top;text-align:left">
    <!-- START MIDDLE LEFT FIELD AVI -->
    <?php echo $fornamn; ?>&nbsp;<?php echo $efternamn; ?><br>
    Bokningsnr: <b><?php echo $bokningid; ?></b>
    
    
    <!-- END MIDDLE LEFT FIELD AVI -->
    </td>
    <td style="height:18mm;width:100mm;vertical-align:top;text-align:left">
    <!-- START MIDDLE RIGHT FIELD AVI -->
    Your Company AB
    
    
    <!-- END MIDDLE RIGHT FIELD AVI -->
    </td>
    </tr>
 
    </table>
 
  </td></tr>
  <tr><td style="height:17mm;width:200mm;padding-left:14mm;vertical-align:top;text-align:left">
 
    <table>
    <tr><td style="height:17mm;width:60mm;vertical-align:top;text-align:left">
    <!-- START BOTTOM LEFT FIELD AVI -->
    
    
    
    <!-- END BOTTOM LEFT FIELD AVI -->
    </td>
    <td style="height:17mm;width:55mm;vertical-align:top;text-align:left">
    <!-- START BOTTOM MIDDLE FIELD AVI -->
    <?php 
    
    if ($anmavg != "TRUE") {
    
    echo "&nbsp;";
    
    } else {
    
    echo (((intval($pris) * intval($antalresande)) - (intval($antalresande) * intval($anmavgpris))) + $totalprisjustering);
    echo ",00 kr";
    }
    
    ?>
    
    
    <!-- END BOTTOM MIDDLE FIELD AVI -->
    </td>
    <td style="height:17mm;width:85mm;vertical-align:top;text-align:left">
    <!-- START BOTTOM LEFT FIELD AVI -->
    XXX-XXXX
    
    
    <!-- END BOTTOM LEFT FIELD AVI -->
    </td></tr>
    </table>
  
  </td></tr>
  </table>
 
 
 </td></tr>
 </table>
 
 <?php
 
 if ($anmavg != "TRUE") {

 
 } else {
 
 echo "<table class='page-break' >";
 echo "<tr>";
 echo "<td style='height:193mm;width:200mm;padding-left:14mm;padding-top:16mm;vertical-align:top;text-align:left'>";
 echo "<!-- START TOP FIELD -->";
 echo "<img src='../img/logga-small.jpg' style='float:left;padding-right:35mm'>";
 echo "<small><span style='padding-left:27mm'>";
 echo "</small><br>";
 echo "<big><big><big><b>Anmälningsavgift</b></big></big></big><br><br><br>";
 echo "<small><b>Bokningsnummer: " . $bokningid . "</b></small>";
 echo "<br>&nbsp;<br>&nbsp;<br>&nbsp;<br>&nbsp;";
 
 echo "<table style='padding:0px;margin:0px'>";
 echo "<tr><td style='padding:0px;margin:0px;width:117mm;vertical-align:top;text-align:left'>";
 echo "&nbsp;<br>&nbsp;<br>";
 echo "<b>&nbsp;</b>";
 echo "</td><td style='padding:0px;margin:0px;width:83mm;vertical-align:top;text-align:left'>";
 echo "&nbsp;<br>";
 echo "<br>&nbsp;<br>";
 echo "</td></tr></table>";
 echo "<br>&nbsp;<br>";
 echo "<table style='width:174mm;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<tr>";
 echo "<td style='padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left'>&nbsp;</td>";
 echo "<td style='padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left'>&nbsp;</td>";
 echo "<td style='padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left'>&nbsp;</td>";
 echo "<td style='padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left'>&nbsp;</td>";
 echo "<td style='padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left'>&nbsp;</td>";
 echo "</tr>";
 echo "</table>";
 echo "<hr style='padding:0px;margin:0px;border-style:none;border-width:1px;width:170mm'>";
 echo "<table style='width:174mm;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<tr>";
 echo "<td style='padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left'>&nbsp;</td>";
 echo "<td style='padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left'>&nbsp;</td>";
 echo "<td style='padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left'>&nbsp;</td>";
 echo "<td style='padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left'>&nbsp;</td>";
 echo "<td style='padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left'>&nbsp;</td>";
 echo "</tr>";
 
 echo "<tr>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "</tr>";
 
 echo "<tr>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "</tr>";
 
 echo "<tr>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "</tr>";
 
 echo "<tr>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "<td></td>";
 echo "</tr>";
 
 echo "<tr>";
 echo "<td>&nbsp;</td>";
 echo "<td>&nbsp;</td>";
 echo "<td>&nbsp;</td>";
 echo "<td>&nbsp;</td>";
 echo "<td>&nbsp;</td>";
 echo "</tr>";
 
 echo "</table>";
 echo "&nbsp;<br>";
 echo "<table style='width:174mm;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<tr>";
 echo "<td style='padding:0px;margin:0px;width:174mm;vertical-align:top;text-align:left'>";
 echo "&nbsp;<br>";
 echo "&nbsp;<br>";
 echo "&nbsp;<br>";
 echo "&nbsp;<br>";
 echo "&nbsp;<br>";
 echo "&nbsp;<br>";
 
 echo "</td>";
 echo "</tr>";
 echo "</table>";
 echo "<br>";
 echo "<hr style='padding:0px;margin:0px;border-style:solid;border-width:1px;width:170mm'>";
 echo "<table style='width:186mm;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<tr>";
 echo "<td style='padding:0px;margin:0px;width:38mm;vertical-align:top;text-align:left'>";
 echo "<small>";
 echo "<b>Postadress</b><br>";
 echo "Box XXXX<br>";
 echo "Your city<br>";
 echo "</small>";
 echo "</td>";
 echo "<td style='padding:0px;margin:0px;width:38mm;vertical-align:top;text-align:left'>";
 echo "<small>";
 echo "<b>Besöksadress</b><br>";
 echo "Your Street 88<br>";
 echo "Your city<br>";
 echo "</small>";
 echo "</td>";
 echo "<td style='padding:0px;margin:0px;width:55mm;vertical-align:top;text-align:left'>";
 echo "<small>";
 echo "<table style='border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<tr><td style='border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<b>Telefon<br>";
 echo "Hemsida<br>";
 echo "E-post</b>";
 echo "</td><td>&nbsp;</td><td style='border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:right'>";
 echo "XX-XXXXXX<br>";
 echo "www.yourcompany.se<br>";
 echo "info@yourcompany.se";
 echo "</td></tr></table>";
 echo "</small>";
 echo "</td>";
 echo "<td style='padding:0px;margin:0px;width:55mm;vertical-align:top;text-align:left'>";
 echo "<small>";
 echo "<table style='border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<tr><td style='border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<b>Bankgiro<br>";
 echo "Org. nr</b>";
 echo "</td><td>&nbsp;</td><td style='border-spacing:0;padding:0px;margin:0px;vertical-align:top;text-align:right'>";
 echo "XXXX-XXXX<br>";
 echo "XXXXXX-XXXX";
 echo "</td></tr></table>";
 echo "</small>";
 echo "</td>";
 echo "</tr>";
 echo "</table>";
 
 
 
 
 echo "<!-- END TOP FIELD -->";
 echo "</td>";
 echo "</tr>";
 echo "<tr>";
 
 echo "<td style='height:69mm;width:200mm;vertical-align:top;text-align:left'>";
 
  echo "<table>";
  echo "<tr>";
  echo "<td style='height:31mm;width:200mm;padding-left:14mm;vertical-align:top;text-align:left'>";
  echo "<!-- START TOP FIELD AVI -->";
  echo "Anmälningsavgift avseende resa " . $resanamn . "&nbsp;" . date('j/n', strtotime($resadatum)) . "<br>";
  echo "<table><tr><td>Önskas avbeställningsskydd inbetalas:</td><td>" . (intval($antalresande) * (intval($avbskyddpris) + intval($anmavgpris))) . ",00 kr</td></tr>";
  echo "<tr><td>Önskas ej avbeställningsskydd inbetalas:</td><td>" . (intval($antalresande) * + intval($anmavgpris)) . ",00 kr</td></tr></table>";
  echo "<small>Vid betalning på annat sätt än med detta inbetalningskort v.v. ange bokningsnr, namn och resa</small>";

    
  echo "<!-- END TOP FIELD AVI -->";
  echo "</td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td style='height:18mm;width:200mm;padding-left:14mm;vertical-align:top;text-align:left'>";
    echo "<table>";
    echo "<tr><td style='height:18mm;width:100mm;vertical-align:top;text-align:left'>";
    echo "<!-- START MIDDLE LEFT FIELD AVI -->";
    echo $fornamn . "&nbsp;" . $efternamn . "<br>";
    echo "Bokningsnr: <b>" . $bokningid . "</b>";
    
    
    echo "<!-- END MIDDLE LEFT FIELD AVI -->";
    echo "</td>";
    echo "<td style='height:18mm;width:100mm;vertical-align:top;text-align:left'>";
    echo "<!-- START MIDDLE RIGHT FIELD AVI -->";
    echo "Your Company AB";
    
    
    echo "<!-- END MIDDLE RIGHT FIELD AVI -->";
    echo "</td>";
    echo "</tr>";
 
    echo "</table>";
 
  echo "</td></tr>";
  echo "<tr><td style='height:17mm;width:200mm;padding-left:14mm;vertical-align:top;text-align:left'>";
 
    echo "<table>";
    echo "<tr><td style='height:17mm;width:60mm;vertical-align:top;text-align:left'>";
    echo "<!-- START BOTTOM LEFT FIELD AVI -->";
    
    
    
    echo "<!-- END BOTTOM LEFT FIELD AVI -->";
    echo "</td>";
    echo "<td style='height:17mm;width:55mm;vertical-align:top;text-align:left'>";
    echo "<!-- START BOTTOM MIDDLE FIELD AVI -->";
    
    
    
    echo "<!-- END BOTTOM MIDDLE FIELD AVI -->";
    echo "</td>";
    echo "<td style='height:17mm;width:85mm;vertical-align:top;text-align:left'>";
    echo "<!-- START BOTTOM LEFT FIELD AVI -->";
    echo "XXX-XXXX";
    
    
    echo "<!-- END BOTTOM LEFT FIELD AVI -->";
    echo "</td></tr>";
    echo "</table>";
  
  echo "</td></tr>";
  echo "</table>";
 
 
 echo "</td></tr>";
 echo "</table>";
 }
 
 
 ?>
</body>
</html>
<?php   
	  sqlsrv_close;
	 
?> 
