<?php

/**
 * Rekå Resor Bokningssystem
 * @author    Håkan Arnoldson
 */

namespace RekoBooking\classes;

use RekoBooking\classes\DB;
use RekoBooking\classes\DBError;

$pdo = DB::get();
$status = $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
echo $status;