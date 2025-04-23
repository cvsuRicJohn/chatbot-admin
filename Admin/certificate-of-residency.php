<?php
// Database connection
$servername = "localhost"; // Change if necessary
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "barangay_bucandala";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $government_id = $_POST['government_id'];
    $complete_address = $_POST['complete_address'];
    $proof_of_residency = $_POST['proof_of_residency'];
    $purpose_of_certificate = $_POST['purpose_of_certificate'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO residency_certificates (first_name, middle_name, last_name, date_of_birth, government_id, complete_address, proof_of_residency, purpose_of_certificate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $first_name, $middle_name, $last_name, $date_of_birth, $government_id, $complete_address, $proof_of_residency, $purpose_of_certificate);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barangay Certificate of Residency</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" />
</head>
<body>
    <div class="container-fluid px-5 py-4">
        <h2 class="text-center mb-4">Barangay Certificate of Residency Form</h2>
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>First Name *</label>
                    <input type="text" name="first_name" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Middle Name *</label>
                    <input type="text" name="middle_name" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Last Name *</label>
                    <input type="text" name="last_name" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Date of Birth *</label>
                    <input type="date" name="date_of_birth" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Government-issued ID *</label>
                    <input type="text" name="government_id" class="form-control" required>
                </div>
                <div class="form-group col-md-12">
                    <label>Complete Address *</label>
                    <input type="text" name="complete_address" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Proof of Residency *</label>
                    <input type="text" name="proof_of_residency" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Purpose of Certificate *</label>
                    <input type="text" name="purpose_of_certificate" class="form-control" required>
                </div>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-5">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>