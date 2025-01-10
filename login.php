<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'healthcare');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM patients WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_type'] = 'patient'; 
            header("Location: dashboard_patient.html"); 
            exit();
        } else {
            $_SESSION['error'] = "كلمة المرور غير صحيحة!"; 
            header("Location: login.php"); 
            exit();
        }
    } else {
        $sql = "SELECT * FROM doctors WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id']; 
                $_SESSION['user_type'] = 'doctor'; 
                header("Location: dashboard_doctor.html");
                exit();
            } else {
                $_SESSION['error'] = "كلمة المرور غير صحيحة!"; 
                header("Location: login.php"); 
                exit();
            }
        } else {
            $_SESSION['error'] = "البريد الإلكتروني غير مسجل!"; 
            header("Location: login.php");
            exit();
        }
    }
}

$conn->close();
?>

<?php

if (isset($_SESSION['error'])) {
    $error_message = $_SESSION['error'];
    unset($_SESSION['error']); 
} else {
    $error_message = "";
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">تسجيل الدخول</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">البريد الإلكتروني:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">كلمة المرور:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">تسجيل الدخول</button>
        </form>

        <p class="mt-3">ليس لديك حساب؟ <a href="register_patient.php">تسجيل كـ مريض</a> أو <a href="register_doctor.php">تسجيل كـ طبيب</a></p>
    </div>
</body>
</html>
