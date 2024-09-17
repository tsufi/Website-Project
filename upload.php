<?php
include '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['log_file'])) {
    $raid_id = $_POST['raid'];
    $difficulty = $_POST['difficulty'];
    $file = $_FILES['log_file'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];

    // Process the file and extract data
    // Example data
    $total_damage = 10000;
    $dps = 500;
    $total_healing = 5000;
    $hps = 250;
    $deaths = 2;
    $death_reason = "Boss Mechanic";

    $stmt = $conn->prepare("INSERT INTO logs (user_id, raid_id, difficulty, file_name, total_damage, dps, total_healing, hps, deaths, death_reason) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissiiissi", $_SESSION['user_id'], $raid_id, $difficulty, $file_name, $total_damage, $dps, $total_healing, $hps, $deaths, $death_reason);
    if ($stmt->execute()) {
        echo "Log uploaded successfully.";
    } else {
        echo "Log upload failed.";
    }
}
?>

<form action="upload.php" method="post" enctype="multipart/form-data">
    <label for="raid">Select Raid:</label>
    <select name="raid" id="raid">
        <?php
        $query = "SELECT id, raid_name FROM raids";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['id'] . '">' . $row['raid_name'] . '</option>';
        }
        ?>
    </select><br>

    <label for="difficulty">Difficulty:</label>
    <select name="difficulty" id="difficulty">
        <option value="Normal">Normal</option>
        <option value="Heroic">Heroic</option>
        <option value="Mythic">Mythic</option>
    </select><br>

    <label for="log_file">Log File:</label>
    <input type="file" id="log_file" name="log_file" accept=".txt" required><br>

    <button type="submit">Upload Log</button>
</form>
