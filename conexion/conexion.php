<?php
$servername = "localhost";
$database = "id21220287_chat";
$username = "id21220287_chats_prueba";
$password = "Operaciones*123";
// Create connection
$con = mysqli_connect($servername, $username, $password, $database);
// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
//echo "Connected successfully";
$con->set_charset('utf8');
?>

