<?php
require_once 'config.php';
session_start();

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: vote.php");
    exit;
}

// Login handling
$alreadyVoted = false;
if (!isset($_SESSION['voterID'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
        $voterID = $_POST['voterID'];
        $voterPass = $_POST['voterPass'];

        $sql = "SELECT * FROM Voters WHERE voterID='$voterID' AND voterStat='active'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($voterPass, $row['voterPass'])) {
                $_SESSION['voterID'] = $voterID;
                $alreadyVoted = $row['voted'] == 'y';
                header("Location: vote.php");
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Voter not found or inactive.";
        }
    }
} else {
    $voterID = $_SESSION['voterID'];
    $voterRow = $conn->query("SELECT * FROM Voters WHERE voterID='$voterID'")->fetch_assoc();
    $alreadyVoted = $voterRow['voted'] == 'y';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_vote'])) {
        if (!$alreadyVoted) {
            $votes = $_POST['votes'];
            foreach ($votes as $posID => $candID) {
                if ($candID != '') {
                    $sql = "INSERT IGNORE INTO Votes (posID, voterID, candID) VALUES ('$posID', '$voterID', '$candID')";
                    $conn->query($sql);
                }
            }
            $conn->query("UPDATE Voters SET voted='y' WHERE voterID='$voterID'");
            $alreadyVoted = true;
            echo "<script>alert('Vote submitted successfully!'); window.location.href='results.php';</script>";
            exit;
        } else {
            $error = "You have already voted.";
        }
    }

    if (!$alreadyVoted) {
        $positions = $conn->query("SELECT * FROM Positions WHERE posStat='open'");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voting System</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f9f9f9; color: #000; }
        h1, h2 { text-align: center; margin-bottom: 20px; }
        .card { max-width: 400px; margin: 20px auto; background: #fff; padding: 20px; border: 1px solid #ccc; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        label, select, input, button { display: block; width: 100%; margin: 8px 0; padding: 8px; font-size: 14px; }
        button { cursor: pointer; background-color: #4CAF50; color: #fff; border: none; border-radius: 4px; }
        button:hover { background-color: #45a049; }
        .center-button { text-align: center; margin: 20px 0; }
        .center-button a { display: inline-block; text-decoration: none; background: #555; color: #fff; padding: 8px 16px; border-radius: 4px; margin: 0 5px; }
        .center-button a:hover { background: #333; }
        .error { color: red; text-align: center; }
    </style>
</head>
<body>

<h1>Voting System</h1>

<?php if (!isset($_SESSION['voterID'])): ?>
    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

   <div style="text-align:center;">
    <a href="index.php">Back to Dashboard</a>
</div>

    <div class="card">
        <form method="post">
            <input type="text" name="voterID" placeholder="Voter ID" required>
            <input type="password" name="voterPass" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>

<?php else: ?>
    <div class="center-button">
        <a href="index.php">Back to Dashboard</a>
        <a href="vote.php?logout=1">Logout</a>
    </div>

    <h2>Voting Interface</h2>
    <p style="text-align:center;">Welcome, <strong><?php echo $_SESSION['voterID']; ?></strong>!</p>

    <?php if($alreadyVoted): ?>
        <div class="card" style="text-align:center; color:red;">
            <p>You have already voted. You cannot vote again.</p>
        </div>
    <?php else: ?>
        <div class="card">
            <form method="post">
                <?php while($posRow = $positions->fetch_assoc()): ?>
                    <label for="pos<?=$posRow['posID']?>"><?=$posRow['posName']?></label>
                    <select name="votes[<?=$posRow['posID']?>]" id="pos<?=$posRow['posID']?>">
                        <option value="">-- Select Candidate --</option>
                        <?php
                        $candidates = $conn->query("SELECT * FROM Candidates WHERE posID=".$posRow['posID']." AND candStat='active'");
                        while($candRow = $candidates->fetch_assoc()):
                        ?>
                            <option value="<?=$candRow['candID']?>"><?=$candRow['candFName']?> <?=$candRow['candMName']?> <?=$candRow['candLName']?></option>
                        <?php endwhile; ?>
                    </select>
                <?php endwhile; ?>
                <button type="submit" name="submit_vote">Submit Vote</button>
            </form>
        </div>
    <?php endif; ?>

<?php endif; ?>

</body>
</html>
