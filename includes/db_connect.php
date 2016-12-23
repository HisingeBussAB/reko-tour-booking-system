<?php

  if (substr( DB_SERVERNAME, 0, 4 ) === "tcp:") {
    $connectionInfo = array("UID" => DB_USER, "pwd" => DB_PASSWORD, "Database" => DB_NAME, "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
  } else {
    $connectionInfo = array("UID" => DB_USER, "pwd" => DB_PASSWORD, "Database" => DB_NAME);
  }

  $conn = sqlsrv_connect(DB_SERVERNAME, $connectionInfo);

  if( !$conn) {
    echo "Connection could not be established.<br />";
    die( print_r( sqlsrv_errors(), true));
  }

?>
