
        <?php
        $PageTitle = "Bokningsläge";
        include ("config-connect.php");
        include ('top.php');
        
 
        ?>
        <script language="javascript">
        
        
        </script>
        
        <?php
  
  
        if (isset($_GET["resaid"])) {
          
          $sql = "SELECT * FROM Resor WHERE resaid=" . $_GET["resaid"];
          $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        
          if (sqlsrv_num_rows($result) > 0) {
            $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC );
            
            $resaname = $row["resa"];
            $boendealt1 = $row["boendealt1"];
            $boendealt1antal = $row["boendealt1antal"];
            $reserverade1 = $row["reserverade1"];
            $boendealt2 = $row["boendealt2"];
            $boendealt2antal = $row["boendealt2antal"];
            $reserverade2 = $row["reserverade2"];
            $boendealt3 = $row["boendealt3"];
            $boendealt3antal = $row["boendealt3antal"];
            $reserverade3 = $row["reserverade3"];
            $boendealt4 = $row["boendealt4"];
            $boendealt4antal = $row["boendealt4antal"];
            $reserverade4 = $row["reserverade4"];
            $boendealt5 = $row["boendealt5"];
            $boendealt5antal = $row["boendealt5antal"];
            $reserverade5 = $row["reserverade5"];

            
            
  
          } else {
          die ( print_r("Något är fel, resan existerar inte. <a href='index.php'>Tillbaka</a>"));
          }
          
          $sql = "SELECT * FROM Bokningar WHERE resaid=" . $_GET["resaid"] . " AND makulerad='false';";
          $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
          
          $resenarer = 0;
          $bokade1 = 0;
          $bokade2 = 0;
          $bokade3 = 0;
          $bokade4 = 0;
          $bokade5 = 0;
          
          if (sqlsrv_num_rows($result) > 0) {
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
              
              $persperrum=$row["persperrum"];
              $resenarer = $resenarer + $row["antalresande"];
              
              if ($row["boendealtkod"] == 1)
                $bokade1 = $bokade1 + ceil(($row["antalresande"]/$persperrum));
              
              if ($row["boendealtkod"] == 2)              
                $bokade2 = $bokade2 + ceil(($row["antalresande"]/$persperrum));
                
              if ($row["boendealtkod"] == 3)              
                $bokade3 = $bokade3 + ceil(($row["antalresande"]/$persperrum));
              
              if ($row["boendealtkod"] == 4)              
                $bokade4 = $bokade4 + ceil(($row["antalresande"]/$persperrum));
                
              if ($row["boendealtkod"] == 5)              
                $bokade5 = $bokade5 + ceil(($row["antalresande"]/$persperrum));
              
              
              
              
              
              }

        
          }
        } else {
          echo "<big>Inga bokningar på resa.</big></body></html>";
          exit;
          
        }
        
        
        
        ?>
        
        <table cellpadding="10" align="center">
        <tr>
        <td colspan="4" align="center">
        <br><big><big><big><b><?php echo $resaname; ?></b></big></big></big><br>      
        </td>
        
        </tr>
        
        
        <tr>
        <td colspan="2" align="right">
        <input type="button" class="no-print" value="Påstigningslista" style="font-size:18px;" onclick="window.open('http://bokningar.rekoresor.se/pastigningslista.php?resaid=<?php echo $_GET["resaid"]; ?>', '', 'width=800,height=700');">
 
        
        </td><td colspan="2" align="left">
        <input type="button" class="no-print" value="Rumslista" style="font-size:18px;" onclick="window.open('http://bokningar.rekoresor.se/rumslista.php?resaid=<?php echo $_GET["resaid"]; ?>', '', 'width=800,height=700');">
        
        
        </td>
        
        </tr>
        </table>
        <table cellpadding="10" align="center">
        
        <tr>
        <td>
        <big><b>Antal resande: <?php echo $resenarer; ?></b></big>
        </td>
        <?php if ($boendealt1!="") echo "<td><big>" . $boendealt1 . "</big></td>"; ?>
        <?php if ($boendealt2!="") echo "<td><big>" . $boendealt2 . "</big></td>"; ?>
        <?php if ($boendealt3!="") echo "<td><big>" . $boendealt3 . "</big></td>"; ?>
        <?php if ($boendealt4!="") echo "<td><big>" . $boendealt4 . "</big></td>"; ?>
        <?php if ($boendealt5!="") echo "<td><big>" . $boendealt5 . "</big></td>"; ?>
        
        </tr><tr>
        
        <td>
        Bokade rum
        </td>
        
        <?php if ($boendealt1!="") echo "<td><big>" . $boendealt1antal . "</big></td>"; ?>
        <?php if ($boendealt2!="") echo "<td><big>" . $boendealt2antal . "</big></td>"; ?>
        <?php if ($boendealt3!="") echo "<td><big>" . $boendealt3antal . "</big></td>"; ?>
        <?php if ($boendealt4!="") echo "<td><big>" . $boendealt4antal . "</big></td>"; ?>
        <?php if ($boendealt5!="") echo "<td><big>" . $boendealt5antal . "</big></td>"; ?>
        
        </tr><tr>
        
        <td>
        <big>Sålda</big>
        </td>
        <?php
        if ($boendealt1!="") {
          echo "<td><big>" . $bokade1;
          if ($reserverade1>0)
            echo " (+" . $reserverade1 . ")";
          echo "</big></td>";
          }
        if ($boendealt2!="") {
          echo "<td><big>" . $bokade2;
          if ($reserverade2>0)
            echo " (+" . $reserverade2 . ")";
          echo "</big></td>";
          }
        if ($boendealt3!="") {
          echo "<td><big>" . $bokade3;
          if ($reserverade3>0)
            echo " (+" . $reserverade3 . ")";
          echo "</big></td>";
          }
        if ($boendealt4!="") {
          echo "<td><big>" . $bokade4;
          if ($reserverade4>0)
            echo " (+" . $reserverade4 . ")";
          echo "</big></td>";
          }
        if ($boendealt5!="") {
          echo "<td><big>" . $bokade5;
          if ($reserverade5>0)
            echo " (+" . $reserverade5 . ")";
          echo "</big></td>";
          }
        ?>
        
        
        </tr><tr>
        
        <td>
        <big><b>Kvar att sälja</b></big>
        </td>
        
        <?php if ($boendealt1!="") echo "<td><big>" . ($boendealt1antal - $bokade1 - $reserverade1) . "</big></td>"; ?>
        <?php if ($boendealt2!="") echo "<td><big>" . ($boendealt2antal - $bokade2 - $reserverade2) . "</big></td>"; ?>
        <?php if ($boendealt3!="") echo "<td><big>" . ($boendealt3antal - $bokade3 - $reserverade3) . "</big></td>"; ?>
        <?php if ($boendealt4!="") echo "<td><big>" . ($boendealt4antal - $bokade4 - $reserverade4) . "</big></td>"; ?>
        <?php if ($boendealt5!="") echo "<td><big>" . ($boendealt5antal - $bokade5 - $reserverade5) . "</big></td>"; ?>
        
        
        </tr>
        </table>
        <table cellpadding="7" align="center">
        <tr><td colspan="6" align="center" cellpadding="0"><hr>
        
        <?php
        
        if (isset($_GET["orderby"]))
          $orderbynum = intval($_GET["orderby"]);
        else
          $orderbynum = 1;
        
        switch ($orderbynum) {
          case 2:
        $orderby = "efternamn";
        break;
          case 3:
        $orderby = "boendealt ASC";
        break;
          case 4:
        $orderby = "avresatid";
        break;
        default:
        $orderby = "Bokningar.bokningid";
      }
      
         echo "<tr><td>";
             
             if (!isset($_GET["orderby"]))
              echo "<u>Bokn</u>";
             else
              echo "<a href='http://bokningar.rekoresor.se/bokningslage.php?resaid=" . $_GET["resaid"] . "'>Bokn&darr;</a>";
             
             echo "</td><td>";
             
             if ($_GET["orderby"] == 2)
              echo "<u>Namn</u>";
             else
              echo "<a href='http://bokningar.rekoresor.se/bokningslage.php?resaid=" . $_GET["resaid"] . "&orderby=2'>Namn&darr;</a>";
             
             echo "</td><td>";
             
             if ($_GET["orderby"] == 3)
              echo "<u>Boende</u>";
             else
              echo "<a href='http://bokningar.rekoresor.se/bokningslage.php?resaid=" . $_GET["resaid"] . "&orderby=3'>Boende&darr;</a>";
             
             echo "</td><td>Telefon</td><td>Önskemål</td><td>";
             
             if ($_GET["orderby"] == 4)
              echo "<u>Avresa</u>";
             else
              echo "<a href='http://bokningar.rekoresor.se/bokningslage.php?resaid=" . $_GET["resaid"] . "&orderby=4'>Avresa&darr;</a>";
              
            echo "</td></tr>";
       
        
      $sql = "SELECT *,REPLACE(onskemal, CHAR(13) + CHAR(10), '<br/>') AS onskemal FROM Bokningar INNER JOIN Resenarer ON Resenarer.bokningid = Bokningar.bokningid WHERE Bokningar.resaid=" . $_GET["resaid"] . " AND Bokningar.makulerad='false' ORDER BY " . $orderby . ";";

          $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
 
         
          if (sqlsrv_num_rows($result) > 0) {
          
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
            
            
        if ($row["gruppbokn"]=="TRUE") 
          $bokningid = "<a href='/visabokning.php?bokningid=20" . $row["bokningid"] . "'>20" . $row["bokningid"] . "</a>";
        else
          $bokningid = "<a href='/visabokning.php?bokningid=10" . $row["bokningid"] . "'>10" . $row["bokningid"] . "</a>";
        
        if ($row["telefon"]==0) 
          $telefon = "-";
        else
          $telefon = "0" . $row["telefon"];
        
        
               
          
          $antalrumisallskap = "";
          if ($row["persperrum"] < $row["antalresande"])
            {
            $antalrumisallskap = ceil(($row["antalresande"]/$row["persperrum"]));
            $antalrumisallskap = $antalrumisallskap . "st ";
            
            }
          

          echo "<tr><td colspan='6' style='padding:0px;'><hr></td></tr><tr><td nowrap>" . $bokningid . "</td><td nowrap>" . $row["fornamn"] . " " . $row["efternamn"] . "</td><td nowrap>" . $antalrumisallskap . $row["boendealt"] . "</td><td nowrap>" . $telefon . "</td><td>" . $row["onskemal"] . "</td><td nowrap>" . $row["avresatid"]->format('H:i') . " " . $row["avresa"] . "</td></tr>";
        
        }
        
        }
        
        ?>
        
        </table>
        
        
        
        
    </body>
</html>

<?php   
	  sqlsrv_close;
?> 
 