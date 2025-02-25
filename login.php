<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            
            header("Location: dashboard.php"); 
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        /* Consistent background color */
        body {
            background: linear-gradient(to right, #4A90E2, #50C9C3); 
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Card styling */
        .card {
            width: 400px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            background: #fff;
        }
        /* Button styling */
        .btn-primary {
            background: #4A90E2;
            border: none;
        }
        .btn-primary:hover {
            background: #3b7dc4;
        }

        .form-control {
            border: 2px solid #4A90E2 !important; 
            border-radius: 5px;
        }
        .form-control:focus {
            border-color: #3b7dc4 !important; 
            box-shadow: none;
        }
        .register-link a {
            color: #3b7dc4;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2 class="text-center mb-4">User Login</h2>
        
        <?php if (isset($_GET['logout']) && $_GET['logout'] == 'success') { ?>
            <div class="alert alert-success text-center">Logout Successful. Please login again.</div>
        <?php } ?>

        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <div class="text-center mt-3 register-link">
            <p>Don't have an account yet? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html>
