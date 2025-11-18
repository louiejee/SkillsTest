<?php
// winners.php - Election Winners
require_once 'config.php';

// Calculate winners
$winners = array();
$positions = $conn->query("SELECT * FROM Positions WHERE posStat='open'");
while($posRow = $positions->fetch_assoc()) {
    $candidates = $conn->query("SELECT * FROM Candidates WHERE posID=".$posRow["posID"]." AND candStat='active'");
    $maxVotes = 0;
    $winnerCandID = null;
    
    while($candRow = $candidates->fetch_assoc()) {
        $totalVotes = $conn->query("SELECT COUNT(*) as count FROM Votes WHERE candID=".$candRow["candID"])->fetch_assoc()["count"];
        
        if ($totalVotes > $maxVotes) {
            $maxVotes = $totalVotes;
            $winnerCandID = $candRow["candID"];
        }
    }
    
    if ($winnerCandID) {
        $winnerName = $conn->query("SELECT candFName, candMName, candLName FROM Candidates WHERE candID=$winnerCandID")->fetch_assoc();
        $winners[] = array(
            "position" => $posRow["posName"],
            "winner" => $winnerName["candFName"]." ".$winnerName["candMName"]." ".$winnerName["candLName"],
            "votes" => $maxVotes
        );
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Election Winners</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; color: #000; padding: 20px; }
        h1 { text-align: center; margin-bottom: 20px; }
        .card { max-width: 500px; margin: 20px auto; background: #fff; padding: 15px; border: 1px solid #ccc; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #4CAF50; color: #fff; }
        .center-button { text-align: center; margin: 20px 0; }
        .center-button a { text-decoration: none; background: #555; color: #fff; padding: 6px 12px; border-radius: 4px; }
        .center-button a:hover { background: #333; }
    </style>
</head>
<body>

<h1>Election Winners</h1>

<div class="card">
    <table>
        <tr>
            <th>Elective Position</th>
            <th>Winner</th>
            <th>Total Votes</th>
        </tr>
        <?php foreach($winners as $winner): ?>
            <tr>
                <td><?= $winner["position"] ?></td>
                <td><?= $winner["winner"] ?></td>
                <td><?= $winner["votes"] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div style="text-align:center;">
    <a href="index.php">Back to Dashboard</a>
</div>

</body>
</html>
