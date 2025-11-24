<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth.php?action=login");
    exit;
}
include '../config/db_connect.php';

$id = ''; $name = $dosage = $quantity = $price = $expiry_date = '';

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
 
    $id = trim($_GET["id"]);
    $sql = "SELECT * FROM medicines WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $name = $row['name']; $dosage = $row['dosage']; $quantity = $row['quantity'];
                $price = $row['price']; $expiry_date = $row['expiry_date'];
            } else { header("location: index.php"); exit(); }
        }
        $stmt->close();
    }
} elseif (isset($_POST["id"]) && !empty($_POST["id"])) {
    // 2. Process form submission (Update logic)
    $id = $_POST['id']; $name = $conn->real_escape_string($_POST['name']); 
    $dosage = $conn->real_escape_string($_POST['dosage']); $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price']; $expiry_date = $conn->real_escape_string($_POST['expiry_date']);

    $sql = "UPDATE medicines SET name=?, dosage=?, quantity=?, price=?, expiry_date=? WHERE id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssidsi", $name, $dosage, $quantity, $price, $expiry_date, $id);
        if ($stmt->execute()) { header("location: index.php"); exit(); } else { echo "Error."; }
        $stmt->close();
    }
} else {
    header("location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Medicine</title>
    <style>
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }

       
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }

      
        .btn { display: inline-block; padding: 10px 15px; margin: 5px; text-decoration: none; border-radius: 5px; cursor: pointer; text-align: center; transition: background-color 0.3s; border: 1px solid transparent; }
        .btn.primary { background-color: #ffc107; color: #333; border: none; }
        .btn.primary:hover { background-color: #e0a800; }
        a.btn { background-color: #ccc; color: #333; border: 1px solid #aaa; }
        a.btn:hover { background-color: #bbb; }
        .form-group:last-child { text-align: right; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Medicine</h2>
        <form action="edit.php" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <div class="form-group"><label>Medicine Name:</label><input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required></div>
            <div class="form-group"><label>Dosage:</label><input type="text" name="dosage" value="<?php echo htmlspecialchars($dosage); ?>" required></div>
            <div class="form-group"><label>Quantity (Stock):</label><input type="number" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>" required min="1"></div>
            <div class="form-group"><label>Price:</label><input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($price); ?>" required min="0.01"></div>
            <div class="form-group"><label>Expiry Date:</label><input type="date" name="expiry_date" value="<?php echo htmlspecialchars($expiry_date); ?>" required></div>
            <div class="form-group">
                <input type="submit" value="Update" class="btn primary">
                <a href="index.php" class="btn">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>