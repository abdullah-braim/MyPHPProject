<?php
session_start(); 

$conn = new mysqli('localhost', 'root', '', 'healthcare');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];

    $sql = "SELECT id FROM patients WHERE id = '$patient_id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        $_SESSION['error'] = "رقم المريض غير صحيح!";
        header("Location: appointments.php");
        exit();
    }

    $sql = "SELECT id FROM doctors WHERE id = '$doctor_id'";
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        $_SESSION['error'] = "رقم الطبيب غير صحيح!";
        header("Location: appointments.php");
        exit();
    }

    $sql = "SELECT id FROM appointments WHERE doctor_id = '$doctor_id' AND appointment_date = '$appointment_date'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "هذا الموعد محجوز مسبقًا!";
        header("Location: appointments.php");
        exit();
    }

    $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date) VALUES ('$patient_id', '$doctor_id', '$appointment_date')";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "تم حجز الموعد بنجاح!";
        header("Location: appointments.php");
        exit();
    } else {
        $_SESSION['error'] = "حدث خطأ أثناء حجز الموعد: " . $conn->error;
        header("Location: appointments.php");
        exit();
    }
}

$conn->close();
?>