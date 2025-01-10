<?php
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

$conn = new mysqli('localhost', 'root', '', 'healthcare');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$patient_id = $_SESSION['user_id'];  
$sql_patient = "SELECT id, name FROM patients WHERE id = $patient_id";
$patient = $conn->query($sql_patient)->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رفع ملفات طبية</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">رفع ملفات طبية</h1>
        <form action="upload_file.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="patient_id">المريض:</label>
                <input type="text" class="form-control" id="patient_id" value="<?php echo $patient['name']; ?>" readonly>
                <input type="hidden" name="patient_id" value="<?php echo $patient['id']; ?>">
            </div>
            <div class="form-group">
                <label for="file">اختر ملف:</label>
                <input type="file" class="form-control" id="file" name="file" required>
            </div>
            <button type="submit" class="btn btn-primary">رفع الملف</button>
        </form>
    </div>
</body>
</html>
