<?php

session_start();

include 'config/db_connect.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'login';


if ($action === 'logout') {
    $_SESSION = array(); 
    session_destroy();
    header("location: auth.php?action=login");
    exit;
}

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: secure/index.php");
    exit;
}


$username = $password = "";
$username_err = $password_err = "";


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['form_action']; 

    if (empty(trim($_POST["username"]))) { $username_err = "Please enter username."; } else { $username = trim($_POST["username"]); }
    if (empty(trim($_POST["password"]))) { $password_err = "Please enter your password."; } else { $password = trim($_POST["password"]); }

 
    if ($action === 'signup' && empty($username_err) && empty($password_err)) {
        $sql = "SELECT id FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $insert_sql = "INSERT INTO users (username, password) VALUES (?, ?)";
                    if ($insert_stmt = $conn->prepare($insert_sql)) {
                        $param_password = password_hash($password, PASSWORD_DEFAULT); 
                        $insert_stmt->bind_param("ss", $username, $param_password);
                        
                        if ($insert_stmt->execute()) {
                            header("location: auth.php?action=login&success=1");
                            exit();
                        } else { echo "Error creating account."; }
                        $insert_stmt->close();
                    }
                }
            }
            $stmt->close();
        }
    }

    if ($action === 'login' && empty($username_err) && empty($password_err)) {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            if ($stmt->execute()) {
                $stmt->store_result();
                if ($stmt->num_rows == 1) {                    
                    $stmt->bind_result($id, $username, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            header("location: secure/index.php");
                            exit();
                        } else { $password_err = "Invalid password."; }
                    }
                } else { $username_err = "No account found with that username."; }
            }
            $stmt->close();
        }
    }
    $conn->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo ucfirst($action); ?> - Inventory</title>
    <style>
    
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            max-width: 400px;
            width: 100%;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        
     
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        
        .form-group.has-error input {
            border-color: #dc3545; 
        }

        .help-block {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 5px;
            display: block;
        }

      
        .btn {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s;
            border: 1px solid transparent;
            width: 100%; /* Full width for auth buttons */
        }

        .btn.primary {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn.primary:hover {
            background-color: #0056b3;
        }

        p {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($action === 'login'): ?>
            
            <h2>Medicine Inventory Login</h2>
            <?php if (isset($_GET['success'])): ?>
                <p style="color: green; margin-bottom: 20px;">Sign up successful! Please log in.</p>
            <?php endif; ?>

            <form action="auth.php" method="post">
                <input type="hidden" name="form_action" value="login">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label>Username</label><input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label>Password</label><input type="password" name="password">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn primary" value="Login">
                </div>
                <p>Don't have an account? <a href="auth.php?action=signup">Sign up now</a>.</p>
            </form>

        <?php elseif ($action === 'signup'): ?>

            <h2>Sign Up for Inventory Access</h2>
            <form action="auth.php" method="post">
                <input type="hidden" name="form_action" value="signup">
                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                    <label>Username</label><input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
                    <span class="help-block"><?php echo $username_err; ?></span>
                </div>    
                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                    <label>Password</label><input type="password" name="password">
                    <span class="help-block"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn primary" value="Sign Up">
                </div>
                <p>Already have an account? <a href="auth.php?action=login">Login here</a>.</p>
            </form>

        <?php endif; ?>
    </div>    
</body>
</html>