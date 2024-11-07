<?php
session_start();
require 'admin_conx.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $transactionId = $_POST['transaction_id'];
    $newStatus = $_POST['status'];

    // Fetch the transaction details
    $sqlFetchTransaction = "SELECT product_id, quantity, total_price, status FROM transactions WHERE transaction_id = ?";
    $stmtFetch = $conn->prepare($sqlFetchTransaction);
    $stmtFetch->bind_param("i", $transactionId);
    $stmtFetch->execute();
    $resultFetch = $stmtFetch->get_result();

    if ($resultFetch->num_rows > 0) {
        $transaction = $resultFetch->fetch_assoc();
        $productId = $transaction['product_id'];
        $quantity = $transaction['quantity'];
        $totalPrice = $transaction['total_price'];
        $oldStatus = $transaction['status'];

        // Update the transaction status
        $sqlUpdateStatus = "UPDATE transactions SET status = ? WHERE transaction_id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdateStatus);
        $stmtUpdate->bind_param("si", $newStatus, $transactionId);
        $stmtUpdate->execute();

        // If status is changed to cancelled, adjust the income and product stock
        if ($oldStatus != 'cancelled' && $newStatus == 'cancelled') {
            $sqlUpdateIncome = "UPDATE income SET total_income = total_income - ? WHERE product_id = ?";
            $stmtUpdateIncome = $conn->prepare($sqlUpdateIncome);
            $stmtUpdateIncome->bind_param("di", $totalPrice, $productId);
            $stmtUpdateIncome->execute();

            $sqlUpdateStock = "UPDATE products SET stock = stock + ? WHERE product_id = ?";
            $stmtUpdateStock = $conn->prepare($sqlUpdateStock);
            $stmtUpdateStock->bind_param("ii", $quantity, $productId);
            $stmtUpdateStock->execute();
        }
    }

    $stmtFetch->close();
    $stmtUpdate->close();
    $conn->close();
}
?>
