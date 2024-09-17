<?php
include '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="../css/styles.css">
    <title>View Logs</title>
</head>
<body>
    <h1>View Raid Logs</h1>
    <form action="view_logs.php" method="get">
        <label for="raid">Select Raid:</label>
        <select name="raid" id="raid">
            <option value="">All Raids</option>
            <?php
            $query = "SELECT id, raid_name FROM raids";
            $result = $conn->query($query);
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['id'] . '">' . $row['raid_name'] . '</option>';
            }
            ?>
        </select><br>

        <label for="difficulty">Select Difficulty:</label>
        <select name="difficulty" id="difficulty">
            <option value="">All Difficulties</option>
            <option value="Normal">Normal</option>
            <option value="Heroic">Heroic</option>
            <option value="Mythic">Mythic</option>
        </select><br>

        <button type="submit">Filter</button>
        <button type="submit" name="export" value="true">Export to CSV</button>
    </form>

    <?php
    $raidFilter = isset($_GET['raid']) ? $_GET['raid'] : '';
    $difficultyFilter = isset($_GET['difficulty']) ? $_GET['difficulty'] : '';

    $query = "SELECT logs.*, raids.raid_name FROM logs 
              JOIN raids ON logs.raid_id = raids.id WHERE 1=1";

    if ($raidFilter) {
        $query .= " AND raid_id = " . intval($raidFilter);
    }

    if ($difficultyFilter) {
        $query .= " AND difficulty = '" . $conn->real_escape_string($difficultyFilter) . "'";
    }

    $result = $conn->query($query);

    if (isset($_GET['export']) && $_GET['export'] == 'true') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=logs.csv');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['File Name', 'Raid Name', 'Difficulty', 'Total Damage', 'DPS', 'Total Healing', 'HPS', 'Deaths', 'Reason for Death']);

        while ($row = $result->fetch_assoc()) {
            fputcsv($output, [
                $row['file_name'],
                $row['raid_name'],
                $row['difficulty'],
                $row['total_damage'],
                $row['dps'],
                $row['total_healing'],
                $row['hps'],
                $row['deaths'],
                $row['death_reason']
            ]);
        }
        fclose($output);
        exit();
    } else {
        while ($row = $result->fetch_assoc()) {
            echo "<p>Log: " . $row['file_name'] . " - Raid: " . $row['raid_name'] . " - Difficulty: " . $row['difficulty'] . "<br>";
            echo "Total Damage: " . $row['total_damage'] . " - DPS: " . $row['dps'] . "<br>";
            echo "Total Healing: " . $row['total_healing'] . " - HPS: " . $row['hps'] . "<br>";
            echo "Deaths: " . $row['deaths'] . " - Reason for Death: " . $row['death_reason'] . "</p><br>";
        }
    }
    ?>
</body>
</html>
