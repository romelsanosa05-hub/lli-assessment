<?php 
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$user = "root"; 
$pass = "";     
$db   = "llidb";

$conn = new mysqli($host, $user, $pass, $db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $result = $conn->query("SELECT * FROM tbluser ORDER BY userid DESC");
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode($users);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->username) && !empty($data->password) && !empty($data->fullname)) {
            $stmt = $conn->prepare("INSERT INTO tbluser (username, password, fullname) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $data->username, $data->password, $data->fullname);
            
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "User created"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to create user"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Incomplete data"]);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && !empty($data->username) && !empty($data->password) && !empty($data->fullname)) {
            $stmt = $conn->prepare("UPDATE tbluser SET username = ?, password = ?, fullname = ? WHERE userid = ?");
            $stmt->bind_param("sssi", $data->username, $data->password, $data->fullname, $data->id);
            
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "User updated"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update user"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Incomplete data"]);
        }
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id)) {
            $stmt = $conn->prepare("DELETE FROM tbluser WHERE userid = ?");
            $stmt->bind_param("i", $data->id);
            
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "message" => "User deleted"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to delete user"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing user ID"]);
        }
        break;
}
?>