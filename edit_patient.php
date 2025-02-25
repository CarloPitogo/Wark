<?php
session_start(); 
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$patient_id = $_GET['id'];

$query = "SELECT patients.*, objective_records.blood_pressure, objective_records.heart_rate, objective_records.temperature, objective_records.weight, objective_records.diagnostic_test 
          FROM patients 
          LEFT JOIN objective_records ON patients.id = objective_records.patient_id 
          WHERE patients.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    echo "Patient not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $age = $_POST['age'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $symptoms = $_POST['symptoms'] ?? '';
    $medical_history = $_POST['medical_history'] ?? '';
    $blood_pressure = $_POST['blood_pressure'] ?? '';
    $heart_rate = $_POST['heart_rate'] ?? '';
    $temperature = $_POST['temperature'] ?? '';
    $weight = $_POST['weight'] ?? '';
    $diagnostic_test = $_POST['diagnostic_test'] ?? '';

    if (empty($name) || empty($age) || empty($gender) || empty($symptoms) || empty($medical_history) || 
        empty($blood_pressure) || empty($heart_rate) || empty($temperature) || empty($weight) || empty($diagnostic_test)) {
        die("All fields are required.");
    }

    $update_patient_query = "UPDATE patients SET name = ?, age = ?, gender = ?, symptoms = ?, medical_history = ? WHERE id = ?";
    $stmt = $conn->prepare($update_patient_query);
    $stmt->bind_param("sisssi", $name, $age, $gender, $symptoms, $medical_history, $patient_id);

    if (!$stmt->execute()) {
        die("Error updating patient information: " . $conn->error);
    }

    $check_objective_query = "SELECT id FROM objective_records WHERE patient_id = ?";
    $stmt = $conn->prepare($check_objective_query);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $existing_objective = $result->fetch_assoc();

    if ($existing_objective) {
        $update_objective_query = "UPDATE objective_records SET blood_pressure = ?, heart_rate = ?, temperature = ?, weight = ?, diagnostic_test = ? WHERE patient_id = ?";
        $stmt = $conn->prepare($update_objective_query);
        $stmt->bind_param("siddsi", $blood_pressure, $heart_rate, $temperature, $weight, $diagnostic_test, $patient_id);
    } else {
        $insert_objective_query = "INSERT INTO objective_records (patient_id, blood_pressure, heart_rate, temperature, weight, diagnostic_test) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_objective_query);
        $stmt->bind_param("isidds", $patient_id, $blood_pressure, $heart_rate, $temperature, $weight, $diagnostic_test);
    }

    if (!$stmt->execute()) {
        die("Error updating objective record: " . $conn->error);
    }

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient - SOAP System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #EBE5C2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .header {
            background: #1a1a2e;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
        }
        .nav-buttons {
            display: flex;
            gap: 10px;
        }
        .footer {
            background: #1a1a2e;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: auto;
            width: 100%;
        }
        .form-label {
            font-weight: bold;
        }
        textarea, input {
            background: white !important;
            border: 2px solid #C14600 !important;
            border-radius: 5px;
            padding: 10px;
            color: black;
            outline: none;
            width: 100%;
        }
        textarea::placeholder, input::placeholder {
            color: rgba(0, 0, 0, 0.5);
        }
        .btn-primary {
            background: #C14600;
            border: none;
        }
        .btn-primary:hover {
            background: #a23a00;
        }
        .btn-success {
            background: #198754;
            border: none;
        }
        .btn-success:hover {
            background: #157347;
        }
        .btn-danger {
            background: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .button-container {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }
        .content {
            width: 80%;
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>SOAP System - Edit Patient</h2>
        <div class="nav-buttons">
            <a href="dashboard.php" class="btn btn-success">Dashboard</a>
            <a href="add_patient.php" class="btn btn-success">Add Patient</a>
            <a href="add_soap.php" class="btn btn-success">Add SOAP</a>
            <a href="view_soap.php" class="btn btn-success">View SOAP</a>
            <a href="login.php?logout=success" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <div class="content">
        <h3 class="text-center">Update Patient Information</h3>
        <form method="POST">
           
            <div class="mb-3">
                <label class="form-label">Name:</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($patient['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Age:</label>
                <input type="number" name="age" class="form-control" value="<?= $patient['age'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Gender:</label>
                <input type="text" name="gender" class="form-control" value="<?= htmlspecialchars($patient['gender']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Symptoms:</label>
                <textarea name="symptoms" class="form-control" required><?= htmlspecialchars($patient['symptoms']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Medical History:</label>
                <textarea name="medical_history" class="form-control" required><?= htmlspecialchars($patient['medical_history']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Blood Pressure:</label>
                <input type="text" name="blood_pressure" class="form-control" value="<?= htmlspecialchars($patient['blood_pressure'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Heart Rate:</label>
                <input type="number" name="heart_rate" class="form-control" value="<?= htmlspecialchars($patient['heart_rate'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Temperature (°C):</label>
                <input type="number" step="0.1" name="temperature" class="form-control" value="<?= htmlspecialchars($patient['temperature'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Weight (kg):</label>
                <input type="number" step="0.1" name="weight" class="form-control" value="<?= htmlspecialchars($patient['weight'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Diagnostic Test:</label>
                <textarea name="diagnostic_test" class="form-control" required><?= htmlspecialchars($patient['diagnostic_test'] ?? '') ?></textarea>
            </div>

            <div class="button-container">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2025 SOAP System. All rights reserved.</p>
    </div>
</body>
</html>