<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
</head>
<body>
<?php

  include '../../config/config.php';
  include '../../includes/db_connect.php';

  $arrTables = array();

  $sql = "SELECT * FROM information_schema.tables;";
  echo "<br>";
  echo $sql;
  echo "<br>";

  $stmt = sqlsrv_query( $conn, $sql);

  if( $stmt === false) { die( print_r( sqlsrv_errors(), true) ); }

  $i = 0;
  while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
    $arrTables[$i] = $row['TABLE_NAME'];
    echo $row['TABLE_NAME'];
    echo "<br>";
    $i++;
  }
  sqlsrv_free_stmt( $stmt);

  $max = count($arrTables);
    for ($i = 0; $i < $max; $i++) {
    $sql = "SELECT * FROM information_schema.columns WHERE table_name = '" . $arrTables[$i] . "';";
    echo "<br>";
    echo "<br>";
    echo $sql;

    $stmt = sqlsrv_query( $conn, $sql);

    if( $stmt === false) { die( print_r( sqlsrv_errors(), true) ); }

    echo "<br><br>";
    echo $stmt['TABLE_NAME'];
    echo "<br>";
    while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
      echo $row['COLUMN_NAME'];
      echo "<br>";
    }

    sqlsrv_free_stmt( $stmt);
  }

  sqlsrv_close( $conn);

 ?>
</body>
</html>
