<!-- START TOP INCLUDE -->
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
<head>
<title><?php echo $PageTitle ?> | Your Company</title>
<link rel="shortcut icon" href="/img/favicon.png" type="image/png">
<link rel="icon" href="/img/favicon.png" type="image/png">
<meta http-equiv="content-type" content="text/html" charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	<style>
 @media print {
    .page-break	{ display: block; page-break-before: always; }

    .no-print, .no-print *
    {
        display: none !important;
    }
    .noborder	{ border-color:transparent; }


  }
  @page {
    size:8.27in 11.69in;
    margin:25mm;
    mso-header-margin:0mm;
    mso-footer-margin:0mm;
    mso-paper-source:1;
  }

  .topcell {
  white-space: nowrap
  font-size:16px;
  font-weight:normal;


  }

  .toph3 {
  font-size:22px;
  font-weight:bold;

  }

  table#top tr td {
  font-size:16px;
  }

  td {white-space: nowrap}


  body {
      font-size:16px;
      }

 </style>
       <script language="javascript">
        function goResabokningslage() {
            id = document.getElementById('bokningslage').value;
            window.location.assign("/bokningslage.php?resaid=" + id);
        }

        function goResaallabokningar() {
            id = document.getElementById('allabokningar').value;
            window.location.assign("/allabokningar.php?resaid=" + id);
        }

        function goSokbokning() {
            id = document.getElementById('visabokning').value;
            window.location.assign("/visabokning.php?bokningid=" + id);
        }

        </script>
</head>
<body>
<table width="100%" class="no-print topcell" id="top">
<tr>
<td width="25%" align="center" class="topcell"><a href="/index.php"><img src="/img/logga.gif" width="130" alt="Huvudmeny" class="no-print"></a></td>
<td width="25%" align="center" class="topcell"><h3 class="no-print toph3"><a href="/bokningar/">BOKNINGAR</a></h3></td>
<td width="25%" align="center" class="topcell"><h3 class="no-print toph3"><a href="/kalkyler/">KALKYLER</a></h3></td>
<td width="25%" align="center" class="topcell"><h3 class="no-print toph3"><a href="/kundregister/">KUNDDATA</a></h3></td>
</tr>
<tr>

<td width="20%" align="center" class="no-print topcell">
<input id="visabokning" placeholder="Sök bokningsnummer" type="text"><input type="button" value="SÖK" onclick="goSokbokning();">
</td>

<td width="20%" align="center" class="no-print topcell"><select id="bokningslage" onchange="goResabokningslage();">
             <option value="1" SELECTED>--BOKNINGSLÄGE--</option>
             <?php
             $sql = "SELECT resaid, resa, date FROM Resor WHERE aktiv=1 ORDER BY date ASC";
             $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

             if (sqlsrv_num_rows($result) > 0) {

               while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {

               echo "<option value=" . $row["resaid"] . ">" . $row["resa"] . " " . $row["date"]->format('j/n') . "</option>";

             }
             }


             ?>


        </select>
        </td>
<td width="20%" align="center" class="no-print topcell">


</td>


        <td width="20%" align="center" class="no-print topcell">
        <select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
        <option value="" SELECTED>--SNABBGENVÄGAR--</option>
        <option value="/bokningar/boka-ny.php">Skapa bokning</option>
        <option value="/bokningar/betalning-ny.php">Registera betalning</option>
        <option value="programbestallning-ny.php">Spara programbetällning</option>
        </select>

</td>
</tr>
<tr>
</table>
<hr class="no-print">
<!-- END TOP INCLUDE -->
