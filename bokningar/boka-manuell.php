<?php
include ("../config-connect.php");
if ($_GET["anmavg"] == 1)
  $anmavg = "TRUE";
else
  $anmavg = "FALSE";
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
  
  ::-webkit-input-placeholder { /* WebKit browsers */
      color: transparent;
  }
  :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
      color: transparent;
  }
  ::-moz-placeholder { /* Mozilla Firefox 19+ */
      color: transparent;
  }
  :-ms-input-placeholder { /* Internet Explorer 10+ */
      color: transparent;
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
 <a href="index.php"><input class="no-print" type="button" value="Huvudmeny - Bokningar" style="font-size:18px;"></a>
 <input class="no-print" type="button" value="Skriv ut" onclick="window.print();" style="font-size:18px;">
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
 <input type="text" style="font-weight:bold;border-color:transparent;" size=4 placeholder='Boknnr' id="bokningid" onblur="document.getElementById('bokningid3').value = document.getElementById('bokningid').value;document.getElementById('bokningid1').value = document.getElementById('bokningid').value;document.getElementById('bokningid2').value = document.getElementById('bokningid').value">
  </b></small>
 <br><br><br><br>
 
 
 <br>
 
 
 
 <table style="padding:0px;margin:0px">
 <tr><td style="padding:0px;margin:0px;width:117mm;vertical-align:top;text-align:left">
 Vi tackar för er bokning av vår resa:<br><br>
 <b>
 <input type="text" size=30 id="resa" style="font-weight:bold;font-size:16px;border-color:transparent;" id="resa" placeholder='Resa namn och datum' onblur="document.getElementById('resa3').value = document.getElementById('resa').value;document.getElementById('resa1').value = document.getElementById('resa').value;">
 </b>
 </td><td style="padding:0px;margin:0px;width:83mm;vertical-align:top;text-align:left">
 <input type="text" style="font-size:16px;border-color:transparent;" id="namn" size=22 placeholder='Namn' onblur="document.getElementById('namn1c').value = document.getElementById('namn').value;document.getElementById('namn1a').value = document.getElementById('namn').value;document.getElementById('namn1b').value = document.getElementById('namn').value;"><br><br>
 <input type="text" style="font-size:16px;border-color:transparent;" size=22 placeholder='Adress'><br>
 <input type="text" style="font-size:16px;border-color:transparent;" size=3 placeholder='Postnr'>&nbsp;<input type="text" style="font-size:16px;border-color:transparent;" size=13 placeholder='Postort'>
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
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;font-size:16px;border-color:transparent;" type="text" id="namn1a" size=24 placeholder='Namn1'></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=9 placeholder='Boendealt'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 </tr>

 <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;font-size:16px;border-color:transparent;" type="text" size=24 placeholder='Namn2'></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=9 placeholder='Boendealt'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 </tr>
 
 <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;font-size:16px;border-color:transparent;" type="text" size=24 placeholder='Namn3'></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=9 placeholder='Boendealt'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 </tr>
 
 <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;font-size:16px;border-color:transparent;" type="text" size=24 placeholder='Namn4'></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=9 placeholder='Boendealt'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 </tr>
 
 <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;font-size:16px;border-color:transparent;" type="text" size=24 placeholder='Namn5'></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=9 placeholder='Boendealt'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 </tr>
  <tr>
 <td style="padding:0px;margin:0px;width:72mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;font-size:16px;border-color:transparent;" type="text" size=24 placeholder='Namn6'></td>
 <td style="padding:0px;margin:0px;width:30mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=9 placeholder='Boendealt'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 <td style="padding:0px;margin:0px;width:24mm;vertical-align:top;text-align:left"><input style="border:0px;padding:0px;border-color:transparent;font-size:16px;" type="text" size=5 placeholder='0,00 kr'></td>
 </tr>

 
 </table>
  
 <?php
 
 if ($anmavg != "TRUE") {
 
 echo "<table style='width:174mm;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<tr>";
 echo "<td style='padding:0px;margin:0px;width:174mm;vertical-align:top;text-align:left'>";
 echo "&nbsp;<br>";
 echo "&nbsp;<br>";
 echo "&nbsp;<br>";

 echo "Slutlikvid om <input style='border:0px;padding:0px;border-color:transparent;font-size:16px;' type='text' size=8 placeholder='0,00 kr'> ska vara oss tillhanda senast den <input style='border:0px;padding:0px;border-color:transparent;font-size:16px;width:40px' type='text' size=1 placeholder='1/1'><br>";
 echo "Önskas ej avbeställningsskydd avräknas detta från slutlikviden.<br>";
 echo "<br>";
 echo "Avresa från <input style='border:0px;padding:0px;border-color:transparent;font-size:16px;' type='text' size=50 placeholder='Plats den 1/1 kl 00:00'>";
 echo "</td>";
 echo "</tr>";
 echo "</table>";
 
 } else {
 
 echo "<table style='width:174mm;padding:0px;margin:0px;vertical-align:top;text-align:left'>";
 echo "<tr>";
 echo "<td style='border:0px;padding:0px;margin:0px;width:174mm;vertical-align:top;text-align:left'>";
 echo "För aktuell resa erläggs en anmälningsavift för <input style='border:0px;padding:0px;border-color:transparent;font-size:16px;width:10px' type='text' size=1 placeholder='0'> personer á <input style='border:0px;padding:0px;border-color:transparent;font-size:16px;' type='text' size=5 placeholder='0,00 kr'> = <input style='border:0px;padding:0px;border-color:transparent;font-size:16px;' type='text' size=5 placeholder='0,00 kr'>.<br>";
 echo "Denna inbetalas senast 10 dagar efter bokning tillsammans med avbeställningsskyddet om<br>";
 echo "sådant önskas. (Inbetalningskort för anmälningsavgift se sida 2)<br>";
 echo "<br>";
 echo "Slutlikvid om <input style='border:0px;padding:0px;border-color:transparent;font-size:16px;' type='text' size=8 placeholder='0,00 kr'> ska vara oss tillhanda senast den <input style='border:0px;padding:0px;border-color:transparent;font-size:16px;width:40px' type='text' size=1 placeholder='1/1'><br>";
 echo "<br>";
 echo "Avresa från <input style='border:0px;padding:0px;border-color:transparent;font-size:16px;' type='text' size=50 placeholder='Plats den 1/1 kl 00:00'>";
 echo "</td>";
 echo "</tr>";
 echo "</table>";

 }
 ?>
 
 <br>
 <hr style="padding:0px;margin:0px;border-style:solid;border-width:1px;width:170mm">
 <small>
 <table style="width:186mm;padding:0px;margin:0px;vertical-align:top;text-align:left">
 <tr>
 <td style="padding:0px;margin:0px;width:38mm;vertical-align:top;text-align:left">
 <b>Postadress</b><br>
 Your street<br>
 Your city<br>
 </td>
 <td style="padding:0px;margin:0px;width:38mm;vertical-align:top;text-align:left">
 <b>Besöksadress</b><br>
 Your street<br>
 Your city<br>
 </td>
 <td style="padding:0px;margin:0px;width:55mm;vertical-align:top;text-align:left">
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
  
  echo "Slutlikvid avseende resa <input style='border:0px;padding:0px;font-weight:bold;font-size:16px;border-color:transparent;' type='text' id='resa1' size=35 placeholder='Resa namn och datum'><br>";
  echo "<table><tr><td>Önskas avbeställningsskydd inbetalas:</td><td><input style='border:0px;padding:0px;border-color:transparent;font-size:16px;' type='text' size=8 placeholder='0,00 kr'></td></tr>";
  echo "<tr><td>Önskas ej avbeställningsskydd inbetalas:</td><td><input style='border:0px;padding:0px;border-color:transparent;font-size:16px;' type='text' size=8 placeholder='0,00 kr'></td></tr></table>";
  echo "<small>Vid betalning på annat sätt än med detta inbetalningskort v.v. ange bokningsnr, namn och resa</small>";  
  
  } else {
  
  echo "Slutlikvid avseende resa: <input style='border:0px;padding:0px;font-size:16px;font-weight:bold;border-color:transparent;' type='text' id='resa1' size=35 placeholder='Resa namn och datum'><br><br>";
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
    <input style="border:0px;padding:0px;font-size:16px;border-color:transparent;" type="text" id="namn1b" size=24 placeholder='Namn1'><br>
    Bokningsnr: <input style="border:0px;padding:0px;font-weight:bold;font-size:16px;border-color:transparent;" type="text" id="bokningid1" size=4 placeholder='Bokningnr'>
    
    
    <!-- END MIDDLE LEFT FIELD AVI -->
    </td>
    <td style="height:18mm;width:100mm;vertical-align:top;text-align:left">
    <!-- START MIDDLE RIGHT FIELD AVI -->
    Your company AB
    
    
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
    
    echo "<input style='border:0px;padding:0px;border-color:transparent;font-size:16px;' type='text' size=8 placeholder='0,00 kr'>";
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
 echo "<small><b>Bokningsnummer: <input type='text' style='font-weight:bold;border-color:transparent;' size=4 placeholder='Boknnr' id='bokningid2'></b></small>";
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
 echo "Your street<br>";
 echo "Your city<br>";
 echo "</small>";
 echo "</td>";
 echo "<td style='padding:0px;margin:0px;width:38mm;vertical-align:top;text-align:left'>";
 echo "<small>";
 echo "<b>Besöksadress</b><br>";
 echo "Your street<br>";
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
 echo "XXX-XXXX<br>";
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
  echo "Anmälningsavgift avseende resa <input style='border:0px;padding:0px;font-weight:bold;font-size:16px;border-color:transparent;' type='text' id='resa3' size=35 placeholder='Resa namn och datum'><br>";
  echo "<table><tr><td>Önskas avbeställningsskydd inbetalas:</td><td><input style='border:0px;padding:0px;border-color:transparent;font-size:16px;' type='text' size=6 placeholder='0,00 kr'></td></tr>";
  echo "<tr><td>Önskas ej avbeställningsskydd inbetalas:</td><td><input style='border:0px;padding:0px;border-color:transparent;font-size:16px;' type='text' size=6 placeholder='0,00 kr'></td></tr></table>";
  echo "<small>Vid betalning på annat sätt än med detta inbetalningskort v.v. ange bokningsnr, namn och resa</small>";

    
  echo "<!-- END TOP FIELD AVI -->";
  echo "</td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td style='height:18mm;width:200mm;padding-left:14mm;vertical-align:top;text-align:left'>";
    echo "<table>";
    echo "<tr><td style='height:18mm;width:100mm;vertical-align:top;text-align:left'>";
    echo "<!-- START MIDDLE LEFT FIELD AVI -->";
    echo "<input style='border:0px;padding:0px;font-size:16px;border-color:transparent;' type='text' id='namn1c' size=24 placeholder='Namn1'><br>";
    echo "Bokningsnr: <input style='border:0px;padding:0px;font-size:16px;font-weight:bold;border-color:transparent;' type='text' id='bokningid3' size=4 placeholder='Bokningnr'>";
    
    
    echo "<!-- END MIDDLE LEFT FIELD AVI -->";
    echo "</td>";
    echo "<td style='height:18mm;width:100mm;vertical-align:top;text-align:left'>";
    echo "<!-- START MIDDLE RIGHT FIELD AVI -->";
    echo "Your company AB";
    
    
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
