<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Disable error display to prevent HTML output in JSON responses
error_reporting(0);
ini_set('display_errors', 0);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barangay_bucandala";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  http_response_code(500);
  echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
  exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$id = $request[0] ?? null;

if ($method === 'OPTIONS') {
  http_response_code(200);
  exit();
}

switch ($method) {
  case 'GET':
    if ($id) {
      $stmt = $conn->prepare("SELECT * FROM barangay_clearance WHERE id = ?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $result = $stmt->get_result();
      $data = $result->fetch_assoc();
      echo json_encode($data ?: []);
      exit();
    } else {
      $result = $conn->query("SELECT * FROM barangay_clearance");
      $forms = [];
      while ($row = $result->fetch_assoc()) {
        $forms[] = $row;
      }
      echo json_encode($forms);
      exit();
    }
    break;

  case 'POST':
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      http_response_code(400);
      echo json_encode(["error" => "Invalid JSON data received"]);
      exit();
    }
    
    $required = [
      'first_name', 'middle_name', 'last_name', 'address',
      'birth_date', 'age', 'mobile_number', 'purpose',
      'student_patient_name', 'student_patient_address',
      'relationship', 'email', 'shipping_method'
    ];
    
    foreach ($required as $field) {
      if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required field: $field"]);
        exit();
      }
    }

    // Assign variables for bind_param (must be passed by reference)
    $first_name = $data['first_name'];
    $middle_name = $data['middle_name'];
    $last_name = $data['last_name'];
    $address = $data['address'];
    $birth_date = $data['birth_date'];
    $age = $data['age'];
    $mobile_number = $data['mobile_number'];
    $years_of_stay = $data['years_of_stay'] ?? null;
    $purpose = $data['purpose'];
    $student_patient_name = $data['student_patient_name'];
    $student_patient_address = $data['student_patient_address'];
    $relationship = $data['relationship'];
    $email = $data['email'];
    $shipping_method = $data['shipping_method'];

    $stmt = $conn->prepare("INSERT INTO barangay_clearance (
      first_name, middle_name, last_name, address, birth_date, age, 
      mobile_number, years_of_stay, purpose, student_patient_name, 
      student_patient_address, relationship, email, shipping_method
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
      "sssssissssssss",
      $first_name,
      $middle_name,
      $last_name,
      $address,
      $birth_date,
      $age,
      $mobile_number,
      $years_of_stay,
      $purpose,
      $student_patient_name,
      $student_patient_address,
      $relationship,
      $email,
      $shipping_method
    );

    if ($stmt->execute()) {
      echo json_encode(["id" => $stmt->insert_id]);
      exit();
    } else {
      http_response_code(500);
      echo json_encode(["error" => "Failed to insert: " . $conn->error]);
      exit();
    }
    break;

  default:
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit();
}

$conn->close();
?>
