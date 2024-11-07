<?php
session_start(); // Start the session
require 'admin_conx.php'; // Include the database connection file

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get selected sorting options
    $sortByDate = $_POST['sort_by_date'];
    $sortByStatus = $_POST['sort_by_status'];
    
    // Redirect with sorting parameters
    header("Location: Transactions.php?type=date&order=$sortByDate&status=$sortByStatus");
    exit();
}

// Fetch data from transactions table ordered by the selected sorting options
$type = isset($_GET['type']) ? $_GET['type'] : 'date';
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$sqlFetchTransactions = "SELECT transaction_id, product_id, quantity, unit_price, total_price, transaction_date, status FROM transactions";

// Apply status filter
if ($status != '') {
    $sqlFetchTransactions .= " WHERE status = '$status'";
}

// Apply sorting options
if ($type == 'date') {
    $sqlFetchTransactions .= " ORDER BY transaction_date $order";
} elseif ($type == 'status') {
    $sqlFetchTransactions .= " ORDER BY status $order";
}

$resultTransactions = mysqli_query($conn, $sqlFetchTransactions);
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
    padding: 5px 10px;
    border-radius: 3px;
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

    <!-- Form for sorting -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label for="sort_by_date">Sort by Date:</label>
            <select id="sort_by_date" name="sort_by_date">
                <option value="asc" <?php echo ($type == 'date' && $order == 'asc') ? 'selected' : ''; ?>>Ascending</option>
                <option value="desc" <?php echo ($type == 'date' && $order == 'desc') ? 'selected' : ''; ?>>Descending</option>
            </select>
            <label for="sort_by_status">Filter by Status:</label>
            <select id="sort_by_status" name="sort_by_status">
                <option value="" <?php echo ($status == '') ? 'selected' : ''; ?>>All</option>
                <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo ($status == 'approved') ? 'selected' : ''; ?>>Approved</option>
                <option value="finished" <?php echo ($status == 'finished') ? 'selected' : ''; ?>>Finished</option>
                <option value="cancelled" <?php echo ($status == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <button type="submit">Apply Filters</button>
        </div>
    </form>

    <!-- Transactions table -->
    <h2>Transactions</h2>
    <button onclick="window.location.href='transactions_report.php'">Download PDF report</button>
    <table>
      <thead>
        <tr>
          <th>Transaction ID</th>
          <th>Product ID</th>
          <th>Quantity</th>
          <th>Unit Price</th>
          <th>Total Price</th>
          <th>Transaction Date</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if ($resultTransactions && mysqli_num_rows($resultTransactions) > 0) {
          while($rowTransaction = mysqli_fetch_assoc($resultTransactions)) {
            $disabled = ($rowTransaction['status'] == 'cancelled') ? 'disabled' : '';
            echo "<tr>";
            echo "<td>" . $rowTransaction['transaction_id'] . "</td>";
            echo "<td>" . $rowTransaction['product_id'] . "</td>";
            echo "<td>" . $rowTransaction['quantity'] . "</td>";
            echo "<td>" . $rowTransaction['unit_price'] . "</td>";
            echo "<td>" . $rowTransaction['total_price'] . "</td>";
            echo "<td>" . $rowTransaction['transaction_date'] . "</td>";
            echo "<td>";
            echo "<select id='status_" . $rowTransaction['transaction_id'] . "' $disabled>";
            echo "<option value='pending'" . ($rowTransaction['status'] == 'pending' ? ' selected' : '') . ">Pending</option>";
            echo "<option value='approved'" . ($rowTransaction['status'] == 'approved' ? ' selected' : '') . ">Approved</option>";
            echo "<option value='finished'" . ($rowTransaction['status'] == 'finished' ? ' selected' : '') . ">Finished</option>";
            echo "<option value='cancelled'" . ($rowTransaction['status'] == 'cancelled' ? ' selected' : '') . ">Cancelled</option>";
            echo "</select>";
            echo "</td>";
            echo "<td><button onclick=\"updateStatus(" . $rowTransaction['transaction_id'] . ")\" $disabled>Update</button></td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='8'>No transactions found</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function updateStatus(transactionId) {
    var statusSelect = document.getElementById('status_' + transactionId);
    var newStatus = statusSelect.value;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "transactions_update.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert(xhr.responseText);
            window.location.reload();
        }
    };
    xhr.send("transaction_id=" + transactionId + "&status=" + newStatus);
}
</script>

</body>
</html>