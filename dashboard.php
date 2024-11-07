<?php
session_start();
require 'admin_conx.php';

// Fetch data from transactions table
$sql_transactions = "SELECT transaction_id, product_id, quantity, unit_price, total_price, status, transaction_date, user_id FROM transactions";
$result_transactions = $conn->query($sql_transactions);

// Fetch data from products table
$sql_products = "SELECT product_id, product_name, price, stock, image_path FROM products";
$result_products = $conn->query($sql_products);

// Fetch data from login_audit table
$sql_login_audit = "SELECT id, user_id, event_type, event_time FROM login_audit";
$result_login_audit = $conn->query($sql_login_audit);

// Fetch data from income table
$sql_income = "SELECT income_id, product_id, quantity, total_income, sale_date FROM income";
$result_income = $conn->query($sql_income);

// Calculate best-selling product
$sql_best_selling = "SELECT p.product_id, p.product_name, SUM(t.quantity) AS total_quantity_sold
                    FROM transactions t
                    JOIN products p ON t.product_id = p.product_id
                    GROUP BY t.product_id
                    ORDER BY total_quantity_sold DESC
                    LIMIT 1";
$result_best_selling = $conn->query($sql_best_selling);
$best_selling_product = $result_best_selling->fetch_assoc();

// Calculate sales today
$today_date = date("Y-m-d");
$sql_sales_today = "SELECT SUM(total_price) AS total_sales_today
                    FROM transactions
                    WHERE DATE(transaction_date) = '$today_date'";
$result_sales_today = $conn->query($sql_sales_today);
$sales_today = $result_sales_today->fetch_assoc()['total_sales_today'];

// Calculate number of orders today
$sql_orders_today = "SELECT COUNT(transaction_id) AS total_orders_today
                    FROM transactions
                    WHERE DATE(transaction_date) = '$today_date'";
$result_orders_today = $conn->query($sql_orders_today);
$orders_today = $result_orders_today->fetch_assoc()['total_orders_today'];

// Calculate number of orders this month
$current_month = date("m");
$sql_orders_this_month = "SELECT COUNT(transaction_id) AS total_orders_this_month
                          FROM transactions
                          WHERE MONTH(transaction_date) = '$current_month'";
$result_orders_this_month = $conn->query($sql_orders_this_month);
$orders_this_month = $result_orders_this_month->fetch_assoc()['total_orders_this_month'];

// Calculate total income today
$sql_total_income_today = "SELECT SUM(total_income) AS total_income_today
                          FROM income
                          WHERE DATE(sale_date) = '$today_date'";
$result_total_income_today = $conn->query($sql_total_income_today);
$total_income_today = $result_total_income_today->fetch_assoc()['total_income_today'];

// Calculate total income this month
$sql_total_income_this_month = "SELECT SUM(total_income) AS total_income_this_month
                                FROM income
                                WHERE MONTH(sale_date) = '$current_month'";
$result_total_income_this_month = $conn->query($sql_total_income_this_month);
$total_income_this_month = $result_total_income_this_month->fetch_assoc()['total_income_this_month'];

// Fetch data for monthly orders and income charts
$sql_monthly_data_orders = "SELECT 
                            DATE_FORMAT(transaction_date, '%Y-%m') AS month,
                            COUNT(transaction_id) AS total_orders
                            FROM transactions
                            GROUP BY month
                            ORDER BY month ASC";
$result_monthly_data_orders = $conn->query($sql_monthly_data_orders);

$sql_monthly_data_income = "SELECT 
                            DATE_FORMAT(sale_date, '%Y-%m') AS month,
                            SUM(total_income) AS total_income
                            FROM income
                            GROUP BY month
                            ORDER BY month ASC";
$result_monthly_data_income = $conn->query($sql_monthly_data_income);

$months = [];
$monthly_orders_data = [];
$monthly_income_data = [];

while ($row_orders = $result_monthly_data_orders->fetch_assoc()) {
    $months[] = $row_orders['month'];
    $monthly_orders_data[] = $row_orders['total_orders'];
}

while ($row_income = $result_monthly_data_income->fetch_assoc()) {
    $monthly_income_data[] = $row_income['total_income'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #077a07;
            color: #fff;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 999;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .menu {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .menu li {
            margin-right: 20px;
        }
        .menu li:last-child {
            margin-right: 0;
        }
        .menu a {
            color: #fff;
            text-decoration: none;
            padding: 10px;
        }
        .container {
            max-width: 800px;
            margin: 100px auto;
            text-align: center;
        }
        .content {
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            margin-top: 50px;
        }
        h1 {
            color: #077a07;
        }
        p {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #077a07;
            color: #fff;
        }
        button {
            background-color: #077a07;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #529e52;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }

        .container {
            max-width: 800px;
            margin: 100px auto;
            text-align: center;
        }
        .card {
            margin-bottom: 20px;
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-weight: bold;
            margin-bottom: 15px;
        }
        .card-text {
            margin-bottom: 0;
        }
    </style>
</head>
<body>

    <div class="header">
        <ul class="menu">
            <li><button onclick="window.location.href='dashboard.php'">Dashboard</button></li>
            <li><button onclick="window.location.href='adminPage.php'">User Logs</button></li>
            <li><button onclick="window.location.href='Transactions.php'">Transactions</button></li>
            <li><button onclick="window.location.href='productTable.php'">Products</button></li>
            <li><button onclick="window.location.href='income.php'">Income</button></li>
            <li><button onclick="window.location.href='logoutAdmin.php'">Logout</button></li>
        </ul>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Best Selling Product</h5>
                        <p class="card-text"><?php echo htmlspecialchars($best_selling_product['product_name']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Sales Today</h5>
                        <p class="card-text">₱<?php echo number_format($sales_today, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Number of Orders Today</h5>
                        <p class="card-text"><?php echo $orders_today; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Number of Orders This Month</h5>
                        <p class="card-text"><?php echo $orders_this_month; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Income Today</h5>
                        <p class="card-text">₱<?php echo number_format($total_income_today, 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Income This Month</h5>
                        <p class="card-text">₱<?php echo number_format($total_income_this_month, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Orders</h5>
                        <canvas id="monthlyOrdersChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Income</h5>
                        <canvas id="monthlyIncomeChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies (for optional interactivity) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var months = <?php echo json_encode($months); ?>;
            var monthlyOrdersData = <?php echo json_encode($monthly_orders_data); ?>;
            var monthlyIncomeData = <?php echo json_encode($monthly_income_data); ?>;

            var monthlyOrdersCanvas = document.getElementById('monthlyOrdersChart').getContext('2d');
            var monthlyIncomeCanvas = document.getElementById('monthlyIncomeChart').getContext('2d');

            new Chart(monthlyOrdersCanvas, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Number of Orders',
                        data: monthlyOrdersData,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }]
                    }
                }
            });

            new Chart(monthlyIncomeCanvas, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Monthly Income',
                        data: monthlyIncomeData,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value, index, values) {
                                    return '₱' + value.toFixed(2);
                                }
                            }
                        }]
                    }
                }
            });
        });
    </script>

</body>
</html>
