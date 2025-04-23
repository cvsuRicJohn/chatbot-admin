<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

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

// Get form_type from query parameter or request body
$form_type = $_GET['form_type'] ?? null;
if (!$form_type && in_array($method, ['POST', 'PUT'])) {
    $input = json_decode(file_get_contents('php://input'), true);
    $form_type = $input['form_type'] ?? null;
}

if ($method === 'OPTIONS') {
  http_response_code(200);
  exit();
}

// Helper function to get table name by form_type
function getTableName($form_type) {
    $map = [
        'clearance' => 'barangay_clearance',
        'id' => 'barangay_id',
        'indigency' => 'indigency_certificates',
        'residency' => 'residency_certificates'
    ];
    return $map[strtolower($form_type)] ?? null;
}

// Helper function to get primary key fields and required fields per table
function getTableFields($form_type) {
    $fields = [
        'clearance' => [
            'primary_key' => 'id',
            'required' => ['first_name', 'middle_name', 'last_name', 'address', 'birth_date', 'age', 'mobile_number', 'purpose', 'student_patient_name', 'student_patient_address', 'relationship', 'email', 'shipping_method', 'civil_status', 'status'],
            'all_fields' => ['first_name', 'middle_name', 'last_name', 'address', 'birth_date', 'age', 'mobile_number', 'years_of_stay', 'purpose', 'student_patient_name', 'student_patient_address', 'relationship', 'email', 'shipping_method', 'civil_status', 'status']
        ],
        'id' => [
            'primary_key' => 'id',
            'required' => ['first_name', 'middle_name', 'last_name', 'date_of_birth', 'address', 'government_id', 'status'],
            'all_fields' => ['first_name', 'middle_name', 'last_name', 'date_of_birth', 'address', 'government_id', 'status']
        ],
        'indigency' => [
            'primary_key' => 'id',
            'required' => ['first_name', 'middle_name', 'last_name', 'date_of_birth', 'civil_status', 'occupation', 'monthly_income', 'proof_of_residency', 'government_id', 'number_of_dependents', 'status'],
            'all_fields' => ['first_name', 'middle_name', 'last_name', 'date_of_birth', 'civil_status', 'occupation', 'monthly_income', 'proof_of_residency', 'government_id', 'spouse_name', 'number_of_dependents', 'status']
        ],
        'residency' => [
            'primary_key' => 'id',
            'required' => ['first_name', 'middle_name', 'last_name', 'date_of_birth', 'government_id', 'complete_address', 'proof_of_residency', 'purpose_of_certificate', 'status'],
            'all_fields' => ['first_name', 'middle_name', 'last_name', 'date_of_birth', 'government_id', 'complete_address', 'proof_of_residency', 'purpose_of_certificate', 'status']
        ]
    ];
    return $fields[strtolower($form_type)] ?? null;
}

switch ($method) {
  case 'GET':
    if ($id) {
      if (!$form_type) {
        http_response_code(400);
        echo json_encode(["error" => "form_type parameter is required for GET by ID"]);
        exit();
      }
      $table = getTableName($form_type);
      if (!$table) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid form_type"]);
        exit();
      }
      $fields = getTableFields($form_type);
      $stmt = $conn->prepare("SELECT * FROM $table WHERE {$fields['primary_key']} = ?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $result = $stmt->get_result();
      $data = $result->fetch_assoc();
      error_log("GET request for ID $id, form_type $form_type: " . json_encode($data));
      echo json_encode($data ?: []);
      exit();
    } else {
      // Aggregate all forms from all tables with form_type label
      $allForms = [];

      // Clearance
      $result = $conn->query("SELECT id, first_name, purpose AS details, status, 'Clearance' AS form_type FROM barangay_clearance");
      while ($row = $result->fetch_assoc()) {
        error_log("GET all forms clearance: " . json_encode($row));
        $allForms[] = $row;
      }
      // ID
      $result = $conn->query("SELECT id, first_name, government_id AS details, status, 'ID' AS form_type FROM barangay_id");
      while ($row = $result->fetch_assoc()) {
        error_log("GET all forms id: " . json_encode($row));
        $allForms[] = $row;
      }
      // Indigency
      $result = $conn->query("SELECT id, first_name, occupation AS details, status, 'Indigency' AS form_type FROM indigency_certificates");
      while ($row = $result->fetch_assoc()) {
        error_log("GET all forms indigency: " . json_encode($row));
        $allForms[] = $row;
      }
      // Residency
      $result = $conn->query("SELECT id, first_name, purpose_of_certificate AS details, status, 'Residency' AS form_type FROM residency_certificates");
      while ($row = $result->fetch_assoc()) {
        error_log("GET all forms residency: " . json_encode($row));
        $allForms[] = $row;
      }

      echo json_encode($allForms);
      exit();
    }
    break;

  case 'POST':
    if (!$form_type) {
      http_response_code(400);
      echo json_encode(["error" => "form_type parameter is required for POST"]);
      exit();
    }
    $table = getTableName($form_type);
    $fields = getTableFields($form_type);
    if (!$table || !$fields) {
      http_response_code(400);
      echo json_encode(["error" => "Invalid form_type"]);
      exit();
    }
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      http_response_code(400);
      echo json_encode(["error" => "Invalid JSON data received"]);
      exit();
    }
    // For clearance, set status to Pending if missing
    if (strtolower($form_type) === 'clearance' && empty($data['status'])) {
      $data['status'] = 'Pending';
    }
    foreach ($fields['required'] as $field) {
      if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required field: $field"]);
        exit();
      }
    }
    $columns = [];
    $placeholders = [];
    $types = '';
    $values = [];
    foreach ($fields['all_fields'] as $field) {
      if (isset($data[$field])) {
        $columns[] = $field;
        $placeholders[] = '?';
        // Determine type for bind_param
        if (is_int($data[$field])) {
          $types .= 'i';
        } elseif (is_double($data[$field]) || is_float($data[$field])) {
          $types .= 'd';
        } else {
          $types .= 's';
        }
        $values[] = $data[$field];
      }
    }
    $sql = "INSERT INTO $table (" . implode(',', $columns) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    if ($stmt->execute()) {
      echo json_encode(["id" => $stmt->insert_id]);
      exit();
    } else {
      http_response_code(500);
      echo json_encode(["error" => "Failed to insert: " . $conn->error]);
      exit();
    }
    break;

  case 'PUT':
    if (!$id) {
      http_response_code(400);
      echo json_encode(["error" => "ID is required"]);
      exit();
    }
    if (!$form_type) {
      http_response_code(400);
      echo json_encode(["error" => "form_type parameter is required for PUT"]);
      exit();
    }
    $table = getTableName($form_type);
    $fields = getTableFields($form_type);
    if (!$table || !$fields) {
      http_response_code(400);
      echo json_encode(["error" => "Invalid form_type"]);
      exit();
    }
    $data = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
      http_response_code(400);
      echo json_encode(["error" => "Invalid JSON data received"]);
      exit();
    }
    // Normalize status value if present
    if (isset($data['status'])) {
      $allowedStatuses = ['pending', 'approved', 'rejected'];
      $statusLower = strtolower($data['status']);
      if (in_array($statusLower, $allowedStatuses)) {
        $data['status'] = ucfirst($statusLower);
      } else {
        http_response_code(400);
        echo json_encode(["error" => "Invalid status value"]);
        exit();
      }
    }
    foreach ($fields['required'] as $field) {
      if (!isset($data[$field])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required field: $field"]);
        exit();
      }
    }
    $setClauses = [];
    $types = '';
    $values = [];
    foreach ($fields['all_fields'] as $field) {
      if (isset($data[$field])) {
        $setClauses[] = "$field = ?";
        if (is_int($data[$field])) {
          $types .= 'i';
        } elseif (is_double($data[$field]) || is_float($data[$field])) {
          $types .= 'd';
        } else {
          $types .= 's';
        }
        $values[] = $data[$field];
      }
    }
    $types .= 'i'; // for id
    $values[] = $id;
    $sql = "UPDATE $table SET " . implode(', ', $setClauses) . " WHERE {$fields['primary_key']} = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
      http_response_code(500);
      echo json_encode(["error" => "Failed to prepare statement: " . $conn->error]);
      exit();
    }
    $stmt->bind_param($types, ...$values);
    error_log("PUT request to update ID $id, form_type $form_type with data: " . json_encode($data));
    $executeResult = $stmt->execute();
    if ($executeResult) {
        error_log("Update successful for ID $id, affected rows: " . $stmt->affected_rows);
        echo json_encode(["updated" => $stmt->affected_rows]);
        exit();
    } else {
        error_log("Update failed for ID $id: " . $stmt->error);
        http_response_code(500);
        echo json_encode(["error" => "Failed to update form: " . $stmt->error]);
        exit();
    }
    break;

  case 'DELETE':
    if (!$id) {
      http_response_code(400);
      echo json_encode(["error" => "ID is required"]);
      exit();
    }
    if (!$form_type) {
      http_response_code(400);
      echo json_encode(["error" => "form_type parameter is required for DELETE"]);
      exit();
    }
    $table = getTableName($form_type);
    if (!$table) {
      http_response_code(400);
      echo json_encode(["error" => "Invalid form_type"]);
      exit();
    }
    $stmt = $conn->prepare("DELETE FROM $table WHERE id=?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
      echo json_encode(["deleted" => $stmt->affected_rows]);
      exit();
    } else {
      http_response_code(500);
      echo json_encode(["error" => "Failed to delete form"]);
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
