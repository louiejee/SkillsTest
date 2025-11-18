<?php
require_once 'config.php';

// Calculate results
$results = [];
$positions = $conn->query("SELECT * FROM Positions WHERE posStat='open'");
while ($pos = $positions->fetch_assoc()) {
    $results[$pos["posID"]] = [
        "position" => $pos["posName"],
        "candidates" => []
    ];
    
    $cands = $conn->query("SELECT * FROM Candidates WHERE posID=".$pos["posID"]." AND candStat='active'");
    while ($cand = $cands->fetch_assoc()) {
        $votes = $conn->query("SELECT COUNT(*) as count FROM Votes WHERE candID=".$cand["candID"])->fetch_assoc()["count"];
        $voters = $conn->query("SELECT COUNT(*) as count FROM Voters WHERE voterStat='active' AND voted='y'")->fetch_assoc()["count"];
        $perc = $voters > 0 ? round(($votes / $voters) * 100, 2) : 0;
        
        $results[$pos["posID"]]["candidates"][] = [
            "name" => $cand["candFName"]." ".$cand["candMName"]." ".$cand["candLName"],
            "votes" => $votes,
            "perc" => $perc
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Election Results</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; color: #000; padding: 20px; }
        h1, h3 { text-align: center; margin-bottom: 20px; }
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

<h1>Election Results</h1>

<?php foreach($results as $data): ?>
    <div class="card">
        <h3><?= $data["position"] ?></h3>
        <table>
            <tr><th>Candidate</th><th>Votes</th><th>%</th></tr>
            <?php foreach($data["candidates"] as $cand): ?>
                <tr>
                    <td><?= $cand["name"] ?></td>
                    <td><?= $cand["votes"] ?></td>
                    <td><?= $cand["perc"] ?>%</td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endforeach; ?>

<div style="text-align:center;">
    <a href="index.php">Back to Dashboard</a>
</div>

</body>
</html>
