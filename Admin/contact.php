<?php
// contact.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barangay Website - Contact</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" type="image/png" href="image/imus-logo.png">
    <link rel="stylesheet" href="css/contact.css" />
</head>
<body>

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

    <div style="background-color: #12c009; color: black; font-weight: bold; height: 50px; overflow: hidden; display: flex; align-items: center;">
        <marquee behavior="scroll" direction="left" scrollamount="5" style="width: 100%;">
            🔔 Latest Announcement: Barangay Assembly on April 10, 2025 | Free Medical Check-up on April 15, 2025 | Stay Updated with Barangay Bucandala 1!
        </marquee>
    </div>
    
    <nav>
        <a href="index.php">Home</a>
      
        <div class="dropdown">
          <a href="#" class="dropbtn">Services ▾</a>
          <div class="dropdown-content">
            <a href="barangay-clearance.php">Barangay Clearance</a>
            <a href="certificate-of-indigency.php">Certificate of Indigency</a>
            <a href="certificate-of-residency.php">Certificate of Residency</a>
            <a href="barangay-id.php">Barangay ID</a>
          </div>
        </div>
      
        <a href="contact.php">Contact</a>
        <a href="faq.php">FAQ</a>
      </nav>
    
    <div class="container content">
        <h2>Contact Us</h2>
        <ul class="contact-info">
            <li><strong>Barangay Office:</strong> Bucandala 1, Imus, Philippines, 4103</li>
            <li><strong>Phone:</strong> +46 40 256 14</li>
            <li><strong>Email:</strong> barangaybucandala1@gmail.com</li>
        </ul>
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

    <script src="js/index.js"></script>

</body>
</html>