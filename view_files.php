<?php
session_start(); 

$conn = new mysqli('localhost', 'root', '', 'healthcare');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

if ($user_type === 'patient') {
    $sql = "SELECT medical_files.id, medical_files.file_name, medical_files.file_path
            FROM medical_files 
            WHERE medical_files.patient_id = '$user_id'";
} 
else if ($user_type === 'doctor') {
    $sql = "SELECT medical_files.id, patients.name AS patient_name, medical_files.file_name, medical_files.file_path 
            FROM medical_files 
            JOIN patients ON medical_files.patient_id = patients.id 
            JOIN appointments ON patients.id = appointments.patient_id 
            WHERE appointments.doctor_id = '$user_id'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض الملفات الطبية</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">عرض الملفات الطبية</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم المريض</th>
                    <th>اسم الملف</th>
                    <th>رابط الملف</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <?php if ($user_type === 'doctor'): ?>
                                <td><?php echo $row['patient_name']; ?></td>
                            <?php else: ?>
                                <td>أنت</td>
                            <?php endif; ?>
                            <td><?php echo $row['file_name']; ?></td>
                            <td><a href="<?php echo $row['file_path']; ?>" target="_blank">تحميل الملف</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">لا توجد ملفات طبية!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
