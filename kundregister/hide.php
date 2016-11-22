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
      
      $typ = $_GET["src"];
      $id = $_GET["id"];
      
      if ($typ == "P")      
      $sql = "INSERT INTO Hidden (programbid) VALUES (" . $id . ");";

      if ($typ == "R")       
      $sql = "INSERT INTO Hidden (resenarid) VALUES (" . $id . ");";


      if (sqlsrv_query($conn, $sql)) {
                echo "<div align='center'>
                " . $typ . $id . " spärrad för utskick. <a href='index.php'>Tillbaka</a>
                </div>";
            
                } else {
                  echo "<br>Error creating post: ";
                die( print_r( sqlsrv_errors(), true));
                }      
   

$sql = "SELECT * FROM Hidden;"; 

 if ($result = sqlsrv_query($conn, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ))) {
        
              if (sqlsrv_num_rows($result) > 0) {   

              echo "<table>";  
                while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC )) {
                echo "<tr><td>" . $row['programbid'] . " | " . $row['resenarid'] ."</td></tr>";

                }
              echo "<tr><td><a href='index.php'>Tillbaka</a></td></tr></table>";
              }}

?>  

</body>
</html>
<?php   
	  sqlsrv_close;
?> 
