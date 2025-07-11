<?php
include 'db_connect.php';
include 'user_check.php';

$user_id = $_SESSION['id'];

$result = mysqli_query($conn, "SELECT id, stockName FROM stockTable");
$bal = mysqli_query($conn, "SELECT balance FROM users WHERE id = $user_id");
$money = number_format(mysqli_fetch_assoc($bal)['balance'], 2);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Stocks</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="stock.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="chart-page">

<!-- Header -->
    <header class="chart-header">
        <h1 class="stockchrt">Stock Chart Viewer</h1>
        <div class="header-buttons">
        <?php echo ($_SESSION['isAdmin']) ? '<a href="admin.php" class="back-btn" id = "isAdminBa">Admin Panel</a>' : '';?>
        <a href="stocks.php" class="back-btn">Available Stocks</a>
        <a href="portfolio.php" class="back-btn">Portfolio</a>
        <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
    </header>
<h1>Available Stocks</h1>

<?php while ($row = mysqli_fetch_assoc($result)): ?>
    <?php
    $stock_id = $row['id'];
    $stock_name = $row['stockName'];

    $hist_result = mysqli_query($conn,
        "SELECT value FROM stockHistory
            WHERE stockId = $stock_id
            ORDER BY recordedAt DESC LIMIT 50");

    $history = [];
    while ($h = mysqli_fetch_assoc($hist_result)) {
        $history[] = $h['value'];
    }

    $history = array_reverse($history);
    $chart_id = "chart_$stock_id";
    $chart_data = json_encode($history);
    $latest_value = end($history);
    ?>
    
    <div class="stock-box">
        <h3 class="chart-title"><?= htmlspecialchars($stock_name) ?> (Php<?= number_format($latest_value, 2) ?>)</h3>
        <canvas id="<?= $chart_id ?>"></canvas>
        <form action="buy.php" method="POST" class="cash-form">
            <input type="hidden" name="stock_id" value="<?= $stock_id ?>">
            <input type="number" name="amount" step="1" placeholder="Current Balance: ₱ <?= $money ?>" style="margin-top: 10px;" required>
            <button type="submit">Buy</button>
        </form>
    </div>

    <script>
    const ctx<?= $stock_id ?> = document.getElementById("<?= $chart_id ?>").getContext('2d');
    new Chart(ctx<?= $stock_id ?>, {
        type: 'line',
        data: {
            labels: Array(<?= count($history) ?>).fill(""),
            datasets: [{
                data: <?= $chart_data ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0,123,255,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                x: { display: false },
                y: { beginAtZero: false }
            }
        }
    });
    </script>
<?php endwhile; ?>
<br>
</body>
</html>