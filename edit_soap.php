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

$soap_id = $_GET['id'];

$query = "SELECT * FROM soap_records WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $soap_id);
$stmt->execute();
$result = $stmt->get_result();
$soap = $result->fetch_assoc();

if (!$soap) {
    echo "SOAP record not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_name = $_POST['patient_name'] ?? '';
    $diagnosis = $_POST['diagnosis'] ?? '';
    $medications = $_POST['medications'] ?? '';
    $therapies = $_POST['therapies'] ?? '';
    $follow_up = $_POST['follow_up'] ?? '';

    $update_query = "UPDATE soap_records SET patient_name = ?, diagnosis = ?, medications = ?, therapies = ?, follow_up = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssssi", $patient_name, $diagnosis, $medications, $therapies, $follow_up, $soap_id);

    if ($stmt->execute()) {
        header("Location: view_soap.php");
        exit();
    } else {
        echo "Error updating SOAP record.";
    }
}


$follow_up_value = '';
if (!empty($soap['follow_up'])) {
    $follow_up_value = date('Y-m-d\TH:i', strtotime($soap['follow_up']));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit SOAP Record - SOAP System</title>
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

        .btn-primary {
            background: #C14600 !important; 
            border: none; 
            color: white; 
        }

        .btn-primary:hover {
            background: #a23a00 !important; 
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>SOAP System - Edit SOAP Record</h2>
        <div class="nav-buttons">
            <a href="dashboard.php" class="btn btn-success">Dashboard</a>
            <a href="add_patient.php" class="btn btn-success">Add Patient</a>
            <a href="add_soap.php" class="btn btn-success">View Diagnosis & Treatment Plan</a>
            <a href="view_soap.php" class="btn btn-success">View SOAP</a>
            <a href="login.php?logout=success" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <div class="content">
        <h3 class="text-center">Update SOAP Record</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Patient Name:</label>
                <input type="text" name="patient_name" class="form-control" value="<?= htmlspecialchars($soap['patient_name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Diagnosis:</label>
                <textarea name="diagnosis" class="form-control" required><?= htmlspecialchars($soap['diagnosis']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Medications:</label>
                <textarea name="medications" class="form-control" required><?= htmlspecialchars($soap['medications']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Therapies:</label>
                <textarea name="therapies" class="form-control" required><?= htmlspecialchars($soap['therapies']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Follow-up Appointment:</label>
                <input type="datetime-local" name="follow_up" class="form-control" value="<?= $follow_up_value ?>" required>
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
