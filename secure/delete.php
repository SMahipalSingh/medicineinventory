<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../auth.php?action=login");
    exit;
}
include '../config/db_connect.php';

if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id = trim($_GET["id"]);

    $sql = "DELETE FROM medicines WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            header("location: index.php");
            exit();
        } else {
       
            header("location: index.php");
            exit(); 
        }
        $stmt->close();
    }
} else {
    header("location: index.php");
    exit();
}
?>