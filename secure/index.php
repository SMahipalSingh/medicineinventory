<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth.php?action=login");
    exit;
}
include '../config/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medicine Inventory - Welcome</title>
    <style>
        /* EMBEDDED CSS FOR INDEX.PHP */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background-color: #f4f4f9; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        h2 { color: #333; margin: 0; font-size: 1.5em; }

        /* Header and Buttons */
        .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { display: inline-block; padding: 10px 15px; margin: 5px 5px 15px 0; text-decoration: none; border-radius: 5px; cursor: pointer; text-align: center; transition: background-color 0.3s; border: 1px solid transparent; }
        .btn.primary { background-color: #007bff; color: white; border: none; }
        .btn.primary:hover { background-color: #0056b3; }
        .btn.delete { background-color: #dc3545; color: white; border: none; }
        .btn.delete:hover { background-color: #c82333; }
        .add-button { float: right; }

        /* Table Styles */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        table th { background-color: #f2f2f2; color: #333; font-weight: bold; }
        table tr:nth-child(even) { background-color: #f9f9f9; }
        table tr:hover { background-color: #f1f1f1; }
        
        /* Action Buttons */
        .action-btn { padding: 5px 10px; margin: 2px; text-decoration: none; border-radius: 3px; font-size: 0.9em; }
        .action-btn.edit { background-color: #ffc107; color: #333; }
        .action-btn.delete { background-color: #dc3545; color: white; }
        .action-btn.edit:hover { background-color: #e0a800; }
        .action-btn.delete:hover { background-color: #c82333; }
        .no-records { text-align: center; color: #777; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-bar">
            <h2>💊 Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
            <a href="../auth.php?action=logout" class="btn delete">Logout</a>
        </div>

        <a href="add.php" class="btn primary add-button">Add New Medicine</a>
        
        <?php
        $sql = "SELECT * FROM medicines ORDER BY name ASC";
        if ($result = $conn->query($sql)) {
            if ($result->num_rows > 0) {
                echo "<table><thead><tr><th>ID</th><th>Name</th><th>Dosage</th><th>Quantity</th><th>Price</th><th>Expiry Date</th><th>Actions</th></tr></thead><tbody>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>" . $row['dosage'] . "</td>";
                            echo "<td>" . $row['quantity'] . "</td>";
                            echo "<td>$" . number_format($row['price'], 2) . "</td>";
                            echo "<td>" . $row['expiry_date'] . "</td>";
                            echo "<td>";
                                echo "<a href='edit.php?id=" . $row['id'] . "' class='action-btn edit'>Edit</a>";
                                echo "<a href='delete.php?id=" . $row['id'] . "' class='action-btn delete' onclick='return confirm(\"Are you sure you want to delete this item?\");'>Delete</a>";
                            echo "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                $result->free();
            } else {
                echo "<p class='no-records'>No medicines were found in the inventory.</p>";
            }
        } else {
            echo "ERROR: Could not execute $sql. " . $conn->error;
        }
        $conn->close();
        ?>
    </div>
</body>
</html>