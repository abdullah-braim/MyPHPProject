<?php
$conn = new mysqli('localhost', 'root', '', 'healthcare');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
    $specialty = $_POST['specialty'];

    $sql_check = "SELECT id FROM doctors WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email); 
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $error_message = "البريد الإلكتروني هذا مسجل بالفعل!";
        $stmt_check->close(); 
    } else {
        $sql = "INSERT INTO doctors (name, email, password, specialty) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password, $specialty); 
        if ($stmt->execute()) {
            header("Location: login.php"); 
            exit();
        } else {
            $error_message = "حدث خطأ أثناء التسجيل: " . $conn->error;
        }

        $stmt->close();
    }
}

// إغلاق الاتصال
$conn->close();
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الطبيب</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">تسجيل الطبيب</h1>
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="register_doctor.php" method="POST">
            <div class="form-group">
                <label for="name">الاسم:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">البريد الإلكتروني:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">كلمة المرور:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="specialty">التخصص:</label>
                <input type="text" class="form-control" id="specialty" name="specialty" required>
            </div>
            <button type="submit" class="btn btn-primary">تسجيل</button>
            <p>هل لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a></p>
        </form>
    </div>
</body>
</html>
