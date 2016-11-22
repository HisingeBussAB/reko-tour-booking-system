
        <?php
        $PageTitle = "Visa bokning";
        include ("config-connect.php");
        include 'top.php';
        $resaid = -1;
        $gruppbokn = -1;
        $postnr = 0;
        $telefon = "-";
        
      ?>
        <script language="javascript">
        
        
        </script>
        
        <?php
        $bokningid = mb_substr($_GET["bokningid"], 2);
        $bokningidsepcountersql = $bokningid + 1;
        $bokningidsepcounter = intval($_GET["bokningid"]) + 1;
        if (!isset($_GET["bokningid"])) 
            die ( print_r("Ingen bokning är vald. <a href='index.php'>Tillbaka</a>"));
        
        if (isset($_GET["bokningidraw"])) {
          $bokningid = intval($_GET["bokningidraw"]);
          $bokningidsepcountersql = $bokningid + 1;
          $bokningidsepcounter = "10" . (intval($_GET["bokningid"]) + 1);
          }
          
        $resenar = array();
        $sql = "SELECT * FROM Bokningar WHERE bokningid=" . $bokningid . ";";
        
          if ($result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
        
              if (sqlsrv_num_rows($result) > 0) {
                $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC );
                
                if ($row["resaid"]!=0)
                  $resaid = $row["resaid"];
                  $gruppbokn = $row["gruppbokn"];
                  $grundpris = $row["pris"];
                  $boendealt = $row["boendealt"];
                  $antalresande = $row["antalresande"];
                  $avbskyddpris = $row["avbskyddpris"];
                  $anmavgpris = $row["anmavgpris"];
                  $betalningsdatum1  = $row["betalningsdatum1"]->format('Y-m-d');
                  $betalningsdatum2  = $row["betalningsdatum2"]->format('Y-m-d');
                  $persperrum  = $row["persperrum"];
                  $anmavg  = $row["anmavg"];
                  $makulerad  = $row["makulerad"];
                  $resenar[0] = $row["resenar1"];
                  $resenar[1] = $row["resenar2"];
                  $resenar[2] = $row["resenar3"];
                  $resenar[3] = $row["resenar4"];
                  $resenar[4] = $row["resenar5"];
                  $resenar[5] = $row["resenar6"];
                
                } else {
                die ( print_r("Bokningen finns inte. <a href='index.php'>Tillbaka</a>"));
                }

                } else {
                echo ( print_r( sqlsrv_errors(), true));
                exit;
              }
              
              
              if ($gruppbokn == "TRUE") {
              $bokningprint = "20" . $bokningid;
              } else {
              $bokningprint = "10" . $bokningid;
              }
              
              
           
                
                
                
                
                
                
                /*
                
                
                               
                if ($row["postnr"]!=0)
                  $postnr = chunk_split($row["postnr"], 3, ' ');
                
                $postort = $row["postort"];
                
                if ($row["telefon"]!=0) {
                   $telefon = "0" . $row["telefon"];
                   $telefon = strrev(chunk_split (strrev($telefon), 2,' '));
                   $telefon = preg_replace('/\s+/', '', $telefon, 2);
                   }
                   

                
                 
          
                    
          
          
          for (i
                    
          $sql = "SELECT *,REPLACE(onskemal, CHAR(13) + CHAR(10), '<br/>') AS onskemal FROM Resenarer WHERE bokningid=" . $bokningid . " ORDER BY bekraftelse, resenarid;";
          if ($result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
        
              if (sqlsrv_num_rows($result) > 0) {
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
              
              $namn[$i] = $row["fornamn"] . " " . $row["efternamn"];
              $adress[$i] = $row["adress"];
              $postort[$i] =  $row["postort"];
              if ($row["postnr"]!=0)
                  $postnr[$i] = chunk_split($row["postnr"], 3, ' ');
                  
              if ($row["telefon"]!=0) {
                  $telefon[$i] = "0" . $row["telefon"];
                  $telefon[$i] = strrev(chunk_split (strrev($telefon[$i]), 2,' '));
                  $telefon[$i] = preg_replace('/\s+/', '', $telefon[$i], 2);
                  }
              $prisjustering[$i] = $row["prisjustering"];
              $bekraftelse[$i] = $row["bekraftelse"];
              $onskemal[$i] = $row["onskemal"];
              $email[$i] = $row["email"];
            $i++;  
           }}}
          
          
          */
          
       
          
        $sql = "SELECT resa,date FROM Resor WHERE resaid=" . $resaid . ";";          
        if ($result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
        
              if (sqlsrv_num_rows($result) > 0) {
                $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC );
              
              $resa = $row["resa"];
              $date = $row["date"]->format('Y-m-d');
              
              
           }}
           
        $multibek=false; 
        $sql = "SELECT bekraftelse FROM Resenarer WHERE bokningid=" . $bokningid . ";";
        $x=0;
        
          if ($result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
              if (sqlsrv_num_rows($result) > 0) {
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
                if ($x == 0) {
                  $bektemp = $row["bekraftelse"];
                  
                 } else {
                  if ($bektemp != $row["bekraftelse"])
                      $multibek=true;
                  $bektemp = $row["bekraftelse"];
                  }
                  $x++;
                }
              }
            }  
        
        
        echo "<table width='50%' align='center'><tr><td align='center'>";
        echo "<a href='/bokningar/boka-edit.php?bokningid=" . $bokningid . "'><input type='button' value='Ändra bokning/resenärer' style='font-size:18px;'></a>";
         echo "</td><td align='center'>";
        echo "<a href='/bokningar/boka-makulera.php?bokningid=" . $bokningid . "&confirmed=false'><input type='button' value='Makulera hela bokningen'style='font-size:18px;'></a>";
        echo "</td></tr></table>";
        ?>
        <table width="90%" align="center"><tr><td valign="top" align="left">
        
        <table width="100%" cellpadding="10" align="center" style="font-size:20px">
        <tr><td>
        <?php 
        echo "<big><b>Bokning: " . $bokningprint . "</b></big>";
        echo "</td></tr><tr><td>"; 
        echo "<b>" . $resa . " " . $date . "</b>"; 
        echo "</td></tr><tr><td>";
        echo ceil($antalresande/$persperrum) . "st " . $boendealt; 
        echo "</td></tr><tr><td>";
        
        if ($gruppbokn=="TRUE")
          $pre = "20";
        else
          $pre = "10";
        
        for ($i=0;$i<=$antalresande;$i++) {
        
         $sql = "SELECT *,REPLACE(onskemal, CHAR(13) + CHAR(10), '<br/>') AS onskemal FROM Resenarer WHERE bokningid=" . $bokningid . " AND bekraftelse=" . $i . " ORDER BY resenarid;";
        
          if ($result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
              $antalpabek = sqlsrv_num_rows($result);
              $x=0;
               $prisjust = 0;
              if (sqlsrv_num_rows($result) > 0) {
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
                
             
                
                if ($x==0) {
                  
                  $prisjust = $prisjust + $row["prisjustering"];
                  
                  echo "<br><b>Bekräftelse: " . $pre . $row["bokningid"];
                  
                  if ($multibek)
                    echo "-" . $row["bekraftelse"];
                   
                  echo "</b></td></tr><tr><td>";
                  echo $row["fornamn"] . " " . $row["efternamn"] . "<br>";
                  echo $row["adress"] . "<br>";
                  if ($row["postnr"]!=0)
                    $postnr = chunk_split($row["postnr"], 3, ' ');
                  echo $postnr . " " . $row["postort"];
                  echo "</td></tr><tr><td>";
                  if ($row["telefon"]!=0) {
                    $telefon = "0" . $row["telefon"];
                    $telefon = strrev(chunk_split (strrev($telefon), 2,' '));
                    $telefon = preg_replace('/\s+/', '', $telefon, 2);
                    echo "Telefon: " . $telefon . "<br>";
                  }
                  if ($row["email"]!="") {
                    echo "E-mail: " . $row["email"];
                  }
                  echo "</td></tr><tr><td>";
                  if ($row["onskemal"]!="") {
                    echo "Önskemål: " . $row["onskemal"];
                    echo "</td></tr><tr><td>";
                    }
                  
                  echo "Påstigning:<br>" . $row["avresa"] . " kl " . ($row["avresatid"]->format('H:i'));
                  
                  if ($antalpabek>1) {
                    echo "</td></tr><tr><td>"; 
                    echo "Medresenärer:";
                    }
                $x++;
                
                } else {
                
                $prisjust = $prisjust + $row["prisjustering"];
                
                echo "<br>" . $row["fornamn"] . " " . $row["efternamn"];
                
                $x++;
                
                
                }

                
                if ($x >= $antalpabek) {
                echo "</td></tr><tr><td>";
                $pris = ($grundpris * $antalpabek) + $prisjust;
                echo "Totalt att betala (bekräftelse " . $row["bekraftelse"] . "): " . $pris . "kr.";
                
                $avbskyddbet = false;
                $summavbbetalt = 0;
                $summabetalt = 0;
                $sql2 = "SELECT * FROM Betalningar WHERE bokning=" . $bokningid . " AND bekraftelse=" . $row["bekraftelse"] . ";";
                
                    if ($result2 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
                            if (sqlsrv_num_rows($result2) > 0) {
                              while ($row2 = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC )) {
                              
                              if ($row2["avbskyddbet"]==1)
                                $avbskyddbet = true;
                              
                              $summavbbetalt = $summavbbetalt + $row2["summavb"];
                              $summabetalt = $summabetalt + $row2["summa"];
                              
                              }
                            }
                          }  
                          
                
                
                
                
                echo "<br>Kvar att betala: " . ($pris - $summabetalt) . "kr.";
                echo "<br><br>Avbeställningsskydd (" . $avbskyddpris . "kr): ";
                if ($avbskyddbet == true) {
                  echo "<b>JA</b> - betalt med " . $summavbbetalt . "kr";
                } else {
                  echo "<b>NEJ</b>";
                }
                  
                echo "</td></tr><tr><td>";
                }            
              
              
            }  
           }}
           }
        
        

        ?>
        </td></tr>  
        </table>
        
        </td><td valign="top" align="right">
        <table width="100%" cellpadding="10" align="center" style="font-size:20px">
        <tr><td>
        <?php
        
        $sql = "SELECT *,REPLACE(referens, CHAR(13) + CHAR(10), '<br/>') AS referens FROM Betalningar WHERE bokning=" . $bokningid . " ORDER BY bekraftelse, datum;";
          if ($result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
            if (sqlsrv_num_rows($result) > 0) {
              while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
              
              echo "Betalning #" . $row["betalningid"] . ".<br>Datum: " . ($row["datum"]->format('Y-m-d'));
              echo "</td></tr><tr><td>";
              echo "Betalning för " . $bokningprint . "-<b>" . $row["bekraftelse"] . "</b>";
              echo "</td></tr><tr><td>";
              echo "Summa betalt: " . $row["summa"] . "kr.";
              if ($row["avbskyddbet"] == 1)
                echo "<br>+avbeställningskydd : " . $row["summavb"] . "kr.";
              echo "</td></tr><tr><td>";
              $metod = $row["metod"];
              if ($metod == "bg")
                $metod="BankGiro/(PlusGiro)";
              echo "Betalt med " . $metod;
              if ($row["referens"] != "") 
                echo "<br>Notering: " . $row["referens"]; 
              echo "</td></tr><tr><td><br>";
              
              }
            }
          }     
        
        
        
        
        ?>
        </td></tr></table>
        </td></tr></table>
        
    </body>
</html>

<?php   
	  sqlsrv_close;
?> 
 