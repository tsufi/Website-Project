<?php
// export.php

$servername = "localhost";
$username = "tsufi";
$password = "kalle69"; // Update with your password
$dbname = "combat_logs"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$encounterId = intval($_GET['encounter']);
$sql = "SELECT p.name, ed.damage, ed.dps, ed.healing, ed.hps, ed.disp
