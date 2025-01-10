<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'healthcare');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_size = $_FILES['file']['size'];
    $file_error = $_FILES['file']['error'];
    
    $sql = "SELECT id FROM patients WHERE id = '$patient_id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        $_SESSION['error'] = "رقم المريض غير صحيح!";
        header("Location: upload_files.php");
        exit();
    }

    if ($file_error !== UPLOAD_ERR_OK) {
        $_SESSION['error'] = "حدث خطأ أثناء رفع الملف. من فضلك حاول مرة أخرى.";
        header("Location: upload_files.php");
        exit();
    }

    $allowed_types = ['pdf', 'jpg', 'jpeg', 'png'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_types)) {
        $_SESSION['error'] = "الملف المرفوع يجب أن يكون من النوع PDF أو صورة (JPG, PNG) فقط.";
        header("Location: upload_files.php");
        exit();
    }

    $max_size = 5 * 1024 * 1024;
    if ($file_size > $max_size) {
        $_SESSION['error'] = "حجم الملف أكبر من الحد المسموح به (5 ميغابايت).";
        header("Location: upload_files.php");
        exit();
    }

    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); 
    }

    $file_path = $upload_dir . basename($file_name);
    if (move_uploaded_file($file_tmp, $file_path)) {
        $sql = "INSERT INTO medical_files (patient_id, file_name, file_path) VALUES ('$patient_id', '$file_name', '$file_path')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success'] = "تم رفع الملف بنجاح!";
            header("Location: upload_files.php");
            exit();
        } else {
            $_SESSION['error'] = "حدث خطأ أثناء إدخال البيانات: " . $conn->error;
            header("Location: upload_files.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "حدث خطأ أثناء رفع الملف! تأكد من أن الملف صالح.";
        header("Location: upload_files.php");
        exit();
    }
}

$conn->close();
?>
