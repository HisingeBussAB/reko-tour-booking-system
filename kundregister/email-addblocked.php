<?php
  include ("../config-connect.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN">
<HTML>
 <HEAD>
 <TITLE>Spärrlista</TITLE>
 <meta http-equiv="content-type" content="text/html" charset="UTF-8" />
 <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
 </HEAD>
 <BODY style="font-family: Arial, Helvetica, sans-serif" >


<?php	
      
      $email = $_POST["email"];
      
      $sql = "SELECT * FROM Blockemail WHERE email='" . $email . "';";

      
      
      if ($result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
        
              if (sqlsrv_num_rows($result) == 0) {
              
              $sql2 = "INSERT INTO Blockemail (email) VALUES ('" . $email . "');";

              if (sqlsrv_query($conn, $sql2)) {
                echo "<div align='center'>
                " . $email . " spärrad för utskick. <a href='index.php'>Tillbaka</a>
                </div>";
            
                } else {
                  echo "<br>Error creating post: ";
                die( print_r( sqlsrv_errors(), true));
                }


              }
              } else {
              echo ( print_r("Kritiskt databasfel"));
              die( print_r( sqlsrv_errors(), true));
              }
              
 $sql = "SELECT * FROM Blockemail;"; 

 if ($result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
        
              if (sqlsrv_num_rows($result) > 0) {   

              echo "<table>";  
                while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
                echo "<tr><td>" . $row['email'] . "</td></tr>";

                }
              echo "<tr><td><a href='index.php'>Tillbaka</a></td></tr></table>";
              }}
      
?>	   

</body>
</html>
<?php   
	  sqlsrv_close;
?> 

