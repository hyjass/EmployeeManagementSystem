<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 0, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        if (!empty($_FILES['profile_picture']['name']) && $_FILES['profile_picture']['error'] === 0) {
            $target_folder = "uploads/"; // Folder to store uploaded files
            $target_file = $target_folder . basename($_FILES['profile_picture']['name']); // Full file path

            // Ab uploaded file ko uploads folder me daldo temporary location se
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                // database me update kro
                $sql = "UPDATE Users SET profile_picture = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt->execute([$target_file, $user_id])) {
                    $response = ['status' => 1, 'message' => 'Profile picture updated', 'filePath' => $target_file];
                } else {
                    $response = ['status' => 0, 'message' => 'Failed to update database'];
                }
            } else {
                $response = ['status' => 0, 'message' => 'Failed to upload file'];
            }
        }
    } catch (PDOException $e) {
        $response = ['status' => 0, 'message' => 'Database error: ' . $e->getMessage()];
    }

    echo json_encode($response);
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid request method']);
}

?>