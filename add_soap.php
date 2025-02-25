<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?logout=success");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_name = $_POST['patient_name'];
    $diagnosis = $_POST['diagnosis'];
    $medications = $_POST['medications'];
    $therapies = $_POST['therapies'];
    $follow_up = $_POST['follow_up'];

    $follow_up_datetime = date('Y-m-d H:i:s', strtotime($follow_up));

    $query = "INSERT INTO soap_records (patient_name, diagnosis, medications, therapies, follow_up) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sssss", $patient_name, $diagnosis, $medications, $therapies, $follow_up_datetime);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success text-center'>SOAP Record added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger text-center'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        $error = "Query preparation failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add SOAP Record - SOAP System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #EBE5C2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .header {
            background: #1a1a2e;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-left {
            display: flex;
            align-items: center;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
            color: white;
        }
        .footer {
            background: #1a1a2e;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: auto;
        }
        .logout-btn {
            background: #dc3545;
            border: none;
            padding: 8px 15px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
        }
        .logout-btn:hover {
            background: #c82333;
        }
        .container {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            margin-top: 20px;
        }
        
        .form-control {
            border: 2px solid #C14600;
            transition: all 0.3s ease-in-out;
            width: 500px;
            margin: 0 auto; 
            display: flex;
            flex-direction: column;
            align-items: center;
            
        }
        .form-control:focus {
            border-color: #C14600;
            box-shadow: 0 0 8px rgba(193, 70, 0, 0.5);
        }
        .btn-primary {
            background: #C14600;
            border: none;
        }
        .mb-3 {
            display: flex;
            flex-direction: column; 
            align-items: flex-start;
            width: 500px; 
            margin: 0 auto; 
        }
    </style>
</head>
<body>

  <div class="header">
        <div class="header-left">
            <h2>SOAP System - SOAP Entry</h2>
        </div>
        <div>
            <a href="dashboard.php" class="btn btn-success">Dashboard</a>
            <a href="add_patient.php" class="btn btn-success">Add Patient</a>
            <a href="view_soap.php" class="btn btn-success">View Diagnosis & Treatment Plan</a>
            <a href="login.php?logout=success" class="btn btn-danger">Logout</a>
        </div>
    </div>

        <h2>Diagnosis and Treatment Plan</h2>
        <form action="add_soap.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Patient Name:</label>
                <input type="text" name="patient_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Diagnosis:</label>
                <textarea name="diagnosis" class="form-control" required></textarea>
            </div>
            <h2>Treatment Plan</h2>
            <div class="mb-3">
                <label class="form-label">Medications:</label>
                <textarea name="medications" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Therapies:</label>
                <textarea name="therapies" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Follow-up Appointment:</label>
                <input type="datetime-local" name="follow_up" class="form-control" required>
            </div>

            
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2025 SOAP System. All rights reserved.</p>
    </div>

</body>
</html>