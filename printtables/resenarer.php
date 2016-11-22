<?php
$PageTitle = "Resenarer";
include ("../config-connect.php");
include ("../top.php");
echo "<table border=1>";
echo "<tr><td>resenarid</td><td>fornman</td><td>efternamn</td><td>avresa</td><td>avresatid</td><td>prisjustering</td><td>onskemal</td><td>adress</td><td>postnr</td><td>postort</td><td>telefon</td><td>email</td><td>resaid</td><td>bokningid</td><td>bekraftelse</td></tr>";
        $sql = "SELECT * FROM Resenarer;"; 

          $result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
          if (sqlsrv_num_rows($result) > 0) {
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
            
            echo "<tr><td>" . $row['resenarid'] . "</td><td>" . $row['fornamn'] . "</td><td>" . $row['efternamn'] . "</td><td>" . $row['avresa'] . "</td><td>" . ($row['avresatid']->format('H:i')) . "</td><td>" . $row['prisjustering'] . "</td><td>" . $row['onskemal'] . "</td><td>" . $row['adress'] . "</td><td>" . $row['postnr'] . "</td><td>" . $row['postort'] . "</td><td>" . $row['telefon'] . "</td><td>" . $row['email'] . "</td><td>" . $row['resaid'] . "</td><td>" . $row['bokningid'] . "</td><td>" . $row['bekraftelse'] . "</td></tr>";

            

            }
          }
          echo "</table>";
        sqlsrv_close();




?>