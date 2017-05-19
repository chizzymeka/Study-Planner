<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "p00702";
// Create connection
$mysqli = new mysqli($servername, $username, $password, $dbname);
/* Commented out: if statement, used to test database connections
// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
echo "Connected successfully";
*/