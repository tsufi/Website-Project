<?php
// index.php

$servername = "localhost";
$username = "tsufi";
$password = "kalle69"; // Update with your password
$dbname = "combat_logs"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file']['tmp_name'];
    $content = file_get_contents($file);
    processCombatLog($content, $conn);
}

$sql = "SELECT id, title FROM encounters";
$result = $conn->query($sql);
$encounters = [];
while ($row = $result->fetch_assoc()) {
    $encounters[] = $row;
}

$conn->close();

function processCombatLog($content, $conn) {
    // Placeholder: Parse the content and extract encounter details
    $encounters = parseEncounters($content);
    foreach ($encounters as $encounter) {
        $title = $conn->real_escape_string($encounter['title']);
        $conn->query("INSERT INTO encounters (title, date) VALUES ('$title', NOW())");
        $encounterId = $conn->insert_id;

        foreach ($encounter['players'] as $player) {
            $playerName = $conn->real_escape_string($player['name']);
            $conn->query("INSERT IGNORE INTO players (name) VALUES ('$playerName')");
            $playerId = $conn->insert_id;

            $conn->query(
                "INSERT INTO encounter_details (encounter_id, player_id, damage, dps, healing, hps, dispels, interrupts, deaths, reason_for_death, time_to_wipe) 
                 VALUES ($encounterId, $playerId, {$player['damage']}, {$player['dps']}, {$player['healing']}, {$player['hps']}, {$player['dispels']}, {$player['interrupts']}, {$player['deaths']}, '{$player['reason_for_death']}', {$player['time_to_wipe']})"
            );
        }
    }
}

function parseEncounters($content) {
    return [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WoW Combat Log Analyzer</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>WoW Combat Log Analyzer</h1>

    <form action="index.php" method="post" enctype="multipart/form-data">
        <label for="file">Upload Combat Log:</label>
        <input type="file" id="file" name="file" required>
        <button type="submit">Upload</button>
    </form>

    <h2>View Encounters</h2>
    <form id="encounter-form" action="view_encounter.php" method="get">
        <label for="encounter">Select Encounter:</label>
        <select id="encounter" name="encounter">
            <?php foreach ($encounters as $encounter): ?>
                <option value="<?= $encounter['id'] ?>"><?= htmlspecialchars($encounter['title']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">View Details</button>
    </form>
</body>
</html>
