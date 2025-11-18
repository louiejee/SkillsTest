<?php
include 'config.php';

// Edit mode
$edit_mode = false;
$edit_voter = null;
if(isset($_GET['edit'])){
    $edit_mode = true;
    $edit_id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM Voters WHERE voterID='$edit_id'");
    $edit_voter = $result->fetch_assoc();
}

// Add Voter
if(isset($_POST['add'])){
    $voterID = $_POST['voterID'];
    $voterPass = password_hash($_POST['voterPass'], PASSWORD_DEFAULT);
    $voterFName = $_POST['voterFName'];
    $voterMName = $_POST['voterMName'];
    $voterLName = $_POST['voterLName'];
    $voterStat = $_POST['voterStat'];
    $voted = $_POST['voted'];
    $conn->query("INSERT INTO Voters (voterID, voterPass, voterFName, voterMName, voterLName, voterStat, voted) VALUES ('$voterID', '$voterPass', '$voterFName', '$voterMName', '$voterLName', '$voterStat', '$voted')");
}

// Edit Voter
if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $voterPass = password_hash($_POST['voterPass'], PASSWORD_DEFAULT);
    $voterFName = $_POST['voterFName'];
    $voterMName = $_POST['voterMName'];
    $voterLName = $_POST['voterLName'];
    $voterStat = $_POST['voterStat'];
    $voted = $_POST['voted'];
    $conn->query("UPDATE Voters SET voterPass='$voterPass', voterFName='$voterFName', voterMName='$voterMName', voterLName='$voterLName', voterStat='$voterStat', voted='$voted' WHERE voterID='$id'");
}

// Deactivate Voter
if(isset($_GET['deactivate'])){
    $id = $_GET['deactivate'];
    $conn->query("UPDATE Voters SET voterStat='inactive' WHERE voterID='$id'");
}

// Fetch voters
$voters = $conn->query("SELECT * FROM Voters");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voters Management</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; color: #000; }
        h1 { text-align: center; font-size: 20px; }
        form {
            margin-bottom: 20px;
            padding: 10px;
            background: #fff;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #ccc;
        }
        input, select, button, a { margin: 5px 0; padding: 5px; font-size: 14px; }
        button { cursor: pointer; }
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
        th { font-weight: bold; }
        a { text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<h1>Voters Management</h1>

<?php if($edit_mode && $edit_voter): ?>
<form method="post">
    <input type="hidden" name="id" value="<?=$edit_voter['voterID']?>">
    Voter ID: <input type="text" name="voterID" value="<?=$edit_voter['voterID']?>" required><br>
    Password: <input type="password" name="voterPass" required><br>
    First Name: <input type="text" name="voterFName" value="<?=$edit_voter['voterFName']?>" required><br>
    Middle Name: <input type="text" name="voterMName" value="<?=$edit_voter['voterMName']?>"><br>
    Last Name: <input type="text" name="voterLName" value="<?=$edit_voter['voterLName']?>" required><br>
    Status:
    <select name="voterStat">
        <option value="active" <?=$edit_voter['voterStat']=='active'?'selected':''?>>Active</option>
        <option value="inactive" <?=$edit_voter['voterStat']=='inactive'?'selected':''?>>Inactive</option>
    </select><br>
    Voted:
    <select name="voted">
        <option value="n" <?=$edit_voter['voted']=='n'?'selected':''?>>No</option>
        <option value="y" <?=$edit_voter['voted']=='y'?'selected':''?>>Yes</option>
    </select><br>
    <button type="submit" name="edit">Update Voter</button>
    <a href="voters.php">Cancel</a>
</form>
<?php else: ?>
<form method="post">
    Voter ID: <input type="text" name="voterID" required><br>
    Password: <input type="password" name="voterPass" required><br>
    First Name: <input type="text" name="voterFName" required><br>
    Middle Name: <input type="text" name="voterMName"><br>
    Last Name: <input type="text" name="voterLName" required><br>
    Status:
    <select name="voterStat">
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select><br>
    Voted:
    <select name="voted">
        <option value="n" selected>No</option>
        <option value="y">Yes</option>
    </select><br>
    <button type="submit" name="add">Add Voter</button>
</form>
<?php endif; ?>

<div style="text-align:center;">
    <a href="index.php">Back to Dashboard</a>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Status</th>
        <th>Voted</th>
        <th>Actions</th>
    </tr>
    <?php while($row = $voters->fetch_assoc()): ?>
    <tr>
        <td><?=$row['voterID']?></td>
        <td><?=$row['voterFName']?> <?=$row['voterMName']?> <?=$row['voterLName']?></td>
        <td><?=$row['voterStat']?></td>
        <td><?= $row['voted']=='y' ? 'yes' : 'no' ?></td>
        <td>
            <a href="?edit=<?=$row['voterID']?>">Edit</a>
            <?php if($row['voterStat']=='active'): ?>
                | <a href="?deactivate=<?=$row['voterID']?>">Deactivate</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
