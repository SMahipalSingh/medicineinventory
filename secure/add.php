<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth.php?action=login");
    exit;
}
include '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $dosage = $conn->real_escape_string($_POST['dosage']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $expiry_date = $conn->real_escape_string($_POST['expiry_date']);

    $sql = "INSERT INTO medicines (name, dosage, quantity, price, expiry_date) VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssids", $name, $dosage, $quantity, $price, $expiry_date);
        
        if ($stmt->execute()) {
            header("location: index.php"); 
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Medicine</title>
    <style>
        /* EMBEDDED CSS FOR ADD/EDIT.PHP FORMS */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }

        /* Form Layout */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }

        /* Buttons */
        .btn { display: inline-block; padding: 10px 15px; margin: 5px; text-decoration: none; border-radius: 5px; cursor: pointer; text-align: center; transition: background-color 0.3s; border: 1px solid transparent; }
        .btn.primary { background-color: #28a745; color: white; border: none; }
        .btn.primary:hover { background-color: #1e7e34; }
        a.btn { background-color: #ccc; color: #333; border: 1px solid #aaa; }
        a.btn:hover { background-color: #bbb; }
        .form-group { text-align: right; }
        .form-group:last-child { text-align: right; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Medicine</h2>
        <form action="add.php" method="post">
            <div class="form-group"><label>Medicine Name:</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Dosage:</label><input type="text" name="dosage" required></div>
            <div class="form-group"><label>Quantity (Stock):</label><input type="number" name="quantity" required min="1"></div>
            <div class="form-group"><label>Price:</label><input type="number" step="0.01" name="price" required min="0.01"></div>
            <div class="form-group"><label>Expiry Date:</label><input type="date" name="expiry_date" required></div>
            <div class="form-group">
                <input type="submit" value="Submit" class="btn primary">
                <a href="index.php" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>