<?php
$PageTitle = "Betalningar";
include ("../config-connect.php");
include ("../top.php");
echo "<table border=1>";
echo "<tr><td>betalningid</td><td>datum</td><td>summa</td><td>avbskyddbet</td><td>summavb</td><td>bokning</td><td>referens</td><td>metod</td>bekraftelse</td></tr>";
        $sql = "SELECT * FROM Betalningar;"; 

          $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
          if (sqlsrv_num_rows($result) > 0) {
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
            
            echo "<tr><td>" . $row['betalningid'] . "</td><td>" . ($row['datum']->format('Y-m-d')) . "</td><td>" . $row['summa'] . "</td><td>" . $row['avbskyddbet'] . "</td><td>" . $row['summavb'] . "</td><td>" . $row['bokning'] . "</td><td>" . $row['referens'] . "</td><td>" . $row['metod'] . "</td><td>" . $row['postnr'] . "</td><td>" . $row['bekraftelse'] . "</td></tr>";

            

            }
          }
          echo "</table>";
        sqlsrv_close();




?>