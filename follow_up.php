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

if ($user_type === 'doctor') {
    $sql = "SELECT appointments.id, patients.name, appointments.appointment_date, follow_ups.follow_up_date, follow_ups.notes 
            FROM appointments 
            JOIN patients ON appointments.patient_id = patients.id 
            LEFT JOIN follow_ups ON appointments.id = follow_ups.appointment_id 
            WHERE appointments.doctor_id = '$user_id'";

    $result = $conn->query($sql);
}

if ($user_type === 'patient') {
    $sql = "SELECT appointments.id, doctors.name AS doctor_name, appointments.appointment_date, follow_ups.follow_up_date, follow_ups.notes 
            FROM appointments 
            JOIN doctors ON appointments.doctor_id = doctors.id 
            LEFT JOIN follow_ups ON appointments.id = follow_ups.appointment_id 
            WHERE appointments.patient_id = '$user_id'";

    $result = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض المتابعات</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">عرض المتابعات الطبية</h1>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>اسم المريض/الطبيب</th>
                    <th>تاريخ الموعد</th>
                    <th>تاريخ المتابعة</th>
                    <th>الملاحظات</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $user_type === 'doctor' ? $row['name'] : $row['doctor_name']; ?></td>
                            <td><?php echo $row['appointment_date']; ?></td>
                            <td><?php echo $row['follow_up_date'] ? $row['follow_up_date'] : 'لا توجد متابعة بعد'; ?></td>
                            <td><?php echo $row['notes'] ? $row['notes'] : 'لا توجد ملاحظات'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">لا توجد مواعيد متابعة!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($user_type === 'doctor'): ?>
        <h2>إضافة متابعة جديدة</h2>
        <form action="add_follow_up.php" method="POST">
            <div class="form-group">
                <label for="appointment_id">اختيار الموعد:</label>
                <select class="form-control" id="appointment_id" name="appointment_id" required>
                    <?php
                    $appointments_sql = "SELECT id, patient_id, appointment_date FROM appointments WHERE doctor_id = '$user_id'";
                    $appointments_result = $conn->query($appointments_sql);

                    while ($appointment = $appointments_result->fetch_assoc()) {
                        echo "<option value='{$appointment['id']}'>موعد المريض رقم {$appointment['patient_id']} - بتاريخ {$appointment['appointment_date']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="follow_up_date">تاريخ المتابعة:</label>
                <input type="date" class="form-control" id="follow_up_date" name="follow_up_date" required>
            </div>
            <div class="form-group">
                <label for="notes">الملاحظات:</label>
                <textarea class="form-control" id="notes" name="notes"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">إضافة المتابعة</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
