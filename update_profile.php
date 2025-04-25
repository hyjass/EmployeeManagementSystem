<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 0, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = [];

    try {
        if (isset($_POST['field']) && isset($_POST['value'])) {
            $field = $_POST['field'];
            $value = $_POST['value'];

            if ($field === 'qualifications' || $field === 'experiences') {
                if (!is_array($value)) {
                    $value = json_decode($value, true); //json string to array kiya hai
                }

                if (!is_array($value)) {
                    $response = ['status' => 0, 'message' => 'Invalid data format'];
                } else {
                    $table = ($field === 'qualifications') ? 'Qualifications' : 'Experiences';
                    // Delete existing records for this user
                    $sql = "DELETE FROM $table WHERE user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$user_id]);

                    // Insert updated records
                    $sql = "INSERT INTO $table (user_id, " . rtrim($field, 's') . ") VALUES (?, ?)";
                    $stmt = $conn->prepare($sql);
                    // print_r($value);

                    foreach ($value as $item) {
                        $stmt->execute([$user_id, $item]);//fresh data store hoga naki update existing value
                    }

                    $response = ['status' => 1, 'message' => ($field) . ' updated successfully'];
                }
            } else {
                // For single-value fields (e.g., full_name, dob)
                $sql = "UPDATE Users SET $field = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt->execute([$value, $user_id])) {
                    $response = ['status' => 1, 'message' => 'Field updated successfully'];
                } else {
                    $response = ['status' => 0, 'message' => 'Failed to update field'];
                }
            }
        } else {
            $response = ['status' => 0, 'message' => 'Invalid request data'];
        }
    } catch (PDOException $e) {
        $response = ['status' => 0, 'message' => 'Database error: ' . $e->getMessage()];
    }

    echo json_encode($response);
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid request method']);
}

?>