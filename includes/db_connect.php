<?php

  $connectionInfo = array("UID" => "DB_USER", "pwd" => "DB_PASSWORD", "Database" => "DB_NAME", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
  $serverName = "DB_SERVERNAME";
  $conn = sqlsrv_connect($serverName, $connectionInfo);

?>
