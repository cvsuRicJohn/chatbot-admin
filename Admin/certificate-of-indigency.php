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
    $civil_status = $_POST['civil_status'];
    $occupation = $_POST['occupation'];
    $monthly_income = $_POST['monthly_income'];
    $proof_of_residency = $_POST['proof_of_residency'];
    $government_id = $_POST['government_id'];
    $spouse_name = $_POST['spouse_name'];
    $number_of_dependents = $_POST['number_of_dependents'];

    // Insert data into the database
    $sql = "INSERT INTO indigency_certificates (first_name, middle_name, last_name, date_of_birth, civil_status, occupation, monthly_income, proof_of_residency, government_id, spouse_name, number_of_dependents)
            VALUES ('$first_name', '$middle_name', '$last_name', '$date_of_birth', '$civil_status', '$occupation', '$monthly_income', '$proof_of_residency', '$government_id', '$spouse_name', '$number_of_dependents')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Certificate of Indigency</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" />
</head>
<body>
    <div class="container-fluid px-5 py-4">
        <h2 class="text-center mb-4">Certificate of Indigency Form</h2>
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
                <div class="form-group col-md-4">
                    <label>Date of Birth *</label>
                    <input type="date" name="date_of_birth" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Civil Status *</label>
                    <select name="civil_status" class="form-control" required>
                        <option value="">Select...</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="widowed">Widowed</option>
                        <option value="divorced">Divorced</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Occupation *</label>
                    <input type="text" name="occupation" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Monthly Income *</label>
                    <input type="number" name="monthly_income" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Proof of Residency *</label>
                    <input type="text" name="proof_of_residency" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Government-issued ID *</label>
                    <input type="text" name="government_id" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Spouse Name</label>
                    <input type="text" name="spouse_name" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <label>Number of Dependents *</label>
                    <input type="number" name="number_of_dependents" class="form-control" required>
                </div>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-5">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>