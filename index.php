
        <?php
        $PageTitle = "Huvudmeny";
        include ("config-connect.php");
        include 'top.php';
        if ($_POST["savedeadline"] == "yes") {
        $sql = "INSERT INTO Deadlines (resaid, text, date, aktiv)
          
          VALUES (
          " . intval($_POST["resaid"]) . ", 
          '" . trim($_POST["text"]) . "', 
          '" . $_POST["date"] . "',
          " . 1 . ");";
          
          
          if (!sqlsrv_query($conn, $sql)) {
          echo "<br>Kritiskt fel. Kunde inte spara. Gå tillbaka (tillbaka pil i webbläsaren) och kontrollera att det inte står bokstäver i sifferfält eller dylikt.<br>Error creating table: ";
          die( print_r( sqlsrv_errors(), true));
        }
        

        }
        
        if ($_POST["tabortdeadline"] == "yes") {
        $sql = "UPDATE Deadlines SET
          aktiv=0
          WHERE id=" . intval($_POST["deadlineid"]) . ";";
          
          
          if (!sqlsrv_query($conn, $sql)) {
          echo "<br>Kritiskt fel. Kunde inte spara. Gå tillbaka (tillbaka pil i webbläsaren) och kontrollera att det inte står bokstäver i sifferfält eller dylikt.<br>Error creating table: ";
          die( print_r( sqlsrv_errors(), true));
        }
        

        }
        ?>
        
        <table align="center" width="80%">
        <tr><td width="40%" valign="top" align="center">
        
        
        <table align="center" width="100%">
        <tr>
        <td align="center" colspan="3" nowrap>
        <h3>Sena betalningar</h3>
        
        <?php
        
        $today;
        $today = date('Y-m-d');
        
        
        
        
        $sql = "SELECT * FROM Bokningar INNER JOIN Resor ON Bokningar.resaid = Resor.resaid WHERE Resor.aktiv=1 AND Bokningar.makulerad=0 AND ((Bokningar.betalningsdatum2 < CONVERT(date, '" . $today . "', 126)) OR (Bokningar.betalningsdatum1 < CONVERT(date, '" . $today . "', 126))) ORDER BY Bokningar.betalningsdatum2, Bokningar.bokningid;";  

        if ($result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
        
              if (sqlsrv_num_rows($result) > 0) {
                while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
                
                $pris=0;
                $antal=0;
                $grundpris = 0;
                $antal = 0;
                $prisjustering=0;
                $anmavg=0;
                
                
                
        $sql2 = "SELECT * FROM Resenarer WHERE bokningid=" . $row["bokningid"] . ";";  

        if ($result2 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
        
              if (sqlsrv_num_rows($result2) > 0) {
                while ($row2 = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC )) {
                
                $prisjustering = $prisjustering + $row2["prisjustering"];    
         
         }}}
         
         $pris = ($row["pris"] * $row["antalresande"]) + $prisjustering;
         $anmavg = ($row["anmavgpris"] * $row["antalresande"]);      
         
         $inbetalt=0;
         
         $sql3 = "SELECT * FROM Betalningar WHERE bokning=" . $row["bokningid"] . ";";  

          if ($result3 = sqlsrv_query($conn, $sql3, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
        
              if (sqlsrv_num_rows($result3) > 0) {
                while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                
                $inbetalt = $inbetalt + $row3["summa"]; 
                
         
          }}}       
           
           if ($row["betalningsdatum1"] == $row["betalningsdatum2"]) {     
                  
                  if ($inbetalt < $pris) {
                  echo "<tr><td align='left' nowrap>";
                  if ($row["gruppbokn"] == "TRUE")
                  echo "<a href='visabokning.php?bokningid=20" . $row["bokningid"] . "'>20";
                    else
                  echo "<a href='visabokning.php?bokningid=10" . $row["bokningid"] . "'>10";
                  echo $row["bokningid"] . "</a></td><td align='left' nowrap>Betalt:" . $inbetalt . " kr</td><td align='left' nowrap>" . $row["resa"] . "</td><tr>";
                  }
            
            } else {
            
                  if ($inbetalt < $anmavg) {
                  echo "<tr><td align='left' nowrap>";
                  if ($row["gruppbokn"] == "TRUE")
                  echo "<a href='visabokning.php?bokningid=20" . $row["bokningid"] . "'>20";
                    else
                  echo "<a href='visabokning.php?bokningid=10" . $row["bokningid"] . "'>10";
                  echo $row["bokningid"] . "</a></td><td align='left' nowrap>Betalt:" . $inbetalt . " kr</td><td align='left' nowrap>" . $row["resa"] . "</td><tr>";
                  }
            
            }
                
                      
                 }}}
                 
                 
                
                
              
        /*
        echo "<tr><td align='left' nowrap>";
        if ($row["gruppbokn"] == "TRUE")
        echo "<a href='visabokning.php?bokningid=20" . $row["bokningid"] . "'>20";
        else
        echo "<a href='visabokning.php?bokningid=10" . $row["bokningid"] . "'>10";
        echo $row["bokningid"] . "-" . $antalbek . "</a></td><td align='left' nowrap>" . $fornamn . " " . $efternamn . "</td><td align='left' nowrap>" . $row["resa"] . "</td><tr>";
        */
        
        
         //end for
        
       
                /*
                
                $fornamn = "";
                $efternamn = "";
                $flagnotpaid = 1;
                
                
                if ($row["betalningsdatum1"]->format('Y-m-d') != $row["betalningsdatum2"]->format('Y-m-d'))
                {
                
                  if ($row["betalningsdatum1"]->format('Y-m-d') < date('Y-m-d')) {
                    $inbetalt = 0;
                    $sql = "SELECT summa FROM Betalningar WHERE bokning=" . $row["bokningid"];
                    $result2 = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result2) > 0) {
                        while ($row2 = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC )) {
                        $inbetalt = $inbetalt + $row2["summa"];
                    }}
                    
                    if ($inbetalt < ($row["anmavpris"] * $row["antalresande"]))
                      $flagnotpaid = 0;
                  
                  }
                  
                  if ($row["betalningsdatum2"]->format('Y-m-d') < date('Y-m-d')) {
                  
                  
                    $inbetalt = 0;
                    $sql = "SELECT summa FROM Betalningar WHERE bokning=" . $row["bokningid"];
                    $result2 = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result2) > 0) {
                        while ($row2 = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC )) {
                        $inbetalt = $inbetalt + $row2["summa"];
                    }}
                    $seperatecount = 0;
                    $prisjusteringtotal = 0;
                    $price = $row["pris"];
                      
                      if ($row["resenar1"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar1"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      if ($row["resenar2"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar2"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      if ($row["resenar3"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar3"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      if ($row["resenar4"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar4"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      if ($row["resenar5"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar5"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      if ($row["resenar6"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar6"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      
                      
                      
                                          
                          $antalresande = $row["antalresande"] - $seperatecount;
                          $price = (($price * $antalresande) + $prisjusteringtotal);
                       
                        
                        
                        
                        
                        
                        
                        
             
                    

                    if ($inbetalt < $price)
                      $flagnotpaid = 0;
                      
                
                
                  }

                } else {
                
                
                if ($row["betalningsdatum2"]->format('Y-m-d') < date('Y-m-d')) {
                 
                    
                    $inbetalt = 0;
                    $sql = "SELECT summa FROM Betalningar WHERE bokning=" . $row["bokningid"];
                    $result2 = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result2) > 0) {
                        while ($row2 = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC )) {
                        $inbetalt = $inbetalt + $row2["summa"];
                    }}
                    
                    $seperatecount = 0;
                    $prisjusteringtotal = 0;
                    $price = $row["pris"];
                      
                      if ($row["resenar1"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar1"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      if ($row["resenar2"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar2"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      if ($row["resenar3"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar3"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      if ($row["resenar4"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar4"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      if ($row["resenar5"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar5"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      if ($row["resenar6"] != -1) {
                      $sql2 = "SELECT prisjustering FROM Resenarer WHERE resenarid=" . $row["resenar6"];
                        $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result3) > 0) {
                        while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                        $prisjusteringtotal = $prisjusteringtotal + $row3["prisjustering"];
                      }}}
                      
                      
                      
                                          
                   $antalresande = $row["antalresande"] - $seperatecount;
                   $price = (($price * $antalresande) + $prisjusteringtotal);
                    
                    if (intval($inbetalt) < intval($price))
                      $flagnotpaid = 0;

                
                }
                }
                
                
                
                if ($flagnotpaid == 0) {
                
                if ($row["resenar1"] != -1) {
                $sql2 = "SELECT fornamn, efternamn FROM Resenarer WHERE resenarid=" . $row["resenar1"];
                $result3 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                if (sqlsrv_num_rows($result3) > 0) {
                while ($row3 = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC )) {
                $fornamn = $row3["fornamn"];
                $efternamn = $row3["efternamn"];
                }}}
                

                echo "<tr><td align='left' nowrap>";
                if ($row["gruppbokning"] == "TRUE")
                  echo "<a href='visabokning.php?bokningid=20" . $row["bokningid"] . "'>20";
                else
                  echo "<a href='visabokning.php?bokningid=10" . $row["bokningid"] . "'>10";
                
                echo $row["bokningid"] . "</a></td><td align='left' nowrap>" . $fornamn . " " . $efternamn . "</td><td align='left' nowrap>" . $row["resa"] . "</td><tr>";

              
                }}
              } else {
              echo "<br><br>Inga sena betalningar";
              }
              }
              else {
              echo ( print_r( sqlsrv_errors(), true));
              }
              */
              
        ?>
        </table>
        
        </td><td width="5px"></td>
        <td width="10px" style="border-left: thin solid grey;"></td>
        <td width="20px">&nbsp;&nbsp;&nbsp;
        </td><td width="60%" valign="top" align="center">
        <table align="center" width="100%" cellpadding="5">
        <tr><td align="center" nowrap colspan="3">
        
        <h3>Deadlines</h3>
        
        <?php

        $expired = "";
        
        $sql = "SELECT * FROM Deadlines WHERE aktiv=1 ORDER BY date ASC;";
             $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result) > 0) {
                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
                        
                        if ($row["date"]->format('Y-m-d') <= date('Y-m-d'))
                            $expired = "style='color:red'";
                         else
                            $expired = "";
                            
                        echo "</td></tr><tr><td nowrap " . $expired . "><b>" . $row["date"]->format('j/n vW') . "</b></td><td>";
                          if ($row["resaid"]!=-1) {
                          $sql2 = "SELECT resa FROM Resor WHERE resaid=" . $row["resaid"] . ";";
                              $result2 = sqlsrv_query($conn, $sql2, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                                    if (sqlsrv_num_rows($result2) > 0) {
                                    $row2 = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC );
                                    echo $row2["resa"];                                 
                                    
                        }} else {
                        echo "Ingen resa vald";   
                        }
                        echo "</td><td nowrap><form action='index.php' name='tabortdeadline' method='POST'>" . $row["text"] . "<input type='hidden' name='tabortdeadline' value='yes'><input type='hidden' name='deadlineid' value='" . $row["id"] . "'><input style='float: right;' type='submit' value='Klar'></form>";
                        
                        }
                      }
        
        ?>
        </td></tr>
        <tr><td nowrap>
        <form action="index.php" name="nydeadline" method="POST">
        <input type="date" name="date">
        </td><td nowrap>
        <select name="resaid">
        <option value="-1">Ingen resa vald</option>
        <?php
        $sql = "SELECT resa, resaid FROM Resor WHERE aktiv=1 ORDER BY date ASC;";
             $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
                      if (sqlsrv_num_rows($result) > 0) {
                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
                        
                                              
                        echo "<option value=" . $row["resaid"] . ">" . $row["resa"] . "</option>";
                        
                        }
                      }
        ?>
        </select>
        </td><td nowrap>
        <input type="text" placeholder="Beskrivning" name="text" maxlength="180" size="30">
        <input type="hidden" value="yes" name="savedeadline">
        <input type="submit" value="Spara">
        </form>
        </td></tr>
        </table>
      
      
      </td></tr></table>
    </body>
</html>

<?php   
	  sqlsrv_close;
?> 