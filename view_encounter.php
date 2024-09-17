<?php
// view_encounter.php

$servername = "localhost";
$username = "tsufi";
$password = "kalle69"; // Update with your password
$dbname = "combat_logs"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$encounterId = intval($_GET['encounter']);
$sql = "SELECT p.name, ed.damage, ed.dps, ed.healing, ed.hps, ed.dispels, ed.interrupts, ed.deaths, ed.reason_for_death, ed.time_to_wipe 
        FROM encounter_details ed
        JOIN players p ON ed.player_id = p.id
        WHERE ed.encounter_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $encounterId);
$stmt->execute();
$result = $stmt->get_result();

$players = [];
while ($row = $result->fetch_assoc()) {
    $players[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encounter Details</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Encounter Details</h1>
    <table>
        <thead>
            <tr>
                <th>Player Name</th>
                <th>Damage</th>
                <th>DPS</th>
                <th>Healing</th>
                <th>HPS</th>
                <th>Dispels</th>
                <th>Interrupts</th>
                <th>Deaths</th>
                <th>Reason for Death</th>
                <th>Time to Wipe</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player): ?>
                <tr>
                    <td><?= htmlspecialchars($player['name']) ?></td>
                    <td><?= htmlspecialchars($player['damage']) ?></td>
                    <td><?= htmlspecialchars($player['dps']) ?></td>
                    <td><?= htmlspecialchars($player['healing']) ?></td>
                    <td><?= htmlspecialchars($player['hps']) ?></td>
                    <td><?= htmlspecialchars($player['dispels']) ?></td>
                    <td><?= htmlspecialchars($player['interrupts']) ?></td>
                    <td><?= htmlspecialchars($player['deaths']) ?></td>
                    <td><?= htmlspecialchars($player['reason_for_death']) ?></td>
                    <td><?= htmlspecialchars($player['time_to_wipe']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="export.php?encounter=<?= $encounterId ?>">Download as Google Sheets</a>
</body>
</html>
