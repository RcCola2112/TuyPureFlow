<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (
    !isset($data['name'], $data['email'], $data['phone'], $data['password'],
    $data['street'], $data['city'], $data['province'], $data['zip_code'],
    $data['latitude'], $data['longitude'])
) {
    echo json_encode(["success" => false, "message" => "Missing required fields."]);
    exit;
}

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "tuypureflow";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Sanitize data
$name = $conn->real_escape_string($data['name']);
$email = strtolower(trim($conn->real_escape_string($data['email'])));
$phone = $conn->real_escape_string($data['phone']);
$passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
$role = "consumer";

// Address fields
$street = $conn->real_escape_string($data['street']);
$city = $conn->real_escape_string($data['city']);
$province = $conn->real_escape_string($data['province']);
$zip_code = $conn->real_escape_string($data['zip_code']);
$latitude = $conn->real_escape_string($data['latitude']);
$longitude = $conn->real_escape_string($data['longitude']);

// Fetch all emails for robust checking
$allEmails = [];
$result = $conn->query("SELECT email FROM consumer");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $dbEmail = strtolower(trim($row['email']));
        $allEmails[] = $dbEmail;
    }
}
if (in_array($email, $allEmails)) {
    echo json_encode(["success" => false, "message" => "Email already exists."]);
    exit;
}

// Insert into consumer
$insertConsumer = "INSERT INTO consumer (name, email, phone, role, password)
                   VALUES ('$name', '$email', '$phone', '$role', '$passwordHash')";

if ($conn->query($insertConsumer) === TRUE) {
    $consumer_id = $conn->insert_id; // Get the new consumer's ID

    // Insert into address (no distributor_id)
    $insertAddress = "INSERT INTO address (consumer_id, street, city, province, zip_code, latitude, longitude)
                      VALUES ('$consumer_id', '$street', '$city', '$province', '$zip_code', '$latitude', '$longitude')";

    if ($conn->query($insertAddress) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        error_log("Address insert failed: " . $conn->error);
        echo json_encode(["success" => false, "message" => "Address insert failed: " . $conn->error]);
    }
} else {
    error_log("Consumer insert failed: " . $conn->error);
    echo json_encode(["success" => false, "message" => "Consumer insert failed: " . $conn->error]);
}

$conn->close();
?> 