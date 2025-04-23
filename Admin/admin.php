<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "barangay_bucandala"; // Changed to match your database

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  http_response_code(500);
  die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
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
      echo json_encode($result->fetch_assoc());
    } else {
      $result = $conn->query("SELECT * FROM barangay_clearance");
      $forms = [];
      while ($row = $result->fetch_assoc()) {
        $forms[] = $row;
      }
      echo json_encode($forms);
    }
    break;

  case 'POST':
    $data = json_decode(file_get_contents('php://input'), true);
    
    $required = [
      'first_name', 'middle_name', 'last_name', 'address',
      'birth_date', 'age', 'mobile_number', 'purpose',
      'student_patient_name', 'student_patient_address',
      'relationship', 'email', 'shipping_method'
    ];
    
    foreach ($required as $field) {
      if (empty($data[$field])) {
        http_response_code(400);
        die(json_encode(["error" => "Missing required field: $field"]));
      }
    }

    $stmt = $conn->prepare("INSERT INTO barangay_clearance (
      first_name, middle_name, last_name, address, birth_date, age, 
      mobile_number, years_of_stay, purpose, student_patient_name, 
      student_patient_address, relationship, email, shipping_method
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
      "sssssissssssss",
      $data['first_name'],
      $data['middle_name'],
      $data['last_name'],
      $data['address'],
      $data['birth_date'],
      $data['age'],
      $data['mobile_number'],
      $data['years_of_stay'] ?? null,
      $data['purpose'],
      $data['student_patient_name'],
      $data['student_patient_address'],
      $data['relationship'],
      $data['email'],
      $data['shipping_method']
    );

    if ($stmt->execute()) {
      echo json_encode(["id" => $stmt->insert_id]);
    } else {
      http_response_code(500);
      echo json_encode(["error" => "Failed to insert: " . $conn->error]);
    }
    break;

  // PUT and DELETE methods would be similar to your existing code
  // but updated for the barangay_clearance table structure

  default:
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
}

$conn->close();
?>