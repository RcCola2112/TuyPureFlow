<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
$requiredFields = ['business_name', 'owner_name', 'contact_number', 'email', 'operating_hours', 'street', 'city', 'region', 'zip_code', 'latitude', 'longitude', 'password'];
foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty(trim($data[$field]))) {
        echo json_encode(["success" => false, "message" => "Missing required field: $field"]);
        exit;
    }
}

// DB credentials
$host = "localhost";
$username = "root";
$password = "";
$database = "tuypureflow";

// Create connection
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Sanitize input
$business_name = $conn->real_escape_string($data['business_name']);
$owner_name = $conn->real_escape_string($data['owner_name']);
$contact_number = $conn->real_escape_string($data['contact_number']);
$email = $conn->real_escape_string($data['email']);
$operating_hours = $conn->real_escape_string($data['operating_hours']);
$street = $conn->real_escape_string($data['street']);
$city = $conn->real_escape_string($data['city']);
$province = $conn->real_escape_string($data['region']);
$zip_code = $conn->real_escape_string($data['zip_code']);
$latitude = floatval($data['latitude']);
$longitude = floatval($data['longitude']);
$passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
$usernameInput = isset($data['username']) ? $conn->real_escape_string($data['username']) : null;
$role = "Distributor";

// Decode base64 file data and save as files, store file paths
$uploadDir = __DIR__ . '/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

function saveBase64File($base64, $prefix, $distributor_id, $uploadDir) {
    if (!$base64) return null;
    $filename = $prefix . '_' . $distributor_id . '_' . uniqid() . '.bin';
    $filePath = $uploadDir . $filename;
    file_put_contents($filePath, base64_decode($base64));
    return 'uploads/' . $filename;
}

$business_permit_path = isset($data['business_permit']) ? saveBase64File($data['business_permit'], 'permit', time(), $uploadDir) : null;
$valid_government_id_path = isset($data['valid_government_id']) ? saveBase64File($data['valid_government_id'], 'id', time(), $uploadDir) : null;
$proof_of_address_path = isset($data['proof_of_address']) ? saveBase64File($data['proof_of_address'], 'address', time(), $uploadDir) : null;

// Check if email already exists
$checkEmail = $conn->prepare("SELECT distributor_id FROM distributor WHERE email = ?");
$checkEmail->bind_param("s", $email);
$checkEmail->execute();
$checkEmail->store_result();
if ($checkEmail->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already exists."]);
    $checkEmail->close();
    $conn->close();
    exit;
}
$checkEmail->close();

// Insert into distributor table (owner_name goes into name column)
$stmt = $conn->prepare("INSERT INTO distributor (name, email, phone, role, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $owner_name, $email, $contact_number, $role, $passwordHash);
if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Failed to insert distributor."]);
    $stmt->close();
    $conn->close();
    exit;
}
$distributor_id = $stmt->insert_id;
$stmt->close();

// Insert into distributor_address table
$insertAddress = $conn->prepare("INSERT INTO distributor_address (distributor_id, street, city, province, zip_code, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?)");
$insertAddress->bind_param("issssdd", $distributor_id, $street, $city, $province, $zip_code, $latitude, $longitude);
if (!$insertAddress->execute()) {
    echo json_encode(["success" => false, "message" => "Failed to insert distributor address."]);
    $insertAddress->close();
    $conn->close();
    exit;
}
$insertAddress->close();

// Insert into distributor_documents table
$insertDocs = $conn->prepare("INSERT INTO distributor_documents (distributor_id, business_permit, valid_government_id, proof_of_address) VALUES (?, ?, ?, ?)");
$insertDocs->bind_param("isss", $distributor_id, $business_permit_path, $valid_government_id_path, $proof_of_address_path);
if (!$insertDocs->execute()) {
    echo json_encode(["success" => false, "message" => "Failed to insert distributor documents."]);
    $insertDocs->close();
    $conn->close();
    exit;
}
$insertDocs->close();

// Insert into shop table (business_name goes into business_name column)
$insertShop = $conn->prepare("INSERT INTO shop (distributor_id, business_name, location, contact_number, latitude, longitude, average_rating, open_time, close_time) VALUES (?, ?, ?, ?, ?, ?, 0, '08:00:00', '17:00:00')");
$insertShop->bind_param("isssdd", $distributor_id, $business_name, $province, $contact_number, $latitude, $longitude);
if (!$insertShop->execute()) {
    echo json_encode(["success" => false, "message" => "Failed to create shop record."]);
    $insertShop->close();
    $conn->close();
    exit;
}
$insertShop->close();

// Final response
echo json_encode(["success" => true, "message" => "Distributor registered successfully."]);
$conn->close();
?> 