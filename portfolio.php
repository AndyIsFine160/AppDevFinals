<?php
include 'db_connect.php';
include 'user_check.php';

$user_id = $_SESSION['id'];

$query = "
    SELECT p.stockIdf, s.stockName, s.stockValue, p.amountSpent, p.buyPrice
    FROM portfolios p
    JOIN stockTable s ON p.stockIdf = s.id
    WHERE p.userIdf = $user_id
";


$result = mysqli_query($conn, $query);
$bal = mysqli_query($conn, "SELECT balance FROM users WHERE id = $user_id");    
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Portfolio</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="stock.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        table { width: 75%; border-collapse: collapse; margin: 0 auto;}
        th, td { padding: 8px; border: 1px solid #ccc; text-align: center; }
        td:nth-child(6) { width: 30%; }
        .stock-box { margin: 0 auto; width: auto;}
        .noBorder {border: 0px #FFFFFF;}
    </style>
</head>
<body>
<header class="chart-header">
        <h1 class="stockchrt">Portfolio</h1>
        <div class="header-buttons">
        <?php echo ($_SESSION['isAdmin']) ? '<a href="admin.php" class="back-btn" id = "isAdminBa">Admin Panel</a>' : '';?>
        <a href="stocks.php" class="back-btn">Available Stocks</a>
        <a href="portfolio.php" class="back-btn">Portfolio</a>
        <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" class="logout-btn">Logout</button>
        </form>
    </div>
</header>
<br/>
<table>
<tr class = "noBorder">
    <td class = "noBorder">
  <form class="cash-form" action="cash_in.php" method="post" onsubmit="return confirm('Confirm cash in?')">
    <h3>Cash In</h3>
    <input type="number" name="amount" placeholder="Enter amount" required min="1" step="0.01">
    <button type="submit">Cash In</button>
  </form>
</td>
<td class = "noBorder">
  <form class="cash-form" action="cash_out.php" method="post" onsubmit="return confirm('Confirm cash out?')">
    <h3>Cash Out</h3>
    <input type="number" name="amount" placeholder="Enter amount" required min="1" step="0.01">
    <button type="submit">Cash Out</button>
  </form>
</td>
<td class = "noBorder">
  <div class="cash-form">
    <h3>Available Balance</h3>
    <p style="font-size: 18px; font-weight: bold; color:(<?=$current_price > $stock_id ?>) ? '#00FF00' : '#FF0000';">
        ₱<?= number_format(mysqli_fetch_assoc($bal)['balance'], 2) ?>
    </p>
    <br>
    </div>
</td>
</tr>
</table>

<h2>My Portfolio</h2>

<table>
    <tr>
        <th>Stock</th>
        <th>Purchase Value</th>
        <th>Current Value</th>
        <th>Amount Spent</th>
        <th>Sell Amount</th>
        <th>Chart</th>
        <th>Action</th>
    </tr>

<?php while ($row = mysqli_fetch_assoc($result)): ?>
    <?php
        $stock_id = $row['stockIdf'];
        $stock_name = $row['stockName'];
        $amount_spent = $row['amountSpent'];
        $buy_price = $row['buyPrice'];
        $current_price = $row['stockValue'];

        $shares = $buy_price > 0 ? $amount_spent / $buy_price : 0;
        $current_value = $shares * $current_price;

        $hist_query = "SELECT value FROM stockHistory WHERE stockId = $stock_id ORDER BY recordedAt DESC LIMIT 25";
        $hist_result = mysqli_query($conn, $hist_query);
        $history = [];

        while ($h = mysqli_fetch_assoc($hist_result)) {
            $history[] = $h['value'];
        }

        $history = array_reverse($history);
        $chart_id = "chart_$stock_id";
        $chart_data = json_encode($history);
        $latest_value = end($history);
    ?>

    <tr>
        <td><?= htmlspecialchars($stock_name) ?></td>
        <td><?= number_format($buy_price, 2) ?></td>
        <td><?= number_format($current_price, 2) ?></td>
        <td><?= number_format($amount_spent, 2) ?></td>
        <td>₱<?= number_format($current_value, 2) ?></td>
        <td><div class="stock-box">
        <h3 class="chart-title"><?= htmlspecialchars($stock_name) ?> (Php<?= number_format($latest_value, 2) ?>)</h3>
        <canvas id="<?= $chart_id ?>"></canvas>
    </div></td>
        <td>
            <form method="POST" action="sell.php">
                <input type="hidden" name="stock_id" value="<?= $stock_id ?>">
                <?php echo ($amount_spent > $current_value) ? '<button class = "sell-btn" type="submit">Sell</button>' : '<button class = "sell-btn green" type="submit">Sell</button>';?>
            </form>
        </td>
    </tr>

    <script>
    const ctx_<?= $stock_id ?> = document.getElementById("<?= $chart_id ?>").getContext('2d');
    new Chart(ctx_<?= $stock_id ?>, {
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
                y: { display: false }
            }
        }
    });
    </script>

<?php endwhile; ?>
</table>
<br>
</body>
</html>
