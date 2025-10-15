<?php
require __DIR__ . '/constants.php';

// Connect to the database
$connection = new mysqli();
if ($secure) {
    // Use SSL for secure connections
    $connection->ssl_set(null, null, null, null, null);
    $connection->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
}
$connection->real_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (mysqli_errno($connection)) {
    die(mysqli_error($connection));
}
$connection->set_charset('utf8mb4');