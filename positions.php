<?php
require_once 'config.php';

// Check if editing
$edit_mode = false;
$edit_position = null;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM Positions WHERE posID=$edit_id");
    $edit_position = $result->fetch_assoc();
}

// Handle Add Position
if (isset($_POST['add'])) {
    $posName = $_POST['posName'];
    $numOfPositions = $_POST['numOfPositions'];
    $posStat = $_POST['posStat'];
    $conn->query("INSERT INTO Positions (posName, numOfPositions, posStat) VALUES ('$posName', '$numOfPositions', '$posStat')");
}

// Handle Edit Position
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $posName = $_POST['posName'];
    $numOfPositions = $_POST['numOfPositions'];
    $posStat = $_POST['posStat'];
    $conn->query("UPDATE Positions SET posName='$posName', numOfPositions='$numOfPositions', posStat='$posStat' WHERE posID=$id");
}

// Handle Deactivate Position
if (isset($_GET['deactivate'])) {
    $id = $_GET['deactivate'];
    $conn->query("UPDATE Positions SET posStat='closed' WHERE posID=$id");
}

// Fetch all positions
$positions = $conn->query("SELECT * FROM Positions");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Positions Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f9f9f9;
            color: #000;
        }
        h1, h2 {
            text-align: center;
            font-size: 20px;
        }
        form {
            margin-bottom: 20px;
            padding: 10px;
            background: #fff;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #ccc;
        }
        input, select, button, a {
            margin: 5px 0;
            padding: 5px;
            font-size: 14px;
        }
        button {
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
            font-size: 14px;
        }
        th {
            font-weight: bold;
        }
        a {
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h1>Positions Management</h1>

<?php if ($edit_mode && $edit_position): ?>
<form method="post">
    <input type="hidden" name="id" value="<?= $edit_position['posID'] ?>">
    Position Name: <input type="text" name="posName" value="<?= $edit_position['posName'] ?>" required><br>
    Number of Positions: <input type="number" name="numOfPositions" value="<?= $edit_position['numOfPositions'] ?>" required><br>
    Status: 
    <select name="posStat">
        <option value="open" <?= $edit_position['posStat'] == 'open' ? 'selected' : '' ?>>Open</option>
        <option value="closed" <?= $edit_position['posStat'] == 'closed' ? 'selected' : '' ?>>Closed</option>
    </select><br>
    <button type="submit" name="edit">Update Position</button>
    <a href="positions.php">Cancel</a>
</form>
<?php else: ?>
<form method="post">
    Position Name: <input type="text" name="posName" required><br>
    Number of Positions: <input type="number" name="numOfPositions" required><br>
    Status: 
    <select name="posStat">
        <option value="open">Open</option>
        <option value="closed">Closed</option>
    </select><br>
    <button type="submit" name="add">Add Position</button>
</form>
<?php endif; ?>

<div style="text-align:center;">
    <a href="index.php">Back to Dashboard</a>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Position Name</th>
        <th>Number of Positions</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $positions->fetch_assoc()): ?>
    <tr>
        <td><?= $row['posID'] ?></td>
        <td><?= $row['posName'] ?></td>
        <td><?= $row['numOfPositions'] ?></td>
        <td><?= $row['posStat'] ?></td>
        <td>
            <a href="?edit=<?= $row['posID'] ?>">Edit</a>
            <?php if ($row['posStat'] == 'open'): ?>
                | <a href="?deactivate=<?= $row['posID'] ?>">Deactivate</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
