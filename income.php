<?php
session_start(); // Start the session
require 'admin_conx.php'; // Include the database connection file

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get selected sorting option
    $sortOption = $_POST['sort_option'];
    
    // Redirect with sorting parameter
    header("Location: income.php?sort_option=$sortOption");
    exit();
}

// Fetch data from income table ordered by the selected sorting option
$sortOption = isset($_GET['sort_option']) ? $_GET['sort_option'] : 'date_desc';

switch ($sortOption) {
    case 'date_asc':
        $orderBy = "sale_date ASC";
        break;
    case 'date_desc':
        $orderBy = "sale_date DESC";
        break;
    case 'amount_asc':
        $orderBy = "total_income ASC";
        break;
    case 'amount_desc':
        $orderBy = "total_income DESC";
        break;
    case 'id_asc':
        $orderBy = "income_id ASC";
        break;
    case 'id_desc':
        $orderBy = "income_id DESC";
        break;
    default:
        $orderBy = "sale_date DESC";
}

$sqlFetchIncome = "SELECT income_id, product_id, quantity, total_income, sale_date FROM income ORDER BY $orderBy";

$resultIncome = mysqli_query($conn, $sqlFetchIncome);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome to the Site</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Icons">
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
  <div class="content">
    <?php if(isset($_SESSION['cName'])): ?>
      <h1>Welcome to the Site, <?php echo $_SESSION['cName']; ?></h1>
    <?php else: ?>
      <h1>Welcome to the Site</h1>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div>
        <label for="sort_option">Sort By:</label>
        <select id="sort_option" name="sort_option">
            <option value="date_asc" <?php echo ($sortOption == 'date_asc') ? 'selected' : ''; ?>>Date Ascending</option>
            <option value="date_desc" <?php echo ($sortOption == 'date_desc') ? 'selected' : ''; ?>>Date Descending</option>
            <option value="amount_asc" <?php echo ($sortOption == 'amount_asc') ? 'selected' : ''; ?>>Total Income Low to High</option>
            <option value="amount_desc" <?php echo ($sortOption == 'amount_desc') ? 'selected' : ''; ?>>Total Income High to Low</option>
            <option value="id_asc" <?php echo ($sortOption == 'id_asc') ? 'selected' : ''; ?>>Income ID Ascending</option>
            <option value="id_desc" <?php echo ($sortOption == 'id_desc') ? 'selected' : ''; ?>>Income ID Descending</option>
        </select>
        <button type="submit">Apply Filter</button>
    </div>
</form>

    
    <h2>Income</h2>
    <button onclick="window.location.href='income_report.php'">Download PDF report</button>
    <table>
      <thead>
        <tr>
          <th>Income ID</th>
          <th>Product ID</th>
          <th>Quantity</th>
          <th>Total Income</th>
          <th>Sale Date</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Initialize total income variable
        $totalIncome = 0;

        if (mysqli_num_rows($resultIncome) > 0) {
            while ($rowIncome = mysqli_fetch_assoc($resultIncome)) {
                // Add total_income to the total
                $totalIncome += $rowIncome['total_income'];

                // Display table row
                echo "<tr>";
                echo "<td>" . $rowIncome['income_id'] . "</td>";
                echo "<td>" . $rowIncome['product_id'] . "</td>";
                echo "<td>" . $rowIncome['quantity'] . "</td>";
                echo "<td>" . $rowIncome['total_income'] . "</td>";
                echo "<td>" . $rowIncome['sale_date'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No income records found</td></tr>";
        }
        ?>
      </tbody>
      <tfoot>
          <tr>
              <td colspan="2"></td>
              <th>Total: </th>
              <td><?php echo $totalIncome; ?></td>
          </tr>
      </tfoot>

    </table>
    
  </div>
</div>

</body>
</html>
