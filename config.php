<?php

$servername = "localhost";
$username = "groupcha_abiel";
$password = "THE/HUN/HACKER1";
$dbname = "groupcha_groupchat";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo nl2br("Connected successfully\n");

?>