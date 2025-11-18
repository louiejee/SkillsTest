<?php
include 'config.php';

// Check if editing
$edit_mode = false;
$edit_candidate = null;
if(isset($_GET['edit'])){
    $edit_mode = true;
    $edit_id = $_GET['edit'];
    $result = $conn->query("SELECT c.*, p.posName
                            FROM Candidates c
                            JOIN Positions p ON c.posID=p.posID
                            WHERE c.candID=$edit_id");
    $edit_candidate = $result->fetch_assoc();
}

// Handle Add Candidate
if(isset($_POST['add'])){
    $candFName = $_POST['candFName'];
    $candMName = $_POST['candMName'];
    $candLName = $_POST['candLName'];
    $posID = $_POST['posID'];
    $candStat = $_POST['candStat'];
    $conn->query("INSERT INTO Candidates (candFName, candMName, candLName, posID, candStat) VALUES ('$candFName', '$candMName', '$candLName', '$posID', '$candStat')");
}

// Handle Edit Candidate
if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $candFName = $_POST['candFName'];
    $candMName = $_POST['candMName'];
    $candLName = $_POST['candLName'];
    $posID = $_POST['posID'];
    $candStat = $_POST['candStat'];
    $conn->query("UPDATE Candidates SET candFName='$candFName', candMName='$candMName', candLName='$candLName', posID='$posID', candStat='$candStat' WHERE candID=$id");
}

// Handle Deactivate Candidate
if(isset($_GET['deactivate'])){
    $id = $_GET['deactivate'];
    $conn->query("UPDATE Candidates SET candStat='inactive' WHERE candID=$id");
}

// Fetch positions and candidates
$positions = $conn->query("SELECT * FROM Positions WHERE posStat='open'");
$candidates = $conn->query("SELECT c.*, p.posName
                            FROM Candidates c
                            JOIN Positions p ON c.posID=p.posID");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Candidates Management</title>
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

<h1>Candidates Management</h1>

<?php if($edit_mode && $edit_candidate): ?>
<form method="post">
    <input type="hidden" name="id" value="<?=$edit_candidate['candID']?>">
    First Name: <input type="text" name="candFName" value="<?=$edit_candidate['candFName']?>" required><br>
    Middle Name: <input type="text" name="candMName" value="<?=$edit_candidate['candMName']?>" required><br>
    Last Name: <input type="text" name="candLName" value="<?=$edit_candidate['candLName']?>" required><br>
    Position:
    <select name="posID">
        <?php
        $positions->data_seek(0); // Reset pointer to start
        while($p = $positions->fetch_assoc()): ?>
            <option value="<?=$p['posID']?>" <?=$p['posID']==$edit_candidate['posID']?'selected':''?>><?=$p['posName']?></option>
        <?php endwhile; ?>
    </select><br>
    Status:
    <select name="candStat">
        <option value="active" <?=$edit_candidate['candStat']=='active'?'selected':''?>>Active</option>
        <option value="inactive" <?=$edit_candidate['candStat']=='inactive'?'selected':''?>>Inactive</option>
    </select><br>
    <button type="submit" name="edit">Update Candidate</button>
    <a href="candidates.php">Cancel</a>
</form>
<?php else: ?>
<form method="post">
    First Name: <input type="text" name="candFName" required><br>
    Middle Name: <input type="text" name="candMName" required><br>
    Last Name: <input type="text" name="candLName" required><br>
    Position:
    <select name="posID">
        <?php
        $positions->data_seek(0); // Reset pointer to start
        while($p = $positions->fetch_assoc()): ?>
            <option value="<?=$p['posID']?>"><?=$p['posName']?></option>
        <?php endwhile; ?>
    </select><br>
    Status:
    <select name="candStat">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select><br>
    <button type="submit" name="add">Add Candidate</button>
</form>
<?php endif; ?>

<div style="text-align:center;">
    <a href="index.php">Back to Dashboard</a>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Position</th>
        <th>Status</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $candidates->fetch_assoc()): ?>
    <tr>
        <td><?=$row['candID']?></td>
        <td><?=$row['candFName']?> <?=$row['candMName']?> <?=$row['candLName']?></td>
        <td><?=$row['posName']?></td>
        <td><?=$row['candStat']?></td>
        <td>
            <a href="?edit=<?=$row['candID']?>">Edit</a>
            <?php if($row['candStat']=='active'): ?>
                | <a href="?deactivate=<?=$row['candID']?>">Deactivate</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
