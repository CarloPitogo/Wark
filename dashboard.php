<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?logout=success");
    exit();
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    $delete_sql = "DELETE FROM patients WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?delete_success=true");
        exit();
    } else {
        die("Delete failed: " . $conn->error);
    }
}

$query = "SELECT patients.id, patients.name, patients.age, patients.gender, patients.symptoms, patients.medical_history, 
                 objective_records.blood_pressure, objective_records.heart_rate, objective_records.temperature, 
                 objective_records.weight, objective_records.diagnostic_test 
          FROM patients 
          LEFT JOIN objective_records ON patients.id = objective_records.patient_id";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - SOAP System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #EBE5C2; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .container {
            flex: 1;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .header {
            background: #1a1a2e;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .footer {
            background: #1a1a2e;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: auto;
        }
        .table th {
            background: #1a1a2e; 
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            border: none;
        }
        .btn-primary {
            background: #D98324 !important; 
            border: none; 
            color: white; 
        }

        .btn-primary:hover {
            background: #b76d1d !important; 
        }
    </style>
    <script>
        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this patient?")) {
                window.location.href = "dashboard.php?delete_id=" + id;
            }
        }
    </script>
</head>
<body>
    <div class="header">
        <h2>SOAP System Dashboard</h2>
        <div>
            <a href="add_patient.php" class="btn btn-success">Add Patient</a>
            <a href="add_soap.php" class="btn btn-success">Provide Diagnosis & Treatment</a>
            <a href="view_soap.php" class="btn btn-success">View Diagnosis & Treatment Plan</a>
            <a href="login.php?logout=success" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <div class="container mt-5">
        <h3 class="text-center">Patients List</h3>

        <?php if (isset($_GET['delete_success'])) { ?>
            <div class="alert alert-success">Patient deleted successfully.</div>
        <?php } ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Symptoms</th>
                    <th>Medical History</th>
                    <th>Blood Pressure</th>
                    <th>Heart Rate</th>
                    <th>Temperature</th>
                    <th>Weight</th>
                    <th>Diagnostic Test</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= $row['age'] ?></td>
                    <td><?= $row['gender'] ?></td>
                    <td><?= htmlspecialchars($row['symptoms']) ?></td>
                    <td><?= htmlspecialchars($row['medical_history']) ?></td>
                    <td><?= htmlspecialchars($row['blood_pressure'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['heart_rate'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['temperature'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['weight'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($row['diagnostic_test'] ?? 'N/A') ?></td>
                    <td>
                        <a href="edit_patient.php?id=<?= $row['id'] ?>" class="btn btn-primary">Edit</a>
                        <button onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-danger">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>&copy; 2025 SOAP System. All rights reserved.</p>
    </div>
</body>
</html>
