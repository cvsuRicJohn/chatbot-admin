<?php
// Database connection parameters
$servername = "localhost"; // Change if your DB server is different
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "barangay_bucandala"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input data
    $firstName = htmlspecialchars($_POST['firstName']);
    $middleName = htmlspecialchars($_POST['middleName']);
    $lastName = htmlspecialchars($_POST['lastName']);
    $dateOfBirth = htmlspecialchars($_POST['dateOfBirth']);
    $address = htmlspecialchars($_POST['address']);
    $governmentId = htmlspecialchars($_POST['governmentId']);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO barangay_id (first_name, middle_name, last_name, date_of_birth, address, government_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstName, $middleName, $lastName, $dateOfBirth, $address, $governmentId);

    // Execute the statement
    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barangay ID Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="image/imus-logo.png">
    <link rel="stylesheet" href="css/services.css" />
</head>
<body>

    <!-- Header and Navigation -->
    <div style="background-color: #0056b3; color: white; display: flex; justify-content: space-between; align-items: center; padding: 5px 20px; font-family: Arial, sans-serif; font-size: 14px;">
        <div>
          <strong>GOVPH</strong> | The Official Website of Barangay Bucandala 1, Imus Cavite
        </div>
        <div style="display: flex; align-items: center; gap: 15px;">
          <a href="#" style="color: white;"><i class="fab fa-facebook-f"></i></a>
          <a href="#" style="color: white;"><i class="fab fa-youtube"></i></a>
          <a href="#" style="color: white;"><i class="fab fa-twitter"></i></a>
          <a href="tel:+464025614" style="color: white;"><i class="fas fa-phone-alt"></i></a>
          <span id="dateTimePH"></span>
        </div>
    </div>
    
    <nav>
        <a href="index.html">Home</a>
      
        <div class="dropdown">
          <a href="#" class="dropbtn">Services ▾</a>
          <div class="dropdown-content">
            <a href="barangay-clearance.html">Barangay Clearance</a>
            <a href="certificate-of-indigency.html">Certificate of Indigency</a>
            <a href="certificate-of-residency.html">Certificate of Residency</a>
            <a href="barangay-id.php">Barangay ID</a>
          </div>
        </div>
      
        <a href="contact.html">Contact</a>
        <a href="faq.html">FAQ</a>
      </nav>
    <div class="container-fluid px-5 py-4">
        <h2 class="text-center mb-4">Barangay ID Form</h2>
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>First Name *</label>
                    <input type="text" name="firstName" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Middle Name *</label>
                    <input type="text" name="middleName" class="form-control" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Last Name *</label>
                    <input type="text" name="lastName" class="form-control" required>
                </div>
    
                <div class="form-group col-md-6">
                    <label>Date of Birth *</label>
                    <input type="date" name="dateOfBirth" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Address *</label>
                    <input type="text" name="address" class="form-control" required>
                </div>
    
                <div class="form-group col-md-12">
                    <label>Government-issued ID *</label>
                    <input type="text" name="governmentId" class="form-control" required>
                </div>
            </div>
    
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary px-5">Submit</button>
            </div>
        </form>
    </div>
    

    <div class="footer">
        <div class="footer-content">
          <img src="image/imus-logo.png" alt="Barangay Logo" class="footer-logo">
          <div class="footer-text">
            <p>Copyright &copy; 2025 The Official Website of Barangay Bucandala 1, Imus Cavite. All Rights Reserved.</p>
            <p>Bucandala 1 Barangay Hall, Imus, Cavite, Philippines 4103.</p>
            <p>Call Us Today: +46 40 256 14</p>
          </div>
        </div>
    </div>

    <iframe src="chatbot.html"
    style="position: fixed; bottom: 10px; right: 10px; width: 340px; height: 800px; border: none; z-index: 999;"> 
    </iframe>

    <script src="js/services.js"></script>

</body>
</html>