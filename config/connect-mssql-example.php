<?php


        $serverName = "SERVER"; //serverName\instanceName
        $connectionInfo = array( "Database"=>"yourDB", "UID"=>"yourDBusername", "PWD"=>"yourDBpassword");
        $conn = sqlsrv_connect( $serverName, $connectionInfo);

?>